# MODUL 4: TRIAGE & VERIFIKASI PETUGAS
**User Stories:**  
- **[US-08]** Dashboard antrean reservasi & laporan yang menunggu penanganan (Prioritas FIFO).  
- **[US-09]** Menyetujui/menolak reservasi manual dengan pencegahan bentrok jadwal otomatis (*double-booking prevention*) & *cascading auto-reject*.  
- **[US-10]** Pembatalan darurat reservasi yang sudah disetujui (kondisi insidental/force majeure) disertai alasan.

---

## 1. DEKONSTRUKSI LAPISAN BLADE VIEW (`resources/views/`)

### A. Berkas Acuan
1. `resources/views/staff/reservations/index.blade.php` (Antrean Kerja Reservasi Aktif)
2. `resources/views/staff/reservations/show.blade.php` (Panel Penanganan Reservasi & Status Badge)
3. `resources/views/staff/reservations/partials/reject-modal.blade.php` (Modal Penolakan Reservasi)
4. `resources/views/staff/reservations/partials/cancel-modal.blade.php` (Modal Pembatalan Mendesak Petugas)

### B. Analisis Direktif Blade & Form Attributes
Pada `resources/views/staff/reservations/show.blade.php`:
```blade
@extends('layouts.staff')

@section('title', 'Proses Reservasi #' . $reservation['reservation_number'] . ' · Petugas')

@section('content')
@php
    $statusVal = $reservation['status'] instanceof \BackedEnum ? $reservation['status']->value : (string) $reservation['status'];
    $resDate = $reservation['date'] instanceof \DateTimeInterface ? $reservation['date']->format('Y-m-d') : $reservation['date'];
@endphp
```

- `@extends('layouts.staff')`: Mengaitkan view ke layout master staf, memuat sidebar kerja, navigasi status antrean, dan breadcrumbs.
- `@php ... @endphp`: Normalisasi Enum PHP 8.4 (`ReservationStatus`) menjadi scalar string murni untuk meminimalkan beban komparasi di template.
- `@if($statusVal === 'menunggu') ... @elseif($statusVal === 'disetujui')`: Kontrol percabangan tombol aksi. Bila reservasi masih `menunggu`, tombol Setujui dan Tolak ditampilkan. Jika telah `disetujui`, tombol Pembatalan Mendesak ditampilkan.
- `@include('staff.reservations.partials.reject-modal')` & `@include('staff.reservations.partials.cancel-modal')`: Menggabungkan sub-view modal ke dalam DOM induk secara modular.

Form Persetujuan (Approval) di `show.blade.php`:
```blade
<form method="POST" action="{{ route('staff.reservations.approve', ['reservation' => $reservation['id']]) }}">
    @csrf
    <button type="submit" class="bg-white hover:bg-[#e6e6e6] text-black font-medium text-xs px-5 py-2 rounded-full transition flex items-center gap-1.5 active:scale-95 shadow-none cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        Setujui Reservasi
    </button>
</form>
```
- Metode HTTP `POST` mutlak digunakan karena persetujuan merubah state basis data secara permanen.
- `@csrf` disematkan guna melindungi request dari penyerangan pemalsuan permintaan antar situs.

### C. Logika Client-Side JavaScript
Pada `resources/views/staff/reservations/index.blade.php` (Penyaringan Cepat Tanpa Reload Server):
```javascript
function filterTable() {
    const query = searchQueue.value.toLowerCase().trim();
    const type = filterType.value.toLowerCase();
    const status = filterStatus.value.toLowerCase();

    rows.forEach(row => {
        const matchesSearch = !query || row.dataset.search.includes(query);
        const matchesType = !type || row.dataset.type === type;
        const matchesStatus = !status || row.dataset.status === status;

        row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
    });
}
```
Setiap elemen `<tr>` membawa atribut HTML5 `data-search`, `data-type`, dan `data-status`. Input pencarian memicu evaluasi instan pada DOM, menghilangkan overhead request jaringan ke server untuk pencarian antrean aktif.

---

## 2. BEDAH MIKRO KODE BACKEND BARIS PER BARIS

### A. Persetujuan & Deteksi Bentrok: `StaffReservationService::approve`
Berkas: `app/Services/StaffReservationService.php` (baris 104–188)

