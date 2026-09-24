@php
    $uId = is_array($user) ? $user['id'] : $user->id;
    $uName = is_array($user) ? $user['name'] : $user->name;
    $uEmail = is_array($user) ? $user['email'] : $user->email;
@endphp

<!-- Modal Penetapan Kata Sandi Baru oleh Admin -->
<div id="passwordModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm hidden" role="dialog" aria-modal="true" aria-labelledby="modal-title-password">
    <div class="bg-[#111111] border border-[#333333] rounded-[8px] max-w-lg w-full p-6 shadow-2xl relative text-left">
        
        <div class="flex items-start justify-between pb-4 border-b border-[#222222]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-[6px] bg-[#001733] text-[#0070f3] flex items-center justify-center border border-[#003b80]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-tight" id="modal-title-password">Tetapkan Kata Sandi Baru</h3>
                    <p class="text-xs text-[#a1a1a1]">Gunakan jika pemilik akun lupa kata sandi</p>
                </div>
            </div>
            <button type="button" onclick="closePasswordModal()" class="close-password-modal text-[#888888] hover:text-white p-1 rounded-[6px] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.accounts.reset-password', is_array($user) ? $user['id'] : $user) }}" class="space-y-4 pt-4">
            @csrf
            
            <div class="p-3 rounded-[6px] bg-[#0a0a0a] border border-[#222222] text-xs text-[#a1a1a1] font-mono">
                <p>Nama Akun: <strong class="text-white font-sans font-medium">{{ $uName }}</strong></p>
                <p class="mt-0.5">Email: <strong class="text-white">{{ $uEmail }}</strong></p>
            </div>

            <!-- New Password Input -->
            <div class="space-y-1.5">
                <label for="modal_password" class="block text-xs font-medium text-[#a1a1a1] uppercase tracking-wider">
                    Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="password" 
                    id="modal_password" 
                    name="password" 
                    required 
                    minlength="8"
                    placeholder="Minimal 8 karakter..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] px-3.5 py-2 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition placeholder-[#666666] font-mono"
                />
                @error('password')
                    <p class="mt-1 text-xs text-rose-400 font-mono">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password Input -->
            <div class="space-y-1.5">
                <label for="modal_password_confirmation" class="block text-xs font-medium text-[#a1a1a1] uppercase tracking-wider">
                    Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="password" 
                    id="modal_password_confirmation" 
                    name="password_confirmation" 
                    required 
                    minlength="8"
                    placeholder="Ulangi kata sandi baru..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] px-3.5 py-2 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition placeholder-[#666666] font-mono"
                />
                <span class="text-[11px] text-[#888888] block pt-1">
                    Kata sandi lama tersimpan dalam hash satu arah dan tidak dapat dilihat oleh administrator.
                </span>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2 border-t border-[#222222]">
                <button 
                    type="button" 
                    onclick="closePasswordModal()" 
                    class="close-password-modal bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-3.5 py-1.5 rounded-[6px] transition">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-1.5 rounded-[6px] transition shadow-none active:scale-95">
                    Simpan Kata Sandi Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal() {
        const modal = document.getElementById('passwordModal') || document.getElementById('passwordResetModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closePasswordModal() {
        const modal = document.getElementById('passwordModal') || document.getElementById('passwordResetModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('passwordModal') || document.getElementById('passwordResetModal');
        const openBtn = document.getElementById('openPasswordResetModalBtn');
        const closeBtns = document.querySelectorAll('.close-password-modal');

        if (openBtn) {
            openBtn.addEventListener('click', openPasswordModal);
        }

        closeBtns.forEach(btn => {
            btn.addEventListener('click', closePasswordModal);
        });

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closePasswordModal();
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closePasswordModal();
            }
        });

        @if ($errors->has('password'))
            openPasswordModal();
        @endif
    });
</script>
