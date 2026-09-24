@extends('layouts.staff')

@section('title', 'Fasilitas Dalam Perbaikan · Petugas')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-[6px] text-xs font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000] mb-3 shadow-none">
                <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a] animate-pulse"></span>
                <span>Pemeliharaan Teknis Aktif</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Fasilitas dalam perbaikan.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Seluruh fasilitas yang reservasi barunya dinonaktifkan karena sedang dalam pemeliharaan atau perbaikan aktif.
            </p>
        </div>

        <div>
            <a href="{{ route('staff.facilities.index') }}" 
               class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-4 py-2 rounded-full transition flex items-center gap-1.5 shadow-none">
                ← Kembali ke Semua Fasilitas
            </a>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-4 mb-6 shadow-none flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-80 relative">
            <input 
                type="text" 
                id="searchMaintenance" 
                placeholder="Cari fasilitas dalam perbaikan..." 
                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-9 pr-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
            />
            <svg class="w-4 h-4 text-[#888888] absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <div>
            <div class="relative custom-dropdown min-w-[170px]" id="dropdown-filterTypeMaintenance">
                <input type="hidden" id="filterTypeMaintenance" value="">
                <button type="button" onclick="toggleCustomDropdown('filterTypeMaintenance')" id="filterTypeMaintenanceTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="filterTypeMaintenanceLabel">Semua Tipe Fasilitas</span>
                    <svg id="filterTypeMaintenanceChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="filterTypeMaintenanceMenu" class="custom-dropdown-menu hidden absolute top-full right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 min-w-[180px]">
                    <div onclick="selectCustomDropdown('filterTypeMaintenance', '', 'Semua Tipe Fasilitas')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white bg-[#1f1f1f] hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between">
                        <span>Semua Tipe Fasilitas</span>
                        <span class="check-icon text-[#50e3c2] text-xs">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeMaintenance', 'kelas', 'Ruang Kelas')" data-value="kelas" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Ruang Kelas</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeMaintenance', 'laboratorium', 'Laboratorium')" data-value="laboratorium" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Laboratorium</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeMaintenance', 'aula', 'Aula')" data-value="aula" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Aula</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeMaintenance', 'alat', 'Alat Khusus')" data-value="alat" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Alat Khusus</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('filterTypeMaintenance', 'lapangan', 'Lapangan')" data-value="lapangan" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                        <span>Lapangan</span>
                        <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Table -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden shadow-none">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]" id="maintenanceTable">
                <thead class="bg-[#161616] border-b border-[#333333] text-[11px] font-mono uppercase text-[#888888] tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-medium">Nama Fasilitas</th>
                        <th class="py-3 px-5 font-medium">Tipe</th>
                        <th class="py-3 px-5 font-medium">Lokasi</th>
                        <th class="py-3 px-5 font-medium">Status Operasional</th>
                        <th class="py-3 px-5 font-medium">Catatan Pemeliharaan</th>
                        <th class="py-3 px-5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($facilities as $f)
                        <tr class="hover:bg-[#161616] transition-colors"
                            data-search="{{ strtolower($f['name']) }}"
                            data-type="{{ $f['type'] }}">
                            
                            <td class="py-3.5 px-5 font-medium text-white">
                                {{ $f['name'] }}
                            </td>

                            <td class="py-3.5 px-5 capitalize text-[#888888]">
                                {{ $f['type'] }}
                            </td>

                            <td class="py-3.5 px-5 text-[#888888]">
                                {{ $f['location'] }}
                            </td>

                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[6px] text-[11px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a] animate-pulse"></span>
                                    Dalam Perbaikan
                                </span>
                            </td>

                            <td class="py-3.5 px-5 text-[#888888] max-w-xs truncate">
                                {{ $f['maintenance_notes'] ?? 'Sedang dalam perbaikan teknis oleh petugas' }}
                            </td>

                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('staff.facilities.show', ['facility' => $f['id']]) }}" 
                                   class="inline-flex items-center gap-1 bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] text-xs font-medium px-3 py-1 rounded-[6px] transition shadow-none">
                                    Kelola
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#888888]">
                                Tidak ada fasilitas yang sedang dalam perbaikan.
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
    const searchMaintenance = document.getElementById('searchMaintenance');
    const filterTypeMaintenance = document.getElementById('filterTypeMaintenance');
    const rowsMaintenance = document.querySelectorAll('#maintenanceTable tbody tr[data-search]');

    function filterMaintenance() {
        const query = searchMaintenance.value.toLowerCase().trim();
        const type = filterTypeMaintenance.value;

        rowsMaintenance.forEach(row => {
            const matchesSearch = !query || row.dataset.search.includes(query);
            const matchesType = !type || row.dataset.type === type;

            row.style.display = (matchesSearch && matchesType) ? '' : 'none';
        });
    }

    searchMaintenance.addEventListener('input', filterMaintenance);

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

        filterMaintenance();
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        }
    });
</script>
@endpush
