@extends('layouts.admin')

@section('title', 'Tambah Fasilitas Master · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-mono text-[#888888] mb-6">
        <a href="{{ route('admin.facilities.index') }}" class="hover:text-white transition">Master Fasilitas</a>
        <span>/</span>
        <span class="text-white">Tambah Baru</span>
    </nav>

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
            Tambah Fasilitas Baru
        </h1>
        <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
            Daftarkan fasilitas baru ke dalam data master sistem.
        </p>
    </div>

    <!-- Form Container -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 max-w-3xl">
        <form method="POST" action="{{ route('admin.facilities.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-medium text-[#a1a1a1]">
                    Nama Fasilitas <span class="text-[#ff453a]">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    placeholder="Contoh: Ruang Kuliah Teori A-101"
                    class="w-full bg-[#0a0a0a] border @error('name') border-[#ff453a] @else border-[#333333] @enderror text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-sans placeholder-[#666666]"
                />
                @error('name')
                    <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                @enderror
            </div>

            <!-- Two Columns: Type & Location -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Type -->
                <div class="space-y-1.5 relative custom-dropdown" id="dropdown-type">
                    <label for="type" class="block text-xs font-medium text-[#a1a1a1]">
                        Tipe Fasilitas <span class="text-[#ff453a]">*</span>
                    </label>
                    <input type="hidden" id="type" name="type" value="{{ old('type', '') }}" required>
                    @php
                        $curCreateType = old('type', '');
                        $curCreateTypeLabel = 'Pilih Tipe Fasilitas';
                        foreach(\App\Enums\FacilityType::cases() as $t) {
                            if ($t->value === $curCreateType) $curCreateTypeLabel = $t->label();
                        }
                    @endphp
                    <button type="button" onclick="toggleCustomDropdown('type')" id="typeTrigger" title="{{ $curCreateTypeLabel }}"
                            class="w-full bg-[#0a0a0a] border @error('type') border-[#ff453a] @else border-[#333333] @enderror hover:border-[#555555] text-white text-xs rounded-[6px] px-3.5 py-2.5 flex items-center justify-between min-w-0 gap-2 focus:outline-none focus:border-white transition font-mono">
                        <span id="typeLabel" class="truncate text-left flex-1" title="{{ $curCreateTypeLabel }}">{{ $curCreateTypeLabel }}</span>
                        <svg id="typeChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div id="typeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                        <div class="p-2 border-b border-[#222222] sticky top-0 bg-[#111111] z-10">
                            <div class="relative">
                                <input type="text" placeholder="Cari tipe fasilitas..." 
                                       id="search-type"
                                       oninput="filterDropdownOptions(this, 'typeMenu')"
                                       onclick="event.stopPropagation()"
                                       class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-[11px] rounded-[4px] pl-7 pr-2 py-1.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]">
                                <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        @foreach(\App\Enums\FacilityType::cases() as $typeEnum)
                            <div onclick="selectCustomDropdown('type', '{{ $typeEnum->value }}', '{{ $typeEnum->label() }}')" data-value="{{ $typeEnum->value }}" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $curCreateType === $typeEnum->value ? 'bg-[#1f1f1f] text-white' : '' }}">
                                <span>{{ $typeEnum->label() }}</span>
                                <span class="check-icon text-[#50e3c2] text-xs {{ $curCreateType === $typeEnum->value ? '' : 'hidden' }}">✓</span>
                            </div>
                        @endforeach
                    </div>
                    @error('type')
                        <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div class="space-y-1.5">
                    <label for="location" class="block text-xs font-medium text-[#a1a1a1]">
                        Lokasi Gedung / Area <span class="text-[#ff453a]">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="location" 
                        name="location" 
                        value="{{ old('location') }}" 
                        required 
                        placeholder="Contoh: Gedung A Lt. 1"
                        class="w-full bg-[#0a0a0a] border @error('location') border-[#ff453a] @else border-[#333333] @enderror text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-sans placeholder-[#666666]"
                    />
                    @error('location')
                        <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Capacity (Optional for Alat) -->
            <div class="space-y-1.5">
                <label for="capacity" class="block text-xs font-medium text-[#a1a1a1]">
                    Kapasitas Maksimal (Orang) <span class="text-[#666666] font-normal">(wajib selain alat)</span>
                </label>
                <input 
                    type="number" 
                    id="capacity" 
                    name="capacity" 
                    value="{{ old('capacity') }}" 
                    min="1"
                    placeholder="Contoh: 45 (kosongkan jika bertipe Alat)"
                    class="w-full bg-[#0a0a0a] border @error('capacity') border-[#ff453a] @else border-[#333333] @enderror text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]"
                />
                <p class="text-[10px] text-[#888888] font-mono">
                    *Kosongkan kapasitas untuk tipe Alat (tanpa kapasitas orang).
                </p>
                @error('capacity')
                    <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-medium text-[#a1a1a1]">
                    Deskripsi & Sarana Pendukung <span class="text-[#ff453a]">*</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    required 
                    placeholder="Tuliskan spesifikasi ruangan, perlengkapan AC, proyektor, whiteboard, Wi-Fi..."
                    class="w-full bg-[#0a0a0a] border @error('description') border-[#ff453a] @else border-[#333333] @enderror text-white text-xs rounded-[6px] p-3.5 focus:outline-none focus:border-white transition placeholder-[#666666] font-sans"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo Upload -->
            <div class="space-y-1.5">
                <label for="photo" class="block text-xs font-medium text-[#a1a1a1]">
                    Foto Utama Fasilitas <span class="text-[#666666] font-normal">(opsional, maks 2MB)</span>
                </label>
                <input 
                    type="file" 
                    id="photo" 
                    name="photo" 
                    accept="image/png,image/jpeg,image/jpg,image/webp"
                    class="w-full bg-[#161616] border border-[#333333] text-[#a1a1a1] text-xs rounded-[6px] file:mr-4 file:py-2 file:px-3 file:rounded-[6px] file:border-0 file:text-xs file:font-medium file:bg-white file:text-black hover:file:bg-[#e6e6e6] cursor-pointer"
                />
                @error('photo')
                    <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                @enderror
            </div>

            <!-- Auto status notice -->
            <div class="p-3.5 rounded-[6px] bg-[#002b11]/40 border border-[#005522] text-xs text-[#50e3c2]">
                <strong class="font-semibold text-[#50e3c2] block mb-0.5">Ketentuan Status Otomatis:</strong>
                Fasilitas baru otomatis berstatus data master <span class="font-medium text-white">Aktif</span> dan status operasional <span class="font-medium text-white">Dapat Digunakan</span> sehingga langsung tersedia di katalog publik.
            </div>

            <div class="pt-4 border-t border-[#333333] flex items-center justify-end gap-3">
                <a href="{{ route('admin.facilities.index') }}" class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-4 py-2 rounded-[6px] transition">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-[6px] shadow-none transition flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Daftarkan Fasilitas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
