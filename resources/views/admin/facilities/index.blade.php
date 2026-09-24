@extends('layouts.admin')

@section('title', 'Kelola Fasilitas — Beranda Admin')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Fasilitas</h1>
            <p class="mt-1 text-sm text-slate-600">
                Beranda direktori fasilitas kampus (ruang kelas, aula, lab, alat, dan lapangan) dengan pemantauan status operasional terkini.
            </p>
        </div>
        <div>
            <a
                href="{{ route('admin.facilities.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200"
            >
                <span class="text-base leading-none">+</span>
                Tambah Fasilitas
            </a>
        </div>
    </div>

    <!-- Filter & Search Panel -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.facilities.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Cari Fasilitas</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nama fasilitas atau lokasi/gedung..."
                    class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
            </div>

            <!-- Type Filter -->
            <div>
                <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tipe Fasilitas</label>
                <select
                    id="type"
                    name="type"
                    class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                    <option value="">Semua Tipe</option>
                    @foreach (\App\Enums\FacilityType::cases() as $typeCase)
                        <option value="{{ $typeCase->value }}" {{ request('type') === $typeCase->value ? 'selected' : '' }}>
                            {{ $typeCase->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Master Status Filter -->
            <div>
                <label for="master_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Status Master Data</label>
                <select
                    id="master_status"
                    name="master_status"
                    class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                    <option value="">Semua Status Master</option>
                    <option value="active" {{ request('master_status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('master_status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Operational Status Filter -->
            <div>
                <label for="operational_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Status Operasional</label>
                <select
                    id="operational_status"
                    name="operational_status"
                    class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                >
                    <option value="">Semua Kondisi</option>
                    @foreach (\App\Enums\OperationalStatus::cases() as $opCase)
                        <option value="{{ $opCase->value }}" {{ request('operational_status') === $opCase->value ? 'selected' : '' }}>
                            {{ $opCase->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit & Reset Buttons -->
            <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-5 pt-1">
                <button
                    type="submit"
                    class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Terapkan Filter
                </button>

                @if(request()->hasAny(['search', 'type', 'master_status', 'operational_status']))
                    <a
                        href="{{ route('admin.facilities.index') }}"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Facilities -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50/75 text-xs font-semibold uppercase tracking-wider text-slate-700">
                    <tr>
                        <th class="px-6 py-4">Fasilitas</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Kapasitas</th>
                        <th class="px-6 py-4">Status Master</th>
                        <th class="px-6 py-4">Status Operasional</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($facilities as $facility)
                        <tr class="transition hover:bg-slate-50/60 {{ ! $facility->is_active ? 'bg-slate-50/40 text-slate-400' : '' }}">
                            <!-- Name & Thumbnail -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $primaryPhoto = $facility->photos->firstWhere('is_primary', true) ?? $facility->photos->first();
                                    @endphp
                                    @if ($primaryPhoto)
                                        <img
                                            src="{{ asset('storage/' . $primaryPhoto->photo_path) }}"
                                            alt="{{ $facility->name }}"
                                            class="h-11 w-11 flex-shrink-0 rounded-xl object-cover border border-slate-200 shadow-sm"
                                        >
                                    @else
                                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-400 font-bold text-xs border border-slate-200">
                                            🏢
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900 {{ ! $facility->is_active ? 'text-slate-600' : '' }}">
                                            {{ $facility->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 line-clamp-1 max-w-xs">
                                            {{ $facility->description }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ $facility->type->label() }}
                                </span>
                            </td>

                            <!-- Location -->
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-700">
                                {{ $facility->location }}
                            </td>

                            <!-- Capacity -->
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-700">
                                {{ $facility->capacity ? number_format($facility->capacity) . ' Orang' : '—' }}
                            </td>

                            <!-- Master Status -->
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($facility->is_active)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-700/10">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 ring-1 ring-inset ring-rose-700/10">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Operational Status -->
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($facility->operational_status->value === 'dapat_digunakan')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-700/10">
                                        <span class="h-1.5 w-1.5 rounded-full bg-sky-600"></span>
                                        Dapat digunakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-700/10">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span>
                                        Dalam perbaikan
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a
                                    href="{{ route('admin.facilities.show', $facility) }}"
                                    class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900"
                                >
                                    Rincian & Kelola
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <span class="text-3xl text-slate-400">🏛</span>
                                    <h3 class="mt-3 text-sm font-semibold text-slate-900">Tidak ada fasilitas ditemukan</h3>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Tidak ada fasilitas yang cocok dengan kriteria pencarian dan filter saat ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($facilities->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
