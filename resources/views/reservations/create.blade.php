@extends('layouts.app')

@section('title', 'Formulir Reservasi · PPK Room Reservation')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">

    <!-- Breadcrumb -->
    <div class="mb-6">
        <a href="{{ route('facilities.show', array_merge(['facility' => $facility['id']], request()->has('as_user') ? ['as_user' => 1] : [])) }}" 
           class="inline-flex items-center gap-1.5 text-xs font-medium text-[#a1a1a1] hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Detail Fasilitas
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-[6px] text-xs font-mono text-[#0070f3] bg-[#001733] border border-[#003b80] mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-[#0070f3]"></span>
            <span>Alur Reservasi · Pengecekan Slot Terjadwal</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-semibold text-white tracking-[-0.03em]">
            Reservasi fasilitas: {{ $facility['name'] }}.
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-[#a1a1a1]">
            Periksa ketersediaan jadwal slot 30 menit (jam operasional 07.00 – 20.00 WIB) sebelum mengirim pengajuan.
        </p>
    </div>

    <!-- Main Card Container: Crisp border, micro 6px radius, no drop shadow -->
    <div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 sm:p-8 space-y-6">

        <!-- Selected Facility Summary Card (Readonly) -->
        <div class="bg-[#161616] border border-[#333333] rounded-[6px] p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ $facility['photo'] }}" alt="{{ $facility['name'] }}" class="w-12 h-12 rounded-[4px] object-cover border border-[#333333]">
                <div>
                    <span class="font-mono text-[10px] uppercase font-medium text-[#888888] tracking-wider">{{ $facility['type'] }}</span>
                    <h3 class="text-sm font-semibold text-white tracking-tight">{{ $facility['name'] }}.</h3>
                    <p class="text-xs text-[#a1a1a1]">{{ $facility['location'] }} • {{ !empty($facility['capacity']) ? 'Kapasitas ' . $facility['capacity'] . ' orang' : 'Unit Alat' }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('facilities.index', request()->has('as_user') ? ['as_user' => 1] : []) }}" class="text-xs font-medium text-[#0070f3] hover:underline flex items-center gap-1 font-mono">
                    Ganti Fasilitas →
                </a>
            </div>
        </div>

        <form id="reservationForm" method="POST" action="{{ route('user.reservations.index', ['as_user' => 1]) }}" class="space-y-6">
            @csrf
            <input type="hidden" name="facility_id" value="{{ $facility['id'] }}">

            <!-- Step 1: Schedule Selection (SRS FR-PG-04 & FR-PGN-06) -->
            <div class="space-y-4">
                <h3 class="text-xs font-mono uppercase tracking-wider text-[#888888] font-medium flex items-center gap-2">
                    <span class="w-4 h-4 rounded-[4px] bg-white text-black text-[10px] font-bold flex items-center justify-center">1</span>
                    Tentukan Waktu Penggunaan (Slot 30 Menit)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Custom Date Picker -->
                    <div class="space-y-1.5 relative custom-datepicker" id="datepicker-reservation_date">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Tanggal Penggunaan</label>
                        <input type="hidden" id="reservation_date" name="date" value="{{ date('Y-m-d') }}" required>
                        <button type="button" onclick="toggleDatePicker()" id="datePickerTrigger"
                                class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                            <span id="datePickerDisplay" class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-[#888888]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span id="datePickerSelectedText">{{ date('d M Y') }}</span>
                            </span>
                            <svg class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>

                        <!-- Dark Vercel Calendar Popup -->
                        <div id="calendarPopup" class="hidden absolute top-full left-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[8px] p-4 shadow-2xl backdrop-blur-md w-72 space-y-3 font-sans">
                            <div class="flex items-center justify-between border-b border-[#222222] pb-2">
                                <span id="calMonthYear" class="font-semibold text-xs text-white"></span>
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

                    <!-- Custom Dropdown: Waktu Mulai -->
                    <div class="space-y-1.5 relative custom-dropdown" id="dropdown-start_time">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Waktu Mulai</label>
                        <input type="hidden" id="start_time" name="start_time" value="08:30" required>
                        <button type="button" onclick="toggleCustomDropdown('start_time')" id="start_timeTrigger"
                                class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                            <span id="start_timeLabel">08:30 WIB</span>
                            <svg id="start_timeChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div id="start_timeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-y-auto max-h-56 py-1">
                            <!-- Dropdown Search Box -->
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
                            @foreach(['07:00','07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30'] as $slot)
                                <div onclick="selectCustomDropdown('start_time', '{{ $slot }}', '{{ $slot }} WIB')" data-value="{{ $slot }}" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $slot == '08:30' ? 'bg-[#1f1f1f] text-white font-medium' : '' }}">
                                    <span>{{ $slot }} WIB</span>
                                    <span class="check-icon text-[#50e3c2] text-xs {{ $slot == '08:30' ? '' : 'hidden' }}">✓</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Custom Dropdown: Waktu Selesai -->
                    <div class="space-y-1.5 relative custom-dropdown" id="dropdown-end_time">
                        <label class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Waktu Selesai</label>
                        <input type="hidden" id="end_time" name="end_time" value="10:30" required>
                        <button type="button" onclick="toggleCustomDropdown('end_time')" id="end_timeTrigger"
                                class="w-full bg-[#0a0a0a] border border-[#333333] hover:border-[#555555] text-white text-xs rounded-[6px] px-3 py-2 flex items-center justify-between focus:outline-none focus:border-white transition font-mono">
                            <span id="end_timeLabel">10:30 WIB</span>
                            <svg id="end_timeChevron" class="custom-dropdown-chevron w-3.5 h-3.5 text-[#888888] transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <div id="end_timeMenu" class="custom-dropdown-menu hidden absolute top-full left-0 right-0 mt-1.5 z-50 bg-[#111111] border border-[#333333] rounded-[6px] shadow-2xl overflow-y-auto max-h-56 py-1">
                            <!-- Dropdown Search Box -->
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
                            @foreach(['07:30','08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00'] as $slot)
                                <div onclick="selectCustomDropdown('end_time', '{{ $slot }}', '{{ $slot }} WIB')" data-value="{{ $slot }}" class="dropdown-item px-3 py-2 text-xs font-mono text-[#cccccc] hover:bg-[#1f1f1f] hover:text-white cursor-pointer flex items-center justify-between {{ $slot == '10:30' ? 'bg-[#1f1f1f] text-white font-medium' : '' }}">
                                    <span>{{ $slot }} WIB</span>
                                    <span class="check-icon text-[#50e3c2] text-xs {{ $slot == '10:30' ? '' : 'hidden' }}">✓</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Check Schedule Availability Button (SRS FR-PG-04) -->
                <div class="flex items-center gap-3 pt-1">
                    <button 
                        type="button" 
                        id="btnCheckSlot"
                        onclick="checkScheduleAvailability()" 
                        class="bg-[#171717] hover:bg-[#222222] text-white border border-[#333333] hover:border-[#555555] font-medium text-xs px-4 py-2 rounded-full transition active:scale-95 inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#0070f3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Cek Ketersediaan Jadwal
                    </button>
                    <span class="text-xs text-[#888888]">Klik untuk memvalidasi bentrokan dengan jadwal perkuliahan atau reservasi lain.</span>
                </div>

                <!-- Dynamic Verification Result Box -->
                <div id="availabilityResult" class="p-3.5 rounded-[6px] border text-xs hidden">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>

            <!-- Step 2: Activity Details / Usage Purpose (SRS FR-PGN-06) -->
            @if(request()->has('as_user') || request()->is('my*'))
                <div id="step2PurposeSection" class="pt-6 border-t border-[#333333] space-y-4">
                    <h3 class="text-xs font-mono uppercase tracking-wider text-[#888888] font-medium flex items-center gap-2">
                        <span class="w-4 h-4 rounded-[4px] bg-white text-black text-[10px] font-bold flex items-center justify-center">2</span>
                        Tujuan Penggunaan & Keperluan
                    </h3>

                    <div class="space-y-1.5">
                        <label for="purpose" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">
                            Tujuan Penggunaan <span class="text-[#ff453a]">*</span>
                        </label>
                        <textarea 
                            id="purpose" 
                            name="purpose" 
                            rows="3" 
                            required 
                            disabled
                            placeholder="Jalankan pengecekan ketersediaan jadwal terlebih dahulu untuk membuka isian tujuan penggunaan..."
                            class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] p-3 focus:outline-none focus:border-white transition placeholder-[#666666] disabled:opacity-50 disabled:cursor-not-allowed"
                        ></textarea>
                    </div>
                </div>
            @endif

            <!-- Action Area: Distinction between Pengunjung (Belum Login) vs Pengguna (Sudah Login) -->
            <div class="pt-6 border-t border-[#333333]">
                @if(request()->has('as_user') || request()->is('my*'))
                    <!-- User is Logged In: Can Submit directly once verified -->
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="text-xs text-[#a1a1a1]">
                            Permohonan Anda akan masuk ke antrian Petugas Sarpras dengan status 
                            <span class="font-mono text-[#f5a623] bg-[#2b1e00] px-1.5 py-0.5 rounded-[4px] border border-[#553c00]">Menunggu</span>.
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('facilities.show', array_merge(['facility' => $facility['id']], ['as_user' => 1])) }}" class="bg-[#171717] hover:bg-[#222222] text-[#a1a1a1] hover:text-white border border-[#333333] font-medium text-xs px-4 py-2 rounded-full transition">
                                Batal
                            </a>
                            <button 
                                type="submit" 
                                id="btnSubmitReservation"
                                disabled
                                class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-6 py-2.5 rounded-full transition flex items-center gap-2 active:scale-95 shadow-none disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Ajukan Reservasi Sekarang
                            </button>
                        </div>
                    </div>
                @else
                    <!-- User is Guest / Pengunjung: Prompt to Save Draft and Login (SRS FR-PG-06, only shown when available) -->
                    <div id="guestLoginPrompt" class="hidden p-4 rounded-[6px] bg-[#161616] border border-[#333333] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#50e3c2]"></span>
                                <h4 class="text-xs font-semibold text-white tracking-tight">Jadwal Tersedia! Masuk untuk Melanjutkan Reservasi.</h4>
                            </div>
                            <p class="text-xs text-[#a1a1a1]">
                                Fasilitas dan rentang jadwal yang Anda pilih akan disimpan sementara sebagai draft sesi.
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                onclick="saveDraftAndLogin()" 
                                class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-5 py-2.5 rounded-full transition flex items-center gap-2 active:scale-95 shadow-none whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Login untuk Melanjutkan
                            </button>
                        </div>
                    </div>
                    <div id="guestInitialNotice" class="text-xs text-[#888888] font-mono">
                        Silakan pilih tanggal dan jam di atas, lalu klik <strong>"Cek Ketersediaan Jadwal"</strong> untuk memeriksa ketersediaan slot.
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const isUser = {{ (request()->has('as_user') || request()->is('my*')) ? 'true' : 'false' }};

    // 1. Calendar State & Functions
    const initialDateStr = document.getElementById('reservation_date').value || '{{ date('Y-m-d') }}';
    const initParts = initialDateStr.split('-');
    let calCurrentYear = parseInt(initParts[0], 10);
    let calCurrentMonth = parseInt(initParts[1], 10) - 1;
    let calSelectedDate = initialDateStr;

    const MONTH_NAMES = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    function initCalendar() {
        renderCalendarGrid();
        updateDateDisplay();
    }

    function updateDateDisplay() {
        const input = document.getElementById('reservation_date');
        const display = document.getElementById('datePickerSelectedText');
        if (!calSelectedDate) {
            if (display) display.innerText = 'Pilih tanggal...';
            if (input) input.value = '';
        } else {
            const parts = calSelectedDate.split('-');
            const y = parts[0];
            const m = parseInt(parts[1], 10) - 1;
            const d = parseInt(parts[2], 10);
            if (display) display.innerText = d + ' ' + MONTH_NAMES[m].substr(0, 3) + ' ' + y;
            if (input) input.value = calSelectedDate;
        }
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

    function changeCalMonth(step) {
        calCurrentMonth += step;
        if (calCurrentMonth < 0) {
            calCurrentMonth = 11;
            calCurrentYear--;
        } else if (calCurrentMonth > 11) {
            calCurrentMonth = 0;
            calCurrentYear++;
        }
        renderCalendarGrid();
    }

    function renderCalendarGrid() {
        const monthYearEl = document.getElementById('calMonthYear');
        if (!monthYearEl) return;
        monthYearEl.innerText = MONTH_NAMES[calCurrentMonth] + ' ' + calCurrentYear;

        const grid = document.getElementById('calDaysGrid');
        if (!grid) return;
        grid.innerHTML = '';

        const firstDay = new Date(calCurrentYear, calCurrentMonth, 1).getDay();
        const offset = firstDay === 0 ? 6 : firstDay - 1;

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
        updateDateDisplay();
        closeDatePicker();
        invalidateVerification();
    }

    function clearDateSelection() {
        calSelectedDate = '';
        updateDateDisplay();
        closeDatePicker();
        invalidateVerification();
    }

    function selectTodayDate() {
        const today = new Date();
        const dStr = String(today.getDate()).padStart(2, '0');
        const mStr = String(today.getMonth() + 1).padStart(2, '0');
        calSelectedDate = today.getFullYear() + '-' + mStr + '-' + dStr;
        calCurrentMonth = today.getMonth();
        calCurrentYear = today.getFullYear();
        updateDateDisplay();
        closeDatePicker();
        invalidateVerification();
    }

    // 2. Custom Dropdown Select Logic
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
                    item.classList.add('bg-[#1f1f1f]', 'text-white', 'font-medium');
                    const check = item.querySelector('.check-icon');
                    if (check) check.classList.remove('hidden');
                } else {
                    item.classList.remove('bg-[#1f1f1f]', 'text-white', 'font-medium');
                    const check = item.querySelector('.check-icon');
                    if (check) check.classList.add('hidden');
                }
            });
            menu.classList.add('hidden');
        }
        if (chevron) chevron.classList.remove('rotate-180');

        invalidateVerification();
    }

    function closeAllPopups() {
        const cal = document.getElementById('calendarPopup');
        if (cal) cal.classList.add('hidden');

        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.custom-dropdown-chevron').forEach(c => c.classList.remove('rotate-180'));
    }

    // Global Click Outside Listener
    document.addEventListener('click', function(e) {
        const inDropdown = e.target.closest('.custom-dropdown');
        const inDatePicker = e.target.closest('.custom-datepicker');

        if (!inDropdown && !inDatePicker) {
            closeAllPopups();
        }
    });

    // Invalidation and Slot Check Handlers
    function invalidateVerification() {
        const resultBox = document.getElementById('availabilityResult');
        resultBox.classList.add('hidden');
        resultBox.innerHTML = '';

        if (!isUser) {
            const guestPrompt = document.getElementById('guestLoginPrompt');
            const guestNotice = document.getElementById('guestInitialNotice');
            if (guestPrompt) guestPrompt.classList.add('hidden');
            if (guestPrompt) guestPrompt.classList.remove('flex');
            if (guestNotice) guestNotice.classList.remove('hidden');
        } else {
            const purposeField = document.getElementById('purpose');
            const submitBtn = document.getElementById('btnSubmitReservation');
            if (purposeField) {
                purposeField.disabled = true;
                purposeField.placeholder = "Jalankan pengecekan ketersediaan jadwal terlebih dahulu untuk membuka isian tujuan penggunaan...";
            }
            if (submitBtn) submitBtn.disabled = true;
        }
    }

    function checkScheduleAvailability() {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const resultBox = document.getElementById('availabilityResult');

        if (startTime >= endTime) {
            resultBox.className = 'p-3.5 rounded-[6px] border border-[#550000] bg-[#2b0000] text-[#ff453a] text-xs block';
            resultBox.innerHTML = '<strong class="text-white block mb-0.5">Format Waktu Tidak Valid:</strong> Waktu selesai harus lebih besar daripada waktu mulai (minimal 1 slot 30 menit).';
            invalidateVerification();
            resultBox.classList.remove('hidden');
            return;
        }

        // Simulate conflict scenario if 11:00 is chosen
        if (startTime === '11:00') {
            resultBox.className = 'p-3.5 rounded-[6px] border border-[#550000] bg-[#2b0000] text-[#ff453a] text-xs block space-y-1';
            resultBox.innerHTML = '<strong class="text-white block mb-0.5">Jadwal Tidak Tersedia (Bentrok):</strong> Pada rentang waktu tersebut telah terdapat jadwal perkuliahan terkonfirmasi. Silakan pilih slot jam lain.';
            invalidateVerification();
            resultBox.classList.remove('hidden');
        } else {
            resultBox.className = 'p-3.5 rounded-[6px] border border-[#005522] bg-[#002b11] text-[#50e3c2] text-xs block space-y-1';
            resultBox.innerHTML = '<strong class="text-white block mb-0.5">✓ Jadwal Tersedia:</strong> Slot waktu ' + startTime + ' - ' + endTime + ' WIB bebas dari jadwal perkuliahan maupun reservasi lain.';
            resultBox.classList.remove('hidden');

            if (!isUser) {
                // For Visitor: Reveal "Login untuk Melanjutkan" button
                const guestPrompt = document.getElementById('guestLoginPrompt');
                const guestNotice = document.getElementById('guestInitialNotice');
                if (guestPrompt) {
                    guestPrompt.classList.remove('hidden');
                    guestPrompt.classList.add('flex');
                }
                if (guestNotice) guestNotice.classList.add('hidden');
            } else {
                // For User: Unlock purpose and submit button
                const purposeField = document.getElementById('purpose');
                const submitBtn = document.getElementById('btnSubmitReservation');
                if (purposeField) {
                    purposeField.disabled = false;
                    purposeField.placeholder = "Contoh: Perkuliahan pengganti Pemrograman Web Lanjut kelas TI-22, persiapan seminar, praktikum mandiri...";
                    purposeField.focus();
                }
                if (submitBtn) submitBtn.disabled = false;
            }
        }
    }

    function saveDraftAndLogin() {
        const date = document.getElementById('reservation_date').value;
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;

        // Store draft into sessionStorage (KEPUTUSAN-GLOBAL.md §3.9)
        sessionStorage.setItem('reservation_draft', JSON.stringify({
            facility_id: '{{ $facility['id'] }}',
            date: date,
            start_time: startTime,
            end_time: endTime
        }));

        window.location.href = "{{ route('login') }}?draft_facility={{ $facility['id'] }}";
    }

    // Initialize calendar and auto-restore draft if returned after login
    window.addEventListener('DOMContentLoaded', () => {
        initCalendar();

        const draftStr = sessionStorage.getItem('reservation_draft');
        if (draftStr && isUser) {
            try {
                const draft = JSON.parse(draftStr);
                if (draft.facility_id === '{{ $facility['id'] }}') {
                    if (draft.date) {
                        selectDate(draft.date);
                    }
                    if (draft.start_time) {
                        selectCustomDropdown('start_time', draft.start_time, draft.start_time + ' WIB');
                    }
                    if (draft.end_time) {
                        selectCustomDropdown('end_time', draft.end_time, draft.end_time + ' WIB');
                    }
                    checkScheduleAvailability();
                }
            } catch (e) {
                console.error("Error parsing reservation draft", e);
            }
        }
    });
</script>
@endpush

