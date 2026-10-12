# MODUL 2: SIKLUS RESERVASI PENGGUNA
**User Stories:**  
- **[US-03]** Mengajukan reservasi pada rentang waktu tertentu dengan agenda kegiatan.  
- **[US-04]** Membatalkan reservasi mandiri sebelum batas waktu (*cutoff time*).  
- **[US-05]** Melihat riwayat, status, dan rincian lengkap reservasi pribadi.

---

## 1. DEKONSTRUKSI LAPISAN BLADE VIEW (`resources/views/`)

### A. Berkas Acuan
1. `resources/views/reservations/create.blade.php` (Form Pengajuan Reservasi)
2. `resources/views/user/reservations/index.blade.php` (Daftar Riwayat Reservasi Personal)
3. `resources/views/user/reservations/show.blade.php` (Detail Reservasi & Trigger Pembatalan)
4. `resources/views/user/reservations/partials/cancel-modal.blade.php` (Modal Pembatalan Mandiri Pengguna)

### B. Analisis Direktif Blade
Pada `resources/views/reservations/create.blade.php`:
```blade
@extends('layouts.app')

@section('title', 'Ajukan Reservasi Fasilitas · Resvi')

@section('content')
<div class="max-w-[800px] mx-auto px-4 py-8">
    <form method="POST" action="{{ route('user.reservations.store') }}" class="space-y-6">
        @csrf

        <!-- Pemilihan Fasilitas -->
        <div>
            <label for="facility_id" class="block text-xs font-mono uppercase text-[#888888] mb-1">
                Fasilitas Sasaran <span class="text-[#ff453a]">*</span>
            </label>
            <select name="facility_id" id="facility_id" required class="...">
                <option value="">Pilih Fasilitas</option>
                @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}" {{ old('facility_id', request('facility_id')) == $facility->id ? 'selected' : '' }}>
                        {{ $facility->name }} ({{ $facility->location }})
                    </option>
                @endforeach
            </select>
            @error('facility_id')
                <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal & Slot Waktu -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <input type="date" name="date" id="res_date" min="{{ now()->toDateString() }}" value="{{ old('date') }}" required />
                @error('date') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="time" name="start_time" id="res_start" step="1800" value="{{ old('start_time') }}" required />
                @error('start_time') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="time" name="end_time" id="res_end" step="1800" value="{{ old('end_time') }}" required />
                @error('end_time') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Tujuan Penggunaan -->
        <div>
            <textarea name="purpose" rows="4" required placeholder="Jelaskan agenda peminjaman...">{{ old('purpose') }}</textarea>
            @error('purpose') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="...">Kirim Permohonan Reservasi</button>
    </form>
</div>
@endsection
```

- `@error('field_name') ... @enderror`: Direktif Blade untuk mengecek error session validasi dari FormRequest. Jika ada error, variabel `$message` diekstrak otomatis dan pesan kesalahan diletakkan tepat di bawah input terkait.
- `step="1800"`: Atribut HTML5 waktu yang membatasi pemilihan menit dalam kelipatan 30 menit (1800 detik = 30 menit), mengimplementasikan aturan bisnis slot setengah jam di level DOM.
- `min="{{ now()->toDateString() }}"`: Membatasi datepicker agar pemohon tidak dapat memilih tanggal lampau (*past date selection*) secara visual di peramban.

Pada `resources/views/user/reservations/show.blade.php`:
```blade
@php
    $canCancel = in_array($reservation->status->value, ['menunggu', 'disetujui']) 
                 && now()->lt($reservation->start_date_time);
@endphp

@if($canCancel)
    <button type="button" onclick="openCancelModal()" class="text-[#ff453a] hover:bg-[#2b0000] border border-[#550000] ...">
        Batalkan Reservasi
    </button>
@endif
```
- Menghitung keabsahan pembatalan (*Cutoff Boundary*) langsung dari objek Model Carbon `$reservation->start_date_time`. Jika waktu kegiatan telah tercapai atau status telah selesai/ditolak, tombol pembatalan sama sekali tidak dirender di DOM klien.

---

## 2. BEDAH MIKRO KODE BACKEND BARIS PER BARIS

