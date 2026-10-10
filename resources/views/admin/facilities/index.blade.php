@extends('layouts.admin')

@section('title', 'Kelola Fasilitas Master · Admin')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Master Fasilitas & Ruangan.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1] mt-1">
                Kelola data sarana prasarana kampus, status master aktif/nonaktif, dan pantau status operasional.
            </p>
        </div>

        <a href="{{ route('admin.facilities.create') }}" 
           class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-2 rounded-full transition inline-flex items-center gap-2 self-start sm:self-auto active:scale-95 shadow-none">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Fasilitas
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-5 mb-8">
        <form method="GET" action="{{ route('admin.facilities.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Search -->
            <div class="space-y-1.5">
                <label for="search" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Cari Fasilitas / Lokasi</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="search"
                        name="search"
                        value="{{ $search ?? request('search', '') }}"
                        placeholder="Nama atau lokasi fasilitas..."
                        class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-8 pr-3 py-2 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]"
                    >
                    <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Tipe Fasilitas -->
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-type">
                <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Tipe Fasilitas</label>
                <input type="hidden" id="type" name="type" value="{{ $selectedType ?? request('type', '') }}">
                @php
                    $curTypeVal = $selectedType ?? request('type', '');
                    $curTypeLabel = 'Semua Tipe';
                    foreach(\App\Enums\FacilityType::cases() as $t) {
                        if ($t->value === $curTypeVal) $curTypeLabel = $t->label();
                    }
                @endphp
                <button type="button" onclick="toggleCustomDropdown('type')" id="typeTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="typeLabel">{{ $curTypeLabel }}</span>
                    <svg id="typeChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="typeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-72 overflow-y-auto">
                    <div class="p-2 border-b border-[#222222] sticky top-0 bg-[#111111] z-10">
                        <div class="relative">
                            <input type="text" placeholder="Cari tipe fasilitas..." 
                                   id="search-type"
                                   oninput="filterDropdownOptions(this, 'typeMenu')"
                                   onclick="event.stopPropagation()"
                                   class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-[11px] rounded-[4px] pl-7 pr-2 py-1.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]">
                            <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div onclick="selectCustomDropdown('type', '', 'Semua Tipe')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between {{ empty($curTypeVal) ? 'bg-[#1f1f1f]' : '' }}">
                        <span>Semua Tipe</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ empty($curTypeVal) ? '' : 'hidden' }}">✓</span>
                    </div>
                    @foreach(\App\Enums\FacilityType::cases() as $typeEnum)
                        <div onclick="selectCustomDropdown('type', '{{ $typeEnum->value }}', '{{ $typeEnum->label() }}')" data-value="{{ $typeEnum->value }}" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $curTypeVal === $typeEnum->value ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>{{ $typeEnum->label() }}</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ $curTypeVal === $typeEnum->value ? '' : 'hidden' }}">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Status Master -->
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-master_status">
                <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Status Data Master</label>
                <input type="hidden" id="master_status" name="master_status" value="{{ $selectedMasterStatus ?? request('master_status', '') }}">
                @php
                    $curMasterVal = $selectedMasterStatus ?? request('master_status', '');
                    $curMasterLabel = $curMasterVal === 'active' ? 'Aktif' : ($curMasterVal === 'inactive' ? 'Nonaktif' : 'Semua Status Master');
                @endphp
                <button type="button" onclick="toggleCustomDropdown('master_status')" id="master_statusTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="master_statusLabel">{{ $curMasterLabel }}</span>
                    <svg id="master_statusChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="master_statusMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                    <div class="p-2 border-b border-[#222222] sticky top-0 bg-[#111111] z-10">
                        <div class="relative">
                            <input type="text" placeholder="Cari status master..." 
                                   id="search-master_status"
                                   oninput="filterDropdownOptions(this, 'master_statusMenu')"
                                   onclick="event.stopPropagation()"
                                   class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-[11px] rounded-[4px] pl-7 pr-2 py-1.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]">
                            <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div onclick="selectCustomDropdown('master_status', '', 'Semua Status Master')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between {{ empty($curMasterVal) ? 'bg-[#1f1f1f]' : '' }}">
                        <span>Semua Status Master</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ empty($curMasterVal) ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('master_status', 'active', 'Aktif')" data-value="active" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $curMasterVal === 'active' ? 'bg-[#1f1f1f] text-white' : '' }}">
                        <span>Aktif</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ $curMasterVal === 'active' ? '' : 'hidden' }}">✓</span>
                    </div>
                    <div onclick="selectCustomDropdown('master_status', 'inactive', 'Nonaktif')" data-value="inactive" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $curMasterVal === 'inactive' ? 'bg-[#1f1f1f] text-white' : '' }}">
                        <span>Nonaktif</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ $curMasterVal === 'inactive' ? '' : 'hidden' }}">✓</span>
                    </div>
                </div>
            </div>

            <!-- Status Operasional -->
            <div class="space-y-1.5 relative custom-dropdown" id="dropdown-operational_status">
                <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Status Operasional</label>
                <input type="hidden" id="operational_status" name="operational_status" value="{{ $selectedOperationalStatus ?? request('operational_status', '') }}">
                @php
                    $curOpVal = $selectedOperationalStatus ?? request('operational_status', '');
                    $curOpLabel = 'Semua Status Operasional';
                    foreach(\App\Enums\OperationalStatus::cases() as $op) {
                        if ($op->value === $curOpVal) $curOpLabel = $op->label();
                    }
                @endphp
                <button type="button" onclick="toggleCustomDropdown('operational_status')" id="operational_statusTrigger"
                        class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                    <span id="operational_statusLabel">{{ $curOpLabel }}</span>
                    <svg id="operational_statusChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="operational_statusMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                    <div class="p-2 border-b border-[#222222] sticky top-0 bg-[#111111] z-10">
                        <div class="relative">
                            <input type="text" placeholder="Cari status operasional..." 
                                   id="search-operational_status"
                                   oninput="filterDropdownOptions(this, 'operational_statusMenu')"
                                   onclick="event.stopPropagation()"
                                   class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-[11px] rounded-[4px] pl-7 pr-2 py-1.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]">
                            <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div onclick="selectCustomDropdown('operational_status', '', 'Semua Status Operasional')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between {{ empty($curOpVal) ? 'bg-[#1f1f1f]' : '' }}">
                        <span>Semua Status Operasional</span>
                        <span class="check-icon text-[#50e3c2] text-xs {{ empty($curOpVal) ? '' : 'hidden' }}">✓</span>
                    </div>
                    @foreach(\App\Enums\OperationalStatus::cases() as $opEnum)
                        <div onclick="selectCustomDropdown('operational_status', '{{ $opEnum->value }}', '{{ $opEnum->label() }}')" data-value="{{ $opEnum->value }}" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $curOpVal === $opEnum->value ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>{{ $opEnum->label() }}</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ $curOpVal === $opEnum->value ? '' : 'hidden' }}">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="sm:col-span-2 lg:col-span-4 flex items-center justify-between pt-2 border-t border-[#333333]">
                <div class="text-xs text-[#888888] font-mono">
                    Total: <strong class="text-white">{{ count($facilities) }}</strong> fasilitas
                </div>
                <div class="flex items-center gap-2">
                    @if(request()->anyFilled(['search', 'type', 'master_status', 'operational_status']))
                        <a href="{{ route('admin.facilities.index') }}" class="text-xs text-[#888888] hover:text-white underline font-mono mr-2">Reset</a>
                    @endif
                    <button type="submit" class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-1.5 rounded-[6px] transition">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Master Facilities Table -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-[#ededed]">
                <thead class="bg-[#161616] border-b border-[#333333] text-[10px] font-mono uppercase text-[#888888] tracking-wider text-center">
                    <tr>
                        <th class="py-3 px-5 text-center font-medium">Nama Fasilitas</th>
                        <th class="py-3 px-5 text-center font-medium">Tipe</th>
                        <th class="py-3 px-5 text-center font-medium">Lokasi Gedung</th>
                        <th class="py-3 px-5 text-center font-medium">Kapasitas</th>
                        <th class="py-3 px-5 text-center font-medium">Status Data Master</th>
                        <th class="py-3 px-5 text-center font-medium">Status Operasional</th>
                        <th class="py-3 px-5 text-center font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#333333] text-xs">
                    @forelse($facilities as $facility)
                        @php
                            $fId = is_array($facility) ? $facility['id'] : $facility->id;
                            $fName = is_array($facility) ? $facility['name'] : $facility->name;
                            $fType = is_array($facility) ? $facility['type'] : ($facility->type instanceof \BackedEnum ? $facility->type->value : $facility->type);
                            $fTypeLabel = is_array($facility) ? ucfirst($facility['type']) : ($facility->type instanceof \App\Enums\FacilityType ? $facility->type->label() : ucfirst($fType));
                            $fLocation = is_array($facility) ? $facility['location'] : $facility->location;
                            $fCapacity = is_array($facility) ? ($facility['capacity'] ?? null) : $facility->capacity;
                            $fIsActive = is_array($facility) ? ($facility['is_active'] ?? true) : $facility->is_active;
                            $fOperational = is_array($facility) ? $facility['operational_status'] : ($facility->operational_status instanceof \BackedEnum ? $facility->operational_status->value : $facility->operational_status);
                        @endphp

                        <tr class="hover:bg-[#161616] transition-colors {{ !$fIsActive ? 'opacity-70 bg-[#161616]/40' : '' }}">
                            <td class="py-3.5 px-5">
                                <span class="font-medium text-white block">{{ $fName }}</span>
                            </td>

                            <td class="py-3.5 px-5 capitalize text-[#888888] font-mono text-[11px]">
                                {{ $fTypeLabel }}
                            </td>

                            <td class="py-3.5 px-5 text-[#888888]">
                                {{ $fLocation }}
                            </td>

                            <td class="py-3.5 px-5 font-mono text-[11px] text-[#888888]">
                                {{ $fCapacity ? $fCapacity . ' Orang' : '—' }}
                            </td>

                            <!-- Master Status -->
                            <td class="py-3.5 px-5 text-center">
                                @if($fIsActive)
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
                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                @if($fOperational === 'dapat_digunakan')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#50e3c2] bg-[#002b11] border border-[#005522] whitespace-nowrap">
                                        Dapat digunakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[10px] font-mono text-[#ff453a] bg-[#2b0000] border border-[#550000] whitespace-nowrap">
                                        Dalam perbaikan
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 text-center space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.facilities.show', $facility) }}" 
                                   class="text-[#a1a1a1] hover:text-white font-medium transition inline-flex items-center gap-1 text-xs font-mono">
                                    Detail & Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-[#888888] font-mono text-xs">
                                Tidak ada data fasilitas yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($facilities, 'hasPages') && $facilities->hasPages())
            <div class="p-4 border-t border-[#333333]">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
