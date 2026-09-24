@extends('layouts.staff')

@section('title', 'Proses Reservasi #' . $reservation['reservation_number'] . ' · Petugas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('staff.reservations.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-[#888888] hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Antrean Kerja
        </a>

        <!-- Status Badge -->
        @if($reservation['status'] === 'menunggu')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#f5a623] bg-[#2b1e00] border border-[#553c00]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#f5a623] animate-pulse"></span>
                Status: Menunggu Persetujuan
            </span>
        @elseif($reservation['status'] === 'disetujui')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                Status: Disetujui
            </span>
        @elseif($reservation['status'] === 'berlangsung')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#c084fc] bg-[#270038] border border-[#581c87]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#c084fc] animate-pulse"></span>
                Status: Sedang Berlangsung
            </span>
        @elseif($reservation['status'] === 'selesai')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#a1a1a1] bg-[#161616] border border-[#333333]">
                Status: Selesai
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

    <!-- Main Detail Panel -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 space-y-6">
        
        <div class="border-b border-[#333333] pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888]">Nomor Registrasi Antrean</span>
                <h1 class="text-2xl font-semibold font-mono text-white tracking-tight">{{ $reservation['reservation_number'] }}</h1>
            </div>
            <div class="text-xs text-[#888888] font-mono sm:text-right">
                <span>Diterima: {{ $reservation['created_at'] }}</span>
            </div>
        </div>

        <!-- 3 Grid Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
            <!-- Applicant -->
            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Identitas Pemohon</span>
                <h3 class="text-sm font-semibold text-white tracking-tight">{{ $reservation['applicant_name'] }}</h3>
                <p class="text-xs text-[#888888] mt-0.5 font-mono">{{ $reservation['applicant_category'] ?? 'Mahasiswa' }} · {{ $reservation['applicant_email'] ?? 'mahasiswa@ppk.test' }}</p>
            </div>

            <!-- Facility -->
            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Fasilitas Sasaran</span>
                <h3 class="text-sm font-semibold text-white tracking-tight">{{ $reservation['facility_name'] }}</h3>
                <p class="text-xs text-[#888888] mt-0.5">{{ $reservation['facility_location'] }}</p>
            </div>

            <!-- Schedule -->
            <div class="bg-[#111111] p-4">
                <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium block mb-1">Jadwal Pinjam</span>
                <h3 class="text-sm font-semibold text-white tracking-tight font-mono">{{ $reservation['date'] }}</h3>
                <p class="text-xs text-[#0070f3] font-mono mt-0.5">{{ $reservation['start_time'] }} – {{ $reservation['end_time'] }} WIB</p>
            </div>
        </div>

        <!-- Purpose of Usage -->
        <div>
            <h4 class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-2">Tujuan / Deskripsi Agenda Penggunaan</h4>
            <div class="bg-[#161616] border border-[#333333] rounded-[6px] p-4 text-xs text-[#ededed] leading-relaxed">
                {{ $reservation['purpose'] }}
            </div>
        </div>

        <!-- System Collision & Operational Notice -->
        <div class="p-4 rounded-[6px] bg-[#161616] border border-[#333333] text-xs text-[#a1a1a1] space-y-1">
            <strong class="font-medium text-white block">Validasi Bentrok Otomatis (SRS BR-PTG-01):</strong>
            <p class="leading-relaxed">
                Jika Anda menyetujui permohonan ini, sistem secara otomatis menolak permohonan lain yang berstatus <em>Menunggu</em> dan memiliki bentrokan jadwal pada fasilitas ini.
            </p>
        </div>

        <!-- Actions Toolbar -->
        <div class="pt-6 border-t border-[#333333] flex items-center justify-between flex-wrap gap-4">
            <div>
                <a href="{{ route('staff.reservations.index') }}" class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] font-medium text-xs px-4 py-2 rounded-full transition">
                    Kembali
                </a>
            </div>

            <div class="flex items-center gap-2">
                @if($reservation['status'] === 'menunggu')
                    <!-- Tolak Button -->
                    <button 
                        type="button" 
                        onclick="openRejectModal()" 
                        class="bg-[#2b0000]/50 hover:bg-[#2b0000] text-[#ff453a] border border-[#550000] text-xs font-medium px-4 py-2 rounded-full transition">
                        Tolak Permohonan
                    </button>

                    <!-- Setujui Button -->
                    <form method="POST" action="{{ route('staff.reservations.approve', ['reservation' => $reservation['id']]) }}">
                        @csrf
                        <button 
                            type="submit" 
                            class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-5 py-2 rounded-full transition flex items-center gap-1.5 active:scale-95 shadow-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Setujui Reservasi
                        </button>
                    </form>
                @elseif($reservation['status'] === 'disetujui')
                    <!-- Urgent Cancellation by Staff -->
                    <button 
                        type="button" 
                        onclick="openCancelModal()" 
                        class="bg-[#2b0000]/50 hover:bg-[#2b0000] text-[#ff453a] border border-[#550000] text-xs font-medium px-4 py-2 rounded-full transition">
                        Batalkan Mendesak
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('staff.reservations.partials.reject-modal')
@include('staff.reservations.partials.cancel-modal')
@endsection