### A. Pembuatan Reservasi: `UserReservationService::createReservation`
```php
22: public function createReservation(User $user, array $data): Reservation
23: {
24:     return DB::transaction(function () use ($user, $data) {
25:         /** @var Facility $facility */
26:         $facility = Facility::where('id', $data['facility_id'])->lockForUpdate()->firstOrFail();
27: 
28:         if (! $facility->is_active) {
29:             throw ValidationException::withMessages([
30:                 'facility_id' => ['Fasilitas yang dipilih tidak aktif.'],
31:             ]);
32:         }
33: 
34:         if ($facility->operational_status->value === 'dalam_perbaikan') {
35:             throw ValidationException::withMessages([
36:                 'facility_id' => ['Fasilitas sedang dalam perbaikan dan tidak dapat direservasi.'],
37:             ]);
38:         }
39: 
40:         // 1. Cek bentrokan jadwal server-side
41:         $availability = $this->availabilityService->check(
42:             facility: $facility,
43:             date: $data['date'],
44:             startTime: $data['start_time'],
45:             endTime: $data['end_time']
46:         );
47: 
48:         if (! $availability->isAvailable) {
49:             throw ValidationException::withMessages([
50:                 'date' => [$availability->message],
51:             ]);
52:         }
53: 
54:         // 2. Cek duplikasi reservasi Pengguna yang sama (BR-PGN-03)
55:         $formattedStart = strlen($data['start_time']) === 5 ? "{$data['start_time']}:00" : $data['start_time'];
56:         $formattedEnd = strlen($data['end_time']) === 5 ? "{$data['end_time']}:00" : $data['end_time'];
57: 
58:         $isDuplicate = Reservation::where('user_id', $user->id)
59:             ->where('facility_id', $facility->id)
60:             ->whereDate('date', $data['date'])
61:             ->where('start_time', $formattedStart)
62:             ->where('end_time', $formattedEnd)
63:             ->whereIn('status', [ReservationStatus::Menunggu->value, ReservationStatus::Disetujui->value])
64:                 ->exists();
65: 
66:         if ($isDuplicate) {
67:             throw ValidationException::withMessages([
68:                 'purpose' => ['Anda telah memiliki pengajuan reservasi yang sama pada jadwal ini yang masih berstatus Menunggu atau Disetujui.'],
69:             ]);
70:         }
71: 
72:         // 3. Generate Nomor Reservasi Unik: RSV-YYYYMMDD-XXXX
73:         $datePrefix = date('Ymd');
74:         $countToday = Reservation::whereDate('created_at', today())->count();
75:         $uniqueCode = str_pad((string) ($countToday + 1), 4, '0', STR_PAD_LEFT);
76:         $reservationNumber = "RSV-{$datePrefix}-{$uniqueCode}";
77: 
78:         // Safety loop for unique number
79:         while (Reservation::where('reservation_number', $reservationNumber)->exists()) {
80:             $uniqueCode = str_pad((string) (random_int(1000, 9999)), 4, '0', STR_PAD_LEFT);
81:             $reservationNumber = "RSV-{$datePrefix}-{$uniqueCode}";
82:         }
83: 
84:         // 4. Buat Reservation
85:         return Reservation::create([
86:             'reservation_number' => $reservationNumber,
87:             'user_id' => $user->id,
88:             'facility_id' => $facility->id,
89:             'date' => $data['date'],
90:             'start_time' => $formattedStart,
91:             'end_time' => $formattedEnd,
92:             'purpose' => $data['purpose'],
93:             'status' => ReservationStatus::Menunggu,
94:         ]);
95:     });
96: }
```

#### Kartu Analisis Baris:
- **Baris 26 (`Facility::where(...)->lockForUpdate()`):** Pessimistic Lock pada baris fasilitas. Menjamin data ketersediaan dan status operasional yang dibaca tidak sedang diubah oleh transaksi paralel staf atau admin.
- **Baris 58–65 (Aturan BR-PGN-03 - Pencegahan Spam / Duplikasi Pemohon):** Query memeriksa apakah pemohon yang sama telah memiliki reservasi aktif (`Menunggu` atau `Disetujui`) pada slot yang identik. Ini mencegah perilaku spam klik ganda (*double submission*) dari peramban klien.
- **Baris 72–82 (Algoritma Penomoran Tiket `RSV-YYYYMMDD-XXXX`):** Format nomor terstruktur diawali tanggal ISO untuk auditabilitas visual. Menggunakan `str_pad` dengan padding 4 digit dan ditunjang *safety loop* berbasis `random_int(1000, 9999)` jika terjadi *collision* pada detik yang sama di dalam database.
- **Baris 93 (`status => ReservationStatus::Menunggu`):** Status awal permohonan selalu diinisialisasi sebagai `Menunggu` (State Machine Entry Point).

---

### B. Pembatalan Mandiri: `UserReservationService::cancelReservation`
```php
101: public function cancelReservation(User $user, Reservation $reservation, string $reason): Reservation
102: {
103:     if ($reservation->user_id !== $user->id) {
104:         abort(403, 'Anda tidak memiliki hak akses untuk membatalkan reservasi ini.');
105:     }
106: 
107:     if (! in_array($reservation->status, [ReservationStatus::Menunggu, ReservationStatus::Disetujui], true)) {
108:         throw ValidationException::withMessages([
109:             'cancellation_reason' => ['Hanya reservasi berstatus Menunggu atau Disetujui yang dapat dibatalkan.'],
110:         ]);
111:     }
112: 
113:     $dateStr = $reservation->date instanceof \DateTimeInterface ? $reservation->date->format('Y-m-d') : (string) $reservation->date;
114:     $startDateTime = Carbon::parse($dateStr . ' ' . $reservation->start_time);
115: 
116:     if (now()->gte($startDateTime)) {
117:         throw ValidationException::withMessages([
118:             'cancellation_reason' => ['Reservasi tidak dapat dibatalkan karena waktu mulai penggunaan telah tercapai atau terlewat.'],
119:         ]);
120:     }
121: 
122:     $reservation->update([
123:         'status' => ReservationStatus::DibatalkanOlehPengguna,
124:         'cancellation_reason' => trim($reason),
125:     ]);
126: 
127:     return $reservation->fresh();
128: }
```

