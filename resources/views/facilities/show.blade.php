@extends('layouts.app')

@section('title', $facility['name'] . ' · Detail Fasilitas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb & Back Navigation -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('facilities.index', request()->has('as_user') ? ['as_user' => 1] : []) }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-[#a1a1a1] hover:text-white transition-colors group">
            <svg class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Katalog
        </a>

        <!-- Operational Status Indicator Pill -->
        @if($facility['operational_status'] === 'dapat_digunakan')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] text-xs font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                Status: Dapat Digunakan
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] text-xs font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span>
                Status: Dalam Perbaikan
            </span>
        @endif
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left 8 Cols: Visuals, Specifications, and Details -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Hero Image Banner with 1px border -->
            <div class="relative h-72 sm:h-96 w-full rounded-[6px] overflow-hidden border border-[#333333] bg-[#111111]">
                <img 
                    src="{{ $facility['photo'] }}" 
                    alt="{{ $facility['name'] }}" 
                    class="w-full h-full object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-transparent"></div>
                
                <div class="absolute bottom-5 left-5 right-5">
                    <span class="inline-block bg-black/80 text-white font-mono text-[10px] uppercase font-medium px-2 py-0.5 rounded-[4px] tracking-wider mb-2 border border-[#333333]">
                        {{ ucfirst($facility['type']) }}
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                        {{ $facility['name'] }}.
                    </h1>
                </div>
            </div>

            <!-- Quick Specs Row: 4 equal-width cells split by 1px divider lines (Vercel Grid Architecture) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
                <div class="bg-[#111111] p-4">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Tipe Fasilitas</span>
                    <strong class="text-xs text-white capitalize font-medium">{{ $facility['type'] }}</strong>
                </div>
                <div class="bg-[#111111] p-4">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Lokasi Gedung</span>
                    <strong class="text-xs text-white font-medium truncate block" title="{{ $facility['location'] }}">{{ $facility['location'] }}</strong>
                </div>
                <div class="bg-[#111111] p-4">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Kapasitas Maks</span>
                    <strong class="text-xs text-white font-medium">
                        {{ !empty($facility['capacity']) ? $facility['capacity'] . ' Orang' : 'Sesuai Unit' }}
                    </strong>
                </div>
                <div class="bg-[#111111] p-4">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Jam Operasional</span>
                    <strong class="text-xs font-mono text-[#0070f3] font-medium">07.00 – 20.00</strong>
                </div>
            </div>

            <!-- Description Card: Clean framed box -->
            <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 space-y-4">
                <h3 class="text-sm font-semibold text-white tracking-tight flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Deskripsi & Spesifikasi Fasilitas.
                </h3>
                <p class="text-xs sm:text-sm text-[#a1a1a1] leading-relaxed whitespace-pre-line">
                    {{ $facility['description'] }}
                </p>

                <!-- Amenities / Supported Info (SRS Section 5 FR-PG-03) -->
                <div class="pt-4 border-t border-[#333333]">
                    <h4 class="font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-3">Fasilitas Penunjang</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-white">
                        <div class="flex items-center gap-2 bg-[#161616] px-3 py-2 rounded-[4px] border border-[#333333]">
                            <svg class="w-3.5 h-3.5 text-[#50e3c2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            AC / Sistem Pendingin Udara
                        </div>
                        <div class="flex items-center gap-2 bg-[#161616] px-3 py-2 rounded-[4px] border border-[#333333]">
                            <svg class="w-3.5 h-3.5 text-[#50e3c2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Proyektor & Layar Presentasi
                        </div>
                        <div class="flex items-center gap-2 bg-[#161616] px-3 py-2 rounded-[4px] border border-[#333333]">
                            <svg class="w-3.5 h-3.5 text-[#50e3c2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Koneksi Internet Wi-Fi Kampus
                        </div>
                        <div class="flex items-center gap-2 bg-[#161616] px-3 py-2 rounded-[4px] border border-[#333333]">
                            <svg class="w-3.5 h-3.5 text-[#50e3c2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Papan Tulis & Spidol Standar
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 4 Cols: Booking Action Box -->
        <div class="lg:col-span-4 space-y-6">
            
            <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 space-y-5">
                <div class="border-b border-[#333333] pb-4">
                    <span class="font-mono text-[10px] text-[#888888] uppercase tracking-wider block mb-1">Aksi Permohonan</span>
                    <h2 class="text-base font-semibold text-white tracking-tight">Ketersediaan & Reservasi.</h2>
                    <p class="text-xs text-[#888888] mt-0.5">Aturan jadwal interval 30 menit 07.00–20.00 WIB.</p>
                </div>

                @if($facility['operational_status'] === 'dapat_digunakan')
                    <div class="p-3.5 rounded-[6px] bg-[#002b11] border border-[#005522] text-xs text-[#50e3c2] space-y-1">
                        <strong class="font-medium text-white block">Siap Digunakan:</strong>
                        <p class="leading-relaxed">Fasilitas beroperasi normal dan siap menerima permohonan reservasi baru.</p>
                    </div>

                    <a href="{{ route('reservations.create', array_merge(['facility_id' => $facility['id']], request()->has('as_user') ? ['as_user' => 1] : [])) }}" 
                       class="w-full bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs py-3 rounded-full transition flex items-center justify-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Pilih Jadwal & Reservasi
                    </a>
                @else
                    <div class="p-3.5 rounded-[6px] bg-[#2b0000] border border-[#550000] text-xs text-[#ff453a] space-y-1">
                        <strong class="font-medium text-white block">Dalam Perbaikan Fisik:</strong>
                        <p class="leading-relaxed">Fasilitas sedang ditangani oleh Petugas Sarpras sehingga reservasi dinonaktifkan sementara.</p>
                    </div>

                    <button disabled class="w-full bg-[#1a1a1a] text-[#666666] border border-[#333333] font-medium text-xs py-3 rounded-full cursor-not-allowed">
                        Reservasi Tidak Tersedia
                    </button>
                @endif

                <!-- Report Issue Button (SRS FR-PGN-12: Hanya Pengguna yang dapat membuat laporan) -->
                @if(request()->has('as_user') || request()->is('my*'))
                    <div class="pt-4 border-t border-[#333333]">
                        <a href="{{ route('user.reports.create', ['facility_id' => $facility['id'], 'as_user' => 1]) }}" 
                           class="w-full bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] hover:border-[#555555] font-medium text-xs py-2.5 rounded-full transition flex items-center justify-center gap-2">
                            <svg class="w-3.5 h-3.5 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Laporkan Masalah / Kerusakan
                        </a>
                    </div>
                @endif
            </div>

            <!-- Operating Notice Box -->
            <div class="border border-[#333333] rounded-[6px] bg-[#111111] p-5 font-mono text-[11px] text-[#a1a1a1] space-y-2">
                <span class="text-[#888888] uppercase tracking-wider block text-[10px]">Ketentuan Sistem</span>
                <p>• Peminjaman hanya pada rentang jam 07.00 - 20.00 WIB.</p>
                <p>• Durasi dihitung kelipatan slot 30 menit.</p>
                <p>• Keputusan persetujuan diterbitkan oleh Petugas Sarpras.</p>
            </div>
        </div>
    </div>
</div>
@endsection
