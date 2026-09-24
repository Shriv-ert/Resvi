@extends('layouts.staff')

@section('title', 'Daftar Seluruh Fasilitas Kampus · Petugas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Daftar fasilitas kerja operasional.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Tinjau status operasional dan status master seluruh fasilitas sarpras kampus.
            </p>
        </div>

        <div>
            <a href="{{ route('staff.facilities.maintenance') }}" 
               class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-4 py-2 rounded-full transition flex items-center gap-1.5 shadow-none">
                <svg class="w-3.5 h-3.5 text-[#ff453a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Lihat Khusus Dalam Perbaikan →
            </a>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-4 mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <div class="w-full md:w-80 relative">
            <input 
                type="text" 
                id="searchFacility" 
                placeholder="Cari nama fasilitas..." 
                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-8 pr-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
            />
            <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative custom-dropdown min-w-[170px]" id="dropdown-filterTypeStaff">
                <input type="hidden" id="filterTypeStaff" value="">
                <button type="button" onclick="toggleCustomDropdown('filterTypeStaff')" id="filterTypeStaffTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="filterTypeStaffLabel">Semua Tipe Fasilitas</span>
                    <svg id="filterTypeStaffChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="filterTypeStaffMenu" class="custom-dropdown-menu hidden absolute top-full right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 min-w-[180px]">
                    <div onclick="selectCustomDropdown('filterTypeStaff', '', 'Semua Tipe Fasilitas')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white bg-[#1f1f1f] hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between">
                        <span>Semua Tipe Fasilitas</span>
                        <span class="check-icon text-[#50e3c2] text-xs">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeStaff', 'kelas', 'Ruang Kelas')" data-value="kelas" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Ruang Kelas</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeStaff', 'laboratorium', 'Laboratorium')" data-value="laboratorium" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Laboratorium</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeStaff', 'aula', 'Aula')" data-value="aula" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Aula</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeStaff', 'alat', 'Alat Khusus')" data-value="alat" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Alat Khusus</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeStaff', 'lapangan', 'Lapangan')" data-value="lapangan" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Lapangan</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Facilities Table -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]" id="staffFacilitiesTable">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-medium">Nama Fasilitas</th>
                        <th class="py-3 px-5 font-medium">Tipe</th>
                        <th class="py-3 px-5 font-medium">Lokasi</th>
                        <th class="py-3 px-5 font-medium">Status Operasional</th>
                        <th class="py-3 px-5 font-medium">Status Data Master</th>
                        <th class="py-3 px-5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($facilities as $f)
                        <tr class="hover:bg-[#161616] transition-colors"
                            data-search="{{ strtolower($f['name']) }}"
                            data-type="{{ $f['type'] }}">
                            
                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $f['name'] }}</span>
                                <span class="text-[#888888] font-mono text-[11px]">Kapasitas: {{ $f['capacity'] ? $f['capacity'] . ' orang' : 'Unit alat' }}</span>
                            </td>

                            <td class="py-3.5 px-5 capitalize text-[#888888] font-mono text-[11px]">
                                {{ $f['type'] }}
                            </td>

                            <td class="py-3.5 px-5 text-[#888888]">
                                {{ $f['location'] }}
                            </td>

                            <td class="py-3.5 px-5">
                                @if($f['operational_status'] === 'dapat_digunakan')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                                        Dapat Digunakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span>
                                        Dalam Perbaikan
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5">
                                @if($f['is_active'] ?? true)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-mono text-[#a1a1a1]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span> Master Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-mono text-[#888888]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#888888]"></span> Master Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('staff.facilities.show', ['facility' => $f['id']]) }}" 
                                   class="text-[#a1a1a1] hover:text-white font-medium transition inline-flex items-center gap-1 text-xs">
                                    Kelola Status →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#888888]">
                                Belum ada data fasilitas sarpras.
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
    const searchFacility = document.getElementById('searchFacility');
    const filterTypeStaff = document.getElementById('filterTypeStaff');
    const rowsStaffFacilities = document.querySelectorAll('#staffFacilitiesTable tbody tr[data-search]');

    function filterStaffFacilitiesTable() {
        const query = searchFacility.value.toLowerCase().trim();
        const type = filterTypeStaff.value.toLowerCase();

        rowsStaffFacilities.forEach(row => {
            const matchesSearch = !query || row.dataset.search.includes(query);
            const matchesType = !type || row.dataset.type === type;

            row.style.display = (matchesSearch && matchesType) ? '' : 'none';
        });
    }

    searchFacility.addEventListener('input', filterStaffFacilitiesTable);

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

        filterStaffFacilitiesTable();
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        }
    });
</script>
@endpush
