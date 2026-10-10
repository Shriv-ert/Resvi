@extends('layouts.app')

@php
    $fId = is_array($facility) ? $facility['id'] : $facility->id;
    $fName = is_array($facility) ? $facility['name'] : $facility->name;
    $fType = is_array($facility) ? $facility['type'] : ($facility->type instanceof \BackedEnum ? $facility->type->value : $facility->type);
    $fTypeLabel = is_array($facility) ? ucfirst($facility['type']) : ($facility->type instanceof \App\Enums\FacilityType ? $facility->type->label() : ucfirst($fType));
    $fLocation = is_array($facility) ? $facility['location'] : $facility->location;
    $fCapacity = is_array($facility) ? ($facility['capacity'] ?? null) : $facility->capacity;
    
    $selectedDate = old('date', $date ?? date('Y-m-d'));
    $selectedStart = old('start_time', $startTime ?? '08:00');
    $selectedEnd = old('end_time', $endTime ?? '10:00');

    $startTimes = [];
    for ($h = 7; $h <= 19; $h++) {
        $startTimes[] = sprintf('%02d:00', $h);
        $startTimes[] = sprintf('%02d:30', $h);
    }

    $endTimes = [];
    for ($h = 7; $h <= 20; $h++) {
        if ($h > 7) {
            $endTimes[] = sprintf('%02d:00', $h);
        }
        if ($h < 20) {
            $endTimes[] = sprintf('%02d:30', $h);
        }
    }
@endphp