```php
104: public function approve(Reservation $reservation, string $staffId): Reservation
105: {
106:     $approvedReservation = DB::transaction(function () use ($reservation, $staffId): ?Reservation {
107:         $lockedReservation = Reservation::query()
108:             ->where('id', $reservation->id)
109:             ->lockForUpdate()
110:             ->firstOrFail();
111: 
112:         if ($lockedReservation->status !== ReservationStatus::Menunggu) {
113:             throw ValidationException::withMessages([
114:                 'approve' => 'Hanya permohonan dengan status Menunggu yang dapat disetujui.',
115:             ]);
116:         }
117: 
118:         if (! $lockedReservation->start_date_time->isFuture()) {
119:             $lockedReservation->update([
120:                 'status' => ReservationStatus::Kedaluwarsa,
121:                 'expiration_reason' => 'Waktu mulai reservasi telah terlampaui sebelum disetujui petugas.',
122:             ]);
123: 
124:             return null;
125:         }
126: 
127:         $lockedFacility = Facility::query()
128:             ->where('id', $lockedReservation->facility_id)
129:             ->lockForUpdate()
130:             ->firstOrFail();
131: 
132:         if (! $lockedFacility->is_active) {
133:             throw ValidationException::withMessages([
134:                 'approve' => 'Fasilitas tidak aktif pada master data sehingga tidak dapat disetujui.',
135:             ]);
136:         }
137: 
138:         if ($lockedFacility->operational_status !== OperationalStatus::DapatDigunakan) {
139:             throw ValidationException::withMessages([
140:                 'approve' => 'Fasilitas sedang dalam perbaikan sehingga tidak dapat disetujui.',
141:             ]);
142:         }
143: 
144:         // Conflict check with other approved or ongoing reservations
145:         $hasConflict = Reservation::query()
146:             ->where('id', '!=', $lockedReservation->id)
147:             ->where('facility_id', $lockedReservation->facility_id)
148:             ->where('date', $lockedReservation->date)
149:             ->whereIn('status', [ReservationStatus::Disetujui, ReservationStatus::Berlangsung])
150:             ->where('start_time', '<', $lockedReservation->end_time)
151:             ->where('end_time', '>', $lockedReservation->start_time)
152:             ->exists();
153: 
154:         if ($hasConflict) {
155:             throw ValidationException::withMessages([
156:                 'approve' => 'Jadwal bentrok dengan reservasi lain yang telah disetujui pada fasilitas ini.',
157:             ]);
158:         }
159: 
160:         // Approve this reservation
161:         $lockedReservation->update([
162:             'status' => ReservationStatus::Disetujui,
163:             'approved_by_id' => $staffId,
164:             'approved_at' => now(),
165:         ]);
166: 
167:         // Auto-reject any other pending reservations conflicting with this slot
168:         $conflictingPending = Reservation::query()
169:             ->where('id', '!=', $lockedReservation->id)
170:             ->where('facility_id', $lockedReservation->facility_id)
171:             ->where('date', $lockedReservation->date)
172:             ->where('status', ReservationStatus::Menunggu)
173:             ->where('start_time', '<', $lockedReservation->end_time)
174:             ->where('end_time', '>', $lockedReservation->start_time)
175:             ->get();
176: 
177:         $now = now();
178:         foreach ($conflictingPending as $pending) {
179:             $pending->update([
180:                 'status' => ReservationStatus::Ditolak,
181:                 'rejection_reason' => 'Jadwal fasilitas telah diberikan kepada pengajuan lain (' . $lockedReservation->reservation_number . ').',
182:                 'rejected_by_id' => $staffId,
183:                 'rejected_at' => $now,
184:             ]);
185:         }
186: 
187:         return $lockedReservation->fresh(['facility', 'user', 'approvedBy']);
188:     });
```

#### Kartu Analisis Baris:
- **Baris 106 (`DB::transaction`):** Menjamin kepatuhan ACID. Seluruh pembaruan status dan auto-reject berhasil secara serempak atau dibatalkan seutuhnya (*Rollback*) bila ada kegagalan.
- **Baris 109 & 129 (`lockForUpdate()`):** Memasang *Exclusive Row Lock (FOR UPDATE)* pada baris reservasi dan baris fasilitas di tabel MySQL InnoDB, mengeliminasi risiko persetujuan paralel bentrok (*Race Condition*).
- **Baris 118–125 (`start_date_time->isFuture()`):** Mencegah *Backdated Approval*. Permohonan yang jadwal mulainya telah berlalu otomatis diubah menjadi `Kedaluwarsa`.
- **Baris 150–151 (`start_time < new_end AND end_time > new_start`):** Teorema Perpotongan Dua Interval Waktu. Mendeteksi seluruh kemungkinan tumpang tindih waktu tanpa false-conflict di batas menit transisi.
- **Baris 168–185 (`Cascading Auto-Reject`):** Menolak secara otomatis pengajuan `Menunggu` lainnya yang tumpang tindih pada fasilitas tersebut disertai catatan nomor reservasi pemenang.

---

