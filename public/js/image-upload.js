/**
 * Client-side image preprocessing before upload.
 *
 * Processes an image file by:
 *  1. Loading it into a canvas
 *  2. Center-cropping to the target aspect ratio
 *  3. Resizing to fit within maxWidth × maxHeight
 *  4. Encoding to WebP at the given quality
 *
 * Returns a Promise<File> (WebP) ready to be injected into a FormData / FileList.
 *
 * @param {File}   file         - Original image file from <input type="file">
 * @param {object} [opts]
 * @param {number} [opts.aspectRatio]  - Target W/H ratio, e.g. 16/9 or 4/3. Null = keep original.
 * @param {number} [opts.maxWidth]     - Max output width in pixels (default 1280).
 * @param {number} [opts.maxHeight]    - Max output height in pixels (default 720 for 16:9, 960 for 4:3).
 * @param {number} [opts.quality]      - WebP quality 0-1 (default 0.82).
 * @param {string} [opts.outputName]   - Output file name (default: original name with .webp extension).
 * @returns {Promise<File>}
 */
export async function preprocessImage(file, opts = {}) {
    const {
        aspectRatio = null,
        maxWidth = 1280,
        maxHeight = maxWidth,
        quality = 0.82,
        outputName = null,
    } = opts;

    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                try {
                    // --- Step 1: Determine crop region ---
                    let srcX = 0, srcY = 0, srcW = img.width, srcH = img.height;

                    if (aspectRatio) {
                        const srcRatio = img.width / img.height;
                        if (srcRatio > aspectRatio) {
                            // Image is wider → crop sides
                            srcW = Math.round(img.height * aspectRatio);
                            srcX = Math.round((img.width - srcW) / 2);
                        } else if (srcRatio < aspectRatio) {
                            // Image is taller → crop top/bottom
                            srcH = Math.round(img.width / aspectRatio);
                            srcY = Math.round((img.height - srcH) / 2);
                        }
                    }

                    // --- Step 2: Determine output dimensions (fit within maxWidth × maxHeight) ---
                    const targetRatio = srcW / srcH;
                    let outW = srcW;
                    let outH = srcH;

                    if (outW > maxWidth) {
                        outW = maxWidth;
                        outH = Math.round(outW / targetRatio);
                    }
                    if (outH > maxHeight) {
                        outH = maxHeight;
                        outW = Math.round(outH * targetRatio);
                    }

                    // --- Step 3: Draw onto canvas ---
                    const canvas = document.createElement('canvas');
                    canvas.width = outW;
                    canvas.height = outH;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, srcX, srcY, srcW, srcH, 0, 0, outW, outH);

                    // --- Step 4: Encode to WebP ---
                    canvas.toBlob(
                        (blob) => {
                            if (!blob) {
                                reject(new Error(
                                    'Browser tidak dapat mengonversi gambar ke WebP. ' +
                                    'Coba gunakan browser yang lebih baru (Chrome / Edge / Firefox).'
                                ));
                                return;
                            }
                            const baseName = (outputName ?? file.name).replace(/\.[^.]+$/, '');
                            const processedFile = new File([blob], `${baseName}.webp`, {
                                type: 'image/webp',
                                lastModified: Date.now(),
                            });
                            resolve(processedFile);
                        },
                        'image/webp',
                        quality,
                    );
                } catch (err) {
                    reject(err);
                }
            };
            img.onerror = () => reject(new Error(
                'File gambar tidak dapat dibaca. Pastikan file tidak rusak.'
            ));
            img.src = e.target.result;
        };
        reader.onerror = () => reject(new Error(
            'Gagal membaca file. Pastikan file tidak rusak atau sedang digunakan program lain.'
        ));
        reader.readAsDataURL(file);
    });
}

/**
 * Show an inline error banner inside the upload wrapper element.
 * Removes any previously shown banner first.
 *
 * @param {HTMLElement|null} wrapper   - The wrapping div that contains the file input.
 * @param {string}           message   - Human-readable error message.
 */
