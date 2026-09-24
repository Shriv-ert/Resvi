@extends('layouts.admin')

@section('title', 'Kelola Master Fasilitas · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Kelola data master fasilitas.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Pengelolaan katalog seluruh fasilitas kampus, status master aktif/nonaktif, dan kapasitas objek.
            </p>
        </div>

        <div>
            <!-- Tambah Fasilitas Button -->
            <a href="{{ route('admin.facilities.create') }}" 
               class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-full transition flex items-center gap-2 active:scale-95 shadow-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Fasilitas Baru
            </a>
        </div>
    </div>

    <!-- Search Controls -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-4 mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-80 relative">
            <input 
                type="text" 
                id="searchAdminFacility" 
                placeholder="Cari fasilitas..." 
                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-8 pr-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
            />
            <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative custom-dropdown min-w-[210px]" id="dropdown-filterMasterActive">
                <input type="hidden" id="filterMasterActive" value="">
                <button type="button" onclick="toggleCustomDropdown('filterMasterActive')" id="filterMasterActiveTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="filterMasterActiveLabel">Semua Status Master</span>
                    <svg id="filterMasterActiveChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="filterMasterActiveMenu" class="custom-dropdown-menu hidden absolute top-full right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 min-w-[210px]">
                    <div onclick="selectCustomDropdown('filterMasterActive', '', 'Semua Status Master')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between bg-[#1f1f1f]">
                        <span>Semua Status Master</span>
                        <span class="check-icon text-[#50e3c2] text-xs">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterMasterActive', 'aktif', 'Aktif')" data-value="aktif" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Aktif</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterMasterActive', 'nonaktif', 'Nonaktif')" data-value="nonaktif" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Nonaktif</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Facilities Table -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]" id="adminFacilitiesTable">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-medium">Nama Fasilitas</th>
                        <th class="py-3 px-5 font-medium">Tipe</th>
                        <th class="py-3 px-5 font-medium">Lokasi Gedung</th>
                        <th class="py-3 px-5 font-medium">Kapasitas</th>
                        <th class="py-3 px-5 font-medium">Status Data Master</th>
                        <th class="py-3 px-5 font-medium">Status Operasional</th>
                        <th class="py-3 px-5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($facilities as $f)
                        <tr class="hover:bg-[#161616] transition-colors {{ !($f['is_active'] ?? true) ? 'opacity-60 bg-[#161616]' : '' }}"
                            data-search="{{ strtolower($f['name'] . ' ' . $f['location']) }}"
                            data-active="{{ ($f['is_active'] ?? true) ? 'aktif' : 'nonaktif' }}">
                            
                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $f['name'] }}</span>
                            </td>

                            <td class="py-3.5 px-5 capitalize text-[#888888] font-mono text-[11px]">
                                {{ $f['type'] }}
                            </td>

                            <td class="py-3.5 px-5 text-[#888888]">
                                {{ $f['location'] }}
                            </td>

                            <td class="py-3.5 px-5 font-mono text-[11px] text-[#888888]">
                                {{ $f['capacity'] ? $f['capacity'] . ' Orang' : '— (Unit Alat)' }}
                            </td>

                            <!-- Master Status -->
                            <td class="py-3.5 px-5">
                                @if($f['is_active'] ?? true)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#888888] bg-[#161616] border border-[#333333]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#888888]"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Operational Status -->
                            <td class="py-3.5 px-5">
                                @if($f['operational_status'] === 'dapat_digunakan')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522]">
                                        Dapat Digunakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                        Dalam Perbaikan
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 text-right space-x-2">
                                <a href="{{ route('admin.facilities.show', ['facility' => $f['id']]) }}" 
                                   class="text-[#a1a1a1] hover:text-white font-medium transition inline-flex items-center gap-1 text-xs">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#888888]">
                                Tidak ada data fasilitas yang sesuai dengan filter.
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
    const searchAdminFacility = document.getElementById('searchAdminFacility');
    const filterMasterActive = document.getElementById('filterMasterActive');
    const rowsFacilities = document.querySelectorAll('#adminFacilitiesTable tbody tr[data-search]');

    function filterAdminFacilities() {
        const query = searchAdminFacility.value.toLowerCase().trim();
        const activeFilter = filterMasterActive.value;

        rowsFacilities.forEach(row => {
            const matchesSearch = !query || row.dataset.search.includes(query);
            const matchesActive = !activeFilter || row.dataset.active === activeFilter;

            row.style.display = (matchesSearch && matchesActive) ? '' : 'none';
        });
    }

    searchAdminFacility.addEventListener('input', filterAdminFacilities);

    function toggleCustomDropdown(id) {
        const menu = document.getElementById(id + 'Menu');
        const chevron = document.getElementById(id + 'Chevron');
        const isOpen = menu && !menu.classList.contains('hidden');

        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));

        if (!isOpen && menu) {
            menu.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        }
    }

    function selectCustomDropdown(id, val, label) {
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

        filterAdminFacilities();
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        }
    });
</script>
@endpush
