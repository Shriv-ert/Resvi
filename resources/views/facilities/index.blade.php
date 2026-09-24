@extends('layouts.app')

@section('title', 'Daftar Fasilitas Kampus')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- ======================================================== -->
    <!-- KATALOG FASILITAS & FILTER (KEPUTUSAN-GLOBAL.md §3.4) -->
    <!-- ======================================================== -->
    <section id="katalog" class="space-y-6">
        <div class="space-y-1">
            <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
                Katalog fasilitas kampus.
            </h1>
            <p class="text-xs sm:text-sm text-[#a1a1a1]">
                Gunakan penyaringan berikut untuk mencari ruangan atau sarana yang siap dipinjam.
            </p>
        </div>

        <div class="border border-[#333333] rounded-[6px] bg-[#111111] p-6 mb-6">
            <form id="filterForm" method="GET" action="{{ route('facilities.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @if(request()->has('as_user'))
                    <input type="hidden" name="as_user" value="1">
                @endif

                <!-- Search by Name -->
                <div class="space-y-1.5">
                    <label for="search" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Pencarian Nama</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Contoh: Lab Komputer, Aula..." 
                            class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] pl-8 pr-3 py-2 focus:outline-none focus:border-white transition placeholder-[#666666]"
                        />
                        <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Filter Type (Custom Dropdown) -->
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
                    <div id="typeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
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

                <!-- Filter Location (Custom Dropdown) -->
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
                        <div onclick="selectCustomDropdown('location', 'Gedung A (Utama)', 'Gedung A (Utama)')" data-value="Gedung A (Utama)" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('location') == 'Gedung A (Utama)' ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>Gedung A (Utama)</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ request('location') == 'Gedung A (Utama)' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('location', 'Gedung B (Laboratorium)', 'Gedung B (Laboratorium)')" data-value="Gedung B (Laboratorium)" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('location') == 'Gedung B (Laboratorium)' ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>Gedung B (Laboratorium)</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ request('location') == 'Gedung B (Laboratorium)' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('location', 'Gedung C (Fakultas)', 'Gedung C (Fakultas)')" data-value="Gedung C (Fakultas)" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('location') == 'Gedung C (Fakultas)' ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>Gedung C (Fakultas)</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ request('location') == 'Gedung C (Fakultas)' ? '' : 'hidden' }}">✓</span>
                        </div>
                        <div onclick="selectCustomDropdown('location', 'Area Terbuka Kampus', 'Area Terbuka Kampus')" data-value="Area Terbuka Kampus" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ request('location') == 'Area Terbuka Kampus' ? 'bg-[#1f1f1f] text-white' : '' }}">
                            <span>Area Terbuka Kampus</span>
                            <span class="check-icon text-[#50e3c2] text-xs {{ request('location') == 'Area Terbuka Kampus' ? '' : 'hidden' }}">✓</span>
                        </div>
                    </div>
                </div>

                <!-- Filter Capacity (Custom Manual Input + Interactive Popup Range Slider) -->
                <div class="space-y-1.5 relative" id="capacityFilterContainer">
                    <div class="flex items-center justify-between">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Kapasitas</label>
                        <span id="capacityValueBadge" class="font-mono text-[10px] text-[#50e3c2] {{ request('capacity') > 0 ? '' : 'hidden' }}">≥ <span id="capacityValueBadgeNum">{{ request('capacity', 0) }}</span> Orang</span>
                    </div>
                    <div class="relative flex items-center">
                        <input 
                            type="number" 
                            id="capacityInput" 
                            min="0" 
                            max="250" 
                            placeholder="Semua (0)" 
                            value="{{ request('capacity', '') }}"
                            oninput="onManualCapacityInput(this.value)"
                            onfocus="openCapacityPopup()"
                            class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] focus:border-white text-white text-xs rounded-[6px] pl-3 pr-9 py-2 focus:outline-none transition font-mono"
                        />
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

                    <input type="hidden" id="capacity" name="capacity" value="{{ request('capacity', '0') }}">

                    <!-- Interactive Popup Range Slider -->
                    <div id="capacityPopup" class="hidden absolute top-full left-0 right-0 sm:right-auto sm:w-80 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[8px] p-4 shadow-2xl backdrop-blur-md space-y-4">
                        <div class="flex items-center justify-between border-b border-[#222222] pb-2.5">
                            <span class="text-xs font-semibold text-white">Range Kapasitas</span>
                            <span class="font-mono text-xs px-2 py-0.5 rounded-[4px] bg-[#1a1a1a] text-[#50e3c2] border border-[#333333]" id="sliderCapDisplay">
                                {{ request('capacity') > 0 ? '≥ ' . request('capacity') . ' Orang' : 'Semua (0 Orang)' }}
                            </span>
                        </div>

                        <!-- Slider -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] font-mono text-[#888888]">
                                <span>0</span>
                                <span>50</span>
                                <span>100</span>
                                <span>150+</span>
                            </div>
                            <input type="range" id="capacitySlider" min="0" max="150" step="5" value="{{ request('capacity', 0) }}"
                                   oninput="onSliderCapacityChange(this.value)"
                                   class="w-full">
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
            </form>

            <div class="mt-4 pt-4 border-t border-[#333333] flex flex-wrap items-center justify-between gap-3 text-xs text-[#888888]">
                <div class="flex items-center gap-2">
                    <span>Menampilkan <strong class="text-white font-mono" id="countDisplay">{{ count($facilities) }}</strong> fasilitas aktif</span>
                    @if(request()->anyFilled(['search', 'type', 'location', 'capacity']))
                        <a href="{{ route('facilities.index', request()->has('as_user') ? ['as_user' => 1] : []) }}" class="text-[#0070f3] hover:underline ml-2 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reset Filter
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <!-- Status Legend -->
                    <div class="hidden sm:flex items-center gap-4 text-[11px] font-mono border-r border-[#333333] pr-4">
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-[#50e3c2]"></span> Dapat digunakan</span>
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-[#ff453a]"></span> Dalam perbaikan</span>
                    </div>

                    <!-- View Mode Switcher -->
                    <div class="inline-flex p-0.5 bg-[#0a0a0a] border border-[#333333] rounded-[6px] text-xs">
                        <button type="button" id="btnViewGrid" onclick="setViewMode('grid')" title="Tampilan Grid / Card" class="px-2.5 py-1 rounded-[4px] font-mono text-[11px] flex items-center gap-1.5 transition text-white bg-[#222222] border border-[#444444]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            <span>Grid</span>
                        </button>
                        <button type="button" id="btnViewList" onclick="setViewMode('list')" title="Tampilan List" class="px-2.5 py-1 rounded-[4px] font-mono text-[11px] flex items-center gap-1.5 transition text-[#888888] hover:text-white">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <span>List</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- VERCEL GRID/LIST ARCHITECTURE FOR FACILITY CATALOG -->
        <!-- Section 1.B: Cards are NOT floating boxes. Adjacent cells separated by 1px divider lines. -->
        <!-- ======================================================== -->
        <style>
            /* List View Mode Styling */
            #facilityGrid.is-list-view {
                display: flex;
                flex-direction: column;
            }

            #facilityGrid.is-list-view .facility-empty-cell {
                display: none !important;
            }

            @media (min-width: 640px) {
                #facilityGrid.is-list-view .facility-card {
                    flex-direction: row;
                    align-items: center;
                    padding: 1.25rem 1.5rem;
                }

                #facilityGrid.is-list-view .facility-card-body {
                    flex-direction: row;
                    align-items: center;
                    flex: 1;
                    min-width: 0;
                    gap: 1.25rem;
                }

                #facilityGrid.is-list-view .facility-card-img {
                    width: 14rem;
                    height: 7.5rem;
                    margin-bottom: 0;
                }

                #facilityGrid.is-list-view .facility-card-desc {
                    margin-bottom: 0;
                }

                #facilityGrid.is-list-view .facility-card-footer {
                    border-top: none;
                    padding-top: 0;
                    margin-top: 0;
                    flex-direction: column;
                    align-items: flex-end;
                    justify-content: center;
                    gap: 0.75rem;
                    flex-shrink: 0;
                    padding-left: 1.5rem;
                    border-left: 1px solid #222222;
                    min-width: 150px;
                }
            }
        </style>

        <div id="facilityGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 bg-[#333333] gap-[1px] border border-[#333333] rounded-[6px] overflow-hidden">
            @forelse($facilities as $facility)
                <a href="{{ route('facilities.show', array_merge(['facility' => $facility['id']], request()->has('as_user') ? ['as_user' => 1] : [])) }}"
                   class="facility-card bg-[#111111] p-6 hover:bg-[#171717] transition-colors flex flex-col justify-between group block cursor-pointer"
                   data-name="{{ strtolower($facility['name']) }}"
                   data-type="{{ $facility['type'] }}"
                   data-location="{{ $facility['location'] }}"
                   data-capacity="{{ $facility['capacity'] ?? 0 }}">
                    
                    <div class="facility-card-body flex flex-col">
                        <!-- Image Container with 1px border -->
                        <div class="facility-card-img relative h-44 w-full overflow-hidden rounded-[4px] bg-[#1a1a1a] border border-[#333333] mb-4 flex-shrink-0">
                            <img 
                                src="{{ $facility['photo'] }}" 
                                alt="{{ $facility['name'] }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            />

                            <!-- Type Tag -->
                            <div class="absolute top-2.5 left-2.5">
                                <span class="bg-black/80 text-white text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 rounded-[4px] border border-[#333333] font-medium">
                                    {{ ucfirst($facility['type']) }}
                                </span>
                            </div>

                            <!-- Capacity Badge -->
                            <div class="absolute top-2.5 right-2.5">
                                @if(!empty($facility['capacity']))
                                    <span class="bg-black/80 text-[#a1a1a1] text-[10px] font-mono px-2 py-0.5 rounded-[4px] border border-[#333333]">
                                        {{ $facility['capacity'] }} Org
                                    </span>
                                @else
                                    <span class="bg-black/80 text-[#888888] text-[10px] font-mono px-2 py-0.5 rounded-[4px] border border-[#333333]">
                                        Unit Alat
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="facility-card-content flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 text-[11px] text-[#888888] mb-1 font-mono">
                                <span>{{ $facility['location'] }}</span>
                            </div>

                            <h2 class="text-base font-semibold text-white tracking-tight mb-2 group-hover:text-white transition-colors leading-snug">
                                {{ $facility['name'] }}.
                            </h2>

                            <p class="text-xs text-[#a1a1a1] line-clamp-2 leading-relaxed mb-4 facility-card-desc">
                                {{ $facility['description'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Operational Status & Arrow Hint -->
                    <div class="facility-card-footer pt-4 border-t border-[#333333] flex items-center justify-between gap-2 mt-2">
                        @if($facility['operational_status'] === 'dapat_digunakan')
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
                <div class="bg-[#111111] p-12 text-center col-span-3 text-[#888888]">
                    Tidak ada fasilitas kampus yang sesuai dengan filter pencarian.
                </div>
            @endforelse

            <!-- Aesthetic Empty Slot Placeholders (Modified Gray Space) -->
            <div id="emptyCell1" class="facility-empty-cell hidden bg-[#0c0c0c] p-5 flex flex-col justify-center items-center text-center relative overflow-hidden min-h-[380px] group transition-colors"
                 style="background-image: repeating-linear-gradient(-45deg, #0d0d0d, #0d0d0d 12px, #121212 12px, #121212 24px);">
                <div class="w-full h-full border border-dashed border-[#222222] rounded-[6px] p-6 flex flex-col items-center justify-center text-center relative z-10 transition-colors group-hover:border-[#333333]">
                    <div class="w-11 h-11 rounded-full bg-[#141414] border border-[#262626] flex items-center justify-center mb-3 text-[#555555]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-xs font-mono font-medium text-[#777777] uppercase tracking-wider mb-1">Slot Fasilitas Kosong</span>
                    <p class="text-[11px] text-[#555555] max-w-[200px] leading-relaxed">Ruang fasilitas lain belum terisi pada kriteria penyaringan ini.</p>
                </div>
            </div>

            <div id="emptyCell2" class="facility-empty-cell hidden bg-[#0c0c0c] p-5 flex flex-col justify-center items-center text-center relative overflow-hidden min-h-[380px] group transition-colors"
                 style="background-image: repeating-linear-gradient(-45deg, #0d0d0d, #0d0d0d 12px, #121212 12px, #121212 24px);">
                <div class="w-full h-full border border-dashed border-[#222222] rounded-[6px] p-6 flex flex-col items-center justify-center text-center relative z-10 transition-colors group-hover:border-[#333333]">
                    <div class="w-11 h-11 rounded-full bg-[#141414] border border-[#262626] flex items-center justify-center mb-3 text-[#555555]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-xs font-mono font-medium text-[#777777] uppercase tracking-wider mb-1">Slot Fasilitas Kosong</span>
                    <p class="text-[11px] text-[#555555] max-w-[200px] leading-relaxed">Ruang fasilitas lain belum terisi pada kriteria penyaringan ini.</p>
                </div>
            </div>

            <!-- Client-side No Results State -->
            <div id="noResultsCell" class="hidden bg-[#111111] p-12 text-center col-span-1 md:col-span-2 lg:col-span-3 text-[#888888] font-mono text-xs">
                Tidak ada fasilitas kampus yang sesuai dengan filter pencarian.
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Live JavaScript Filter & View Switcher
    const searchInput = document.getElementById('search');
    const typeInput = document.getElementById('type');
    const locationInput = document.getElementById('location');
    const capacityInput = document.getElementById('capacity');
    const countDisplay = document.getElementById('countDisplay');
    const facilityCards = document.querySelectorAll('#facilityGrid > [data-name]');

    function setViewMode(mode) {
        const grid = document.getElementById('facilityGrid');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnList = document.getElementById('btnViewList');
        if (!grid) return;

        if (mode === 'list') {
            grid.classList.add('is-list-view');
            grid.classList.remove('md:grid-cols-2', 'lg:grid-cols-3');
            grid.classList.add('grid-cols-1');
            
            if (btnList) btnList.className = 'px-2.5 py-1 rounded-[4px] font-mono text-[11px] flex items-center gap-1.5 transition text-white bg-[#222222] border border-[#444444]';
            if (btnGrid) btnGrid.className = 'px-2.5 py-1 rounded-[4px] font-mono text-[11px] flex items-center gap-1.5 transition text-[#888888] hover:text-white';
            localStorage.setItem('facility_view_mode', 'list');
        } else {
            grid.classList.remove('is-list-view');
            grid.classList.add('md:grid-cols-2', 'lg:grid-cols-3');
            
            if (btnGrid) btnGrid.className = 'px-2.5 py-1 rounded-[4px] font-mono text-[11px] flex items-center gap-1.5 transition text-white bg-[#222222] border border-[#444444]';
            if (btnList) btnList.className = 'px-2.5 py-1 rounded-[4px] font-mono text-[11px] flex items-center gap-1.5 transition text-[#888888] hover:text-white';
            localStorage.setItem('facility_view_mode', 'grid');
        }

        // Re-evaluate empty slots visibility
        const visible = Array.from(facilityCards).filter(c => c.style.display !== 'none').length;
        updateEmptyCatalogSlots(visible);
    }

    // Restore saved view mode preference on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedMode = localStorage.getItem('facility_view_mode');
        if (savedMode === 'list') {
            setViewMode('list');
        }
    });

    function filterFacilities() {
        const query = searchInput.value.toLowerCase().trim();
        const type = typeInput.value.toLowerCase();
        const location = locationInput.value;
        const minCapacity = parseInt(capacityInput.value) || 0;

        let visibleCount = 0;

        facilityCards.forEach(card => {
            const cardName = card.dataset.name;
            const cardType = card.dataset.type.toLowerCase();
            const cardLocation = card.dataset.location;
            const cardCapacity = parseInt(card.dataset.capacity) || 0;

            const matchesQuery = !query || cardName.includes(query);
            const matchesType = !type || cardType === type;
            const matchesLocation = !location || cardLocation === location;
            // null / 0 capacity excluded when minCapacity chosen (KEPUTUSAN-GLOBAL.md §3.5)
            const matchesCapacity = minCapacity === 0 ? true : (cardCapacity > 0 && cardCapacity >= minCapacity);

            if (matchesQuery && matchesType && matchesLocation && matchesCapacity) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (countDisplay) {
            countDisplay.innerText = visibleCount;
        }

        updateEmptyCatalogSlots(visibleCount);
    }

    function updateEmptyCatalogSlots(visibleCount) {
        const empty1 = document.getElementById('emptyCell1');
        const empty2 = document.getElementById('emptyCell2');
        const noRes = document.getElementById('noResultsCell');

        if (empty1) empty1.classList.add('hidden');
        if (empty2) empty2.classList.add('hidden');
        if (noRes) noRes.classList.add('hidden');

        if (visibleCount === 0) {
            if (noRes) noRes.classList.remove('hidden');
        } else if (window.innerWidth >= 1024) {
            if (visibleCount % 3 === 2 && empty1) empty1.classList.remove('hidden');
            else if (visibleCount % 3 === 1) {
                if (empty1) empty1.classList.remove('hidden');
                if (empty2) empty2.classList.remove('hidden');
            }
        } else if (window.innerWidth >= 768) {
            if (visibleCount % 2 === 1 && empty1) empty1.classList.remove('hidden');
        }
    }

    // Initialize empty slots on load
    updateEmptyCatalogSlots({{ count($facilities) }});

    window.addEventListener('resize', () => {
        const visible = Array.from(facilityCards).filter(c => c.style.display !== 'none').length;
        updateEmptyCatalogSlots(visible);
    });

    searchInput.addEventListener('input', filterFacilities);

    // Custom Dropdown Select Logic
    function toggleCustomDropdown(id) {
        const menu = document.getElementById(id + 'Menu');
        const chevron = document.getElementById(id + 'Chevron');
        const isOpen = menu && !menu.classList.contains('hidden');

        closeAllPopups();

        if (!isOpen && menu) {
            menu.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');

            // Reset search and auto-focus
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

        filterFacilities();
    }

    // Custom Capacity Range Slider Logic
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
        filterFacilities();
    }

    function onSliderCapacityChange(val) {
        const num = parseInt(val, 10) || 0;
        document.getElementById('capacity').value = num;
        const input = document.getElementById('capacityInput');
        if (input) input.value = num === 0 ? '' : num;
        updateCapacityUI(num);
        filterFacilities();
    }

    function setCapacityPreset(num) {
        document.getElementById('capacity').value = num;
        const slider = document.getElementById('capacitySlider');
        if (slider) slider.value = Math.min(num, 150);
        const input = document.getElementById('capacityInput');
        if (input) input.value = num === 0 ? '' : num;
        updateCapacityUI(num);
        filterFacilities();
    }

    function updateCapacityUI(num) {
        const display = document.getElementById('sliderCapDisplay');
        const badge = document.getElementById('capacityValueBadge');
        const badgeNum = document.getElementById('capacityValueBadgeNum');

        if (display) {
            display.innerText = num === 0 ? 'Semua (0 Orang)' : '≥ ' + num + ' Orang';
        }
        if (badge && badgeNum) {
            if (num > 0) {
                badge.classList.remove('hidden');
                badgeNum.innerText = num;
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    function closeAllPopups() {
        const cap = document.getElementById('capacityPopup');
        if (cap) cap.classList.add('hidden');

        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
    }

    // Global Click Outside Listener
    document.addEventListener('click', function(e) {
        const inDropdown = e.target.closest('.custom-dropdown');
        const inCapacity = e.target.closest('#capacityFilterContainer');

        if (!inDropdown && !inCapacity) {
            closeAllPopups();
        }
    });
</script>
@endpush
