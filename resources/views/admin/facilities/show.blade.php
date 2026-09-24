@extends('layouts.admin')

@section('title', 'Detail Master ' . $facility['name'] . ' · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb & Statuses -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.facilities.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-medium text-[#888888] hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke kelola fasilitas
        </a>

        <!-- Status Master & Status Operasional Pills -->
        <div class="flex items-center gap-2">
            @if($facility['is_active'] ?? true)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-[11px] font-mono font-medium bg-[#002b11] text-[#50e3c2] border border-[#005522]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                    Master: Aktif
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-[11px] font-mono font-medium bg-[#161616] text-[#888888] border border-[#333333]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#888888]"></span>
                    Master: Nonaktif
                </span>
            @endif

            @if($facility['operational_status'] === 'dapat_digunakan')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-[11px] font-mono font-medium bg-[#002b11] text-[#50e3c2] border border-[#005522]">
                    Operasional: Dapat digunakan
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-[11px] font-mono font-medium bg-[#2b0000] text-[#ff453a] border border-[#550000]">
                    Operasional: Dalam perbaikan
                </span>
            @endif
        </div>
    </div>

    <!-- Main Detail Panel -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 shadow-none space-y-6">
        
        <div class="border-b border-[#333333] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] text-[#888888] uppercase tracking-wider font-mono font-medium">{{ $facility['type'] }}</span>
                <h1 class="text-2xl font-semibold text-white tracking-[-0.03em] mt-0.5">{{ $facility['name'] }}.</h1>
                <p class="text-xs text-[#888888] mt-1">{{ $facility['location'] }}</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.facilities.edit', ['facility' => $facility['id']]) }}" 
                   class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-3.5 py-1.5 rounded-[6px] transition flex items-center gap-1.5 shadow-none">
                    <svg class="w-3.5 h-3.5 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Edit master
                </a>

                @if($facility['is_active'] ?? true)
                    <button 
                        type="button" 
                        onclick="openDeactivateModal()" 
                        class="bg-[#2b0000]/50 hover:bg-[#2b0000] text-[#ff453a] border border-[#550000] text-xs font-medium px-3.5 py-1.5 rounded-[6px] transition flex items-center gap-1.5 shadow-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                        Nonaktifkan
                    </button>
                @else
                    <button 
                        type="button" 
                        onclick="openReactivateModal()" 
                        class="bg-[#002b11]/50 hover:bg-[#002b11] text-[#50e3c2] border border-[#005522] text-xs font-medium px-3.5 py-1.5 rounded-[6px] transition flex items-center gap-1.5 shadow-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Aktifkan kembali
                    </button>
                @endif
            </div>
        </div>

        <!-- Facility Photo Banner if available -->
        @if(!empty($facility['photo']))
            <div class="h-64 rounded-[6px] overflow-hidden border border-[#333333] bg-[#161616]">
                <img src="{{ $facility['photo'] }}" alt="{{ $facility['name'] }}" class="w-full h-full object-cover">
            </div>
        @endif

        <!-- 3 Grid Info Cells -->
        <div class="grid grid-cols-1 sm:grid-cols-3 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
            <div class="bg-[#111111] p-4">
                <span class="text-[11px] text-[#888888] font-mono block mb-0.5">Kapasitas orang</span>
                <h3 class="text-sm font-semibold text-white font-mono">{{ $facility['capacity'] ? $facility['capacity'] . ' Orang' : '— (Unit Alat)' }}</h3>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="text-[11px] text-[#888888] font-mono block mb-0.5">Status operasional fisik</span>
                <h3 class="text-sm font-semibold {{ $facility['operational_status'] === 'dapat_digunakan' ? 'text-[#50e3c2]' : 'text-[#ff453a]' }}">
                    {{ $facility['operational_status'] === 'dapat_digunakan' ? 'Dapat Digunakan' : 'Dalam Perbaikan' }}
                </h3>
                <span class="text-[10px] text-[#666666] font-mono">Dikelola oleh petugas</span>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="text-[11px] text-[#888888] font-mono block mb-0.5">Status data master</span>
                <h3 class="text-sm font-semibold {{ ($facility['is_active'] ?? true) ? 'text-[#50e3c2]' : 'text-[#888888]' }}">
                    {{ ($facility['is_active'] ?? true) ? 'Aktif (Katalog Publik)' : 'Nonaktif (Arsip)' }}
                </h3>
                <span class="text-[10px] text-[#666666] font-mono">Dikelola oleh admin</span>
            </div>
        </div>

        <!-- Description -->
        <div>
            <h4 class="text-xs font-semibold text-[#888888] uppercase tracking-wider mb-2 font-mono">Deskripsi lengkap</h4>
            <div class="bg-[#161616] border border-[#333333] rounded-[6px] p-4 text-xs text-[#ededed] leading-relaxed whitespace-pre-line">
                {{ $facility['description'] }}
            </div>
        </div>

        <!-- Notice -->
        <div class="p-3.5 rounded-[6px] bg-[#161616] border border-[#333333] text-xs text-[#a1a1a1] space-y-1">
            <strong class="text-white block font-semibold font-mono">Pemisahan wewenang sistem (SRS-ADMIN.md):</strong>
            <p>Admin mengelola data spesifikasi master & aktif/nonaktif data. Perubahan operasional perbaikan fisik dikelola oleh Petugas Sarpras.</p>
        </div>
    </div>
</div>

<!-- Modals -->
@include('admin.facilities.partials.deactivate-modal')
@include('admin.facilities.partials.reactivate-modal')
@endsection
