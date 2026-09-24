@extends('layouts.admin')

@section('title', 'Rekapitulasi Operasional & Okupansi — PPK Room Reservation')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8 space-y-6">

    <!-- Header Page & Export Dropdown -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Rekapitulasi Operasional & Okupansi
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Pusat pelaporan analitik berkala tingkat pemanfaatan fasilitas dan frekuensi insiden kerusakan sarana-prasarana kampus.
            </p>
        </div>

        <!-- Export Dropdown -->
        <div class="relative inline-block text-left" id="exportDropdownContainer">
            <button
                type="button"
                id="exportButton"
                onclick="toggleExportMenu()"
                class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-[6px] transition flex items-center gap-2 active:scale-95 shadow-none"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Unduh Laporan</span>
                <svg class="h-3 w-3 text-[#555555]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
                id="exportMenu"
                class="hidden absolute right-0 z-50 mt-2 w-64 origin-top-right rounded-[6px] border border-[#333333] bg-[#111111] p-1.5 shadow-2xl"
            >
                <div class="px-3 py-2 border-b border-[#222222] text-[10px] font-mono uppercase tracking-wider text-[#888888]">
                    Pilih Format Berkas
                </div>
                <a
                    href="{{ route('admin.recaps.export', ['month' => $selectedMonth, 'year' => $selectedYear, 'metric' => $selectedMetric, 'group_by' => $selectedGroupBy, 'format' => 'csv']) }}"
                    class="flex items-center gap-3 rounded-[4px] px-3 py-2 text-xs font-medium text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white transition"
                >
                    <span class="flex h-6 w-6 items-center justify-center rounded-[4px] bg-[#002b11] text-[#50e3c2] border border-[#005522] font-mono font-bold text-[10px]">
                        CSV
                    </span>
                    <div>
                        <div class="font-medium text-white text-xs">Format CSV (.csv)</div>
                        <div class="text-[10px] text-[#888888]">Standar pemisah koma UTF-8</div>
                    </div>
                </a>
                <a
                    href="{{ route('admin.recaps.export', ['month' => $selectedMonth, 'year' => $selectedYear, 'metric' => $selectedMetric, 'group_by' => $selectedGroupBy, 'format' => 'xlsx']) }}"
                    class="flex items-center gap-3 rounded-[4px] px-3 py-2 text-xs font-medium text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white transition"
                >
                    <span class="flex h-6 w-6 items-center justify-center rounded-[4px] bg-[#001733] text-[#0070f3] border border-[#003b80] font-mono font-bold text-[10px]">
                        XLS
                    </span>
                    <div>
                        <div class="font-medium text-white text-xs">Microsoft Excel (.xlsx)</div>
                        <div class="text-[10px] text-[#888888]">Spreadsheet terformat rapi</div>
                    </div>
                </a>
                <a
                    href="{{ route('admin.recaps.export', ['month' => $selectedMonth, 'year' => $selectedYear, 'metric' => $selectedMetric, 'group_by' => $selectedGroupBy, 'format' => 'pdf']) }}"
                    class="flex items-center gap-3 rounded-[4px] px-3 py-2 text-xs font-medium text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white transition"
                >
                    <span class="flex h-6 w-6 items-center justify-center rounded-[4px] bg-[#2b0000] text-[#ff453a] border border-[#550000] font-mono font-bold text-[10px]">
                        PDF
                    </span>
                    <div>
                        <div class="font-medium text-white text-xs">Dokumen PDF (.pdf)</div>
                        <div class="text-[10px] text-[#888888]">Layout landscape siap cetak</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Segmented Control Panel -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-5 space-y-4">
        <form id="recapFilterForm" method="GET" action="{{ route('admin.recaps.index') }}" class="space-y-4">
            <input type="hidden" name="metric" id="metricInput" value="{{ $selectedMetric }}">
            <input type="hidden" name="group_by" id="groupByInput" value="{{ $selectedGroupBy }}">

            <!-- Row 1: Period Selection (Month, Year) -->
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-full sm:w-auto">
                    <label for="month" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1.5">
                        Bulan Kalender
                    </label>
                    <select
                        id="month"
                        name="month"
                        class="w-full sm:w-52 bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] px-3 py-2 focus:outline-none focus:border-white transition font-mono"
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
                    <label for="year" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1.5">
                        Tahun
                    </label>
                    <select
                        id="year"
                        name="year"
                        class="w-full sm:w-36 bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] px-3 py-2 focus:outline-none focus:border-white transition font-mono"
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
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-[#222222]">
                <!-- Metric Toggle -->
                <div class="flex items-center gap-1 rounded-[6px] bg-[#0a0a0a] p-1 border border-[#333333]">
                    <button
                        type="button"
                        onclick="setMetric('occupancy')"
                        class="rounded-[4px] px-3.5 py-1.5 text-xs font-medium transition {{ $selectedMetric === 'occupancy' ? 'bg-white text-black font-semibold shadow-none' : 'text-[#888888] hover:text-white' }}"
                    >
                        📊 Tingkat Okupansi
                    </button>
                    <button
                        type="button"
                        onclick="setMetric('damage_frequency')"
                        class="rounded-[4px] px-3.5 py-1.5 text-xs font-medium transition {{ $selectedMetric === 'damage_frequency' ? 'bg-white text-black font-semibold shadow-none' : 'text-[#888888] hover:text-white' }}"
                    >
                        🔧 Frekuensi Kerusakan
                    </button>
                </div>

                <!-- Grouping Toggle -->
                <div class="flex items-center gap-1 rounded-[6px] bg-[#0a0a0a] p-1 border border-[#333333]">
                    <button
                        type="button"
                        onclick="setGroupBy('facility')"
                        class="rounded-[4px] px-3.5 py-1.5 text-xs font-medium transition {{ $selectedGroupBy === 'facility' ? 'bg-white text-black font-semibold shadow-none' : 'text-[#888888] hover:text-white' }}"
                    >
                        🏢 Per Fasilitas
                    </button>
                    <button
                        type="button"
                        onclick="setGroupBy('location')"
                        class="rounded-[4px] px-3.5 py-1.5 text-xs font-medium transition {{ $selectedGroupBy === 'location' ? 'bg-white text-black font-semibold shadow-none' : 'text-[#888888] hover:text-white' }}"
                    >
                        📍 Per Lokasi Gedung
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Executive Summary Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Card 1: Periode -->
        <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-5">
            <div class="text-[10px] font-mono uppercase tracking-wider text-[#888888]">Periode Kalender</div>
            <div class="mt-2 text-xl font-semibold text-white tracking-tight">
                {{ $recapData['period']['month_name'] ?? DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $recapData['period']['year'] ?? $selectedYear }}
            </div>
            <div class="mt-1 text-[11px] text-[#888888]">
                {{ $recapData['period']['days_in_month'] ?? 30 }} Hari Kalender (07.00–20.00 WIB)
            </div>
        </div>

        <!-- Card 2: Volume Penggunaan / Insiden -->
        <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-5">
            <div class="text-[10px] font-mono uppercase tracking-wider text-[#888888]">
                {{ $selectedMetric === 'occupancy' ? 'Total Durasi Selesai' : 'Total Insiden Kerusakan' }}
            </div>
            <div class="mt-2 text-xl font-semibold text-white tracking-tight">
                @if ($selectedMetric === 'occupancy')
                    {{ number_format($recapData['summary']['total_completed_hours'] ?? 0, 2, ',', '.') }} <span class="text-xs font-normal text-[#888888]">Jam</span>
                @else
                    {{ $recapData['summary']['total_damage_incidents'] ?? 0 }} <span class="text-xs font-normal text-[#888888]">Insiden</span>
                @endif
            </div>
            <div class="mt-1 text-[11px] text-[#888888]">
                {{ $selectedMetric === 'occupancy' ? 'Murni dari reservasi berstatus selesai' : 'Transisi status dapat digunakan → perbaikan' }}
            </div>
        </div>

        <!-- Card 3: Rata-Rata Okupansi / Terdampak -->
        <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-5">
            <div class="text-[10px] font-mono uppercase tracking-wider text-[#888888]">
                {{ $selectedMetric === 'occupancy' ? 'Okupansi Agregat Kampus' : 'Fasilitas Terdampak' }}
            </div>
            <div class="mt-2 text-xl font-semibold tracking-tight text-white">
                @if ($selectedMetric === 'occupancy')
                    @if (($recapData['summary']['overall_occupancy_rate'] ?? null) !== null)
                        <span class="text-[#50e3c2]">{{ number_format($recapData['summary']['overall_occupancy_rate'], 2, ',', '.') }}%</span>
                    @else
                        <span class="text-xs font-normal text-[#888888]">Tidak dapat dihitung</span>
                    @endif
                @else
                    <span class="text-[#ff453a]">{{ collect($recapData['rows'] ?? [])->where('incident_count', '>', 0)->count() }}</span> <span class="text-xs font-normal text-[#888888]">Entitas</span>
                @endif
            </div>
            <div class="mt-1 text-[11px] text-[#888888]">
                {{ $selectedMetric === 'occupancy' ? 'Rasio total selesai dibagi total kapasitas aktif' : 'Entitas yang mengalami minimal 1 insiden' }}
            </div>
        </div>

        <!-- Card 4: Total Inventaris -->
        <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-5">
            <div class="text-[10px] font-mono uppercase tracking-wider text-[#888888]">
                {{ $selectedGroupBy === 'facility' ? 'Total Fasilitas Terdata' : 'Total Lokasi / Gedung' }}
            </div>
            <div class="mt-2 text-xl font-semibold text-white tracking-tight">
                {{ $recapData['summary']['total_items'] ?? count($recapData['rows'] ?? []) }} <span class="text-xs font-normal text-[#888888]">{{ $selectedGroupBy === 'facility' ? 'Objek' : 'Area' }}</span>
            </div>
            <div class="mt-1 text-[11px] text-[#888888]">
                Termasuk fasilitas berstatus aktif & nonaktif
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider">
                    @if ($selectedMetric === 'occupancy')
                        @if ($selectedGroupBy === 'facility')
                            <tr>
                                <th class="py-3 px-5 text-center w-12 font-medium">No</th>
                                <th class="py-3 px-5 font-medium">Fasilitas</th>
                                <th class="py-3 px-5 font-medium">Tipe</th>
                                <th class="py-3 px-5 font-medium">Lokasi</th>
                                <th class="py-3 px-5 text-right font-medium">Durasi Selesai</th>
                                <th class="py-3 px-5 text-right font-medium">Kapasitas Slot Aktif</th>
                                <th class="py-3 px-5 text-center font-medium">Tingkat Okupansi</th>
                            </tr>
                        @else
                            <tr>
                                <th class="py-3 px-5 text-center w-12 font-medium">No</th>
                                <th class="py-3 px-5 font-medium">Lokasi / Gedung</th>
                                <th class="py-3 px-5 text-center font-medium">Jumlah Fasilitas</th>
                                <th class="py-3 px-5 text-right font-medium">Total Durasi Selesai</th>
                                <th class="py-3 px-5 text-right font-medium">Total Kapasitas Aktif</th>
                                <th class="py-3 px-5 text-center font-medium">Tingkat Okupansi Agregat</th>
                            </tr>
                        @endif
                    @else
                        @if ($selectedGroupBy === 'facility')
                            <tr>
                                <th class="py-3 px-5 text-center w-12 font-medium">No</th>
                                <th class="py-3 px-5 font-medium">Fasilitas</th>
                                <th class="py-3 px-5 font-medium">Tipe</th>
                                <th class="py-3 px-5 font-medium">Lokasi</th>
                                <th class="py-3 px-5 text-center font-medium">Jumlah Insiden Perbaikan</th>
                                <th class="py-3 px-5 text-center font-medium">Insiden Terakhir</th>
                            </tr>
                        @else
                            <tr>
                                <th class="py-3 px-5 text-center w-12 font-medium">No</th>
                                <th class="py-3 px-5 font-medium">Lokasi / Gedung</th>
                                <th class="py-3 px-5 text-center font-medium">Jumlah Fasilitas</th>
                                <th class="py-3 px-5 text-center font-medium">Total Insiden Perbaikan</th>
                                <th class="py-3 px-5 text-center font-medium">Insiden Terakhir</th>
                            </tr>
                        @endif
                    @endif
                </thead>
                <tbody class="divide-y divide-[#222222] text-xs">
                    @forelse ($recapData['rows'] ?? [] as $index => $row)
                        @if ($selectedMetric === 'occupancy')
                            @if ($selectedGroupBy === 'facility')
                                <tr class="hover:bg-[#161616] transition-colors">
                                    <td class="py-3.5 px-5 text-center font-mono text-[11px] text-[#666666]">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-medium text-white">{{ $row['name'] }}</div>
                                        <div class="text-[11px] text-[#666666] mt-0.5">
                                            Status Master: {{ $row['is_active'] ? 'Aktif' : 'Nonaktif' }} &bull; {{ $row['active_days'] }} Hari Aktif
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 font-mono text-[11px] text-[#a1a1a1]">
                                        {{ ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']) }}
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-[#a1a1a1]">
                                        {{ $row['location'] }}
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-right font-mono text-white">
                                        {{ number_format($row['used_hours'], 2, ',', '.') }} Jam
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-right font-mono text-[#888888]">
                                        {{ number_format($row['operational_hours'], 2, ',', '.') }} Jam
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-center">
                                        @if ($row['is_calculable'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                                {{ number_format($row['occupancy_rate'], 2, ',', '.') }}%
                                            </span>
                                        @else
                                            <span class="text-[#666666] italic text-[11px]">
                                                Tidak dapat dihitung
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                <tr class="hover:bg-[#161616] transition-colors">
                                    <td class="py-3.5 px-5 text-center font-mono text-[11px] text-[#666666]">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-5 font-medium text-white">
                                        📍 {{ $row['location'] }}
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-center font-mono text-[11px] text-[#a1a1a1]">
                                        {{ $row['facility_count'] }} Fasilitas
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-right font-mono text-white">
                                        {{ number_format($row['used_hours'], 2, ',', '.') }} Jam
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-right font-mono text-[#888888]">
                                        {{ number_format($row['operational_hours'], 2, ',', '.') }} Jam
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-center">
                                        @if ($row['is_calculable'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                                {{ number_format($row['occupancy_rate'], 2, ',', '.') }}%
                                            </span>
                                        @else
                                            <span class="text-[#666666] italic text-[11px]">
                                                Tidak dapat dihitung
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @else
                            @if ($selectedGroupBy === 'facility')
                                <tr class="hover:bg-[#161616] transition-colors">
                                    <td class="py-3.5 px-5 text-center font-mono text-[11px] text-[#666666]">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-5 font-medium text-white">
                                        {{ $row['name'] }}
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 font-mono text-[11px] text-[#a1a1a1]">
                                        {{ ucfirst($row['type'] instanceof \BackedEnum ? $row['type']->value : (string) $row['type']) }}
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-[#a1a1a1]">
                                        {{ $row['location'] }}
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-center">
                                        @if ($row['incident_count'] > 0)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                                <span class="h-1.5 w-1.5 rounded-full bg-[#ff453a]"></span>
                                                {{ $row['incident_count'] }} Kali
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                                0 Kali (Stabil)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-center font-mono text-[11px] text-[#888888]">
                                        {{ $row['latest_incident_at'] ? \Carbon\Carbon::parse($row['latest_incident_at'])->translatedFormat('d M Y H:i') : '—' }}
                                    </td>
                                </tr>
                            @else
                                <tr class="hover:bg-[#161616] transition-colors">
                                    <td class="py-3.5 px-5 text-center font-mono text-[11px] text-[#666666]">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-5 font-medium text-white">
                                        📍 {{ $row['location'] }}
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-center font-mono text-[11px] text-[#a1a1a1]">
                                        {{ $row['facility_count'] }} Fasilitas
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-center">
                                        @if ($row['incident_count'] > 0)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                                <span class="h-1.5 w-1.5 rounded-full bg-[#ff453a]"></span>
                                                {{ $row['incident_count'] }} Kali
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                                0 Kali (Stabil)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap py-3.5 px-5 text-center font-mono text-[11px] text-[#888888]">
                                        {{ $row['latest_incident_at'] ? \Carbon\Carbon::parse($row['latest_incident_at'])->translatedFormat('d M Y H:i') : '—' }}
                                    </td>
                                </tr>
                            @endif
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#888888]">
                                <div class="text-xs font-semibold text-white">Tidak ada data rekapitulasi</div>
                                <div class="mt-1 text-[11px] text-[#666666]">Belum ada fasilitas yang terdata di dalam sistem.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function toggleExportMenu() {
        const menu = document.getElementById('exportMenu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function (e) {
        const container = document.getElementById('exportDropdownContainer');
        const menu = document.getElementById('exportMenu');
        if (container && menu && !container.contains(e.target)) {
            menu.classList.add('hidden');
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
