<!-- Modal Reset Kata Sandi (Floating Window) -->
<div
    id="passwordResetModal"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity duration-200"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div
            id="passwordModalContent"
            class="relative w-full max-w-md transform overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 text-left shadow-2xl transition-all sm:my-8 sm:p-8"
        >
            <!-- Modal Header -->
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700 text-lg shadow-sm">
                        🔑
                    </span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900" id="modal-title">Ganti Kata Sandi</h3>
                        <p class="text-xs text-slate-500">Tetapkan kata sandi baru untuk akun ini</p>
                    </div>
                </div>
                <button
                    type="button"
                    class="close-password-modal rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
                    aria-label="Tutup"
                >
                    ✕
                </button>
            </div>

            <!-- Target User Info -->
            <div class="my-4 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
                <p><strong>Pengguna:</strong> {{ $user->name }}</p>
                <p class="mt-0.5 font-mono text-[11px] text-slate-500">{{ $user->email }}</p>
            </div>

            <!-- Reset Password Form -->
            <form method="POST" action="{{ route('admin.accounts.reset-password', $user) }}" class="space-y-4">
                @csrf

                <div>
                    <label for="modal_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        id="modal_password"
                        name="password"
                        required
                        minlength="8"
                        placeholder="Minimal 8 karakter"
                        class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="modal_password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        id="modal_password_confirmation"
                        name="password_confirmation"
                        required
                        minlength="8"
                        placeholder="Ulangi kata sandi baru"
                        class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                    >
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 text-[11px] leading-4 text-slate-500">
                    ℹ Kata sandi lama tidak diperlukan. Menetapkan kata sandi baru akan langsung meng-hash dan menimpa kata sandi lama akun bersangkutan.
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        class="close-password-modal rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="rounded-xl bg-sky-700 px-5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200"
                    >
                        Simpan Kata Sandi Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Vanilla JS untuk modal dialog handler
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('passwordResetModal');
        const openBtn = document.getElementById('openPasswordResetModalBtn');
        const closeBtns = document.querySelectorAll('.close-password-modal');

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

        // Close on backdrop click
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        // Close on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        @if ($errors->has('password'))
            // If validation errors occurred on modal submission, reopen modal automatically
            openModal();
        @endif
    });
</script>
