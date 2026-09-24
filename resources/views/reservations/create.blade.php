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

                    <!-- Date Selection -->
                    <div class="space-y-1.5">
                        <label for="date" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Tanggal Pemakaian</label>
                        <input
                            type="date"
                            id="date"
                            name="date"
                            value="{{ $selectedDate }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-mono"
                            required
                        >
                    </div>

                    <!-- Time Slot Grid (Start & End Time) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="start_time" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Waktu Mulai (07.00 - 19.30)</label>
                            <input
                                type="time"
                                id="start_time"
                                name="start_time"
                                step="1800"
                                value="{{ $selectedStart }}"
                                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-mono"
                                required
                            >
                        </div>
                        <div class="space-y-1.5">
                            <label for="end_time" class="block font-mono text-[10px] uppercase tracking-wider text-[#888888] font-medium">Waktu Selesai (07.30 - 20.00)</label>
                            <input
                                type="time"
                                id="end_time"
                                name="end_time"
                                step="1800"
                                value="{{ $selectedEnd }}"
                                class="w-full bg-[#0a0a0a] border border-[#333333] text-white text-xs rounded-[6px] px-3.5 py-2.5 focus:outline-none focus:border-white transition font-mono"
                                required
                            >
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

                    <form method="POST" action="{{ route('reservations.store_draft') }}">
                        @csrf
                        <input type="hidden" name="facility_id" value="{{ $fId }}">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                        <input type="hidden" name="start_time" value="{{ $selectedStart }}">
                        <input type="hidden" name="end_time" value="{{ $selectedEnd }}">

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

                    <form method="POST" action="{{ route('user.reservations.index') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="facility_id" value="{{ $fId }}">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                        <input type="hidden" name="start_time" value="{{ $selectedStart }}">
                        <input type="hidden" name="end_time" value="{{ $selectedEnd }}">

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
                
                <dl class="space-y-2.5 text-xs border-t border-[#333333] pt-4 font-mono">
                    <div class="flex justify-between">
                        <dt class="text-[#888888]">Tipe:</dt>
                        <dd class="text-white capitalize">{{ $fTypeLabel }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-[#888888]">Lokasi:</dt>
                        <dd class="text-white">{{ $fLocation }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-[#888888]">Kapasitas:</dt>
                        <dd class="text-white">{{ !empty($fCapacity) ? $fCapacity . ' Org' : 'Unit Alat' }}</dd>
                    </div>
                </dl>
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
