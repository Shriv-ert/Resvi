@extends('layouts.admin')

@section('title', 'Rekapitulasi Operasional & Okupansi — PPK Room Reservation')

@section('content')
<div class="space-y-6">
    <!-- Header Page & Export Dropdown -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rekapitulasi Operasional & Okupansi</h1>
            <p class="mt-1 text-sm text-slate-600">
                Pusat pelaporan analitik berkala tingkat pemanfaatan fasilitas dan frekuensi insiden kerusakan sarana-prasarana kampus.
            </p>
        </div>

        <!-- Export Dropdown -->
        <div class="relative inline-block text-left" id="exportDropdownContainer">
            <button
                type="button"
                id="exportButton"
                class="inline-flex items-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-200"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Unduh Laporan</span>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
                id="exportMenu"
                class="hidden absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-2xl border border-slate-200 bg-white p-2 shadow-xl ring-1 ring-black/5"
            >
                <div class="px-3 py-2 border-b border-slate-100 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    Pilih Format Berkas
                </div>
                <a
                    href="{{ route('admin.recaps.export', ['month' => $selectedMonth, 'year' => $selectedYear, 'metric' => $selectedMetric, 'group_by' => $selectedGroupBy, 'format' => 'csv']) }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900"
                >
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 font-bold text-xs">
                        CSV
                    </span>
                    <div>
                        <div class="font-semibold text-slate-900">Format CSV (.csv)</div>
                        <div class="text-[11px] text-slate-500">Standar pemisah koma UTF-8</div>
                    </div>
                </a>
                <a
                    href="{{ route('admin.recaps.export', ['month' => $selectedMonth, 'year' => $selectedYear, 'metric' => $selectedMetric, 'group_by' => $selectedGroupBy, 'format' => 'xlsx']) }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900"
                >
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-sky-50 text-sky-700 font-bold text-xs">
                        XLS
                    </span>
                    <div>
                        <div class="font-semibold text-slate-900">Microsoft Excel (.xlsx)</div>
                        <div class="text-[11px] text-slate-500">Spreadsheet terformat rapi</div>
                    </div>
                </a>
                <a
                    href="{{ route('admin.recaps.export', ['month' => $selectedMonth, 'year' => $selectedYear, 'metric' => $selectedMetric, 'group_by' => $selectedGroupBy, 'format' => 'pdf']) }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900"
                >
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-50 text-rose-600 font-bold text-xs">
                        PDF
                    </span>
                    <div>
                        <div class="font-semibold text-slate-900">Dokumen PDF (.pdf)</div>
                        <div class="text-[11px] text-slate-500">Layout landscape siap cetak</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Segmented Control Panel -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
        <form id="recapFilterForm" method="GET" action="{{ route('admin.recaps.index') }}" class="space-y-4">
            <input type="hidden" name="metric" id="metricInput" value="{{ $selectedMetric }}">
            <input type="hidden" name="group_by" id="groupByInput" value="{{ $selectedGroupBy }}">

            <!-- Row 1: Period Selection (Month, Year) -->
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-full sm:w-auto">
                    <label for="month" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Bulan Kalender
                    </label>
                    <select
                        id="month"
                        name="month"
                        class="block w-full sm:w-52 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                        onchange="this.form.submit()"
                    >
                        @php
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ];
                        @endphp
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" @selected($selectedMonth == $num)>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-auto">
                    <label for="year" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">
                        Tahun
                    </label>
                    <select
                        id="year"
                        name="year"
                        class="block w-full sm:w-36 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm shadow-sm outline-none transition focus:border-sky-600 focus:ring-4 focus:ring-sky-100"
                        onchange="this.form.submit()"
                    >
                        @for ($y = (int) now()->year + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" @selected($selectedYear == $y)>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Row 2: Segmented Controls (Metric & Grouping) -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pt-3 border-t border-slate-100">
                <!-- Metric Toggle -->
                <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1.5 border border-slate-200">
                    <button
                        type="button"
                        onclick="setMetric('occupancy')"
                        class="rounded-lg px-4 py-2 text-xs font-bold transition {{ $selectedMetric === 'occupancy' ? 'bg-white text-sky-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        📊 Tingkat Okupansi
                    </button>
                    <button
                        type="button"
                        onclick="setMetric('damage_frequency')"
                        class="rounded-lg px-4 py-2 text-xs font-bold transition {{ $selectedMetric === 'damage_frequency' ? 'bg-white text-rose-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        🔧 Frekuensi Kerusakan
                    </button>
                </div>

                <!-- Grouping Toggle -->
                <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1.5 border border-slate-200">
                    <button
                        type="button"
                        onclick="setGroupBy('facility')"
                        class="rounded-lg px-4 py-2 text-xs font-bold transition {{ $selectedGroupBy === 'facility' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        🏢 Per Fasilitas
                    </button>
                    <button
                        type="button"
                        onclick="setGroupBy('location')"
                        class="rounded-lg px-4 py-2 text-xs font-bold transition {{ $selectedGroupBy === 'location' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        📍 Per Lokasi / Gedung
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Executive Summary Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Card 1: Periode -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Periode Kalender</div>
            <div class="mt-2 text-xl font-bold tracking-tight text-slate-900">
                {{ $recapData['period']['month_name'] }} {{ $recapData['period']['year'] }}
            </div>
            <div class="mt-1 text-xs text-slate-500">
                {{ $recapData['period']['days_in_month'] }} Hari Kalender (07.00–20.00 WIB)
            </div>
        </div>

        <!-- Card 2: Volume Penggunaan / Insiden -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                {{ $selectedMetric === 'occupancy' ? 'Total Durasi Selesai' : 'Total Insiden Kerusakan' }}
            </div>
            <div class="mt-2 text-xl font-bold tracking-tight text-slate-900">
                @if ($selectedMetric === 'occupancy')
                    {{ number_format($recapData['summary']['total_completed_hours'], 2, ',', '.') }} <span class="text-sm font-semibold text-slate-600">Jam</span>
                @else
                    {{ $recapData['summary']['total_damage_incidents'] }} <span class="text-sm font-semibold text-slate-600">Insiden</span>
                @endif
            </div>
            <div class="mt-1 text-xs text-slate-500">
                {{ $selectedMetric === 'occupancy' ? 'Murni dari reservasi berstatus selesai' : 'Transisi status dapat digunakan → perbaikan' }}
            </div>
        </div>

        <!-- Card 3: Rata-Rata Okupansi / Terdampak -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                {{ $selectedMetric === 'occupancy' ? 'Okupansi Agregat Kampus' : 'Fasilitas Terdampak' }}
            </div>
            <div class="mt-2 text-xl font-bold tracking-tight {{ $selectedMetric === 'occupancy' ? 'text-sky-700' : 'text-rose-700' }}">
                @if ($selectedMetric === 'occupancy')
                    @if ($recapData['summary']['overall_occupancy_rate'] !== null)
                        {{ number_format($recapData['summary']['overall_occupancy_rate'], 2, ',', '.') }}%
                    @else
                        <span class="text-sm font-normal text-slate-400">Tidak dapat dihitung</span>
                    @endif
                @else
                    {{ $recapData['rows']->where('incident_count', '>', 0)->count() }} <span class="text-sm font-semibold text-slate-600">Entitas</span>
                @endif
            </div>
            <div class="mt-1 text-xs text-slate-500">
                {{ $selectedMetric === 'occupancy' ? 'Rasio total selesai dibagi total kapasitas aktif' : 'Entitas yang mengalami minimal 1 insiden' }}
            </div>
        </div>

        <!-- Card 4: Total Inventaris -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                {{ $selectedGroupBy === 'facility' ? 'Total Fasilitas Terdata' : 'Total Lokasi / Gedung' }}
            </div>
            <div class="mt-2 text-xl font-bold tracking-tight text-slate-900">
                {{ $recapData['summary']['total_items'] }} <span class="text-sm font-semibold text-slate-600">{{ $selectedGroupBy === 'facility' ? 'Objek' : 'Area' }}</span>
            </div>
            <div class="mt-1 text-xs text-slate-500">
                Termasuk fasilitas berstatus aktif & nonaktif
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="border-b border-slate-200 bg-slate-50/75 text-xs font-semibold uppercase tracking-wider text-slate-700">
                    @if ($selectedMetric === 'occupancy')
                        @if ($selectedGroupBy === 'facility')
                            <tr>
                                <th class="px-6 py-4 text-center w-12">No</th>
                                <th class="px-6 py-4">Fasilitas</th>
                                <th class="px-6 py-4">Tipe</th>
                                <th class="px-6 py-4">Lokasi</th>
                                <th class="px-6 py-4 text-right">Durasi Selesai</th>
                                <th class="px-6 py-4 text-right">Kapasitas Slot Aktif</th>
                                <th class="px-6 py-4 text-center">Tingkat Okupansi</th>
                            </tr>
                        @else
                            <tr>
                                <th class="px-6 py-4 text-center w-12">No</th>
                                <th class="px-6 py-4">Lokasi / Gedung</th>
                                <th class="px-6 py-4 text-center">Jumlah Fasilitas</th>
                                <th class="px-6 py-4 text-right">Total Durasi Selesai</th>
                                <th class="px-6 py-4 text-right">Total Kapasitas Aktif</th>
                                <th class="px-6 py-4 text-center">Tingkat Okupansi Agregat</th>
                            </tr>
                        @endif
                    @else
                        @if ($selectedGroupBy === 'facility')
                            <tr>
                                <th class="px-6 py-4 text-center w-12">No</th>
                                <th class="px-6 py-4">Fasilitas</th>
                                <th class="px-6 py-4">Tipe</th>
                                <th class="px-6 py-4">Lokasi</th>
                                <th class="px-6 py-4 text-center">Jumlah Insiden Perbaikan</th>
                                <th class="px-6 py-4 text-center">Insiden Terakhir</th>
                            </tr>
                        @else
                            <tr>
                                <th class="px-6 py-4 text-center w-12">No</th>
                                <th class="px-6 py-4">Lokasi / Gedung</th>
                                <th class="px-6 py-4 text-center">Jumlah Fasilitas</th>
                                <th class="px-6 py-4 text-center">Total Insiden Perbaikan</th>
                                <th class="px-6 py-4 text-center">Insiden Terakhir</th>
                            </tr>
                        @endif
                    @endif
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($recapData['rows'] as $index => $row)
                        @if ($selectedMetric === 'occupancy')
                            @if ($selectedGroupBy === 'facility')
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-6 py-4 text-center text-xs text-slate-400 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $row['name'] }}</div>
                                        <div class="text-xs text-slate-400">
                                            Status Master: {{ $row['is_active'] ? 'Aktif' : 'Nonaktif' }} &bull; {{ $row['active_days'] }} Hari Aktif
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-700">
                                        {{ $row['location'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-slate-900">
                                        {{ number_format($row['used_hours'], 2, ',', '.') }} Jam
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-slate-600">
                                        {{ number_format($row['operational_hours'], 2, ',', '.') }} Jam
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        @if ($row['is_calculable'])
                                            <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 ring-1 ring-inset ring-sky-700/15">
                                                {{ number_format($row['occupancy_rate'], 2, ',', '.') }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 italic">
                                                Tidak dapat dihitung
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-6 py-4 text-center text-xs text-slate-400 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        📍 {{ $row['location'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center font-medium text-slate-700">
                                        {{ $row['facility_count'] }} Fasilitas
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-slate-900">
                                        {{ number_format($row['used_hours'], 2, ',', '.') }} Jam
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-slate-600">
                                        {{ number_format($row['operational_hours'], 2, ',', '.') }} Jam
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        @if ($row['is_calculable'])
                                            <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 ring-1 ring-inset ring-sky-700/15">
                                                {{ number_format($row['occupancy_rate'], 2, ',', '.') }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500 italic">
                                                Tidak dapat dihitung
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @else
                            @if ($selectedGroupBy === 'facility')
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-6 py-4 text-center text-xs text-slate-400 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $row['name'] }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-700">
                                        {{ $row['location'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        @if ($row['incident_count'] > 0)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-700/15">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>
                                                {{ $row['incident_count'] }} Kali
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-700/15">
                                                0 Kali (Stabil)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center text-xs text-slate-500">
                                        {{ $row['latest_incident_at'] ? \Carbon\Carbon::parse($row['latest_incident_at'])->translatedFormat('d M Y H:i') : '—' }}
                                    </td>
                                </tr>
                            @else
                                <tr class="transition hover:bg-slate-50/60">
                                    <td class="px-6 py-4 text-center text-xs text-slate-400 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        📍 {{ $row['location'] }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center font-medium text-slate-700">
                                        {{ $row['facility_count'] }} Fasilitas
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        @if ($row['incident_count'] > 0)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-700/15">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>
                                                {{ $row['incident_count'] }} Kali
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-700/15">
                                                0 Kali (Stabil)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center text-xs text-slate-500">
                                        {{ $row['latest_incident_at'] ? \Carbon\Carbon::parse($row['latest_incident_at'])->translatedFormat('d M Y H:i') : '—' }}
                                    </td>
                                </tr>
                            @endif
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <div class="text-sm font-semibold text-slate-700">Tidak ada data rekapitulasi</div>
                                <div class="mt-1 text-xs text-slate-400">Belum ada fasilitas yang terdata di dalam sistem.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pure Vanilla JS for Segmented Controls & Dropdown (Zero node_modules) -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Export Dropdown
        const exportBtn = document.getElementById('exportButton');
        const exportMenu = document.getElementById('exportMenu');

        if (exportBtn && exportMenu) {
            exportBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                exportMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!exportMenu.contains(e.target) && !exportBtn.contains(e.target)) {
                    exportMenu.classList.add('hidden');
                }
            });
        }
    });

    function setMetric(metric) {
        document.getElementById('metricInput').value = metric;
        document.getElementById('recapFilterForm').submit();
    }

    function setGroupBy(groupBy) {
        document.getElementById('groupByInput').value = groupBy;
        document.getElementById('recapFilterForm').submit();
    }
</script>
@endsection
