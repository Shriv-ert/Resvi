@extends('layouts.app')

@section('title', $facility->name . ' · Detail Fasilitas')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-sky-700 transition">Katalog Fasilitas</a>
        <span>/</span>
        <span class="text-slate-900 truncate">{{ $facility->name }}</span>
    </nav>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main Detail (Left 2 Columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                <div class="flex flex-wrap items-center gap-2.5 mb-3">
                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-800">
                        {{ $facility->type->label() }}
                    </span>
                    <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-bold {{ $facility->operational_status->badgeClasses() }}">
                        {{ $facility->operational_status->label() }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 leading-snug">
                    {{ $facility->name }}
                </h1>

                <!-- Meta Specs -->
                <div class="mt-4 flex flex-wrap gap-y-2 gap-x-6 text-sm text-slate-600 border-t border-slate-100 pt-4">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Lokasi: <strong>{{ $facility->location }}</strong></span>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @if ($facility->capacity !== null)
                            <span>Kapasitas: <strong>{{ $facility->capacity }} Orang</strong></span>
                        @else
                            <span class="text-slate-400 italic">Kapasitas: Tidak berlaku (Alat)</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-3">Deskripsi & Spesifikasi Fasilitas</h2>
                <div class="text-sm leading-relaxed text-slate-600 space-y-3 whitespace-pre-line">
                    {{ $facility->description }}
                </div>
            </div>

            <!-- Photo Showcase / Gallery -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Galeri Fasilitas</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse ($facility->photos as $photo)
                        <div class="relative h-48 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 flex items-center justify-center">
                            <div class="text-center p-4 text-slate-400">
                                <svg class="h-10 w-10 mx-auto opacity-50 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-semibold">{{ $facility->name }} - Foto {{ $loop->iteration }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="sm:col-span-2 py-8 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                            Belum ada foto tambahan yang diunggah untuk fasilitas ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sticky Reservation Action Sidebar (Right Column) -->
        <div class="space-y-6">
            <div class="sticky top-24 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-2">Aksi Penggunaan Fasilitas</h3>
                <p class="text-xs text-slate-500 mb-5 leading-relaxed">
                    Periksa jadwal yang kosong dan ajukan reservasi melalui form bertahap.
                </p>

                @if ($facility->operational_status === \App\Enums\OperationalStatus::DapatDigunakan)
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 mb-5 text-xs text-emerald-800 leading-relaxed">
                        <strong>Fasilitas Siap Digunakan</strong><br>
                        Fasilitas dalam kondisi operasional baik. Anda dapat memeriksa jadwal ketersediaan untuk tanggal yang diinginkan.
                    </div>

                    <a
                        href="{{ route('reservations.create', ['facility_id' => $facility->id]) }}"
                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200 transition"
                    >
                        Pilih Jadwal & Cek Ketersediaan
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @else
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 mb-5 text-xs text-amber-800 leading-relaxed">
                        <strong class="text-amber-900">Sedang Dalam Perbaikan</strong><br>
                        Fasilitas ini sedang dalam pemeliharaan atau perbaikan sarana prasarana. Reservasi dinonaktifkan sementara hingga perbaikan selesai.
                    </div>

                    <button
                        type="button"
                        disabled
                        class="w-full cursor-not-allowed rounded-xl bg-slate-200 px-5 py-3 text-sm font-bold text-slate-400 transition"
                    >
                        Reservasi Dinonaktifkan
                    </button>
                @endif

                <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                    <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 hover:underline">
                        ← Kembali ke Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
