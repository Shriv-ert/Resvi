# MODUL 7: MASTER DATA & ANALYTICS
**User Stories:**  
- **[US-16]** Pengelolaan master fasilitas (Tambah, Edit, Deaktivasi dengan Guard Reservasi Aktif, Reaktivasi).  
- **[US-17]** Rekapitulasi okupansi fasilitas & frekuensi kerusakan dengan ekspor dokumen multi-format (CSV, Excel, PDF).

---

## 1. DEKONSTRUKSI LAPISAN BLADE VIEW (`resources/views/`)

### A. Berkas Acuan
1. `resources/views/admin/facilities/index.blade.php` (Manajemen Master Fasilitas)
2. `resources/views/admin/facilities/create.blade.php` & `edit.blade.php` (Form Master Fasilitas & Upload Foto)
3. `resources/views/admin/facilities/partials/deactivate-modal.blade.php` (Modal Deaktivasi Berguard)
4. `resources/views/admin/recaps/index.blade.php` (Dashboard Analytics, Metrik Okupansi & Kerusakan)
5. `resources/views/admin/recaps/pdf.blade.php` (Template Cetak Resmi DomPDF)

### B. Analisis Direktif Blade & Form Rekapitulasi
Pada `resources/views/admin/recaps/index.blade.php`:
```blade
@extends('layouts.admin')

@section('title', 'Rekapitulasi Sarpras · Administrator')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 py-8">
    <!-- Filter Bar Metrik & Periode -->
    <form method="GET" action="{{ route('admin.recaps.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 p-4 bg-[#111111] border border-[#333333] rounded-[6px]">
        <select name="month" class="...">
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ $period['month'] == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->translatedFormat('F') }}
                </option>
            @endforeach
        </select>

        <select name="year" class="...">
            @foreach(range(now()->year - 2, now()->year + 1) as $y)
                <option value="{{ $y }}" {{ $period['year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>

        <select name="metric" class="...">
            <option value="occupancy" {{ $metric === 'occupancy' ? 'selected' : '' }}>Tingkat Okupansi</option>
            <option value="damage" {{ $metric === 'damage' ? 'selected' : '' }}>Frekuensi Kerusakan</option>
        </select>

        <select name="group_by" class="...">
            <option value="facility" {{ $groupBy === 'facility' ? 'selected' : '' }}>Per Fasilitas</option>
            <option value="location" {{ $groupBy === 'location' ? 'selected' : '' }}>Per Gedung / Lokasi</option>
        </select>

        <button type="submit" class="...">Terapkan Filter</button>
    </form>

    <!-- Export Action Dropdown / Toolbar -->
    <div class="flex items-center justify-end gap-2 mt-4">
        <a href="{{ route('admin.recaps.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="...">
            Unduh Dokumen PDF
        </a>
        <a href="{{ route('admin.recaps.export', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="...">
            Unduh Excel (.xlsx)
        </a>
        <a href="{{ route('admin.recaps.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="...">
            Unduh CSV
        </a>
    </div>
</div>
@endsection
```

- `array_merge(request()->query(), ['format' => '...'])`: Menggabungkan seluruh query string filter aktif (`month`, `year`, `metric`, `group_by`) dengan parameter format ekspor, memastikan file yang diunduh mencerminkan persis data yang sedang dilihat di layar.

Template PDF di `resources/views/admin/recaps/pdf.blade.php`:
```blade
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10pt; line-height: 1.4; }
        .kop { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 16px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>SISTEM SARANA PRASARANA KAMPUS RESVI</h2>
        <h3>LAPORAN REKAPITULASI {{ strtoupper($data['metric'] === 'occupancy' ? 'OKUPANSI' : 'KERUSAKAN') }}</h3>
        <p>Periode: {{ $data['period']['month_name'] }} {{ $data['period']['year'] }}</p>
    </div>
    ...
</body>
</html>
```
- Spesifikasi Cetak DomPDF: Menggunakan CSS murni yang kompatibel dengan parser DomPDF (tabel dengan `border-collapse`, font standar sans-serif, dan pemisah halaman `.page-break`).

---

## 2. BEDAH MIKRO KODE BACKEND BARIS PER BARIS

### A. Deaktivasi Berguard: `FacilityService::deactivateFacility`
Berkas: `app/Services/FacilityService.php` (baris 144–202)