#### Kartu Analisis Baris:
- **Baris 103–105 (`$reservation->user_id !== $user->id`):** Otorisasi horizontal (*Horizontal Privilege Escalation Prevention*). Memastikan pengguna tidak dapat membatalkan reservasi milik mahasiswa/dosen lain melalui manipulasi parameter ID pada URL (`Insecure Direct Object Reference / IDOR Mitigation`).
- **Baris 116–120 (`now()->gte($startDateTime)`):** Penegakan aturan batas waktu (*Cutoff Time Enforcement*). Sekalipun pengguna merekayasa request HTTP melalui cURL/Postman untuk menembus form yang disembunyikan di browser, validasi backend server-side akan memotong eksekusi secara absolut.

---

## 3. CODE DEFENSE & ORAL EXAMINATION SIMULATOR

### Uji 1: Penanganan Akses Ilegal Antar Pengguna (IDOR Prevention)
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Jika Mahasiswa A mengetahui ID Reservasi milik Mahasiswa B (misal ID 45), apa yang mencegah Mahasiswa A mengirimkan request `POST /my/reservations/45/cancel` untuk membatalkan reservasi milik Mahasiswa B?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Aplikasi dilindungi oleh pengecekan otorisasi multi-layer di backend:  
> 1. Pada [routes/web.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/routes/web.php#L43), endpoint dibungkus middleware `['auth', 'role:pengguna']` sehingga pemanggil harus memiliki sesi login valid.  
> 2. Pada lapisan service di [app/Services/UserReservationService.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Services/UserReservationService.php#L103-L105), baris pertama metode `cancelReservation` memeriksa secara ketat: `if ($reservation->user_id !== $user->id) { abort(403); }`.  
> Jika ID pengguna di sesi autentikasi tidak identik dengan foreign key `user_id` pada entitas database, eksekusi langsung dihentikan dengan status HTTP 403 Forbidden sebelum perubahan data apapun terjadi. Ini mengeliminasi celah kerentanan *Insecure Direct Object Reference (IDOR)* secara mutlak."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[IDOR (Insecure Direct Object Reference)]`, `[Horizontal Privilege Escalation Mitigation]`, `[Ownership Guard Verification]`, `[HTTP 403 Forbidden Abort]`.

---

### Uji 2: Validasi Slot Waktu Setengah Jam & Jam Operasional Kampus
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Bagaimana Anda memastikan pengguna tidak memasukkan jam sembarangan, misalnya jam 02:15 dini hari atau durasi ganjil seperti 08:12 sampai 08:47?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Kami menegakkan validasi dua tingkat:  
> 1. Di lapisan browser DOM (`reservations/create.blade.php`), input waktu dibatasi atribut `step="1800"` (kelipatan 30 menit).  
> 2. Validasi penentu utama berada di lapisan backend pada [app/Http/Requests/User/StoreReservationRequest.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Http/Requests/User/StoreReservationRequest.php) yang menginjeksi dua Custom Validation Rules: [`HalfHourSlotRule`](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Rules/HalfHourSlotRule.php) dan [`OperationalHoursRule`](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Rules/OperationalHoursRule.php). `HalfHourSlotRule` mengekstrak menit dan detik waktu (`Carbon::parse($time)->minute % 30 === 0 && second === 0`), sedangkan `OperationalHoursRule` memvalidasi rentang waktu berada dalam jam operasional kampus (07:00–20:00). Jika melanggar, request ditolak dengan kode validasi 422."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Custom Validation Rules]`, `[HalfHourSlotRule (Modulo 30)]`, `[OperationalHoursRule Boundary]`, `[Defense-in-Depth Validation]`.

---

### Uji 3: Penanganan Duplikasi Pengajuan (*Double Click Prevention*)
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Bagaimana jika jaringan lambat dan pemohon mengklik tombol submit berkali-kali secara cepat sehingga browser mengirim 3 request HTTP `POST` secara paralel?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Sistem kami menangani skenario ini di level database transaction dan service logic:  
> Seluruh proses pengajuan dibungkus dalam `DB::transaction` dengan `Facility::lockForUpdate()`. Begitu request pertama masuk, baris fasilitas dikunci. Di baris 58–70 [UserReservationService.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Services/UserReservationService.php#L58-L70), sistem mengeksekusi pengecekan duplikasi: `Reservation::where('user_id', $user->id)->where('facility_id', ...)->whereDate('date', ...)->where('start_time', ...)->whereIn('status', ['menunggu', 'disetujui'])->exists()`.  
> Ketika request kedua dan ketiga yang tertahan antrean lock akhirnya berjalan, query duplikasi ini langsung mendeteksi reservasi yang baru saja dibuat oleh request pertama, melempar `ValidationException`, dan membatalkan transaksi tanpa membuat data ganda."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Idempotent Submission]`, `[Pessimistic Facility Lock]`, `[Duplicate Pre-existence Query (BR-PGN-03)]`, `[Rollback on ValidationException]`.

