@extends('layouts.staff')

@section('title', 'Riwayat Laporan Final · Petugas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Riwayat laporan kerusakan final.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Arsip laporan kerusakan sarpras yang telah selesai ditangani atau ditolak.
            </p>
        </div>

        <div>
            <a href="{{ route('staff.reports.index') }}" 
               class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-4 py-2 rounded-full transition flex items-center gap-1.5 shadow-none">
                Kembali ke Antrean Aktif
            </a>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-4 mb-6 shadow-none flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-80 relative">
            <input 
                type="text" 
                id="searchReportHistory" 
                placeholder="Cari pelapor atau fasilitas..." 
                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-9 pr-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
            />
            <svg class="w-4 h-4 text-[#888888] absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
            <!-- Filter Type -->
            <div class="relative custom-dropdown min-w-[170px]" id="dropdown-filterTypeReportHistory">
                <input type="hidden" id="filterTypeReportHistory" value="">
                <button type="button" onclick="toggleCustomDropdown('filterTypeReportHistory')" id="filterTypeReportHistoryTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="filterTypeReportHistoryLabel">Semua Tipe Fasilitas</span>
                    <svg id="filterTypeReportHistoryChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="filterTypeReportHistoryMenu" class="custom-dropdown-menu hidden absolute top-full right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 min-w-[180px]">
                    <div onclick="selectCustomDropdown('filterTypeReportHistory', '', 'Semua Tipe Fasilitas')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white bg-[#1f1f1f] hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between">
                        <span>Semua Tipe Fasilitas</span>
                        <span class="check-icon text-[#50e3c2] text-xs">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeReportHistory', 'kelas', 'Ruang Kelas')" data-value="kelas" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Ruang Kelas</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeReportHistory', 'laboratorium', 'Laboratorium')" data-value="laboratorium" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Laboratorium</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeReportHistory', 'aula', 'Aula')" data-value="aula" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Aula</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeReportHistory', 'alat', 'Alat Khusus')" data-value="alat" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Alat Khusus</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeReportHistory', 'lapangan', 'Lapangan')" data-value="lapangan" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Lapangan</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                </div>
            </div>

            <!-- Filter Status -->
            <div class="relative custom-dropdown min-w-[160px]" id="dropdown-filterStatusReportHistory">
                <input type="hidden" id="filterStatusReportHistory" value="">
                <button type="button" onclick="toggleCustomDropdown('filterStatusReportHistory')" id="filterStatusReportHistoryTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="filterStatusReportHistoryLabel">Semua Status Final</span>
                    <svg id="filterStatusReportHistoryChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="filterStatusReportHistoryMenu" class="custom-dropdown-menu hidden absolute top-full right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 min-w-[170px]">
                    <div onclick="selectCustomDropdown('filterStatusReportHistory', '', 'Semua Status Final')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white bg-[#1f1f1f] hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between">
                        <span>Semua Status Final</span>
                        <span class="check-icon text-[#50e3c2] text-xs">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatusReportHistory', 'selesai', 'Selesai Ditangani')" data-value="selesai" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Selesai Ditangani</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatusReportHistory', 'ditolak', 'Ditolak')" data-value="ditolak" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Ditolak</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden shadow-none">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]" id="reportHistoryTable">
                <thead class="bg-[#161616] border-b border-[#333333] text-[11px] font-mono uppercase text-[#888888] tracking-wider text-center">
                    <tr>
                        <th class="py-3 px-5 text-center font-medium">No. Laporan</th>
                        <th class="py-3 px-5 text-center font-medium">Nama Pelapor</th>
                        <th class="py-3 px-5 text-center font-medium">Fasilitas Sasaran</th>
                        <th class="py-3 px-5 text-center font-medium">Kategori</th>
                        <th class="py-3 px-5 text-center font-medium">Status Final</th>
                        <th class="py-3 px-5 text-center font-medium">Waktu Lapor</th>
                        <th class="py-3 px-5 text-center font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($reports as $rep)
                        @php
                            $statusVal = $rep['status'] instanceof \BackedEnum ? $rep['status']->value : (string) $rep['status'];
                            $catVal = $rep['category'] instanceof \BackedEnum ? $rep['category']->value : (string) $rep['category'];
                        @endphp
                        <tr class="hover:bg-[#161616] transition-colors"
                            data-search="{{ strtolower($rep['reporter_name'] . ' ' . $rep['facility_name']) }}"
                            data-type="{{ $rep['facility_type'] ?? 'kelas' }}"
                            data-status="{{ $statusVal }}">
                            
                            <td class="py-3.5 px-5 font-mono text-white font-medium">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $rep['report_number'] }}</span>
                                    @if(!empty($rep['photo_path']))
                                        <span class="inline-flex items-center gap-1 text-[9px] font-sans font-medium text-[#50e3c2] bg-[#002b11] border border-[#005522] px-1.5 py-0.5 rounded-[3px]" title="Lampiran foto tersedia">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Foto
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3.5 px-5 font-medium text-white">
                                {{ $rep['reporter_name'] }}
                            </td>

                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $rep['facility_name'] }}</span>
                                <span class="text-[#888888] text-[11px]">{{ $rep['facility_location'] ?? 'Kampus' }}</span>
                            </td>

                            <td class="py-3.5 px-5 text-[#888888] capitalize">
                                {{ str_replace('_', ' ', $catVal) }}
                            </td>

                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                @if($statusVal === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-[11px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522] whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-[11px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000] whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 text-[#888888] font-mono text-[11px]">
                                {{ $rep['created_at'] }}
                            </td>

                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <a href="{{ route('staff.reports.show', ['report' => $rep['id']]) }}" 
                                   class="inline-flex items-center gap-1 bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-3 py-1 rounded-[6px] transition shadow-none">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#888888]">
                                Belum ada riwayat laporan final.
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
    const searchReportHistory = document.getElementById('searchReportHistory');
    const filterTypeReportHistory = document.getElementById('filterTypeReportHistory');
    const filterStatusReportHistory = document.getElementById('filterStatusReportHistory');
    const rowsReportHistory = document.querySelectorAll('#reportHistoryTable tbody tr[data-search]');

    function filterReportHistory() {
        const query = searchReportHistory.value.toLowerCase().trim();
        const type = filterTypeReportHistory.value;
        const status = filterStatusReportHistory.value;

        rowsReportHistory.forEach(row => {
            const matchesSearch = !query || row.dataset.search.includes(query);
            const matchesType = !type || row.dataset.type === type;
            const matchesStatus = !status || row.dataset.status === status;

            row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
        });
    }

    searchReportHistory.addEventListener('input', filterReportHistory);

    function toggleCustomDropdown(name) {
        const menu = document.getElementById(name + 'Menu');
        const chevron = document.getElementById(name + 'Chevron');
        const isOpen = !menu.classList.contains('hidden');

        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));

        if (!isOpen) {
            menu.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        }
    }

    function selectCustomDropdown(name, value, label) {
        document.getElementById(name).value = value;
        document.getElementById(name + 'Label').textContent = label;
        
        const menu = document.getElementById(name + 'Menu');
        menu.querySelectorAll('.dropdown-item').forEach(item => {
            const check = item.querySelector('.check-icon');
            if (item.getAttribute('data-value') === value) {
                item.classList.add('bg-[#1f1f1f]', 'text-white');
                item.classList.remove('text-[#cccccc]');
                if (check) check.classList.remove('hidden');
            } else {
                item.classList.remove('bg-[#1f1f1f]', 'text-white');
                item.classList.add('text-[#cccccc]');
                if (check) check.classList.add('hidden');
            }
        });

        menu.classList.add('hidden');
        const chevron = document.getElementById(name + 'Chevron');
        if (chevron) chevron.classList.remove('rotate-180');

        filterReportHistory();
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        }
    });
</script>
@endpush