```php
144: public function deactivateFacility(Facility $facility, string $reason, string $adminId): void
145: {
146:     if (! $facility->is_active) {
147:         return;
148:     }
149: 
150:     // Guard Check: Check for active reservations
151:     $hasActiveReservations = $facility->reservations()
152:         ->whereIn('status', [ReservationStatus::Disetujui, ReservationStatus::Berlangsung])
153:         ->exists();
154: 
155:     if ($hasActiveReservations) {
156:         throw ValidationException::withMessages([
157:             'deactivate' => 'Fasilitas tidak dapat dinonaktifkan karena masih memiliki reservasi yang telah disetujui atau sedang berlangsung.',
158:         ]);
159:     }
160: 
161:     DB::transaction(function () use ($facility, $reason, $adminId): void {
162:         $facility->update([
163:             'is_active' => false,
164:         ]);
165: 
166:         // Auto-reject future pending reservations; mark past pending ones as expired
167:         $pendingReservations = $facility->reservations()
168:             ->where('status', ReservationStatus::Menunggu)
169:             ->get();
170: 
171:         $now = now();
172: 
173:         foreach ($pendingReservations as $reservation) {
174:             $dateString = $reservation->date instanceof Carbon ? $reservation->date->format('Y-m-d') : (string) $reservation->date;
175:             $startDateTime = Carbon::parse($dateString.' '.$reservation->start_time);
176: 
177:             if ($startDateTime->isFuture()) {
178:                 $reservation->update([
179:                     'status' => ReservationStatus::Ditolak,
180:                     'rejection_reason' => 'Fasilitas dinonaktifkan oleh administrator sistem: '.trim($reason),
181:                     'rejected_by_id' => $adminId,
182:                     'rejected_at' => $now,
183:                 ]);
184:             } else {
185:                 $reservation->update([
186:                     'status' => ReservationStatus::Kedaluwarsa,
187:                     'expiration_reason' => 'Jadwal telah dimulai sebelum persetujuan.',
188:                 ]);
189:             }
190:         }
191: 
192:         FacilityStatusLog::query()->create([
193:             'facility_id' => $facility->id,
194:             'changed_by_id' => $adminId,
195:             'change_type' => FacilityStatusChangeType::IsActive,
196:             'old_value' => '1',
197:             'new_value' => '0',
198:             'reason' => trim($reason),
199:             'changed_at' => $now,
200:         ]);
201:     });
202: }
```

#### Kartu Analisis Baris:
- **Baris 151–159 (Guard Integritas Operasional):** Admin tidak diizinkan mendonaktifkan fasilitas dari master data jika masih ada komitmen acara yang berstatus `Disetujui` atau `Berlangsung`. Ini mencegah kekacauan operasional di mana mahasiswa telah memegang izin resmi namun ruangannya tiba-tiba dihapus dari master sistem.
- **Baris 167–190 (Pembersihan Antrean Menunggu):** Seluruh pengajuan `Menunggu` otomatis dibatalkan/ditolak dengan alasan resmi deaktivasi master data.

---

### B. Kalkulasi Okupansi & Ekspor Streamed: `RecapService.php`
Berkas: `app/Services/RecapService.php` (baris 78–86 & baris 417–443)

```php
78: $facilityUsageMap = Reservation::query()
79:     ->where('status', ReservationStatus::Selesai)
80:     ->whereDate('date', '>=', $period['start_date'])
81:     ->whereDate('date', '<=', $period['end_date'])
82:     ->selectRaw('facility_id, SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time))) / 3600 as used_hours')
83:     ->groupBy('facility_id')
84:     ->pluck('used_hours', 'facility_id')
85:     ->map(fn ($hours) => round((float) $hours, 2))
86:     ->all();
```

```php
417: return response()->stream(function () use ($data): void {
418:     $handle = fopen('php://output', 'w');
419:     fputcsv($handle, ['LAPORAN REKAPITULASI — RESVI']);
420:     // ... Loop menulis baris data CSV ...
421:     fclose($handle);
422: }, 200, $headers);
```

