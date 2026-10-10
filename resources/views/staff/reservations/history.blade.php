@extends('layouts.staff')

@section('title', 'Riwayat Reservasi Final · Petugas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Riwayat reservasi final.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Arsip transaksi reservasi yang telah selesai, ditolak, dibatalkan, atau kedaluwarsa.
            </p>
        </div>

        <div>
            <a href="{{ route('staff.reservations.index') }}" 
               class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-4 py-2 rounded-full transition flex items-center gap-1.5 shadow-none">
                Kembali ke Antrean Aktif
            </a>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-4 mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-80 relative">
            <input 
                type="text" 
                id="searchHistory" 
                placeholder="Cari pemohon atau fasilitas..." 
                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-8 pr-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
            />
            <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
            <!-- Filter Type -->
            <div class="relative custom-dropdown min-w-[170px]" id="dropdown-filterType">
                <input type="hidden" id="filterType" value="">
                <button type="button" onclick="toggleCustomDropdown('filterType')" id="filterTypeTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="filterTypeLabel">Semua Tipe Fasilitas</span>
                    <svg id="filterTypeChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="filterTypeMenu" class="custom-dropdown-menu hidden absolute top-full right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 min-w-[180px]">
                    <div onclick="selectCustomDropdown('filterType', '', 'Semua Tipe Fasilitas')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white bg-[#1f1f1f] hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between">
                        <span>Semua Tipe Fasilitas</span>
                        <span class="check-icon text-[#50e3c2] text-xs">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterType', 'kelas', 'Ruang Kelas')" data-value="kelas" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Ruang Kelas</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterType', 'laboratorium', 'Laboratorium')" data-value="laboratorium" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Laboratorium</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterType', 'aula', 'Aula')" data-value="aula" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Aula</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterType', 'alat', 'Alat Khusus')" data-value="alat" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Alat Khusus</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterType', 'lapangan', 'Lapangan')" data-value="lapangan" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Lapangan</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                </div>
            </div>

            <!-- Filter Status -->
            <div class="relative custom-dropdown min-w-[170px]" id="dropdown-filterStatus">
                <input type="hidden" id="filterStatus" value="">
                <button type="button" onclick="toggleCustomDropdown('filterStatus')" id="filterStatusTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="filterStatusLabel">Semua Status Final</span>
                    <svg id="filterStatusChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="filterStatusMenu" class="custom-dropdown-menu hidden absolute top-full right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 min-w-[210px]">
                    <div onclick="selectCustomDropdown('filterStatus', '', 'Semua Status Final')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white bg-[#1f1f1f] hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between">
                        <span>Semua Status Final</span>
                        <span class="check-icon text-[#50e3c2] text-xs">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatus', 'selesai', 'Selesai')" data-value="selesai" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Selesai</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatus', 'ditolak', 'Ditolak')" data-value="ditolak" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Ditolak</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatus', 'dibatalkan_oleh_pengguna', 'Dibatalkan Pengguna')" data-value="dibatalkan_oleh_pengguna" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Dibatalkan Pengguna</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatus', 'dibatalkan_oleh_petugas', 'Dibatalkan Petugas')" data-value="dibatalkan_oleh_petugas" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Dibatalkan Petugas</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatus', 'kedaluwarsa', 'Kedaluwarsa')" data-value="kedaluwarsa" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Kedaluwarsa</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]" id="historyTable">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider text-center">
                    <tr>
                        <th class="py-3 px-5 text-center font-medium">No. Reservasi</th>
                        <th class="py-3 px-5 text-center font-medium">Nama Pemohon</th>
                        <th class="py-3 px-5 text-center font-medium">Fasilitas</th>
                        <th class="py-3 px-5 text-center font-medium">Jadwal Pinjam</th>
                        <th class="py-3 px-5 text-center font-medium">Status Final</th>
                        <th class="py-3 px-5 text-center font-medium">Waktu Pengajuan</th>
                        <th class="py-3 px-5 text-center font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($reservations as $res)
                        @php
                            $statusVal = $res['status'] instanceof \BackedEnum ? $res['status']->value : (string) $res['status'];
                            $resDate = $res['date'] instanceof \DateTimeInterface ? $res['date']->format('Y-m-d') : $res['date'];
                        @endphp
                        <tr class="hover:bg-[#161616] transition-colors"
                            data-search="{{ strtolower($res['applicant_name'] . ' ' . $res['facility_name']) }}"
                            data-type="{{ $res['facility_type'] ?? 'kelas' }}"
                            data-status="{{ $statusVal }}">
                            
                            <td class="py-3.5 px-5 font-mono text-white font-medium">
                                {{ $res['reservation_number'] }}
                            </td>

                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $res['applicant_name'] }}</span>
                                <span class="text-[#888888] font-mono text-[11px]">{{ $res['applicant_category'] ?? 'Mahasiswa' }}</span>
                            </td>

                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $res['facility_name'] }}</span>
                                <span class="text-[#888888] text-[11px] font-mono">{{ $res['facility_location'] }}</span>
                            </td>

                            <td class="py-3.5 px-5 font-mono">
                                <span class="font-medium text-white block">{{ $resDate }}</span>
                                <span class="text-[#888888] text-[11px]">{{ $res['start_time'] }} – {{ $res['end_time'] }} WIB</span>
                            </td>

                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                @if($statusVal === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#a1a1a1] bg-[#161616] border border-[#333333]">
                                        Selesai
                                    </span>
                                @elseif($statusVal === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                        Ditolak
                                    </span>
                                @elseif($statusVal === 'dibatalkan_oleh_pengguna')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#888888] bg-[#161616] border border-[#333333]">
                                        Batal (Pengguna)
                                    </span>
                                @elseif($statusVal === 'dibatalkan_oleh_petugas')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                        Batal (Petugas)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#888888] bg-[#161616] border border-[#333333]">
                                        Kedaluwarsa
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 font-mono text-[11px] text-[#888888]">
                                {{ $res['created_at'] }}
                            </td>

                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                <a href="{{ route('staff.reservations.show', ['reservation' => $res['id']]) }}" 
                                   class="text-[#a1a1a1] hover:text-white font-medium transition inline-flex items-center gap-1 text-xs">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#888888]">
                                Tidak ada data riwayat transaksi reservasi.
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
    const searchHistory = document.getElementById('searchHistory');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    const rows = document.querySelectorAll('#historyTable tbody tr[data-search]');

    function filterTable() {
        const query = searchHistory.value.toLowerCase().trim();
        const type = filterType.value.toLowerCase();
        const status = filterStatus.value.toLowerCase();

        rows.forEach(row => {
            const matchesSearch = !query || row.dataset.search.includes(query);
            const matchesType = !type || row.dataset.type === type;
            const matchesStatus = !status || row.dataset.status === status;

            row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
        });
    }

    searchHistory.addEventListener('input', filterTable);

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

        filterTable();
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        }
    });
</script>
@endpush