@section('title', 'Pengecekan Jadwal & Reservasi · ' . $fName)

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-mono text-[#888888] mb-6">
        <a href="{{ route('facilities.index', request()->has('as_user') ? ['as_user' => 1] : []) }}" class="hover:text-white transition">Katalog Fasilitas</a>
        <span>/</span>
        <a href="{{ route('facilities.show', array_merge(['facility' => $fId], request()->has('as_user') ? ['as_user' => 1] : [])) }}" class="hover:text-white transition truncate max-w-xs">{{ $fName }}</a>
        <span>/</span>
        <span class="text-white">Reservasi</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left 8 Cols: Step-by-Step Reservation Flow -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Flow Header -->
            <div class="border-b border-[#333333] pb-4">
                <span class="font-mono text-[10px] text-[#888888] uppercase tracking-wider block mb-1">Formulir Terpadu</span>
                <h1 class="text-2xl font-semibold text-white tracking-[-0.03em]">Pilih Jadwal & Reservasi.</h1>
                <p class="text-xs text-[#a1a1a1] mt-1">
                    Pengecekan ketersediaan jadwal slot 30 menit (07.00 - 20.00 WIB) untuk <strong class="text-white">{{ $fName }}</strong>.
                </p>
            </div>

            <!-- Server-Side Validation Errors (If Any) -->
            @if ($errors->any())
                <div class="p-4 rounded-[6px] border border-[#550000] bg-[#2b0000] text-xs text-[#ff453a] space-y-1">
                    <strong class="font-semibold text-white block">Gagal Memproses Permintaan:</strong>
                    <ul class="list-disc list-inside space-y-1 text-[#ededed]">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Server-Side Availability Result (From Controller) -->
            @if(isset($availability))
                @if($availability->isAvailable)
                    <div class="p-4 rounded-[6px] border border-[#005522] bg-[#002b11] text-xs text-[#50e3c2] space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm">✓</span>
                            <strong class="text-sm font-semibold text-white">Jadwal Tersedia!</strong>
                        </div>
                        <p class="text-xs leading-relaxed text-[#ededed]">
                            Fasilitas <strong class="text-white">{{ $fName }}</strong> dapat dipinjam pada tanggal 
                            <strong class="text-white font-mono">{{ $selectedDate }}</strong> pukul 
                            <strong class="text-white font-mono">{{ $selectedStart }} - {{ $selectedEnd }} WIB</strong>.
                        </p>
                    </div>
                @else
                    <div class="p-4 rounded-[6px] border border-[#550000] bg-[#2b0000] text-xs text-[#ff453a] space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm">✕</span>
                            <strong class="text-sm font-semibold text-white">Jadwal Tidak Tersedia (Bentrok / Di Luar Jam)</strong>
                        </div>
                        <p class="text-xs leading-relaxed text-[#ededed]">
                            {{ $availability->message ?? 'Terdapat bentrok jadwal dengan reservasi lain yang telah terkonfirmasi. Silakan pilih slot jam lain.' }}
                        </p>
                    </div>
                @endif
            @endif

            <!-- Step 1: Schedule Picker Card -->
            <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-[#333333] pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-white text-black font-mono font-bold text-[10px] flex items-center justify-center">1</span>
                        <h2 class="text-sm font-semibold text-white">Pilih Waktu & Cek Ketersediaan</h2>
                    </div>
                    <span class="font-mono text-[10px] text-[#888888]">Slot 30 Menit</span>
                </div>

                <form method="POST" action="{{ route('reservations.check_availability') }}" id="checkAvailabilityForm" class="space-y-4">
                    @csrf
                    <input type="hidden" name="facility_id" value="{{ $fId }}">

                    <!-- Custom Date Picker -->
                    <div class="space-y-1.5 relative custom-datepicker" id="datepicker-container">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Tanggal Pemakaian</label>
                        <input type="hidden" id="date" name="date" value="{{ $selectedDate }}">
                        @php
                            $initDateText = \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y');
                        @endphp
                        <button type="button" onclick="toggleDatePicker()" id="datePickerTrigger" title="{{ $initDateText }}"
                                class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2.5 flex items-center justify-between min-w-0 gap-2 focus:outline-none focus:border-white transition font-mono">
                            <span id="datePickerDisplay" class="flex items-center gap-2 min-w-0 truncate text-left flex-1" title="{{ $initDateText }}">
                                <svg class="w-3.5 h-3.5 text-[#888888] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span id="datePickerSelectedText" class="truncate">
                                    {{ $initDateText }}
                                </span>
                            </span>
                            <svg class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>

                        <!-- Dark Vercel Calendar Popup -->
                        <div id="calendarPopup" class="hidden absolute top-full left-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[8px] p-4 shadow-2xl backdrop-blur-md w-72 space-y-3 font-sans">
                            <div class="flex items-center justify-between border-b border-[#222222] pb-2">
                                <span id="calMonthYear" class="font-semibold text-xs text-white">September 2026</span>
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="changeCalMonth(-1)" class="w-6 h-6 rounded flex items-center justify-center text-[#888888] hover:text-white hover:bg-[#222222] transition">‹</button>
                                    <button type="button" onclick="changeCalMonth(1)" class="w-6 h-6 rounded flex items-center justify-center text-[#888888] hover:text-white hover:bg-[#222222] transition">›</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-1 text-center font-mono text-[10px] text-[#888888]">
                                <div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div><div>Su</div>
                            </div>
                            <div id="calDaysGrid" class="grid grid-cols-7 gap-1 text-center text-xs">
                                <!-- Generated by JS -->
                            </div>
                            <div class="border-t border-[#222222] pt-2 flex items-center justify-between text-[11px]">
                                <button type="button" onclick="clearDateSelection()" class="text-[#888888] hover:text-white underline">Hapus</button>
                                <button type="button" onclick="selectTodayDate()" class="text-[#50e3c2] hover:underline font-medium">Hari Ini</button>
                            </div>
                        </div>
                    </div>

                    <!-- Time Slot Grid (Start & End Time) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Custom Dropdown: Waktu Mulai -->
                        <div class="space-y-1.5 relative custom-dropdown" id="dropdown-start_time">
                            <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Waktu Mulai (07.00 - 19.30)</label>
                            <input type="hidden" id="start_time" name="start_time" value="{{ $selectedStart }}">
                            @php
                                $initStartLabel = $selectedStart ? $selectedStart . ' WIB' : 'Pilih Waktu Mulai';
                            @endphp
                            <button type="button" onclick="toggleCustomDropdown('start_time')" id="start_timeTrigger" title="{{ $initStartLabel }}"
                                    class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2.5 flex items-center justify-between min-w-0 gap-2 focus:outline-none focus:border-white transition font-mono">
                                <span id="start_timeLabel" class="truncate text-left flex-1" title="{{ $initStartLabel }}">{{ $initStartLabel }}</span>
                                <svg id="start_timeChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div id="start_timeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                                <div class="p-2 border-b border-[#222222] sticky top-0 bg-[#111111] z-10">
                                    <div class="relative">
                                        <input type="text" placeholder="Cari waktu mulai..." 
                                               id="search-start_time"
                                               oninput="filterDropdownOptions(this, 'start_timeMenu')"
                                               onclick="event.stopPropagation()"
                                               class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-[11px] rounded-[4px] pl-7 pr-2 py-1.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]">
                                        <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                @foreach($startTimes as $t)
                                    <div onclick="selectCustomDropdown('start_time', '{{ $t }}', '{{ $t }} WIB')" data-value="{{ $t }}" 
                                         class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $selectedStart == $t ? 'bg-[#1f1f1f] text-white' : '' }}">
                                        <span>{{ $t }} WIB</span>
                                        <span class="check-icon text-[#50e3c2] text-xs {{ $selectedStart == $t ? '' : 'hidden' }}">✓</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Custom Dropdown: Waktu Selesai -->
                        <div class="space-y-1.5 relative custom-dropdown" id="dropdown-end_time">
                            <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Waktu Selesai (07.30 - 20.00)</label>
                            <input type="hidden" id="end_time" name="end_time" value="{{ $selectedEnd }}">
                            @php
                                $initEndLabel = $selectedEnd ? $selectedEnd . ' WIB' : 'Pilih Waktu Selesai';
                            @endphp
                            <button type="button" onclick="toggleCustomDropdown('end_time')" id="end_timeTrigger" title="{{ $initEndLabel }}"
                                    class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2.5 flex items-center justify-between min-w-0 gap-2 focus:outline-none focus:border-white transition font-mono">
                                <span id="end_timeLabel" class="truncate text-left flex-1" title="{{ $initEndLabel }}">{{ $initEndLabel }}</span>
                                <svg id="end_timeChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>
                            <div id="end_timeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-hidden py-1 max-h-60 overflow-y-auto">
                                <div class="p-2 border-b border-[#222222] sticky top-0 bg-[#111111] z-10">
                                    <div class="relative">
                                        <input type="text" placeholder="Cari waktu selesai..." 
                                               id="search-end_time"
                                               oninput="filterDropdownOptions(this, 'end_timeMenu')"
                                               onclick="event.stopPropagation()"
                                               class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-[11px] rounded-[4px] pl-7 pr-2 py-1.5 focus:outline-none focus:border-white transition font-mono placeholder-[#666666]">
                                        <svg class="w-3.5 h-3.5 text-[#888888] absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                @foreach($endTimes as $t)
                                    <div onclick="selectCustomDropdown('end_time', '{{ $t }}', '{{ $t }} WIB')" data-value="{{ $t }}" 
                                         class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $selectedEnd == $t ? 'bg-[#1f1f1f] text-white' : '' }}">
                                        <span>{{ $t }} WIB</span>
                                        <span class="check-icon text-[#50e3c2] text-xs {{ $selectedEnd == $t ? '' : 'hidden' }}">✓</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <button type="submit" class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-5 py-2 rounded-full transition active:scale-95 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Cek Ketersediaan Server
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Confirmation / Action Depending on User Role -->
            @guest
                <!-- Visitor Mode: Guest Draft & Login Prompt -->
                <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 space-y-4">
                    <div class="flex items-center gap-2 border-b border-[#333333] pb-3">
                        <span class="w-5 h-5 rounded-full bg-[#1f1f1f] text-[#888888] font-mono font-bold text-[10px] flex items-center justify-center">2</span>
                        <h2 class="text-sm font-semibold text-white">Lanjutkan Pengajuan Sebagai Pengguna</h2>
                    </div>

                    <p class="text-xs text-[#a1a1a1] leading-relaxed">
                        Anda mengakses sebagai <strong class="text-white">Pengunjung</strong>. Pengunjung dapat memeriksa ketersediaan jadwal, namun pengajuan reservasi resmi memerlukan akun civitas kampus terdaftar (Mahasiswa/Dosen/Staf).
                    </p>

                    <form method="POST" action="{{ route('reservations.store_draft') }}" id="guestDraftForm">
                        @csrf
                        <input type="hidden" name="facility_id" value="{{ $fId }}">
                        <input type="hidden" name="date" id="guest_date" value="{{ $selectedDate }}">
                        <input type="hidden" name="start_time" id="guest_start_time" value="{{ $selectedStart }}">
                        <input type="hidden" name="end_time" id="guest_end_time" value="{{ $selectedEnd }}">

                        <button type="submit" class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-5 py-2.5 rounded-full transition active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            Simpan Draft & Masuk / Login
                        </button>
                    </form>
                </div>
            @else
                <!-- Authenticated User Mode: Purpose Form -->
                <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 space-y-4">
                    <div class="flex items-center gap-2 border-b border-[#333333] pb-3">
                        <span class="w-5 h-5 rounded-full bg-white text-black font-mono font-bold text-[10px] flex items-center justify-center">2</span>
                        <h2 class="text-sm font-semibold text-white">Tujuan Penggunaan & Konfirmasi Pengajuan</h2>
                    </div>

                    <form method="POST" action="{{ route('user.reservations.store') }}" class="space-y-4" id="userReservationForm">
                        @csrf
                        <input type="hidden" name="facility_id" value="{{ $fId }}">
                        <input type="hidden" name="date" id="user_date" value="{{ $selectedDate }}">
                        <input type="hidden" name="start_time" id="user_start_time" value="{{ $selectedStart }}">
                        <input type="hidden" name="end_time" id="user_end_time" value="{{ $selectedEnd }}">

                        <div class="space-y-1.5">
                            <label for="purpose" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Tujuan Penggunaan Fasilitas</label>
                            <textarea
                                id="purpose"
                                name="purpose"
                                rows="3"
                                required
                                placeholder="Contoh: Perkuliahan pengganti Pemrograman Web Lanjut kelas TI-22, persiapan seminar, praktikum mandiri..."
                                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] p-3 focus:outline-none focus:border-white transition font-sans placeholder-[#666666]"
                            ></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-6 py-2.5 rounded-full transition active:scale-95 flex items-center gap-2">
                                Ajukan Reservasi ke Petugas
                            </button>
                        </div>
                    </form>
                </div>
            @endguest
        </div>

        <!-- Right 4 Cols: Facility Info Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 space-y-4">
                <span class="font-mono text-[10px] text-[#888888] uppercase tracking-wider block">Fasilitas Terpilih</span>
                <h3 class="text-base font-semibold text-white tracking-tight">{{ $fName }}</h3>
                
                <div class="border-t border-[#333333] pt-4 space-y-3 text-xs font-mono">
                    <div class="grid grid-cols-[80px_1fr] items-start gap-2">
                        <span class="text-[#888888]">Tipe</span>
                        <span class="text-white capitalize font-medium">{{ $fTypeLabel }}</span>
                    </div>
                    <div class="grid grid-cols-[80px_1fr] items-start gap-2">
                        <span class="text-[#888888]">Lokasi</span>
                        <span class="text-white font-medium leading-relaxed">{{ $fLocation }}</span>
                    </div>
                    <div class="grid grid-cols-[80px_1fr] items-start gap-2">
                        <span class="text-[#888888]">Kapasitas</span>
                        <span class="text-white font-medium">{{ !empty($fCapacity) ? $fCapacity . ' Orang' : 'Unit Alat' }}</span>
                    </div>
                </div>
            </div>

            <div class="border border-[#333333] rounded-[6px] bg-[#111111] p-5 font-mono text-[11px] text-[#a1a1a1] space-y-2">
                <span class="text-[#888888] uppercase tracking-wider block text-[10px]">Ketentuan Reservasi</span>
                <p>• Slot waktu harus kelipatan 30 menit (contoh: 08.00, 08.30).</p>
                <p>• Peminjaman hanya pada rentang jam 07.00 - 20.00 WIB.</p>
                <p>• Pengajuan diproses oleh Petugas Sarpras secara berurutan.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const MONTH_NAMES = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    const MONTH_SHORT = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
    ];

    let calCurrentYear = new Date().getFullYear();
    let calCurrentMonth = new Date().getMonth();
    let calSelectedDate = '{{ $selectedDate }}';

    if (calSelectedDate) {
        const parts = calSelectedDate.split('-');
        if (parts.length === 3) {
            calCurrentYear = parseInt(parts[0], 10);
            calCurrentMonth = parseInt(parts[1], 10) - 1;
        }
    }

    function formatDisplayDate(dateStr) {
        if (!dateStr) return 'Pilih Tanggal';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const day = parseInt(parts[2], 10);
        const monthIdx = parseInt(parts[1], 10) - 1;
        const year = parts[0];
        return `${day} ${MONTH_SHORT[monthIdx] || ''} ${year}`;
    }

    function renderCalendarGrid() {
        const monthYearEl = document.getElementById('calMonthYear');
        if (!monthYearEl) return;
        monthYearEl.innerText = MONTH_NAMES[calCurrentMonth] + ' ' + calCurrentYear;

        const grid = document.getElementById('calDaysGrid');
        if (!grid) return;
        grid.innerHTML = '';

        const firstDay = new Date(calCurrentYear, calCurrentMonth, 1).getDay();
        const offset = firstDay === 0 ? 6 : firstDay - 1; // Mo=0 ... Su=6

        const prevMonthDays = new Date(calCurrentYear, calCurrentMonth, 0).getDate();
        for (let i = offset - 1; i >= 0; i--) {
            const cell = document.createElement('div');
            cell.className = 'py-1.5 text-center text-[#444444] font-mono text-[11px] cursor-not-allowed select-none';
            cell.innerText = prevMonthDays - i;
            grid.appendChild(cell);
        }

        const totalDays = new Date(calCurrentYear, calCurrentMonth + 1, 0).getDate();
        const today = new Date();
        const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

        for (let d = 1; d <= totalDays; d++) {
            const dStr = String(d).padStart(2, '0');
            const mStr = String(calCurrentMonth + 1).padStart(2, '0');
            const dateKey = calCurrentYear + '-' + mStr + '-' + dStr;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerText = d;
            btn.className = 'py-1.5 rounded-[4px] font-mono text-[11px] transition text-center flex items-center justify-center';

            const isSelected = dateKey === calSelectedDate;
            const isToday = dateKey === todayStr;

            if (isSelected) {
                btn.className += ' bg-white text-black font-semibold shadow-sm';
            } else if (isToday) {
                btn.className += ' text-white border border-[#555555] bg-[#1a1a1a] hover:bg-[#262626]';
            } else {
                btn.className += ' text-[#cccccc] hover:bg-[#222222] hover:text-white';
            }

            btn.onclick = () => {
                selectDate(dateKey);
            };
            grid.appendChild(btn);
        }
    }

    function selectDate(dateStr) {
        calSelectedDate = dateStr;
        const dateInput = document.getElementById('date');
        if (dateInput) dateInput.value = dateStr;

        const dateText = formatDisplayDate(dateStr);
        const disp = document.getElementById('datePickerSelectedText');
        if (disp) {
            disp.innerText = dateText;
            disp.title = dateText;
        }
        const dispWrapper = document.getElementById('datePickerDisplay');
        if (dispWrapper) dispWrapper.title = dateText;
        const trigger = document.getElementById('datePickerTrigger');
        if (trigger) trigger.title = dateText;

        const guestDate = document.getElementById('guest_date');
        if (guestDate) guestDate.value = dateStr;

        const userDate = document.getElementById('user_date');
        if (userDate) userDate.value = dateStr;

        closeDatePicker();
    }

    function clearDateSelection() {
        selectDate('');
    }

    function selectTodayDate() {
        const today = new Date();
        const dStr = String(today.getDate()).padStart(2, '0');
        const mStr = String(today.getMonth() + 1).padStart(2, '0');
        const todayKey = today.getFullYear() + '-' + mStr + '-' + dStr;
        calCurrentMonth = today.getMonth();
        calCurrentYear = today.getFullYear();
        selectDate(todayKey);
        renderCalendarGrid();
    }

    function changeCalMonth(delta) {
        calCurrentMonth += delta;
        if (calCurrentMonth > 11) {
            calCurrentMonth = 0;
            calCurrentYear++;
        } else if (calCurrentMonth < 0) {
            calCurrentMonth = 11;
            calCurrentYear--;
        }
        renderCalendarGrid();
    }

    function toggleDatePicker() {
        const popup = document.getElementById('calendarPopup');
        const isOpen = popup && !popup.classList.contains('hidden');
        closeAllPopups();
        if (!isOpen && popup) {
            popup.classList.remove('hidden');
            renderCalendarGrid();
        }
    }

    function closeDatePicker() {
        const popup = document.getElementById('calendarPopup');
        if (popup) popup.classList.add('hidden');
    }

    function closeAllPopups() {
        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
        closeDatePicker();
    }

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
        const trigger = document.getElementById(id + 'Trigger');
        const menu = document.getElementById(id + 'Menu');
        const chevron = document.getElementById(id + 'Chevron');

        if (input) input.value = val;
        if (labelEl) {
            labelEl.innerText = label;
            labelEl.title = label;
        }
        if (trigger) trigger.title = label;

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

        // Sync with Step 2 forms
        if (id === 'start_time') {
            const gStart = document.getElementById('guest_start_time');
            if (gStart) gStart.value = val;
            const uStart = document.getElementById('user_start_time');
            if (uStart) uStart.value = val;
        } else if (id === 'end_time') {
            const gEnd = document.getElementById('guest_end_time');
            if (gEnd) gEnd.value = val;
            const uEnd = document.getElementById('user_end_time');
            if (uEnd) uEnd.value = val;
        }
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-dropdown') && !e.target.closest('#datepicker-container')) {
            closeAllPopups();
        }
    });
</script>
@endpush