#### Kartu Analisis Baris:
- **Baris 82 (`selectRaw SUM(TIME_TO_SEC(TIMEDIFF(...))) / 3600`):** Agregasi durasi penggunaan ruangan dieksekusi langsung oleh mesin SQL MySQL `ppk_db`. Menghitung selisih waktu detik (`TIMEDIFF`), mengonversi ke detik absolut (`TIME_TO_SEC`), lalu menjumlahkan dan membagi 3600 untuk mendapatkan total jam pemakaian secara presisi.
- **Baris 417–422 (`response()->stream(...)` dengan `php://output`):** Ekspor file CSV menggunakan teknik *HTTP Response Streaming*. Data ditulis langsung ke socket output HTTP tanpa membuat berkas sementara di hard disk server dan tanpa menampung seluruh teks CSV di RAM PHP.

---

## 3. CODE DEFENSE & ORAL EXAMINATION SIMULATOR

### Uji 1: Integritas Guard Deaktivasi Fasilitas (US-16)
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa pada proses penonaktifan fasilitas (US-16), sistem menolak aksi jika masih ada reservasi disetujui, padahal pada proses pengalihan ke 'Dalam Perbaikan' (US-12), sistem memperbolehkan aksi dan otomatis membatalkannya?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Ada perbedaan fundamental antara **Kondisi Force Majeure Darurat** dan **Perubahan Master Data**:  
> 1. Pada status **Dalam Perbaikan (US-12)**, terjadi kendala fisik darurat (misal korsleting, kebocoran) yang membahayakan keselamatan civitas. Maka dari itu, pembatalan darurat otomatis (*emergency cancel*) wajib terjadi secara instan demi keselamatan pengguna di lapangan.  
> 2. Pada status **Deaktivasi Master Data (US-16)**, ini adalah keputusan administratif terencana oleh Administrator (misal ruangan akan dialihfungsikan menjadi kantor). Admin tidak boleh seenaknya menghapus ruangan dari master data sebelum menghormati komitmen jadwal civitas yang telah disetujui sebelumnya. Admin harus menyelesaikan atau merelokasi reservasi yang ada terlebih dahulu."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Administrative vs Emergency State Boundary]`, `[Contractual Obligation / Commitment Guarantee]`, `[Safety-first Force Majeure Override]`.

---

### Uji 2: Agregasi Tingkat Okupansi Database-Level vs PHP Loop
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa Anda menghitung durasi penggunaan reservasi di `RecapService` menggunakan fungsi SQL `SUM(TIME_TO_SEC(TIMEDIFF(end_time, start_time)))` alih-alih menarik seluruh model `Reservation` lalu menghitungnya dengan Carbon di perulangan PHP?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Jika dihitung menggunakan perulangan PHP (*In-Memory Aggregation*), server harus menghidupkan ribuan objek model Eloquent ke dalam memori RAM PHP (*Memory Exhaustion / Out of Memory Crash*).  
> Dengan mendelegasikan perhitungan durasi ke mesin MySQL via `selectRaw SUM(TIME_TO_SEC(TIMEDIFF(...)))`, database MySQL mengeksekusi perhitungan matematika secara native di kernel engine dan hanya mengembalikan satu baris agregat angka `used_hours` per fasilitas. Ini menghemat penggunaan RAM PHP hingga lebih dari 95% dan memangkas waktu eksekusi dari detik menjadi milidetik."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Database-Level Aggregation]`, `[Memory Exhaustion Prevention]`, `[MySQL Native Date Functions]`, `[O(1) Memory Footprint]`.

---

### Uji 3: Ekspor Streamed CSV vs File Buffer
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa ekspor CSV di `RecapService::exportCsv` menggunakan `response()->stream()` dan `fopen('php://output', 'w')` daripada menulis file ke `storage/app/` lalu mengirimnya dengan `response()->download()`?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Pendekatan `response()->download()` memerlukan dua tahap yang tidak efisien: pertama mengalokasikan ruang disk dan menulis berkas fisik ke storage, kedua membacanya kembali untuk dikirim ke klien, lalu menghapusnya dengan cron/cleaner. Jika disk kontainer penuh, ekspor akan gagal (*Disk Space Exhaustion*).  
> Dengan `response()->stream()` ke pointer `php://output`, data di-stream secara langsung bit per bit ke socket jaringan HTTP peramban klien (*chunked transfer encoding*). Server tidak menggunakan ruang disk penyimpanan sama sekali dan memori yang digunakan tetap konstan berapa pun jumlah baris yang diekspor."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[HTTP Response Streaming]`, `[php://output Stream Pointer]`, `[Zero Disk Footprint]`, `[Chunked Transfer Encoding]`.

