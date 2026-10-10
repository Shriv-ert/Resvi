@extends('layouts.app')

@php
    $fId = is_array($facility) ? $facility['id'] : $facility->id;
    $fName = is_array($facility) ? $facility['name'] : $facility->name;
    $fType = is_array($facility) ? $facility['type'] : ($facility->type instanceof \BackedEnum ? $facility->type->value : $facility->type);
    $fTypeLabel = is_array($facility) ? ucfirst($facility['type']) : ($facility->type instanceof \App\Enums\FacilityType ? $facility->type->label() : ucfirst($fType));
    $fLocation = is_array($facility) ? $facility['location'] : $facility->location;
    $fCapacity = is_array($facility) ? ($facility['capacity'] ?? null) : $facility->capacity;
    $fDescription = is_array($facility) ? $facility['description'] : $facility->description;
    $fOperational = is_array($facility) ? $facility['operational_status'] : ($facility->operational_status instanceof \BackedEnum ? $facility->operational_status->value : $facility->operational_status);
    
    if (is_array($facility)) {
        $primaryPhotoUrl = $facility['photo'] ?? 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80';
        $photos = [];
    } else {
        $primaryPhoto = $facility->photos?->firstWhere('is_primary', true) ?? $facility->photos?->first();
        $primaryPhotoUrl = $facility->primary_photo_url ?? ($primaryPhoto ? $primaryPhoto->url : 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80');
        $photos = $facility->photos ?? collect();
    }
@endphp

@section('title', $fName . ' · Detail Fasilitas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-mono text-[#888888] mb-6">
        <a href="{{ route('facilities.index', request()->has('as_user') ? ['as_user' => 1] : []) }}" class="hover:text-white transition">Katalog Fasilitas</a>
        <span>/</span>
        <span class="text-white truncate max-w-xs sm:max-w-md">{{ $fName }}</span>
    </nav>

    <!-- Main Content 12-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left 8 Cols: Images & Facility Specs -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Main Hero Image Showcase with 1px hairline border -->
            <div class="relative w-full h-80 sm:h-96 rounded-[6px] overflow-hidden bg-[#161616] border border-[#333333]">
                <img 
                    src="{{ $primaryPhotoUrl }}" 
                    alt="{{ $fName }}" 
                    class="w-full h-full object-cover"
                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&auto=format&fit=crop&q=80';"
                />

                <div class="absolute top-4 left-4 flex flex-wrap gap-2">
                    <span class="bg-white/90 text-[#0f172a] border border-black/10 dark:bg-black/80 dark:text-white dark:border-[#333333] backdrop-blur-md text-[11px] font-mono uppercase tracking-wider px-3 py-1 rounded-[4px] font-medium shadow-sm">
                        {{ $fTypeLabel }}
                    </span>
                    @if($fOperational === 'dapat_digunakan')
                        <span class="bg-white/95 text-[#059669] border border-[#a7f3d0] dark:bg-[#002b11]/90 dark:text-[#50e3c2] dark:border-[#005522] backdrop-blur-md text-[11px] font-mono px-3 py-1 rounded-[4px] flex items-center gap-1.5 font-medium shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#10b981] dark:bg-[#50e3c2]"></span>
                            Dapat digunakan
                        </span>
                    @else
                        <span class="bg-white/95 text-[#dc2626] border border-[#fecaca] dark:bg-[#2b0000]/90 dark:text-[#ff453a] dark:border-[#550000] backdrop-blur-md text-[11px] font-mono px-3 py-1 rounded-[4px] flex items-center gap-1.5 font-medium shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#ef4444] dark:bg-[#ff453a]"></span>
                            Dalam perbaikan
                        </span>
                    @endif
                </div>
            </div>

            <!-- Title & Basic Key Indicators -->
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em] mb-2">
                    {{ $fName }}.
                </h1>
                <p class="text-xs sm:text-sm text-[#888888] font-mono flex items-center gap-2">
                    <span>{{ $fLocation }}</span>
                    <span>•</span>
                    <span>ID: {{ $fId }}</span>
                </p>
            </div>

            <!-- Specs Grid (4 Cols) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-px bg-[#333333] border border-[#333333] rounded-[6px] overflow-hidden">
                <div class="bg-[#111111] p-4">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Tipe Sarana</span>
                    <strong class="text-xs text-white font-medium capitalize">{{ $fTypeLabel }}</strong>
                </div>
                <div class="bg-[#111111] p-4">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Lokasi Gedung</span>
                    <strong class="text-xs text-white font-medium block leading-snug break-words" title="{{ $fLocation }}">{{ $fLocation }}</strong>
                </div>
                <div class="bg-[#111111] p-4">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Kapasitas Maks</span>
                    <strong class="text-xs text-white font-medium">
                        {{ !empty($fCapacity) ? $fCapacity . ' Orang' : 'Sesuai Unit Alat' }}
                    </strong>
                </div>
                <div class="bg-[#111111] p-4">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-[#888888] block mb-1">Jam Operasional</span>
                    <strong class="text-xs font-mono text-[#0070f3] font-medium">07.00 – 20.00 WIB</strong>
                </div>
            </div>

            <!-- Description Card -->
            <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 space-y-4">
                <h3 class="text-sm font-semibold text-white tracking-tight flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Deskripsi & Spesifikasi Fasilitas.
                </h3>
                <p class="text-xs sm:text-sm text-[#a1a1a1] leading-relaxed whitespace-pre-line">
                    {{ $fDescription }}
                </p>

                <!-- Amenities / Supported Info -->
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

                @if($fOperational === 'dapat_digunakan')
                    <div class="p-3.5 rounded-[6px] bg-[#002b11] border border-[#005522] text-xs text-[#50e3c2] space-y-1">
                        <strong class="font-medium text-white block">Siap Digunakan:</strong>
                        <p class="leading-relaxed">Fasilitas beroperasi normal dan siap menerima permohonan reservasi baru.</p>
                    </div>

                    <a href="{{ route('reservations.create', array_merge(['facility_id' => $fId], request()->has('as_user') ? ['as_user' => 1] : [])) }}" 
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

                <!-- Report Issue Button -->
                @if(auth()->check() || request()->has('as_user') || request()->is('my*'))
                    <div class="pt-4 border-t border-[#333333]">
                        <a href="{{ route('user.reports.create', array_merge(['facility_id' => $fId], request()->has('as_user') ? ['as_user' => 1] : [])) }}" 
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
