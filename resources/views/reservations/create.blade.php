@extends('layouts.app')

@section('title', 'Pengecekan Ketersediaan Jadwal · ' . $facility->name)

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Breadcrumb & Back -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('facilities.show', $facility) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-sky-700 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Detail Fasilitas
        </a>

        <a href="{{ route('home') }}" class="text-xs font-semibold text-sky-700 hover:underline">
            Ganti Fasilitas Lain
        </a>
    </div>

    <!-- Facility Info Card -->
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">
            Fasilitas Terpilih
        </span>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 leading-tight">
                    {{ $facility->name }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    {{ $facility->location }} ·
                    @if ($facility->capacity !== null)
                        Kapasitas: {{ $facility->capacity }} Orang
                    @else
                        Kapasitas: Tidak berlaku (Alat)
                    @endif
                </p>
            </div>
            <div>
                <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-bold {{ $facility->operational_status->badgeClasses() }}">
                    {{ $facility->operational_status->label() }}
                </span>
            </div>
        </div>
    </div>

    @if (session('info'))
        <div class="mb-6 rounded-xl border border-sky-200 bg-sky-50 p-4 text-xs font-semibold text-sky-800">
            {{ session('info') }}
        </div>
    @endif

    @if ($hasDraft)
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800 flex items-center gap-2">
            <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Draft fasilitas dan jadwal Anda sebelumnya telah otomatis dipulihkan dari sesi.
        </div>
    @endif

    <!-- Form Pengecekan Jadwal (Tahap 1) -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900">
                Tahap 1: Pilih & Periksa Ketersediaan Jadwal
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Jam operasional kampus: 07.00 – 20.00 WIB. Slot waktu menggunakan interval 30 menit.
            </p>
        </div>

        <form method="POST" action="{{ route('reservations.check_availability') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="facility_id" value="{{ $facility->id }}">

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <!-- Date Picker -->
                <div>
                    <label for="date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tanggal Penggunaan
                    </label>
                    <input
                        type="date"
                        name="date"
                        id="date"
                        min="{{ date('Y-m-d') }}"
                        value="{{ old('date', $date) }}"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-sky-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100 transition"
                    >
                    @error('date')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Time Slot -->
                <div>
                    <label for="start_time" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Waktu Mulai (WIB)
                    </label>
                    <select
                        name="start_time"
                        id="start_time"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-sky-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100 transition"
                    >
                        @php
                            $startSlots = [
                                '07:00', '07:30', '08:00', '08:30', '09:00', '09:30',
                                '10:00', '10:30', '11:00', '11:30', '12:00', '12:30',
                                '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
                                '16:00', '16:30', '17:00', '17:30', '18:00', '18:30',
                                '19:00', '19:30'
                            ];
                        @endphp
                        @foreach ($startSlots as $slot)
                            <option value="{{ $slot }}" @selected(old('start_time', substr($startTime, 0, 5)) === $slot)>
                                {{ $slot }} WIB
                            </option>
                        @endforeach
                    </select>
                    @error('start_time')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Time Slot -->
                <div>
                    <label for="end_time" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Waktu Selesai (WIB)
                    </label>
                    <select
                        name="end_time"
                        id="end_time"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-sky-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100 transition"
                    >
                        @php
                            $endSlots = [
                                '07:30', '08:00', '08:30', '09:00', '09:30',
                                '10:00', '10:30', '11:00', '11:30', '12:00', '12:30',
                                '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
                                '16:00', '16:30', '17:00', '17:30', '18:00', '18:30',
                                '19:00', '19:30', '20:00'
                            ];
                        @endphp
                        @foreach ($endSlots as $slot)
                            <option value="{{ $slot }}" @selected(old('end_time', substr($endTime, 0, 5)) === $slot)>
                                {{ $slot }} WIB
                            </option>
                        @endforeach
                    </select>
                    @error('end_time')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Check Availability Button -->
            <div class="flex items-center justify-between pt-2">
                <span class="text-xs text-slate-500 italic">
                    Perubahan tanggal atau jam akan membatalkan hasil pengecekan sebelumnya.
                </span>

                <button
                    type="submit"
                    class="rounded-xl bg-sky-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200 transition"
                >
                    Cek Ketersediaan
                </button>
            </div>
        </form>

        <!-- Evaluation Results Box -->
        @if ($availability !== null)
            <div class="mt-8 pt-6 border-t border-slate-200">
                @if ($availability->isAvailable)
                    <!-- Available State Box (Emerald) -->
                    <div class="rounded-2xl border border-emerald-300 bg-emerald-50/80 p-6 text-emerald-950">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white shrink-0 mt-0.5">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-emerald-900">
                                    Jadwal Tersedia!
                                </h3>
                                <p class="mt-1 text-sm text-emerald-800 leading-relaxed">
                                    Fasilitas dapat digunakan pada tanggal <strong>{{ \Carbon\CarbonImmutable::parse($date)->translatedFormat('d F Y') }}</strong> pukul <strong>{{ substr($startTime, 0, 5) }} – {{ substr($endTime, 0, 5) }} WIB</strong>.
                                </p>

                                @guest
                                    <!-- Guest Action: Store Draft and Login -->
                                    <div class="mt-5 pt-4 border-t border-emerald-200/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <p class="text-xs text-emerald-800">
                                            Untuk melanjutkan pengajuan reservasi resmi, silakan login ke akun Anda. Pilihan jadwal Anda otomatis tersimpan.
                                        </p>
                                        <form method="POST" action="{{ route('reservations.store_draft') }}">
                                            @csrf
                                            <input type="hidden" name="facility_id" value="{{ $facility->id }}">
                                            <input type="hidden" name="date" value="{{ $date }}">
                                            <input type="hidden" name="start_time" value="{{ substr($startTime, 0, 5) }}">
                                            <input type="hidden" name="end_time" value="{{ substr($endTime, 0, 5) }}">
                                            <button
                                                type="submit"
                                                class="rounded-xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-800 transition whitespace-nowrap"
                                            >
                                                Login untuk Melanjutkan →
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="mt-4 pt-4 border-t border-emerald-200/60 flex items-center justify-between">
                                        <span class="text-xs text-emerald-800">
                                            Anda masuk sebagai <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role->label() }}).
                                        </span>
                                        <span class="inline-flex items-center rounded-lg bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                            Siap Dilanjutkan ke Tahap Form Tujuan
                                        </span>
                                    </div>
                                @endguest
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Unavailable State Box (Rose/Amber) -->
                    <div class="rounded-2xl border border-rose-300 bg-rose-50/80 p-6 text-rose-950">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-600 text-white shrink-0 mt-0.5">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-rose-900">
                                    Jadwal Tidak Tersedia
                                </h3>
                                <p class="mt-1 text-sm text-rose-800 leading-relaxed">
                                    {{ $availability->message }}
                                </p>
                                <p class="mt-3 text-xs text-rose-700">
                                    Silakan ubah jam mulai, jam selesai, atau tanggal di atas, kemudian tekan tombol <strong>Cek Ketersediaan</strong> kembali.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
