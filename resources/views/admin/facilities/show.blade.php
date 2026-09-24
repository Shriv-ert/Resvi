@extends('layouts.admin')

@section('title', 'Detail Fasilitas: ' . $facility->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
        <a href="{{ route('admin.facilities.index') }}" class="hover:text-slate-900 transition">Kelola Fasilitas</a>
        <span>/</span>
        <span class="text-slate-900">Rincian Fasilitas</span>
    </nav>

    <!-- Header Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $facility->name }}</h1>
            <p class="mt-1 text-xs text-slate-500">{{ $facility->location }} — {{ $facility->type->label() }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a
                href="{{ route('admin.facilities.edit', $facility) }}"
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900"
            >
                <span>✏</span>
                Edit Master
            </a>

            @if ($facility->is_active)
                <button
                    type="button"
                    id="openDeactivateModalBtn"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-200"
                >
                    <span>⚠</span>
                    Nonaktifkan
                </button>
            @else
                <button
                    type="button"
                    id="openReactivateModalBtn"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-sky-700 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200"
                >
                    <span>↺</span>
                    Aktifkan Kembali
                </button>
            @endif

            <a
                href="{{ route('admin.facilities.index') }}"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                ← Kembali
            </a>
        </div>
    </div>

    <!-- Main Overview Card -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Photo & Badges Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col items-center text-center">
            @php
                $primaryPhoto = $facility->photos->firstWhere('is_primary', true) ?? $facility->photos->first();
            @endphp
            @if ($primaryPhoto)
                <img
                    src="{{ asset('storage/' . $primaryPhoto->photo_path) }}"
                    alt="{{ $facility->name }}"
                    class="h-48 w-full rounded-xl object-cover border border-slate-200 shadow-sm"
                >
            @else
                <div class="flex h-48 w-full items-center justify-center rounded-xl bg-slate-100 text-slate-400 font-bold text-base border border-slate-200">
                    🏢 Tidak Ada Foto
                </div>
            @endif

            <div class="mt-4 flex flex-wrap justify-center gap-2">
                <!-- Master Status Badge -->
                @if ($facility->is_active)
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-700/10">
                        Status Master: Aktif
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-700/10">
                        Status Master: Nonaktif
                    </span>
                @endif

                <!-- Operational Status Badge -->
                @if ($facility->operational_status->value === 'dapat_digunakan')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-700/10">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-600"></span>
                        Operasional: Dapat digunakan
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-700/10">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span>
                        Operasional: Dalam perbaikan
                    </span>
                @endif
            </div>

            <div class="mt-4 w-full border-t border-slate-100 pt-3 text-left text-xs text-slate-500 space-y-1">
                <p><strong>UUID:</strong> <span class="font-mono text-[11px]">{{ $facility->id }}</span></p>
                <p><strong>Didaftarkan:</strong> {{ $facility->created_at ? $facility->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB</p>
                <p><strong>Pembaruan Terakhir:</strong> {{ $facility->updated_at ? $facility->updated_at->translatedFormat('d F Y, H:i') : '-' }} WIB</p>
            </div>
        </div>

        <!-- Specifications & Description -->
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                Spesifikasi & Informasi Master
            </h2>

            <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tipe Objek</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $facility->type->label() }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Lokasi / Gedung</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $facility->location }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kapasitas Daya Tampung</dt>
                    <dd class="mt-1 font-semibold text-slate-900">
                        {{ $facility->capacity ? number_format($facility->capacity) . ' Orang' : '— (Objek Alat / Tidak Ditentukan)' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Wewenang Operasional</dt>
                    <dd class="mt-1 text-xs text-slate-600">
                        Status operasional fisik dikelola secara eksklusif oleh <strong>Petugas</strong> melalui penanganan laporan kerusakan & pemeliharaan.
                    </dd>
                </div>
            </dl>

            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Deskripsi Fasilitas</h3>
                <div class="rounded-xl bg-slate-50 p-4 text-xs leading-relaxed text-slate-700 whitespace-pre-line border border-slate-100">
                    {{ $facility->description }}
                </div>
            </div>
        </div>
    </div>

    <!-- Status History & Audit Logs Section -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50/75 px-6 py-4">
            <h2 class="text-sm font-bold text-slate-900">
                Riwayat Status & Mutasi Fasilitas
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Rekam jejak perubahan status master data (oleh Admin) dan status operasional fisik (oleh Petugas).
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50/50 text-slate-700 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-3">Waktu Mutasi</th>
                        <th class="px-6 py-3">Jenis Perubahan</th>
                        <th class="px-6 py-3">Transisi Nilai</th>
                        <th class="px-6 py-3">Alasan / Catatan</th>
                        <th class="px-6 py-3">Pelaku</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($facility->statusLogs as $log)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="whitespace-nowrap px-6 py-3.5 font-medium text-slate-500">
                                {{ $log->changed_at ? $log->changed_at->translatedFormat('d M Y, H:i') : '-' }} WIB
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5">
                                <span class="font-semibold text-slate-900">
                                    {{ $log->change_type->label() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5 font-mono text-xs">
                                <span class="text-slate-400">{{ $log->old_value }}</span>
                                <span class="mx-1 text-slate-400">→</span>
                                <span class="font-bold text-slate-900">{{ $log->new_value }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 max-w-sm">
                                {{ $log->reason ?? '—' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-3.5">
                                <div class="font-medium text-slate-900">{{ $log->changedBy->name ?? 'Sistem' }}</div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $log->changedBy->role->label() ?? 'System' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Belum ada riwayat mutasi status yang tercatat untuk fasilitas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('admin.facilities.partials.deactivate-modal', ['facility' => $facility])
@include('admin.facilities.partials.reactivate-modal', ['facility' => $facility])
@endsection
