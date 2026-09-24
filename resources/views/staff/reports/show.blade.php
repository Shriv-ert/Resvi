@extends('layouts.staff')

@section('title', 'Detail Laporan #' . $report['report_number'] . ' · Petugas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('staff.reports.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-[#888888] hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Antrean Laporan
        </a>

        <!-- Status Badge -->
        @if($report['status'] === 'baru')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-xs font-mono text-[#f5a623] bg-[#2b1e00] border border-[#553c00]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#f5a623] animate-pulse"></span>
                Status: Baru
            </span>
        @elseif($report['status'] === 'diproses')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-xs font-mono text-[#0070f3] bg-[#001733] border border-[#003b80]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#0070f3] animate-pulse"></span>
                Status: Sedang Ditangani
            </span>
        @elseif($report['status'] === 'selesai')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-xs font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                Status: Selesai Ditangani
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-xs font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span>
                Status: Ditolak
            </span>
        @endif
    </div>

    <!-- Main Detail Panel -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 shadow-none space-y-6">
        
        <div class="border-b border-[#333333] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888]">Nomor Registrasi Laporan</span>
                <h1 class="text-2xl font-semibold font-mono text-white tracking-tight">{{ $report['report_number'] }}</h1>
            </div>
            <div class="text-xs text-[#888888] font-mono sm:text-right">
                <span>Diterima pada: {{ $report['created_at'] }}</span>
            </div>
        </div>

        <!-- 3 Grid Info Cells -->
        <div class="grid grid-cols-1 sm:grid-cols-3 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Identitas Pelapor</span>
                <h3 class="text-sm font-semibold text-white">{{ $report['reporter_name'] }}</h3>
                <span class="text-xs text-[#888888] font-mono">Civitas Kampus</span>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Fasilitas Sasaran</span>
                <h3 class="text-sm font-semibold text-white">{{ $report['facility_name'] }}</h3>
                <span class="text-xs text-[#888888]">{{ $report['facility_location'] ?? 'Kampus' }}</span>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Kategori Laporan</span>
                <h3 class="text-sm font-semibold text-white capitalize">{{ str_replace('_', ' ', $report['category']) }}</h3>
            </div>
        </div>

        <!-- Problem Description -->
        <div>
            <h4 class="font-mono text-[11px] uppercase tracking-wider text-[#888888] font-medium mb-2">Deskripsi Masalah / Kerusakan</h4>
            <div class="bg-[#161616] border border-[#333333] rounded-[6px] p-4 text-xs text-[#ededed] leading-relaxed whitespace-pre-line">
                {{ $report['description'] }}
            </div>
        </div>

        <!-- Photo Evidence if attached -->
        @if(!empty($report['photo_path']))
            <div>
                <h4 class="font-mono text-[11px] uppercase tracking-wider text-[#888888] font-medium mb-2">Foto Lampiran Bukti</h4>
                <div class="max-w-md rounded-[6px] overflow-hidden border border-[#333333] bg-[#161616]">
                    <img src="{{ $report['photo_path'] }}" alt="Foto Bukti Masalah" class="w-full h-auto object-cover">
                </div>
            </div>
        @endif

        <!-- Resolution Notes if Completed -->
        @if(!empty($report['resolution_notes']))
            <div class="p-4 rounded-[6px] bg-[#002b11]/40 border border-[#005522] text-xs text-[#50e3c2]">
                <strong class="font-medium text-sm block mb-1 font-mono">Catatan Resolusi Petugas:</strong>
                <p class="leading-relaxed text-[#ededed]">{{ $report['resolution_notes'] }}</p>
                <span class="block text-[10px] text-[#50e3c2]/80 mt-2 font-mono">Status: Selesai Ditangani</span>
            </div>
        @endif

        <!-- Rejection Reason if Rejected -->
        @if(!empty($report['rejection_reason']))
            <div class="p-4 rounded-[6px] bg-[#2b0000]/40 border border-[#550000] text-xs text-[#ff453a]">
                <strong class="font-medium text-sm block mb-1 font-mono">Alasan Penolakan Laporan:</strong>
                <p class="leading-relaxed text-[#ededed]">{{ $report['rejection_reason'] }}</p>
            </div>
        @endif

        <!-- Action Bar for Staff -->
        <div class="pt-6 border-t border-[#333333] flex flex-wrap items-center justify-between gap-4">
            
            @if($report['status'] === 'baru')
                <div class="text-xs text-[#888888] font-mono">
                    Laporan baru harus dimulai proses penanganannya sebelum dapat diselesaikan atau ditolak.
                </div>

                <!-- Mulai Proses Button -->
                <form method="POST" action="{{ route('staff.reports.start-process', ['report' => $report['id']]) }}">
                    @csrf
                    <button 
                        type="submit" 
                        class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-[6px] shadow-none transition flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Mulai Proses Penanganan
                    </button>
                </form>

            @elseif($report['status'] === 'diproses')
                <div class="text-xs text-[#888888] font-mono">
                    Laporan sedang ditangani. Tentukan resolusi penyelesaian atau tandai fasilitas dalam perbaikan.
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Ubah Status Fasilitas Button -->
                    <button 
                        type="button" 
                        onclick="openFacilityStatusModal()" 
                        class="bg-[#171717] hover:bg-[#222222] text-[#f5a623] border border-[#553c00] text-xs font-medium px-3.5 py-2 rounded-[6px] transition flex items-center gap-1.5 shadow-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Ubah Status Fasilitas
                    </button>

                    <!-- Tolak Button -->
                    <button 
                        type="button" 
                        onclick="openRejectReportModal()" 
                        class="bg-[#2b0000]/50 hover:bg-[#2b0000] text-[#ff453a] border border-[#550000] text-xs font-medium px-3.5 py-2 rounded-[6px] transition shadow-none">
                        Tolak Laporan
                    </button>

                    <!-- Selesaikan Button -->
                    <button 
                        type="button" 
                        onclick="openResolveModal()" 
                        class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-[6px] shadow-none transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Selesaikan Laporan
                    </button>
                </div>
            @else
                <div class="text-xs text-[#888888] font-mono italic">
                    Status laporan ini sudah final.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
@include('staff.reports.partials.decision-modal')
@include('staff.facilities.partials.status-modal')
@endsection
