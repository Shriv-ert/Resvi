@extends('layouts.staff')

@section('title', 'Kelola Operasional ' . $facility['name'] . ' · Petugas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('staff.facilities.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-[#888888] hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Fasilitas
        </a>

        @if($facility['operational_status'] === 'dapat_digunakan')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-xs font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                Operasional: Dapat Digunakan
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-xs font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a] animate-pulse"></span>
                Operasional: Dalam Perbaikan
            </span>
        @endif
    </div>

    <!-- Main Detail Panel -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 shadow-none space-y-6">
        
        <div class="border-b border-[#333333] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="font-mono text-[10px] uppercase font-medium text-[#888888] tracking-wider">{{ $facility['type'] }}</span>
                <h1 class="text-2xl font-semibold text-white tracking-[-0.03em]">{{ $facility['name'] }}.</h1>
                <p class="text-xs text-[#888888] mt-1">{{ $facility['location'] }}</p>
            </div>
            
            <!-- Quick Action Button -->
            <div>
                <button 
                    type="button" 
                    onclick="openFacilityStatusModal()" 
                    class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-[6px] shadow-none transition flex items-center gap-2 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    Ubah Status Operasional
                </button>
            </div>
        </div>

        <!-- 3 Grid Info Cells -->
        <div class="grid grid-cols-1 sm:grid-cols-3 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Kapasitas Maksimal</span>
                <h3 class="text-sm font-semibold text-white">{{ $facility['capacity'] ? $facility['capacity'] . ' Orang' : 'Sesuai Unit' }}</h3>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Status Data Master</span>
                <h3 class="text-sm font-semibold font-mono {{ ($facility['is_active'] ?? true) ? 'text-[#50e3c2]' : 'text-[#888888]' }}">
                    {{ ($facility['is_active'] ?? true) ? 'Aktif (Dikelola Admin)' : 'Nonaktif (Dikelola Admin)' }}
                </h3>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Jam Operasional Harian</span>
                <h3 class="text-sm font-semibold font-mono text-white">07.00 – 20.00 WIB</h3>
            </div>
        </div>

        <!-- Description -->
        <div>
            <h4 class="font-mono text-[11px] uppercase tracking-wider text-[#888888] font-medium mb-2">Deskripsi Fasilitas</h4>
            <div class="bg-[#161616] border border-[#333333] rounded-[6px] p-4 text-xs text-[#ededed] leading-relaxed whitespace-pre-line">
                {{ $facility['description'] }}
            </div>
        </div>

        <!-- Operational Status History Summary / Rules Notice -->
        <div class="p-4 rounded-[6px] bg-[#161616] border border-[#333333] text-xs text-[#a1a1a1] space-y-2">
            <strong class="text-white block font-mono text-[11px] uppercase tracking-wider">Ketentuan Operasional Petugas:</strong>
            <p>• Perubahan ke <strong class="text-[#ff453a]">Dalam perbaikan</strong> secara otomatis membatalkan reservasi berstatus disetujui di masa depan dan menolak permohonan yang masih menunggu pada fasilitas ini.</p>
            <p>• Perubahan kembali ke <strong class="text-[#50e3c2]">Dapat digunakan</strong> mewajibkan catatan pemeriksaan/perbaikan dan membuka kembali jadwal reservasi publik.</p>
        </div>
    </div>
</div>

<!-- Modal Status Operasional -->
@include('staff.facilities.partials.status-modal')
@endsection
