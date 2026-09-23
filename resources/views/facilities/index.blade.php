@extends('layouts.app')

@section('title', 'Katalog Fasilitas & Ruangan')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Header / Banner Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
            Katalog Fasilitas Kampus
        </h1>
        <p class="mt-2 text-base text-slate-600 max-w-3xl">
            Jelajahi dan temukan ruang kuliah, laboratorium, aula serbaguna, peralatan tetap, serta lapangan olahraga yang tersedia di lingkungan kampus.
        </p>
    </div>

    <!-- Filter & Search Section -->
    <section class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-100" aria-label="Filter Fasilitas">
        <form method="GET" action="{{ route('home') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Search by Name -->
                <div>
                    <label for="q" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Fasilitas
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="q"
                            id="q"
                            value="{{ $filters['q'] ?? '' }}"
                            placeholder="Cari nama fasilitas..."
                            class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100 transition"
                        >
                    </div>
                </div>

                <!-- Filter by Type -->
                <div>
                    <label for="type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tipe Fasilitas
                    </label>
                    <select
                        name="type"
                        id="type"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-sky-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100 transition"
                    >
                        <option value="">Semua Tipe</option>
                        @foreach ($types as $typeEnum)
                            <option value="{{ $typeEnum->value }}" @selected(($filters['type'] ?? '') === $typeEnum->value)>
                                {{ $typeEnum->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter by Location -->
                <div>
                    <label for="location" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Lokasi / Gedung
                    </label>
                    <select
                        name="location"
                        id="location"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-sky-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100 transition"
                    >
                        <option value="">Semua Lokasi</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc }}" @selected(($filters['location'] ?? '') === $loc)>
                                {{ $loc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter by Minimum Capacity -->
                <div>
                    <label for="capacity" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kapasitas Minimal (Orang)
                    </label>
                    <input
                        type="number"
                        name="capacity"
                        id="capacity"
                        min="1"
                        value="{{ $filters['capacity'] ?? '' }}"
                        placeholder="Contoh: 30"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100 transition"
                    >
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100">
                <div class="text-xs text-slate-500">
                    Menampilkan <span class="font-bold text-slate-800">{{ $facilities->total() }}</span> fasilitas aktif
                </div>

                <div class="flex items-center gap-2">
                    @if (!empty($filters['q']) || !empty($filters['type']) || !empty($filters['location']) || !empty($filters['capacity']))
                        <a
                            href="{{ route('home') }}"
                            class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition"
                        >
                            Reset Filter
                        </a>
                    @endif

                    <button
                        type="submit"
                        class="rounded-xl bg-sky-700 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200 transition"
                    >
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- Error Validation Feedback -->
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Facilities Grid Section -->
    @if ($facilities->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-16 px-6 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Tidak ada fasilitas yang ditemukan</h3>
            <p class="mt-1 text-sm text-slate-500 max-w-md mx-auto">
                Tidak ada data yang cocok dengan kriteria pencarian atau filter yang Anda terapkan.
            </p>
            <div class="mt-6">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-sky-700 px-4 py-2 text-xs font-semibold text-white hover:bg-sky-800 transition">
                    Bersihkan Semua Filter
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($facilities as $facility)
                <article class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md hover:border-slate-300 overflow-hidden">
                    <!-- Photo Cover Container -->
                    <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                        @if ($facility->primaryPhoto)
                            <div class="h-full w-full flex items-center justify-center bg-gradient-to-tr from-sky-100 to-indigo-50 text-sky-800">
                                <svg class="h-16 w-16 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gradient-to-tr from-slate-100 to-slate-200 text-slate-400">
                                <svg class="h-16 w-16 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        @endif

                        <!-- Type Badge (Floating top-left) -->
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center rounded-lg bg-white/90 backdrop-blur px-2.5 py-1 text-xs font-bold text-slate-800 shadow-sm">
                                {{ $facility->type->label() }}
                            </span>
                        </div>

                        <!-- Operational Status Badge (Floating top-right) -->
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold shadow-sm {{ $facility->operational_status->badgeClasses() }}">
                                {{ $facility->operational_status->label() }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="flex flex-1 flex-col p-5">
                        <h2 class="text-lg font-bold text-slate-900 group-hover:text-sky-700 leading-snug">
                            {{ $facility->name }}
                        </h2>

                        <!-- Location & Capacity Meta -->
                        <div class="mt-3 space-y-1.5 text-xs text-slate-600">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">{{ $facility->location }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                @if ($facility->capacity !== null)
                                    <span>Kapasitas: <strong>{{ $facility->capacity }} Orang</strong></span>
                                @else
                                    <span class="text-slate-400 italic">Kapasitas: Tidak berlaku (Alat)</span>
                                @endif
                            </div>
                        </div>

                        <!-- Description Snippet -->
                        <p class="mt-3 text-xs leading-relaxed text-slate-500 line-clamp-2">
                            {{ $facility->description }}
                        </p>

                        <!-- Card Action Footer -->
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                            @if ($facility->operational_status === \App\Enums\OperationalStatus::DalamPerbaikan)
                                <span class="text-xs font-semibold text-amber-700">
                                    Sedang Diperbaiki
                                </span>
                            @else
                                <span class="text-xs font-semibold text-emerald-700">
                                    Siap Direservasi
                                </span>
                            @endif

                            <a
                                href="{{ url('/facilities/' . $facility->id) }}"
                                class="inline-flex items-center gap-1 text-xs font-bold text-sky-700 hover:text-sky-900 transition hover:underline"
                            >
                                Lihat Detail
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination Section -->
        <div class="mt-8">
            {{ $facilities->links() }}
        </div>
    @endif
</div>
@endsection
