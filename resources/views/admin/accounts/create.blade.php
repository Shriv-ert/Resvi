@extends('layouts.admin')

@section('title', 'Buat Akun Civitas Baru · Admin')

@section('content')
<div class="max-w-[800px] mx-auto px-4 sm:px-6 py-8">

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
            Pendaftaran Akun Baru
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-[#a1a1a1]">
            Daftarkan akun Petugas atau Pengguna (Mahasiswa/Dosen/Staf) secara langsung tanpa registrasi mandiri.
        </p>
    </div>

    <!-- Security Info Banner -->
    <div class="mb-6 p-4 rounded-[6px] bg-[#001733] border border-[#003b80] text-xs text-[#70a5ff] leading-relaxed">
        <strong class="font-semibold text-white block mb-1 font-mono text-[11px] uppercase tracking-wide">Keamanan Kata Sandi Awal:</strong>
        Sistem akan membangkitkan kata sandi acak berkeamanan tinggi yang <strong class="text-white">hanya ditampilkan satu kali</strong> kepada Admin sesaat setelah formulir ini disimpan. Pastikan Anda mencatat dan menyampaikannya kepada pemilik akun.
    </div>

    <!-- Form Container -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 shadow-none">
        <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-6" id="createAccountForm">
            @csrf

            <!-- Full Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-medium text-[#a1a1a1]">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    required 
                    placeholder="Contoh: Muhammad Raihan, Dr. Ir. Hendra..."
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-sm rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition placeholder-[#666666]"
                />
                @error('name')
                    <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-medium text-[#a1a1a1]">
                    Alamat Email Kampus / Pribadi <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required 
                    placeholder="nama@kampus.ac.id"
                    class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-sm rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition placeholder-[#666666] font-mono"
                />
                @error('email')
                    <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role Selection -->
            <div class="space-y-2">
                <label class="block text-xs font-medium text-[#a1a1a1]">
                    Peran Akun <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-start gap-3 p-3.5 rounded-[6px] border border-[#333333] bg-[#0a0a0a] cursor-pointer hover:border-[#555555] transition">
                        <input 
                            type="radio" 
                            name="role" 
                            value="pengguna" 
                            class="role-radio mt-0.5" 
                            {{ old('role', 'pengguna') === 'pengguna' ? 'checked' : '' }}
                            onchange="handleRoleChange(this.value)"
                        />
                        <div class="text-xs">
                            <span class="font-medium text-white block">Pengguna Fasilitas</span>
                            <span class="text-[#888888] text-[11px] mt-0.5 block">Mahasiswa, Dosen, atau Staf (dapat mereservasi & melapor)</span>
                        </div>
                    </label>

                    <label class="relative flex items-start gap-3 p-3.5 rounded-[6px] border border-[#333333] bg-[#0a0a0a] cursor-pointer hover:border-[#555555] transition">
                        <input 
                            type="radio" 
                            name="role" 
                            value="petugas" 
                            class="role-radio mt-0.5" 
                            {{ old('role') === 'petugas' ? 'checked' : '' }}
                            onchange="handleRoleChange(this.value)"
                        />
                        <div class="text-xs">
                            <span class="font-medium text-white block">Petugas Sarpras</span>
                            <span class="text-[#888888] text-[11px] mt-0.5 block">Staf operasional (memproses reservasi & status operasional)</span>
                        </div>
                    </label>
                </div>
                @error('role')
                    <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Field (Required if role = pengguna, SRS FR-ADM-03) -->
            <div id="categoryContainer" class="space-y-1.5 relative custom-dropdown" id="dropdown-user_category">
                <label for="user_category" class="block text-xs font-medium text-[#a1a1a1]">
                    Kategori Pengguna <span class="text-rose-500">*</span>
                </label>
                <input type="hidden" id="user_category" name="user_category" value="{{ old('user_category', '') }}">
                @php
                    $curUserCat = old('user_category', '');
                    $curUserCatLabel = $curUserCat === 'mahasiswa' ? 'Mahasiswa' : ($curUserCat === 'dosen' ? 'Dosen' : ($curUserCat === 'staf' ? 'Staf' : 'Pilih Kategori Civitas...'));
                @endphp
                <button type="button" onclick="toggleCustomDropdown('user_category')" id="user_categoryTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3.5 py-2.5 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="user_categoryLabel">{{ $curUserCatLabel }}</span>
                    <svg id="user_categoryChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="user_categoryMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                    <div class="p-2 border-b border-[#222222] sticky top-0 bg-[#111111] z-10">
                        <div class="relative">
                            <input type="text" placeholder="Cari kategori civitas..." 
                                   id="search-user_category"
                                   oninput="filterDropdownOptions(this, 'user_categoryMenu')"
                                   onclick="event.stopPropagation()"
                                   class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-[11px] rounded-[4px] pl-7 pr-2 py-1.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]">
                            <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div onclick="selectCustomDropdown('user_category', '', 'Pilih Kategori Civitas...')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between {{ empty($curUserCat) ? 'bg-[#1f1f1f]' : '' }}">
                        <span>Pilih Kategori Civitas...</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ empty($curUserCat) ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('user_category', 'mahasiswa', 'Mahasiswa')" data-value="mahasiswa" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $curUserCat === 'mahasiswa' ? 'bg-[#1f1f1f] text-white' : '' }}">
                        <span>Mahasiswa</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ $curUserCat === 'mahasiswa' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('user_category', 'dosen', 'Dosen')" data-value="dosen" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $curUserCat === 'dosen' ? 'bg-[#1f1f1f] text-white' : '' }}">
                        <span>Dosen</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ $curUserCat === 'dosen' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('user_category', 'staf', 'Staf')" data-value="staf" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $curUserCat === 'staf' ? 'bg-[#1f1f1f] text-white' : '' }}">
                        <span>Staf</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ $curUserCat === 'staf' ? '' : 'hidden' }}">✓</span>
                    </div>
                </div>
                @error('user_category')
                    <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notice on Password Generation -->
            <div class="p-3.5 rounded-[6px] bg-[#0a0a0a] border border-[#333333] text-xs text-[#a1a1a1] space-y-1">
                <strong class="font-medium text-white block font-mono text-[11px]">Penetapan kata sandi awal:</strong>
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
                    Buat Akun & Generate Sandi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function handleRoleChange(role) {
        const cat = document.getElementById('categoryContainer');
        const catSelect = document.getElementById('user_category');
        if (role === 'petugas') {
            cat.style.display = 'none';
            if (catSelect) catSelect.disabled = true;
        } else {
            cat.style.display = 'block';
            if (catSelect) catSelect.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const checkedRole = document.querySelector('input[name="role"]:checked')?.value || 'pengguna';
        handleRoleChange(checkedRole);
    });
</script>
@endpush
@endsection
