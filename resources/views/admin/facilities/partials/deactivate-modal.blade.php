<!-- Modal Konfirmasi Nonaktifkan Fasilitas -->
<div
    id="deactivateFacilityModal"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity duration-200"
    aria-labelledby="modal-title-deactivate"
    role="dialog"
    aria-modal="true"
>
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div
            id="deactivateModalContent"
            class="relative w-full max-w-lg transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-left shadow-2xl transition-all sm:my-8 sm:p-8"
        >
            <!-- Modal Header -->
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700 text-lg shadow-sm">
                        ⚠
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900" id="modal-title-deactivate">Nonaktifkan Fasilitas</h3>
                        <p class="text-xs text-slate-500">Konfirmasi penonaktifan data master fasilitas</p>
                    </div>
                </div>
                <button
                    type="button"
                    class="close-deactivate-modal rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
                    aria-label="Tutup"
                >
                    ✕
                </button>
            </div>

            <!-- Warning Notice -->
            <div class="my-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-900 space-y-1.5 leading-relaxed">
                <p class="font-bold">Konsekuensi Penonaktifan Master Data:</p>
                <ul class="list-disc pl-4 space-y-1 text-rose-800">
                    <li>Fasilitas akan disembunyikan dari katalog publik Pengunjung dan Pengguna.</li>
                    <li>Seluruh pengajuan reservasi berstatus <strong>Menunggu</strong> yang belum dimulai akan <strong>otomatis Ditolak</strong> oleh sistem.</li>
                    <li>Sistem akan menolak penonaktifan jika masih ada reservasi aktif berstatus <strong>Disetujui</strong> atau <strong>Berlangsung</strong>.</li>
                </ul>
            </div>

            <!-- Facility Summary -->
            <div class="mb-4 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
                <p><strong>Fasilitas:</strong> {{ $facility->name }}</p>
                <p class="text-slate-500 mt-0.5">{{ $facility->type->label() }} — {{ $facility->location }}</p>
            </div>

            <!-- Form Deactivate -->
            <form method="POST" action="{{ route('admin.facilities.deactivate', $facility) }}" class="space-y-4">
                @csrf

                <div>
                    <label for="deactivate_reason" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Alasan Penonaktifan <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="deactivate_reason"
                        name="reason"
                        rows="3"
                        required
                        minlength="5"
                        placeholder="Contoh: Renovasi gedung jangka panjang / inventaris dipindahkan permanen..."
                        class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-rose-600 focus:ring-4 focus:ring-rose-100"
                    >{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        class="close-deactivate-modal rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-200"
                    >
                        Konfirmasi Nonaktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('deactivateFacilityModal');
        const openBtn = document.getElementById('openDeactivateModalBtn');
        const closeBtns = document.querySelectorAll('.close-deactivate-modal');

        function openModal() {
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeModal() {
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        if (openBtn) {
            openBtn.addEventListener('click', openModal);
        }

        closeBtns.forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        @if ($errors->has('reason'))
            openModal();
        @endif
    });
</script>
