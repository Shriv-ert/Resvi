@extends('layouts.app')

@section('title', 'Riwayat Reservasi Saya')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-[6px] text-xs font-mono text-[#0070f3] bg-[#001733] border border-[#003b80] mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-[#0070f3]"></span>
                <span>Portal Pengguna · Status Reservasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Riwayat dan status reservasi saya.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Daftar permohonan reservasi fasilitas kampus yang telah Anda ajukan.
            </p>
        </div>

        <div>
            <a href="{{ route('facilities.index', ['as_user' => 1]) }}" 
               class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-full transition flex items-center gap-2 active:scale-95 shadow-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Reservasi Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-[6px] border border-[#005522] bg-[#002b11] text-xs text-[#50e3c2] flex items-center justify-between">
            <div class="flex items-center gap-2 font-mono">
                <span class="font-bold">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 rounded-[6px] border border-[#550000] bg-[#2b0000] text-xs text-[#ff453a] flex items-center justify-between">
            <div class="flex items-center gap-2 font-mono">
                <span class="font-bold">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Reservations Table: Crisp border, micro 6px radius, NO soft shadows -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#a1a1a1]">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-medium">No. Reservasi</th>
                        <th class="py-3 px-5 font-medium">Fasilitas</th>
                        <th class="py-3 px-5 font-medium">Jadwal Penggunaan</th>
                        <th class="py-3 px-5 font-medium">Status</th>
                        <th class="py-3 px-5 font-medium">Diajukan Pada</th>
                        <th class="py-3 px-5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($reservations as $res)
                        @php
                            $statusVal = is_object($res['status']) && property_exists($res['status'], 'value') ? $res['status']->value : (string)$res['status'];
                            $resDate = $res['date'] instanceof \DateTimeInterface ? $res['date']->format('Y-m-d') : (string)$res['date'];
                            $resStart = substr((string)$res['start_time'], 0, 5);
                            $resEnd = substr((string)$res['end_time'], 0, 5);
                            $resCreated = $res['created_at'] instanceof \DateTimeInterface ? $res['created_at']->format('Y-m-d H:i') : (string)$res['created_at'];
                        @endphp
                        <tr class="hover:bg-[#171717] transition-colors">
                            <!-- Reservation Number -->
                            <td class="py-3.5 px-5 font-mono text-white font-medium">
                                {{ $res['reservation_number'] }}
                            </td>

                            <!-- Facility Info -->
                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $res['facility_name'] }}</span>
                                <span class="text-[#888888] text-[11px] font-mono">{{ $res['facility_location'] }}</span>
                            </td>

                            <!-- Schedule Time -->
                            <td class="py-3.5 px-5 font-mono">
                                <span class="font-medium text-white block">{{ $resDate }}</span>
                                <span class="text-[#0070f3] text-[11px]">{{ $resStart }} – {{ $resEnd }} WIB</span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-5">
                                @if($statusVal === 'menunggu')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#f5a623] bg-[#2b1e00] border border-[#553c00]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#f5a623] animate-pulse"></span>
                                        Menunggu
                                    </span>
                                @elseif($statusVal === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                                        Disetujui
                                    </span>
                                @elseif($statusVal === 'berlangsung')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#a855f7] bg-[#1a0033] border border-[#4a1d96]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#a855f7] animate-pulse"></span>
                                        Berlangsung
                                    </span>
                                @elseif($statusVal === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#a1a1a1] bg-[#161616] border border-[#333333]">
                                        Selesai
                                    </span>
                                @elseif(str_starts_with($statusVal, 'dibatalkan'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#888888] bg-[#161616] border border-[#333333]">
                                        {{ $statusVal === 'dibatalkan_oleh_pengguna' ? 'Dibatalkan Pengguna' : 'Dibatalkan Petugas' }}
                                    </span>
                                @elseif($statusVal === 'kedaluwarsa')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#888888] bg-[#161616] border border-[#333333]">
                                        Kedaluwarsa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Created Timestamp -->
                            <td class="py-3.5 px-5 font-mono text-[11px] text-[#888888]">
                                {{ $resCreated }}
                            </td>

                            <!-- Action Links -->
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('user.reservations.show', ['reservation' => $res['id'], 'as_user' => 1]) }}" 
                                   class="text-white hover:text-[#0070f3] font-medium transition inline-flex items-center gap-1 text-xs">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#888888]">
                                Anda belum memiliki riwayat reservasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
