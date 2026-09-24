@extends('layouts.admin')

@section('title', 'Buat Akun Civitas Baru · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6">
        <a href="{{ route('admin.accounts.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-medium text-[#888888] hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke kelola akun
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
            Pendaftaran akun baru.
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-[#a1a1a1]">
            Daftarkan akun pengguna (Mahasiswa/Dosen/Staf) atau akun petugas sarpras secara langsung.
        </p>
    </div>

    <!-- Form Container (SRS FR-ADM-03) -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 shadow-none">
        <form method="POST" action="{{ route('admin.accounts.created') }}" class="space-y-6">
            @csrf

            <!-- Full Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-medium text-[#a1a1a1]">
                    Nama lengkap <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required 
                    placeholder="Contoh: Muhammad Raihan, Dr. Ir. Hendra..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-sm rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition placeholder-[#666666]"
                />
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-medium text-[#a1a1a1]">
                    Alamat email kampus <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    placeholder="nama@kampus.ac.id"
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-sm rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition placeholder-[#666666] font-mono"
                />
            </div>

            <!-- Role Selection -->
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-role">
                <label class="block text-xs font-medium text-[#a1a1a1]">
                    Peran akun <span class="text-rose-500">*</span>
                </label>
                <input type="hidden" id="role" name="role" value="{{ old('role', 'pengguna') }}" required>
                <button type="button" onclick="toggleCustomDropdown('role')" id="roleTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-sm rounded-[6px] px-3.5 py-2.5 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="roleLabel">{{ old('role') === 'petugas' ? 'Petugas sarpras operasional' : 'Pengguna fasilitas' }}</span>
                    <svg id="roleChevron" class="custom-dropdown-chevron w-4 h-4 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="roleMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1">
                    <div onclick="selectCustomDropdown('role', 'pengguna', 'Pengguna fasilitas')" data-value="pengguna" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('role', 'pengguna') === 'pengguna' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Pengguna fasilitas</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ old('role', 'pengguna') === 'pengguna' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('role', 'petugas', 'Petugas sarpras operasional')" data-value="petugas" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('role') === 'petugas' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Petugas sarpras operasional</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ old('role') === 'petugas' ? '' : 'hidden' }}">✓</span>
                    </div>
                </div>
            </div>

            <!-- Category Field (Required if role = pengguna, SRS FR-ADM-03) -->
            <div id="categoryContainer" class="space-y-1.5 relative custom-dropdown" id="dropdown-user_category">
                <label class="block text-xs font-medium text-[#a1a1a1]">
                    Kategori pengguna <span class="text-rose-500">*</span>
                </label>
                <input type="hidden" id="user_category" name="user_category" value="{{ old('user_category', 'mahasiswa') }}">
                <button type="button" onclick="toggleCustomDropdown('user_category')" id="user_categoryTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-sm rounded-[6px] px-3.5 py-2.5 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="user_categoryLabel">
                        @if(old('user_category') === 'dosen') Dosen
                        @elseif(old('user_category') === 'staf') Staf akademik / tendik
                        @else Mahasiswa
                        @endif
                    </span>
                    <svg id="user_categoryChevron" class="custom-dropdown-chevron w-4 h-4 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="user_categoryMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1">
                    <div onclick="selectCustomDropdown('user_category', 'mahasiswa', 'Mahasiswa')" data-value="mahasiswa" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('user_category', 'mahasiswa') === 'mahasiswa' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Mahasiswa</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ old('user_category', 'mahasiswa') === 'mahasiswa' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('user_category', 'dosen', 'Dosen')" data-value="dosen" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('user_category') === 'dosen' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Dosen</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ old('user_category') === 'dosen' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('user_category', 'staf', 'Staf akademik / tendik')" data-value="staf" class="dropdown-item px-3.5 py-2.5 text-xs font-mono {{ old('user_category') === 'staf' ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }} hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Staf akademik / tendik</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ old('user_category') === 'staf' ? '' : 'hidden' }}">✓</span>
                    </div>
                </div>
            </div>

            <!-- Notice on Password Generation (SRS FR-ADM-03) -->
            <div class="p-3.5 rounded-[6px] bg-[#0a0a0a] border border-[#333333] text-xs text-[#a1a1a1] space-y-1">
                <strong class="font-medium text-white block font-mono">Penetapan kata sandi awal:</strong>
                Sistem akan secara otomatis men-generate kata sandi awal yang aman dan menampilkannya <strong class="text-white">hanya satu kali</strong> kepada admin pada halaman berikutnya.
            </div>

            <div class="pt-4 border-t border-[#333333] flex items-center justify-end gap-3">
                <a href="{{ route('admin.accounts.index') }}" class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-4 py-2 rounded-[6px] transition">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-[6px] shadow-none transition flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Buat akun & generate sandi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleCustomDropdown(name) {
        const menu = document.getElementById(name + 'Menu');
        const chevron = document.getElementById(name + 'Chevron');
        const isOpen = !menu.classList.contains('hidden');

        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));

        if (!isOpen) {
            menu.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        }
    }

    function selectCustomDropdown(name, value, label) {
        document.getElementById(name).value = value;
        document.getElementById(name + 'Label').textContent = label;
        
        const menu = document.getElementById(name + 'Menu');
        menu.querySelectorAll('.dropdown-item').forEach(item => {
            const check = item.querySelector('.check-icon');
            if (item.getAttribute('data-value') === value) {
                item.classList.add('bg-[#1f1f1f]', 'text-white');
                item.classList.remove('text-[#cccccc]');
                if (check) check.classList.remove('hidden');
            } else {
                item.classList.remove('bg-[#1f1f1f]', 'text-white');
                item.classList.add('text-[#cccccc]');
                if (check) check.classList.add('hidden');
            }
        });

        menu.classList.add('hidden');
        const chevron = document.getElementById(name + 'Chevron');
        if (chevron) chevron.classList.remove('rotate-180');

        if (name === 'role') {
            toggleCategoryField();
        }
    }

    function toggleCategoryField() {
        const role = document.getElementById('role').value;
        const cat = document.getElementById('categoryContainer');
        if (role === 'petugas') {
            cat.style.display = 'none';
        } else {
            cat.style.display = 'block';
        }
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        }
    });

    document.addEventListener('DOMContentLoaded', toggleCategoryField);
</script>
@endpush
@endsection
