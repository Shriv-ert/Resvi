@extends('layouts.staff')

@section('title', 'Antrian Reservasi Aktif')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Antrean kerja reservasi aktif.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Prioritas verifikasi permohonan reservasi baru sebelum waktu mulai kegiatan tercapai.
            </p>
        </div>

        <div>
            <a href="{{ route('staff.reservations.history') }}" 
               class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-4 py-2 rounded-full transition flex items-center gap-1.5 shadow-none">
                <svg class="w-3.5 h-3.5 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Buka Riwayat Reservasi Final →
            </a>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-4 mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Left: Search by applicant OR facility name -->
        <div class="w-full md:w-80 relative">
            <input 
                type="text" 
                id="searchQueue" 
                placeholder="Cari pemohon atau fasilitas..." 
                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-8 pr-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
            />
            <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <!-- Right: Filter Type & Status -->
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
                    <span id="filterStatusLabel">Semua Status Aktif</span>
                    <svg id="filterStatusChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="filterStatusMenu" class="custom-dropdown-menu hidden absolute top-full right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 min-w-[180px]">
                    <div onclick="selectCustomDropdown('filterStatus', '', 'Semua Status Aktif')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white bg-[#1f1f1f] hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between">
                        <span>Semua Status Aktif</span>
                        <span class="check-icon text-[#50e3c2] text-xs">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatus', 'menunggu', 'Menunggu Persetujuan')" data-value="menunggu" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Menunggu Persetujuan</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatus', 'disetujui', 'Disetujui')" data-value="disetujui" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Disetujui</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterStatus', 'berlangsung', 'Sedang Berlangsung')" data-value="berlangsung" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Sedang Berlangsung</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Reservations Table -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]" id="queueTable">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-medium">No. Reservasi</th>
                        <th class="py-3 px-5 font-medium">Nama Pemohon</th>
                        <th class="py-3 px-5 font-medium">Fasilitas</th>
                        <th class="py-3 px-5 font-medium">Jadwal Pinjam</th>
                        <th class="py-3 px-5 font-medium">Status</th>
                        <th class="py-3 px-5 font-medium">Diajukan</th>
                        <th class="py-3 px-5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($reservations as $res)
                        @php
                            $statusVal = $res['status'] instanceof \BackedEnum ? $res['status']->value : (string) $res['status'];
                        @endphp
                        <tr class="hover:bg-[#161616] transition-colors {{ $statusVal === 'menunggu' ? 'bg-[#1a1400]/30' : '' }}"
                            data-search="{{ strtolower($res['applicant_name'] . ' ' . $res['facility_name']) }}"
                            data-type="{{ $res['facility_type'] ?? 'kelas' }}"
                            data-status="{{ $statusVal }}">
                            
                            <!-- Reservation Number -->
                            <td class="py-3.5 px-5 font-mono text-white font-medium">
                                {{ $res['reservation_number'] }}
                            </td>

                            <!-- Applicant -->
                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $res['applicant_name'] }}</span>
                                <span class="text-[#888888] font-mono text-[11px]">{{ $res['applicant_category'] ?? 'Mahasiswa' }}</span>
                            </td>

                            <!-- Facility -->
                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $res['facility_name'] }}</span>
                                <span class="text-[#888888] text-[11px] font-mono">{{ $res['facility_location'] }}</span>
                            </td>

                            <!-- Schedule Time -->
                            <td class="py-3.5 px-5 font-mono">
                                <span class="font-medium text-white block">{{ $res['date'] instanceof \DateTimeInterface ? $res['date']->format('Y-m-d') : $res['date'] }}</span>
                                <span class="text-[#0070f3] text-[11px]">{{ $res['start_time'] }} – {{ $res['end_time'] }} WIB</span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-5">
                                @if($statusVal === 'menunggu')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#f5a623] bg-[#2b1e00] border border-[#553c00]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#f5a623] animate-pulse"></span>
                                        Menunggu
                                    </span>
                                @elseif($statusVal === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#c084fc] bg-[#270038] border border-[#581c87]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#c084fc] animate-pulse"></span>
                                        Berlangsung
                                    </span>
                                @endif
                            </td>

                            <!-- Created Timestamp -->
                            <td class="py-3.5 px-5 font-mono text-[11px] text-[#888888]">
                                {{ $res['created_at'] }}
                            </td>

                            <!-- Action Button -->
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('staff.reservations.show', ['reservation' => $res['id']]) }}" 
                                   class="text-[#a1a1a1] hover:text-white font-medium transition inline-flex items-center gap-1 text-xs">
                                    Verifikasi →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#888888]">
                                Tidak ada antrian permohonan reservasi aktif.
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
    const searchQueue = document.getElementById('searchQueue');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    const rows = document.querySelectorAll('#queueTable tbody tr[data-search]');

    function filterTable() {
        const query = searchQueue.value.toLowerCase().trim();
        const type = filterType.value.toLowerCase();
        const status = filterStatus.value.toLowerCase();

        rows.forEach(row => {
            const matchesSearch = !query || row.dataset.search.includes(query);
            const matchesType = !type || row.dataset.type === type;
            const matchesStatus = !status || row.dataset.status === status;

            row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
        });
    }

    searchQueue.addEventListener('input', filterTable);

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
