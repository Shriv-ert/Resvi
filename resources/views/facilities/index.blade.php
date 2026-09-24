@extends('layouts.app')

@section('title', 'Katalog Fasilitas & Ruangan')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Hero / Header Section: Vercel Marketing Title Pattern -->
    <div class="space-y-2 mb-8">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-[4px] text-[11px] font-mono font-medium bg-[#111111] text-[#a1a1a1] border border-[#333333]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                Transparansi Sarpras Kampus
            </span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-semibold text-white tracking-[-0.03em]">
            Katalog Fasilitas Kampus.
        </h1>
        <p class="text-xs sm:text-sm text-[#a1a1a1] max-w-2xl leading-relaxed">
            Eksplorasi ruang kuliah teori, laboratorium komputasi, aula pertemuan, alat audiovisual, dan fasilitas olahraga dengan visibilitas jadwal slot 30 menit.
        </p>
    </div>

    <!-- Filter Bar: Flush Bordered Container -->
    <section class="border border-[#333333] rounded-[6px] bg-[#111111] p-4 sm:p-5 mb-8 shadow-none" aria-label="Filter Fasilitas">
        <form method="GET" action="{{ route('home') }}" id="filterForm">
            @if(request()->has('as_user'))
                <input type="hidden" name="as_user" value="1">
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search by Name -->
                <div class="space-y-1.5">
                    <label for="search" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Cari Fasilitas</label>
                    <div class="relative">
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search', request('q', '')) }}"
                            placeholder="Ketik nama ruang atau fasilitas..."
                            class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-8 pr-3 py-2 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]"
                        >
                        <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Filter Type -->
                <div class="space-y-1.5 relative custom-dropdown" id="dropdown-type">
                    <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Tipe Fasilitas</label>
                    <input type="hidden" id="type" name="type" value="{{ request('type', '') }}">
                    <button type="button" onclick="toggleCustomDropdown('type')" id="typeTrigger"
                            class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                        <span id="typeLabel">
                            @if(request('type') == 'kelas') Ruang Kelas
                            @elseif(request('type') == 'laboratorium') Laboratorium
                            @elseif(request('type') == 'aula') Aula
                            @elseif(request('type') == 'alat') Alat Khusus
                            @elseif(request('type') == 'lapangan') Lapangan
                            @else Semua Tipe
                            @endif
                        </span>
                        <svg id="typeChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div id="typeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-80 overflow-y-auto">
                        <!-- Dropdown Search Box -->
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
                        <div onclick="selectCustomDropdown('type', '', 'Semua Tipe')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between {{ empty(request('type')) ? 'bg-[#1f1f1f]' : '' }}">
                            <span>Semua Tipe</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ empty(request('type')) ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('type', 'kelas', 'Ruang Kelas')" data-value="kelas" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('type') == 'kelas' ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>Ruang Kelas</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ request('type') == 'kelas' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('type', 'laboratorium', 'Laboratorium')" data-value="laboratorium" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('type') == 'laboratorium' ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>Laboratorium</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ request('type') == 'laboratorium' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('type', 'aula', 'Aula')" data-value="aula" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('type') == 'aula' ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>Aula</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ request('type') == 'aula' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('type', 'alat', 'Alat Khusus')" data-value="alat" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('type') == 'alat' ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>Alat Khusus</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ request('type') == 'alat' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('type', 'lapangan', 'Lapangan')" data-value="lapangan" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('type') == 'lapangan' ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>Lapangan</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ request('type') == 'lapangan' ? '' : 'hidden' }}">✓</span>
                        </div>
                    </div>
                </div>

                <!-- Filter Location -->
                <div class="space-y-1.5 relative custom-dropdown" id="dropdown-location">
                    <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Lokasi Gedung</label>
                    <input type="hidden" id="location" name="location" value="{{ request('location', '') }}">
                    <button type="button" onclick="toggleCustomDropdown('location')" id="locationTrigger"
                            class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                        <span id="locationLabel">
                            @if(request('location')) {{ request('location') }}
                            @else Semua Lokasi
                            @endif
                        </span>
                        <svg id="locationChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div id="locationMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                        <!-- Dropdown Search Box -->
                        <div class="p-2 border-b border-[#222222] sticky top-0 bg-[#111111] z-10">
                            <div class="relative">
                                <input type="text" placeholder="Cari lokasi gedung..." 
                                       id="search-location"
                                       oninput="filterDropdownOptions(this, 'locationMenu')"
                                       onclick="event.stopPropagation()"
                                       class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-[11px] rounded-[4px] pl-7 pr-2 py-1.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]">
                                <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div onclick="selectCustomDropdown('location', '', 'Semua Lokasi')" data-value="" class="dropdown-item px-3 py-2 text-xs font-mono text-white hover:bg-[#1f1f1f] cursor-pointer flex items-center justify-between {{ empty(request('location')) ? 'bg-[#1f1f1f]' : '' }}">
                            <span>Semua Lokasi</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ empty(request('location')) ? '' : 'hidden' }}">✓</span>
                        </div>
                        @if(isset($locations))
                            @foreach($locations as $loc)
                                <div onclick="selectCustomDropdown('location', '{{ $loc }}', '{{ $loc }}')" data-value="{{ $loc }}" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('location') == $loc ? 'bg-[#1f1f1f] text-white' : '' }}">
                                    <span>{{ $loc }}</span>
                                    <span class="check-icon text-[#50e3c2] text-xs {{ request('location') == $loc ? '' : 'hidden' }}">✓</span>
                                </div>
                            @endforeach
                        @else
                            <div onclick="selectCustomDropdown('location', 'Gedung A (Utama)', 'Gedung A (Utama)')" data-value="Gedung A (Utama)" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                                <span>Gedung A (Utama)</span>
                                <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                            </div>
                            <div onclick="selectCustomDropdown('location', 'Gedung B (Laboratorium)', 'Gedung B (Laboratorium)')" data-value="Gedung B (Laboratorium)" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between">
                                <span>Gedung B (Laboratorium)</span>
                                <span class="check-icon text-[#50e3c2] text-xs hidden">✓</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Custom Capacity Input + Range Slider Popover -->
                <div class="space-y-1.5 relative" id="capacityFilterContainer">
                    <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Kapasitas</label>
                    <div class="relative flex items-center">
                        <input type="number" id="capacityInput" min="0" max="250" 
                               value="{{ request('capacity') }}"
                               placeholder="Semua (0)" 
                               oninput="onManualCapacityInput(this.value)"
                               onfocus="openCapacityPopup()"
                               class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] focus:border-white text-white text-xs rounded-[6px] pl-3 pr-9 py-2 focus:outline-none transition font-mono">
                        <button type="button" onclick="toggleCapacityPopup()" title="Buka slider range kapasitas" class="absolute right-1 px-1.5 py-1 text-[#888888] hover:text-white transition rounded-[4px] hover:bg-[#1f1f1f]">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="21" x2="4" y2="14"></line>
                                <line x1="4" y1="10" x2="4" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12" y2="3"></line>
                                <line x1="20" y1="21" x2="20" y2="16"></line>
                                <line x1="20" y1="12" x2="20" y2="3"></line>
                                <line x1="1" y1="14" x2="7" y2="14"></line>
                                <line x1="9" y1="8" x2="15" y2="8"></line>
                                <line x1="17" y1="16" x2="23" y2="16"></line>
                            </svg>
                        </button>
                    </div>

                    <input type="hidden" id="capacity" name="capacity" value="{{ request('capacity', 0) }}">

                    <!-- Popup Range Kapasitas Slider -->
                    <div id="capacityPopup" class="hidden absolute top-full left-0 right-0 sm:right-auto sm:w-80 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[8px] p-4 shadow-2xl backdrop-blur-md space-y-4">
                        <div class="flex items-center justify-between border-b border-[#222222] pb-2.5">
                            <span class="text-xs font-semibold text-white">Range Kapasitas</span>
                            <span class="font-mono text-xs px-2 py-0.5 rounded-[4px] bg-[#1a1a1a] text-[#50e3c2] border border-[#333333]" id="sliderCapDisplay">
                                @if(request('capacity') > 0)
                                    ≥ {{ request('capacity') }} Orang
                                @else
                                    Semua (0 Orang)
                                @endif
                            </span>
                        </div>

                        <!-- Slider Range -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-mono text-[#888888]">
                                <span>0</span>
                                <span>50</span>
                                <span>100</span>
                                <span>150+</span>
                            </div>
                            <input type="range" id="capacitySlider" min="0" max="150" step="5" value="{{ request('capacity', 0) }}"
                                   oninput="onSliderCapacityChange(this.value)"
                                   class="w-full accent-[#50e3c2]">
                        </div>

                        <!-- Preset Chips -->
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-mono uppercase text-[#888888] tracking-wider block">Pilihan Cepat</span>
                            <div class="grid grid-cols-4 gap-1.5 text-[11px] font-mono">
                                <button type="button" onclick="setCapacityPreset(0)" class="px-2 py-1 rounded-[4px] bg-[#1a1a1a] hover:bg-[#262626] border border-[#333333] text-[#ededed] text-center transition">Semua</button>
                                <button type="button" onclick="setCapacityPreset(20)" class="px-2 py-1 rounded-[4px] bg-[#1a1a1a] hover:bg-[#262626] border border-[#333333] text-[#ededed] text-center transition">≥ 20</button>
                                <button type="button" onclick="setCapacityPreset(40)" class="px-2 py-1 rounded-[4px] bg-[#1a1a1a] hover:bg-[#262626] border border-[#333333] text-[#ededed] text-center transition">≥ 40</button>
                                <button type="button" onclick="setCapacityPreset(80)" class="px-2 py-1 rounded-[4px] bg-[#1a1a1a] hover:bg-[#262626] border border-[#333333] text-[#ededed] text-center transition">≥ 80</button>
                            </div>
                        </div>

                        <div class="text-[10px] text-[#777777] leading-relaxed border-t border-[#222222] pt-2">
                            *Alat tanpa kapasitas (null) dikecualikan jika kapasitas > 0.
                        </div>

                        <div class="pt-1 flex items-center justify-between text-xs">
                            <button type="button" onclick="setCapacityPreset(0)" class="text-[#888888] hover:text-white underline text-[11px]">Reset</button>
                            <button type="button" onclick="closeCapacityPopup()" class="px-3.5 py-1 bg-white hover:bg-[#e6e6e6] text-black font-medium rounded-[4px] text-xs transition">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-[#333333] flex flex-wrap items-center justify-between gap-3 text-xs text-[#888888]">
                <div class="flex items-center gap-2">
                    <span>Menampilkan <strong class="text-white font-mono" id="countDisplay">{{ count($facilities) }}</strong> fasilitas</span>
                    @if(request()->anyFilled(['search', 'q', 'type', 'location', 'capacity']))
                        <a href="{{ route('facilities.index', request()->has('as_user') ? ['as_user' => 1] : []) }}" class="text-[#0070f3] hover:underline ml-2 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reset Filter
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <!-- Grid / List View Toggle Switch -->
                    <div class="flex items-center gap-1 rounded-[6px] bg-[#0a0a0a] p-1 border border-[#333333]" aria-label="Pilihan Tampilan">
                        <button type="button" id="btnViewGrid" onclick="setCatalogView('grid')" title="Tampilan Grid"
                                class="rounded-[4px] px-2.5 py-1 text-xs font-mono flex items-center gap-1.5 transition text-white bg-[#222222]">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span class="hidden sm:inline">Grid</span>
                        </button>
                        <button type="button" id="btnViewList" onclick="setCatalogView('list')" title="Tampilan List"
                                class="rounded-[4px] px-2.5 py-1 text-xs font-mono flex items-center gap-1.5 transition text-[#888888] hover:text-white">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            <span class="hidden sm:inline">List</span>
                        </button>
                    </div>

                    <button type="submit" class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-4 py-1.5 rounded-[6px] transition">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- Catalog Grid / List Container -->
    <section aria-label="Daftar Fasilitas">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 transition-all duration-300" id="facilityGrid" data-view="grid">
            @forelse($facilities as $facility)
                @php
                    $fId = is_array($facility) ? $facility['id'] : $facility->id;
                    $fName = is_array($facility) ? $facility['name'] : $facility->name;
                    $fType = is_array($facility) ? $facility['type'] : ($facility->type instanceof \BackedEnum ? $facility->type->value : $facility->type);
                    $fTypeLabel = is_array($facility) ? ucfirst($facility['type']) : ($facility->type instanceof \App\Enums\FacilityType ? $facility->type->label() : ucfirst($fType));
                    $fLocation = is_array($facility) ? $facility['location'] : $facility->location;
                    $fCapacity = is_array($facility) ? ($facility['capacity'] ?? null) : $facility->capacity;
                    $fDescription = is_array($facility) ? $facility['description'] : $facility->description;
                    $fOperational = is_array($facility) ? $facility['operational_status'] : ($facility->operational_status instanceof \BackedEnum ? $facility->operational_status->value : $facility->operational_status);
                    $fIsActive = is_array($facility) ? ($facility['is_active'] ?? true) : $facility->is_active;

                    if (is_array($facility)) {
                        $fPhoto = $facility['photo'] ?? 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&auto=format&fit=crop&q=80';
                    } else {
                        $fPhoto = $facility->primary_photo_url;
                    }
                @endphp

                <a href="{{ route('facilities.show', array_merge(['facility' => $fId], request()->has('as_user') ? ['as_user' => 1] : [])) }}"
                   class="facility-card bg-[#111111] border border-[#333333] hover:border-[#666666] rounded-[6px] p-5 flex flex-col justify-between transition-all duration-200 group block relative"
                   data-name="{{ strtolower($fName) }}"
                   data-type="{{ $fType }}"
                   data-location="{{ $fLocation }}"
                   data-capacity="{{ $fCapacity ?? 0 }}">
                    
                    <div class="facility-card-body flex flex-col">
                        <!-- Image Container -->
                        <div class="facility-card-img relative h-44 w-full overflow-hidden rounded-[4px] bg-[#1a1a1a] border border-[#333333] mb-4 flex-shrink-0">
                            <img 
                                src="{{ $fPhoto }}" 
                                alt="{{ $fName }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&auto=format&fit=crop&q=80';"
                            />

                            <!-- Type Tag -->
                            <div class="absolute top-2.5 left-2.5">
                                <span class="bg-black/80 text-white text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 rounded-[4px] border border-[#333333] font-medium">
                                    {{ $fTypeLabel }}
                                </span>
                            </div>

                            <!-- Capacity Badge -->
                            <div class="absolute top-2.5 right-2.5">
                                @if(!empty($fCapacity))
                                    <span class="bg-black/80 text-[#a1a1a1] text-[10px] font-mono px-2 py-0.5 rounded-[4px] border border-[#333333]">
                                        {{ $fCapacity }} Org
                                    </span>
                                @else
                                    <span class="bg-black/80 text-[#888888] text-[10px] font-mono px-2 py-0.5 rounded-[4px] border border-[#333333]">
                                        Unit Alat
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="facility-card-info flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 text-[11px] text-[#888888] mb-1 font-mono">
                                <span>{{ $fLocation }}</span>
                            </div>

                            <h2 class="text-base font-semibold text-white tracking-tight mb-2 group-hover:text-white transition-colors leading-snug">
                                {{ $fName }}
                            </h2>

                            <p class="text-xs text-[#a1a1a1] line-clamp-2 leading-relaxed mb-4">
                                {{ $fDescription }}
                            </p>
                        </div>
                    </div>

                    <!-- Operational Status & Arrow Hint -->
                    <div class="facility-card-footer pt-4 border-t border-[#333333] flex items-center justify-between gap-2 mt-2">
                        @if($fOperational === 'dapat_digunakan')
                            <span class="text-xs font-mono text-[#50e3c2] flex items-center gap-1.5 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span>
                                Dapat digunakan
                            </span>
                        @else
                            <span class="text-xs font-mono text-[#ff453a] flex items-center gap-1.5 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span>
                                Dalam perbaikan
                            </span>
                        @endif

                        <span class="text-xs text-[#888888] group-hover:text-white transition-colors font-mono font-medium flex items-center gap-1">
                            Detail →
                        </span>
                    </div>
                </a>
            @empty
                <div class="bg-[#111111] p-12 text-center col-span-3 text-[#888888] border border-[#333333] rounded-[6px] space-y-3">
                    <p class="text-sm text-white font-medium">Tidak ada fasilitas yang ditemukan</p>
                    <p class="text-xs text-[#888888]">Tidak ada fasilitas kampus yang sesuai dengan kriteria filter saat ini.</p>
                    <div>
                        <a href="{{ route('facilities.index') }}" class="inline-block bg-[#1f1f1f] hover:bg-[#2a2a2a] text-white border border-[#333333] text-xs px-3.5 py-1.5 rounded-[6px] transition">
                            Bersihkan Semua Filter
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        @if(method_exists($facilities, 'hasPages') && $facilities->hasPages())
            <div class="mt-8 border-t border-[#333333] pt-6">
                {{ $facilities->links() }}
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    function toggleCustomDropdown(id) {
        const menu = document.getElementById(id + 'Menu');
        const chevron = document.getElementById(id + 'Chevron');
        const isOpen = menu && !menu.classList.contains('hidden');

        closeAllPopups();

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

    function selectCustomDropdown(id, val, label) {
        const input = document.getElementById(id);
        const labelEl = document.getElementById(id + 'Label');
        const menu = document.getElementById(id + 'Menu');
        const chevron = document.getElementById(id + 'Chevron');

        if (input) input.value = val;
        if (labelEl) labelEl.innerText = label;

        if (menu) {
            menu.querySelectorAll('.dropdown-item').forEach(item => {
                if (item.dataset.value === val) {
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
    }

    // Capacity Popover Logic
    function closeAllPopups() {
        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        const capPopup = document.getElementById('capacityPopup');
        if (capPopup) capPopup.classList.add('hidden');
    }

    function toggleCapacityPopup() {
        const popup = document.getElementById('capacityPopup');
        const isOpen = popup && !popup.classList.contains('hidden');
        closeAllPopups();
        if (!isOpen && popup) {
            popup.classList.remove('hidden');
        }
    }

    function openCapacityPopup() {
        closeAllPopups();
        const popup = document.getElementById('capacityPopup');
        if (popup) popup.classList.remove('hidden');
    }

    function closeCapacityPopup() {
        const popup = document.getElementById('capacityPopup');
        if (popup) popup.classList.add('hidden');
    }

    function onManualCapacityInput(val) {
        let num = parseInt(val, 10);
        if (isNaN(num) || num < 0) num = 0;
        document.getElementById('capacity').value = num;
        const slider = document.getElementById('capacitySlider');
        if (slider) slider.value = Math.min(num, 150);
        updateCapacityUI(num);
    }

    function onSliderCapacityChange(val) {
        const num = parseInt(val, 10) || 0;
        document.getElementById('capacity').value = num;
        const input = document.getElementById('capacityInput');
        if (input) input.value = num === 0 ? '' : num;
        updateCapacityUI(num);
    }

    function setCapacityPreset(num) {
        document.getElementById('capacity').value = num;
        const slider = document.getElementById('capacitySlider');
        if (slider) slider.value = Math.min(num, 150);
        const input = document.getElementById('capacityInput');
        if (input) input.value = num === 0 ? '' : num;
        updateCapacityUI(num);
    }

    function updateCapacityUI(num) {
        const display = document.getElementById('sliderCapDisplay');
        if (display) {
            display.innerText = num > 0 ? `≥ ${num} Orang` : 'Semua (0 Orang)';
        }
    }

    function setCatalogView(mode) {
        const grid = document.getElementById('facilityGrid');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnList = document.getElementById('btnViewList');
        if (!grid) return;

        if (mode === 'list') {
            grid.setAttribute('data-view', 'list');
            grid.classList.remove('grid', 'grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3', 'gap-6');
            grid.classList.add('flex', 'flex-col', 'gap-4');
            if (btnGrid) {
                btnGrid.classList.remove('bg-[#222222]', 'text-white');
                btnGrid.classList.add('text-[#888888]');
            }
            if (btnList) {
                btnList.classList.remove('text-[#888888]');
                btnList.classList.add('bg-[#222222]', 'text-white');
            }
            localStorage.setItem('facility_view_mode', 'list');
        } else {
            grid.setAttribute('data-view', 'grid');
            grid.classList.remove('flex', 'flex-col', 'gap-4');
            grid.classList.add('grid', 'grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3', 'gap-6');
            if (btnList) {
                btnList.classList.remove('bg-[#222222]', 'text-white');
                btnList.classList.add('text-[#888888]');
            }
            if (btnGrid) {
                btnGrid.classList.remove('text-[#888888]');
                btnGrid.classList.add('bg-[#222222]', 'text-white');
            }
            localStorage.setItem('facility_view_mode', 'grid');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedView = localStorage.getItem('facility_view_mode');
        if (savedView === 'list') {
            setCatalogView('list');
        }

        const slider = document.getElementById('capacitySlider');
        if (slider) {
            let isDragging = false;
            const updateFromPointer = (e) => {
                const rect = slider.getBoundingClientRect();
                const pos = Math.max(0, Math.min(rect.width, e.clientX - rect.left));
                const pct = pos / rect.width;
                const min = parseFloat(slider.min) || 0;
                const max = parseFloat(slider.max) || 150;
                const step = parseFloat(slider.step) || 5;
                let val = min + pct * (max - min);
                val = Math.round(val / step) * step;
                val = Math.max(min, Math.min(max, val));
                slider.value = val;
                onSliderCapacityChange(val);
            };

            slider.addEventListener('pointerdown', (e) => {
                isDragging = true;
                slider.setPointerCapture(e.pointerId);
                updateFromPointer(e);
            });
            slider.addEventListener('pointermove', (e) => {
                if (isDragging) {
                    updateFromPointer(e);
                }
            });
            const stopDrag = (e) => {
                if (isDragging) {
                    isDragging = false;
                    try { slider.releasePointerCapture(e.pointerId); } catch(err) {}
                }
            };
            slider.addEventListener('pointerup', stopDrag);
            slider.addEventListener('pointercancel', stopDrag);
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown') && !e.target.closest('#capacityFilterContainer')) {
            closeAllPopups();
        }
    });
</script>

<style>
    /* List View Transformations for Facility Catalog */
    #facilityGrid[data-view="list"] {
        display: flex !important;
        flex-direction: column !important;
        gap: 1rem !important;
    }

    @media (min-width: 640px) {
        #facilityGrid[data-view="list"] .facility-card {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            padding: 1.25rem !important;
            gap: 1.5rem !important;
        }

        #facilityGrid[data-view="list"] .facility-card-body {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            flex: 1 !important;
            min-width: 0 !important;
            gap: 1.5rem !important;
        }

        #facilityGrid[data-view="list"] .facility-card-img {
            width: 14rem !important;
            height: 9rem !important;
            margin-bottom: 0 !important;
            flex-shrink: 0 !important;
        }

        #facilityGrid[data-view="list"] .facility-card-info {
            flex: 1 !important;
            min-width: 0 !important;
        }

        #facilityGrid[data-view="list"] .facility-card-info p {
            margin-bottom: 0 !important;
            -webkit-line-clamp: 2 !important;
        }

        #facilityGrid[data-view="list"] .facility-card-footer {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
            justify-content: center !important;
            border-top: none !important;
            border-left: 1px solid #333333 !important;
            padding-top: 0 !important;
            padding-left: 1.5rem !important;
            margin-top: 0 !important;
            min-width: 150px !important;
            gap: 1rem !important;
            flex-shrink: 0 !important;
        }
    }
</style>
@endpush