### B. Pembatalan Mendesak Petugas: `StaffReservationService::cancelUrgent`
Berkas: `app/Services/StaffReservationService.php` (baris 247–276)
```php
247: public function cancelUrgent(Reservation $reservation, string $reason, string $staffId): Reservation
248: {
249:     return DB::transaction(function () use ($reservation, $reason, $staffId): Reservation {
250:         $lockedReservation = Reservation::query()
251:             ->where('id', $reservation->id)
252:             ->lockForUpdate()
253:             ->firstOrFail();
254: 
255:         if ($lockedReservation->status !== ReservationStatus::Disetujui) {
256:             throw ValidationException::withMessages([
257:                 'cancel' => 'Hanya reservasi dengan status Disetujui yang dapat dibatalkan mendesak.',
258:             ]);
259:         }
260: 
261:         if (! $lockedReservation->start_date_time->isFuture()) {
262:             throw ValidationException::withMessages([
263:                 'cancel' => 'Reservasi yang sudah berlangsung atau selesai tidak dapat dibatalkan.',
264:             ]);
265:         }
266: 
267:         $lockedReservation->update([
268:             'status' => ReservationStatus::DibatalkanOlehPetugas,
269:             'cancellation_reason' => trim($reason),
270:             'cancelled_by_id' => $staffId,
271:             'cancelled_at' => now(),
272:         ]);
273: 
274:         return $lockedReservation->fresh(['facility', 'user', 'cancelledBy']);
275:     });
276: }
```
- Membatasi status asal hanya pada `Disetujui` dan waktu mulai harus di masa depan (`isFuture()`).
- Mencatat audit lengkap petugas yang membatalkan (`cancelled_by_id`), alasan force majeure (`cancellation_reason`), dan timestamp pembatalan (`cancelled_at`).

---

## 3. CODE DEFENSE & ORAL EXAMINATION SIMULATOR

### Uji 1: Penanganan Concurrency & Race Condition
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Dua petugas login bersamaan dan menyetujui dua permohonan ruangan yang saling bentrok pada detik yang sama. Mengapa sistem Anda tidak mungkin menghasilkan double booking?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Melalui penerapan **Pessimistic Locking (`lockForUpdate()`)** dan **ACID Database Transaction (`DB::transaction`)** di [app/Services/StaffReservationService.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Services/StaffReservationService.php#L106-L158). Ketika Petugas A memanggil `lockForUpdate()` pada fasilitas, mesin InnoDB MySQL memasang *Exclusive Row Lock*. Transaksi Petugas B yang mencoba menyetujui jadwal pada fasilitas yang sama diblokir hingga transaksi Petugas A selesai. Transaksi Petugas A berhasil mengubah status menjadi `Disetujui` dan secara otomatis menolak pengajuan Petugas B di baris 178. Begitu transaksi Petugas B memperoleh giliran, status reservasinya sudah bukan `Menunggu` lagi, memicu `ValidationException` di baris 112 dan membatalkan persetujuan kedua secara aman."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Pessimistic Concurrency Lock (lockForUpdate)]`, `[InnoDB Exclusive Row Lock]`, `[ACID Transaction Boundary]`, `[Cascading Auto-Reject]`.

---

### Uji 2: Pembuktian Matematis Deteksi Bentrok
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa formula bentrok waktu ditulis `start_time < new_end AND end_time > new_start` alih-alih klausa SQL `BETWEEN`?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Klausa `BETWEEN` bersifat inklusif ($\le \text{dan} \ge$) yang akan menghasilkan *false positive* jika Reservasi A selesai jam 10:00 dan Reservasi B mulai tepat jam 10:00. Formula kami menerapkan Teorema Perpotongan Interval Waktu: dua rentang $[S_1, E_1)$ dan $[S_2, E_2)$ beririsan jika dan hanya jika $S_1 < E_2 \land E_1 > S_2$. Formula ini menjamin seluruh kasus tumpang tindih terdeteksi dan memungkinkan pergantian penggunaan ruang di batas waktu secara presisi."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Interval Intersection Theorem]`, `[Half-Open Interval Boundary]`, `[False Positive Mitigation]`.

---

### Uji 3: Audit Trail Pembatalan Mendesak vs Hard Delete
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa pembatalan darurat oleh petugas tidak menggunakan perintah `DELETE` di database?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Karena sistem sarpras kampus mewajibkan **Immutable Audit Trail**. Jika record dihapus (*hard delete*), histori institusi hilang: pimpinan tidak dapat mengaudit siapa petugas yang membatalkan, jam berapa dibatalkan, dan apa alasan force majeure-nya. Data ini tetap disimpan dengan status `dibatalkan_oleh_petugas` agar dapat diaudit pada modul rekapitulasi (US-17)."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Immutable Audit Trail]`, `[Accountability & Traceability]`, `[Soft State Transition]`.

