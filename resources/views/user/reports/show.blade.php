@extends('layouts.app')

@section('title', 'Detail Laporan #' . $report['report_number'])

@section('content')
@php
    $statusVal = is_object($report['status']) && property_exists($report['status'], 'value') ? $report['status']->value : (string)$report['status'];
    $catVal = is_object($report['category']) && property_exists($report['category'], 'value') ? $report['category']->value : (string)$report['category'];
    $repCreated = $report['created_at'] instanceof \DateTimeInterface ? $report['created_at']->format('Y-m-d H:i') : (string)$report['created_at'];
    $repUpdated = $report['updated_at'] instanceof \DateTimeInterface ? $report['updated_at']->format('Y-m-d H:i') : (string)$report['updated_at'];
    $photoPath = $report['photo_path'] ?? null;
    $photoUrl = $photoPath ? (str_starts_with($photoPath, 'http') ? $photoPath : asset('storage/' . $photoPath)) : null;
@endphp
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('user.reports.index', ['as_user' => 1]) }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-[#888888] hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Riwayat Laporan
        </a>

        <!-- Status Badge -->
        @if($statusVal === 'baru')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#f5a623] bg-[#2b1e00] border border-[#553c00]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#f5a623] animate-pulse"></span>
                Status: Baru Dikirim
            </span>
        @elseif($statusVal === 'diproses')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#0070f3] bg-[#001733] border border-[#003b80]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#0070f3] animate-pulse"></span>
                Status: Sedang Ditangani
            </span>
        @elseif($statusVal === 'selesai')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                Status: Penanganan Selesai
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span>
                Status: Laporan Ditolak
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-[6px] border border-[#005522] bg-[#002b11] text-xs text-[#50e3c2] flex items-center justify-between">
            <div class="flex items-center gap-2 font-mono">
                <span class="font-bold">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Report Detail Card -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 space-y-6">
        
        <div class="border-b border-[#333333] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888]">Nomor Registrasi Laporan</span>
                <h1 class="text-2xl font-semibold font-mono text-white tracking-tight">{{ $report['report_number'] }}</h1>
            </div>
            <div class="text-xs text-[#888888] font-mono sm:text-right">
                <span>Dilaporkan pada: {{ $repCreated }}</span>
                <span class="block text-[11px] text-[#888888]">Pembaruan: {{ $repUpdated }}</span>
            </div>
        </div>

        <!-- 2 Columns Grid Data -->
        <div class="grid grid-cols-1 sm:grid-cols-2 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Fasilitas Sasaran</span>
                <h3 class="text-sm font-semibold text-white tracking-tight">{{ $report['facility_name'] }}.</h3>
            </div>

            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Kategori Masalah</span>
                <h3 class="text-sm font-semibold text-white tracking-tight capitalize">{{ str_replace('_', ' ', $catVal) }}.</h3>
            </div>
        </div>

        <!-- Problem Description -->
        <div>
            <h4 class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-2">Deskripsi Masalah / Kerusakan</h4>
            <div class="bg-[#161616] border border-[#333333] rounded-[6px] p-4 text-xs text-[#ededed] leading-relaxed whitespace-pre-line">
                {{ $report['description'] }}
            </div>
        </div>

        <!-- Photo Evidence -->
        <div>
            <h4 class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-2">Foto Lampiran Bukti</h4>
            @if(!empty($photoUrl))
                <div class="max-w-md rounded-[6px] overflow-hidden border border-[#333333] bg-[#161616] group">
                    <a href="{{ $photoUrl }}" target="_blank" rel="noopener noreferrer" class="block cursor-zoom-in" title="Klik untuk membuka ukuran penuh">
                        <img src="{{ $photoUrl }}" alt="Bukti Masalah" class="w-full h-auto object-cover group-hover:opacity-90 transition">
                    </a>
                </div>
            @else
                <div class="p-3 rounded-[6px] bg-[#161616] border border-[#333333] text-xs text-[#888888] font-mono">
                    Tidak ada lampiran foto bukti pada laporan ini.
                </div>
            @endif
        </div>

        <!-- Resolution Notes if Completed -->
        @if(!empty($report['resolution_notes']))
            <div class="p-4 rounded-[6px] bg-[#002b11]/40 border border-[#005522] text-xs text-[#50e3c2] space-y-1">
                <div class="flex items-center gap-1.5 font-medium mb-1 font-mono">
                    <svg class="w-4 h-4 text-[#50e3c2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Catatan Resolusi dari Petugas Sarpras:
                </div>
                <p class="leading-relaxed text-[#ededed]">{{ $report['resolution_notes'] }}</p>
                <span class="block text-[10px] text-[#50e3c2]/80 mt-2 font-mono">Status Operasional: Dapat Digunakan Kembali</span>
            </div>
        @endif

        <!-- Rejection Reason if Rejected -->
        @if(!empty($report['rejection_reason']))
            <div class="p-4 rounded-[6px] bg-[#2b0000]/40 border border-[#550000] text-xs text-[#ff453a] space-y-1">
                <strong class="font-medium text-[#ff453a] block">Alasan Penolakan Laporan:</strong>
                <p class="leading-relaxed text-[#ededed]">{{ $report['rejection_reason'] }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
