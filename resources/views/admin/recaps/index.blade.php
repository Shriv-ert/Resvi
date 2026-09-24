@extends('layouts.admin')

@section('title', 'Rekapitulasi Okupansi & Frekuensi Kerusakan · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Rekapitulasi penggunaan & kerusakan.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Laporan okupansi jam operasional fasilitas dan frekuensi kerusakan sarpras periode bulanan.
            </p>
        </div>

        <!-- Export Buttons (SRS FR-ADM-11 & User Story 17: CSV, Excel, PDF) -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="alert('Mengekspor laporan format CSV...')" class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-3.5 py-1.5 rounded-full transition flex items-center gap-1.5 shadow-none">
                <svg class="w-3.5 h-3.5 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                CSV
            </button>
            <button type="button" onclick="alert('Mengekspor laporan format Excel (.xlsx)...')" class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-3.5 py-1.5 rounded-full transition flex items-center gap-1.5 shadow-none">
                <svg class="w-3.5 h-3.5 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Excel
            </button>
            <button type="button" onclick="alert('Mengekspor laporan format PDF...')" class="bg-[#2b0000] hover:bg-[#3d0000] text-[#ff453a] border border-[#550000] text-xs font-medium px-3.5 py-1.5 rounded-full transition flex items-center gap-1.5 shadow-none">
                <svg class="w-3.5 h-3.5 text-[#ff453a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                PDF
            </button>
        </div>
    </div>

    <!-- Filter Configuration Bar: Crisp border, micro 6px radius -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-5 mb-8">
        <form id="recapFilterForm" method="GET" action="{{ route('admin.recaps.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Jenis Rekap -->
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-recap_type">
                <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Jenis Rekapitulasi</label>
                <input type="hidden" id="recap_type" name="recap_type" value="{{ $recapType ?? 'okupansi' }}">
                <button type="button" onclick="toggleCustomDropdown('recap_type')" id="recap_typeTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="recap_typeLabel" class="truncate pr-2">
                        {{ ($recapType ?? 'okupansi') === 'okupansi' ? 'Tingkat Okupansi Pemakaian (%)' : 'Frekuensi Kerusakan Fisik' }}
                    </span>
                    <svg id="recap_typeChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="recap_typeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                    <div onclick="selectCustomDropdown('recap_type', 'okupansi', 'Tingkat Okupansi Pemakaian (%)', true)" data-value="okupansi" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between {{ ($recapType ?? 'okupansi') === 'okupansi' ? 'bg-[#1f1f1f]' : '' }}">
                        <span>Tingkat Okupansi Pemakaian (%)</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ ($recapType ?? 'okupansi') === 'okupansi' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('recap_type', 'kerusakan', 'Frekuensi Kerusakan Fisik', true)" data-value="kerusakan" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ ($recapType ?? '') === 'kerusakan' ? 'bg-[#1f1f1f] text-white' : '' }}">
                        <span>Frekuensi Kerusakan Fisik</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ ($recapType ?? '') === 'kerusakan' ? '' : 'hidden' }}">✓</span>
                    </div>
                </div>
            </div>

            <!-- Pengelompokan -->
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-group_by">
                <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Pengelompokan Data</label>
                <input type="hidden" id="group_by" name="group_by" value="{{ $groupBy ?? 'fasilitas' }}">
                <button type="button" onclick="toggleCustomDropdown('group_by')" id="group_byTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="group_byLabel" class="truncate pr-2">
                        {{ ($groupBy ?? 'fasilitas') === 'fasilitas' ? 'Per Fasilitas Individual' : 'Per Lokasi Gedung' }}
                    </span>
                    <svg id="group_byChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="group_byMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                    <div onclick="selectCustomDropdown('group_by', 'fasilitas', 'Per Fasilitas Individual', true)" data-value="fasilitas" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between {{ ($groupBy ?? 'fasilitas') === 'fasilitas' ? 'bg-[#1f1f1f]' : '' }}">
                        <span>Per Fasilitas Individual</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ ($groupBy ?? 'fasilitas') === 'fasilitas' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('group_by', 'lokasi', 'Per Lokasi Gedung', true)" data-value="lokasi" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ ($groupBy ?? '') === 'lokasi' ? 'bg-[#1f1f1f] text-white' : '' }}">
                        <span>Per Lokasi Gedung</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ ($groupBy ?? '') === 'lokasi' ? '' : 'hidden' }}">✓</span>
                    </div>
                </div>
            </div>

            <!-- Filter Bulan -->
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-month">
                <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Bulan Periode</label>
                <input type="hidden" id="month" name="month" value="{{ $selectedMonth ?? date('n') }}">
                <button type="button" onclick="toggleCustomDropdown('month')" id="monthTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="monthLabel" class="truncate pr-2">
                        {{ DateTime::createFromFormat('!m', $selectedMonth ?? date('n'))->format('F') }}
                    </span>
                    <svg id="monthChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="monthMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                    <div class="p-2 border-b border-[#222222] sticky top-0 bg-[#111111] z-10">
                        <div class="relative">
                            <input type="text" placeholder="Cari bulan..." 
                                   id="search-month"
                                   oninput="filterDropdownOptions(this, 'monthMenu')"
                                   onclick="event.stopPropagation()"
                                   class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-[11px] rounded-[4px] pl-7 pr-2 py-1.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]">
                            <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    @for($m = 1; $m <= 12; $m++)
                        @php $mName = DateTime::createFromFormat('!m', $m)->format('F'); @endphp
                        <div onclick="selectCustomDropdown('month', '{{ $m }}', '{{ $mName }}', true)" data-value="{{ $m }}" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ ($selectedMonth ?? date('n')) == $m ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>{{ $mName }}</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ ($selectedMonth ?? date('n')) == $m ? '' : 'hidden' }}">✓</span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Filter Tahun -->
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-year">
                <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Tahun Periode</label>
                <input type="hidden" id="year" name="year" value="{{ $selectedYear ?? '2026' }}">
                <button type="button" onclick="toggleCustomDropdown('year')" id="yearTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="yearLabel" class="truncate pr-2">{{ $selectedYear ?? '2026' }}</span>
                    <svg id="yearChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="yearMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                    <div onclick="selectCustomDropdown('year', '2026', '2026', true)" data-value="2026" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ ($selectedYear ?? '2026') == '2026' ? 'bg-[#1f1f1f] text-white' : '' }}">
                        <span>2026</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ ($selectedYear ?? '2026') == '2026' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('year', '2025', '2025', true)" data-value="2025" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ ($selectedYear ?? '') == '2025' ? 'bg-[#1f1f1f] text-white' : '' }}">
                        <span>2025</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ ($selectedYear ?? '') == '2025' ? '' : 'hidden' }}">✓</span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Recap Data Table: Crisp border, micro 6px radius, NO soft shadows -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="p-5 border-b border-[#333333] flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-semibold text-white tracking-tight">
                    {{ ($recapType ?? 'okupansi') === 'okupansi' ? 'Tingkat Okupansi Fasilitas' : 'Frekuensi Kejadian Kerusakan' }} 
                    — Kelompok {{ ($groupBy ?? 'fasilitas') === 'fasilitas' ? 'Per Fasilitas' : 'Per Lokasi Gedung' }}
                </h3>
                <p class="text-xs text-[#888888] mt-0.5 font-mono text-[11px]">
                    Periode: {{ DateTime::createFromFormat('!m', $selectedMonth ?? date('n'))->format('F') }} {{ $selectedYear ?? 2026 }} • Operasional: 07.00–20.00 WIB (26 Slot/Hari)
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-medium">{{ ($groupBy ?? 'fasilitas') === 'fasilitas' ? 'Nama Fasilitas' : 'Nama Lokasi Gedung' }}</th>
                        @if(($groupBy ?? 'fasilitas') === 'fasilitas')
                            <th class="py-3 px-5 font-medium">Tipe & Lokasi</th>
                        @endif
                        
                        @if(($recapType ?? 'okupansi') === 'okupansi')
                            <th class="py-3 px-5 font-medium">Total Durasi Selesai</th>
                            <th class="py-3 px-5 font-medium">Kapasitas Slot Aktif</th>
                            <th class="py-3 px-5 font-medium">Persentase Okupansi</th>
                        @else
                            <th class="py-3 px-5 font-medium">Kejadian Masuk Perbaikan</th>
                            <th class="py-3 px-5 font-medium">Status Operasional</th>
                            <th class="py-3 px-5 font-medium">Tingkat Kerawanan</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($recapData as $row)
                        <tr class="hover:bg-[#161616] transition-colors">
                            <td class="py-3.5 px-5 font-medium text-white">
                                {{ $row['label'] }}
                            </td>

                            @if(($groupBy ?? 'fasilitas') === 'fasilitas')
                                <td class="py-3.5 px-5 text-[#a1a1a1] font-mono text-[11px]">
                                    <span class="capitalize">{{ $row['type'] ?? 'Kelas' }}</span> • {{ $row['location'] ?? 'Gedung' }}
                                </td>
                            @endif

                            @if(($recapType ?? 'okupansi') === 'okupansi')
                                <td class="py-3.5 px-5 font-mono text-white">
                                    {{ $row['completed_hours'] }} Jam
                                </td>
                                <td class="py-3.5 px-5 text-[#888888] font-mono text-[11px]">
                                    {{ $row['total_available_hours'] }} Jam ({{ $row['total_available_slots'] }} slot)
                                </td>
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 bg-[#1a1a1a] rounded-[2px] h-1.5 overflow-hidden border border-[#333333]">
                                            <div class="bg-white h-full" style="width: {{ $row['occupancy_rate'] }}%"></div>
                                        </div>
                                        <span class="font-medium text-white font-mono text-xs">{{ $row['occupancy_rate'] }}%</span>
                                    </div>
                                </td>
                            @else
                                <td class="py-3.5 px-5 font-medium text-[#ff453a] font-mono">
                                    {{ $row['breakdown_count'] }} kali
                                </td>
                                <td class="py-3.5 px-5">
                                    @if(($row['operational_status'] ?? '') === 'dalam_perbaikan')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span> Dalam Perbaikan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span> Siap Digunakan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5">
                                    @if($row['breakdown_count'] >= 3)
                                        <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">Tinggi</span>
                                    @elseif($row['breakdown_count'] > 0)
                                        <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-mono text-[#f5a623] bg-[#2b1e00] border border-[#553c00]">Sedang</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-[4px] text-[10px] font-mono text-[#a1a1a1] bg-[#1a1a1a] border border-[#333333]">Rendah</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-[#888888]">
                                Tidak ada data rekapitulasi untuk periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleCustomDropdown(id) {
        const menu = document.getElementById(id + 'Menu');
        const chevron = document.getElementById(id + 'Chevron');
        const isOpen = menu && !menu.classList.contains('hidden');

        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));

        if (!isOpen && menu) {
            menu.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');

            const searchInput = menu.querySelector('input[type="text"]');
            if (searchInput) {
                searchInput.value = '';
                filterDropdownOptions(searchInput, id + 'Menu');
                setTimeout(() => searchInput.focus(), 50);
            }
        }
    }

    function filterDropdownOptions(inputEl, menuId) {
        const q = inputEl.value.toLowerCase().trim();
        const menu = document.getElementById(menuId);
        if (!menu) return;
        menu.querySelectorAll('.dropdown-item').forEach(item => {
            const text = item.innerText.toLowerCase();
            item.style.display = text.includes(q) ? 'flex' : 'none';
        });
    }

    function selectCustomDropdown(id, val, label, autoSubmit) {
        const input = document.getElementById(id);
        const labelEl = document.getElementById(id + 'Label');
        const menu = document.getElementById(id + 'Menu');
        const chevron = document.getElementById(id + 'Chevron');

        if (input) input.value = val;
        if (labelEl) labelEl.innerText = label;

        if (menu) {
            menu.querySelectorAll('.dropdown-item').forEach(item => {
                if (item.dataset.value === String(val)) {
                    item.classList.add('bg-[#1f1f1f]', 'text-white');
                    const check = item.querySelector('.check-icon');
                    if (check) check.classList.remove('hidden');
                } else {
                    item.classList.remove('bg-[#1f1f1f]', 'text-white');
                    const check = item.querySelector('.check-icon');
                    if (check) check.classList.add('hidden');
                }
            });
            menu.classList.add('hidden');
        }
        if (chevron) chevron.classList.remove('rotate-180');

        if (autoSubmit) {
            document.getElementById('recapFilterForm').submit();
        }
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        }
    });
</script>
@endpush