function showUploadError(wrapper, message) {
    // Remove existing error banner
    wrapper?.querySelector('[data-upload-error]')?.remove();

    if (!wrapper) {
        // Last resort fallback
        alert(`Gagal memproses gambar: ${message}`);
        return;
    }

    const banner = document.createElement('p');
    banner.setAttribute('data-upload-error', '');
    banner.className = 'text-[11px] text-[#ff453a] font-mono mt-1.5 flex items-start gap-1.5';
    banner.innerHTML = `
        <svg class="w-3.5 h-3.5 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <span>${message}</span>
    `;
    wrapper.appendChild(banner);

    // Scroll the banner into view so user definitely sees it
    banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Clear any error banner shown by showUploadError.
 *
 * @param {HTMLElement|null} wrapper
 */
function clearUploadError(wrapper) {
    wrapper?.querySelector('[data-upload-error]')?.remove();
}

/**
 * Attaches client-side image preprocessing to a <form> with a file <input>.
 *
 * On form submit, the selected file is preprocessed and injected back into the
 * FormData that is submitted, replacing the original. The original <input> is
 * not modified (so native FileList restrictions are bypassed via FormData).
 *
 * Error handling strategy:
 *  - If preprocessing fails → show inline error banner, DO NOT submit.
 *    The user must pick a different / valid file.
 *  - If network/server fails during fetch → show inline error, button restored.
 *  - On every new submit attempt, previous error banners are cleared.
 *
 * @param {HTMLFormElement}  form       - The form element.
 * @param {HTMLInputElement} fileInput  - The file input inside the form.
 * @param {object}           opts       - Same options as preprocessImage().
 */
export function attachImagePreprocessor(form, fileInput, opts = {}) {
    if (!form || !fileInput) return;

    // The wrapper is the nearest ancestor with id="photo-upload-wrapper",
    // falling back to the immediate parent. Error banners are appended here.
    const wrapper = fileInput.closest('#photo-upload-wrapper') ?? fileInput.parentElement;

    // Clear error banner whenever user picks a new file
    fileInput.addEventListener('change', () => clearUploadError(wrapper));

    form.addEventListener('submit', async (e) => {
        // Clear any previous upload error on each submit attempt
        clearUploadError(wrapper);

        // No file selected → let the form submit normally (photo is optional)
        if (!fileInput.files || fileInput.files.length === 0) return;

        e.preventDefault();

        const submitBtn = form.querySelector('[type="submit"]');
        const originalBtnHTML = submitBtn?.innerHTML ?? '';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Memproses gambar...
            `;
        }

        const restoreBtn = () => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHTML;
            }
        };

        // ── Step 1: Preprocess the image client-side ──────────────────────────
        let processedFile;
        try {
            processedFile = await preprocessImage(fileInput.files[0], opts);
        } catch (err) {
            console.error('[image-upload] Preprocessing error:', err);
            restoreBtn();
            // Show error to user and STOP — do not submit the raw file
            showUploadError(
                wrapper,
                err.message ?? 'Gagal memproses gambar. Coba pilih file lain.'
            );
            return;
        }

        // ── Step 2: Submit via fetch with the processed file ──────────────────
        try {
            const formData = new FormData(form);
            formData.set(fileInput.name, processedFile, processedFile.name);

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            // Non-redirect = validation error or other server response → render it
            const html = await response.text();
            document.open();
            document.write(html);
            document.close();
            history.replaceState(null, '', form.action);

        } catch (networkErr) {
            // Network-level failure: offline, timeout, CORS, etc.
            console.error('[image-upload] Network error during submit:', networkErr);
            restoreBtn();
            showUploadError(
                wrapper,
                'Gagal mengunggah karena masalah jaringan. Periksa koneksi internet dan coba lagi.'
            );
        }
    });
}
