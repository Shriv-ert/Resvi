@extends('layouts.app')

@section('title', 'Detail Reservasi #' . $reservation['reservation_number'])

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('user.reservations.index', ['as_user' => 1]) }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-[#a1a1a1] hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Riwayat Reservasi
        </a>

        <!-- Status Badge -->
        @if($reservation['status'] === 'menunggu')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#f5a623] bg-[#2b1e00] border border-[#553c00]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#f5a623] animate-pulse"></span>
                Menunggu Persetujuan
            </span>
        @elseif($reservation['status'] === 'disetujui')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                Disetujui
            </span>
        @elseif($reservation['status'] === 'berlangsung')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#a855f7] bg-[#1a0033] border border-[#4a1d96]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#a855f7] animate-pulse"></span>
                Sedang Berlangsung
            </span>
        @elseif($reservation['status'] === 'selesai')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#a1a1a1] bg-[#161616] border border-[#333333]">
                Selesai Digunakan
            </span>
        @elseif(str_starts_with($reservation['status'], 'dibatalkan'))
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#888888] bg-[#161616] border border-[#333333]">
                {{ $reservation['status'] === 'dibatalkan_oleh_pengguna' ? 'Dibatalkan Pengguna' : 'Dibatalkan Petugas' }}
            </span>
        @elseif($reservation['status'] === 'kedaluwarsa')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#888888] bg-[#161616] border border-[#333333]">
                Kedaluwarsa
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                Ditolak
            </span>
        @endif
    </div>

    <!-- Main Detail Card: Crisp border, micro 6px radius, NO soft shadows -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 space-y-6">
        
        <div class="border-b border-[#333333] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888]">Nomor Registrasi Reservasi</span>
                <h1 class="text-2xl font-semibold font-mono text-white tracking-tight">{{ $reservation['reservation_number'] }}</h1>
            </div>
            <div class="text-xs text-[#888888] font-mono sm:text-right">
                <span>Diajukan: {{ $reservation['created_at'] }}</span>
                <span class="block text-[11px] text-[#888888]">Pembaruan: {{ $reservation['updated_at'] }}</span>
            </div>
        </div>

        <!-- 2 Columns Grid Data: Adjacent cells split by 1px divider lines -->
        <div class="grid grid-cols-1 sm:grid-cols-2 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
            <!-- Facility Information -->
            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Fasilitas yang Dipesan</span>
                <h3 class="text-sm font-semibold text-white tracking-tight">{{ $reservation['facility_name'] }}.</h3>
                <p class="text-xs text-[#a1a1a1] mt-1 flex items-center gap-1 font-mono">
                    {{ $reservation['facility_location'] }}
                </p>
            </div>

            <!-- Schedule Information -->
            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Jadwal Penggunaan</span>
                <h3 class="text-sm font-semibold text-white tracking-tight font-mono">{{ $reservation['date'] }}</h3>
                <p class="text-xs text-[#0070f3] font-mono mt-1">
                    {{ $reservation['start_time'] }} – {{ $reservation['end_time'] }} WIB (Slot 30m)
                </p>
            </div>
        </div>

        <!-- Purpose of Usage -->
        <div>
            <h4 class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-2">Tujuan Penggunaan Fasilitas</h4>
            <div class="bg-[#161616] border border-[#333333] rounded-[6px] p-4 text-xs text-white leading-relaxed">
                {{ $reservation['purpose'] }}
            </div>
        </div>

        <!-- Cancellation Note (if any) -->
        @if(!empty($reservation['cancellation_reason']))
            <div class="p-4 rounded-[6px] bg-[#2b0000] border border-[#550000] text-xs text-[#ff453a] space-y-1">
                <strong class="font-medium text-white block">Catatan Pembatalan / Penolakan:</strong>
                <p class="leading-relaxed">{{ $reservation['cancellation_reason'] }}</p>
            </div>
        @endif

        <!-- Action Section: Cancel reservation button if status allows -->
        @if($reservation['status'] === 'menunggu' || $reservation['status'] === 'disetujui')
            <div class="pt-6 border-t border-[#333333] flex items-center justify-between flex-wrap gap-4">
                <div class="text-xs text-[#888888]">
                    Anda dapat membatalkan reservasi ini secara mandiri sebelum waktu mulai penggunaan.
                </div>
                <button 
                    type="button" 
                    onclick="openCancelModal()" 
                    class="bg-[#2b0000] hover:bg-[#3b0000] text-[#ff453a] border border-[#550000] text-xs font-medium px-4 py-2 rounded-full transition">
                    Batalkan Reservasi Ini
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Cancel Modal Partial -->
@include('user.reservations.partials.cancel-modal')
@endsection
