@extends('layouts.admin')

@php
    $fId = is_array($facility) ? $facility['id'] : $facility->id;
    $fName = is_array($facility) ? $facility['name'] : $facility->name;
    $fTypeVal = is_array($facility) ? $facility['type'] : ($facility->type instanceof \BackedEnum ? $facility->type->value : $facility->type);
    $fLocation = is_array($facility) ? $facility['location'] : $facility->location;
    $fCapacity = is_array($facility) ? ($facility['capacity'] ?? null) : $facility->capacity;
    $fDescription = is_array($facility) ? $facility['description'] : $facility->description;
@endphp

@section('title', 'Edit Fasilitas · ' . $fName)

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-mono text-[#888888] mb-6">
        <a href="{{ route('admin.facilities.index') }}" class="hover:text-white transition">Master Fasilitas</a>
        <span>/</span>
        <a href="{{ route('admin.facilities.show', $facility) }}" class="hover:text-white transition truncate max-w-xs">{{ $fName }}</a>
        <span>/</span>
        <span class="text-white">Edit Data</span>
    </nav>

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
            Ubah Data Master Fasilitas.
        </h1>
        <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
            Perubahan data master tidak memengaruhi status operasional fisik fasilitas.
        </p>
    </div>

    <!-- Form Container -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 max-w-3xl">
        <form method="POST" action="{{ route('admin.facilities.update', $facility) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-medium text-[#a1a1a1]">
                    Nama Fasilitas <span class="text-[#ff453a]">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $fName) }}" 
                    required 
                    class="w-full bg-[#0a0a0a] border @error('name') border-[#ff453a] @else border-[#333333] @enderror text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-sans placeholder-[#666666]"
                />
                @error('name')
                    <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                @enderror
            </div>

            <!-- Two Columns: Type & Location -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Type -->
                <div class="space-y-1.5">
                    <label for="type" class="block text-xs font-medium text-[#a1a1a1]">
                        Tipe Fasilitas <span class="text-[#ff453a]">*</span>
                    </label>
                    <select
                        id="type"
                        name="type"
                        required
                        class="w-full bg-[#0a0a0a] border @error('type') border-[#ff453a] @else border-[#333333] @enderror text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-mono"
                    >
                        @foreach(\App\Enums\FacilityType::cases() as $typeEnum)
                            <option value="{{ $typeEnum->value }}" @selected(old('type', $fTypeVal) === $typeEnum->value)>
                                {{ $typeEnum->label() }}
                            </option>
                        @endforeach
                    </select>
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
                        value="{{ old('location', $fLocation) }}" 
                        required 
                        class="w-full bg-[#0a0a0a] border @error('location') border-[#ff453a] @else border-[#333333] @enderror text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-sans placeholder-[#666666]"
                    />
                    @error('location')
                        <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Capacity -->
            <div class="space-y-1.5">
                <label for="capacity" class="block text-xs font-medium text-[#a1a1a1]">
                    Kapasitas Maksimal (Orang) <span class="text-[#666666] font-normal">(kosongkan jika bertipe Alat)</span>
                </label>
                <input 
                    type="number" 
                    id="capacity" 
                    name="capacity" 
                    value="{{ old('capacity', $fCapacity) }}" 
                    min="1"
                    class="w-full bg-[#0a0a0a] border @error('capacity') border-[#ff453a] @else border-[#333333] @enderror text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]"
                />
                @error('capacity')
                    <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-medium text-[#a1a1a1]">
                    Deskripsi Lengkap Fasilitas <span class="text-[#ff453a]">*</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    required 
                    class="w-full bg-[#0a0a0a] border @error('description') border-[#ff453a] @else border-[#333333] @enderror text-white text-xs rounded-[6px] p-3.5 focus:outline-none focus:border-white transition placeholder-[#666666] font-sans"
                >{{ old('description', $fDescription) }}</textarea>
                @error('description')
                    <p class="text-[11px] text-[#ff453a] font-mono">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo Upload -->
            <div class="space-y-1.5">
                <label for="photo" class="block text-xs font-medium text-[#a1a1a1]">
                    Ganti Foto Utama <span class="text-[#666666] font-normal">(opsional, biarkan kosong jika tidak diubah)</span>
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

            <div class="pt-4 border-t border-[#333333] flex items-center justify-end gap-3">
                <a href="{{ route('admin.facilities.show', $facility) }}" class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-4 py-2 rounded-[6px] transition">
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-[6px] shadow-none transition flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
