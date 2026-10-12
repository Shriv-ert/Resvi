# MODUL 5: PEMELIHARAAN & STATUS FASILITAS
**User Stories:**  
- **[US-11]** Pembaruan status laporan kendala (Baru $\rightarrow$ Diproses $\rightarrow$ Selesai / Ditolak) beserta catatan resolusi resmi.  
- **[US-12]** Menandai fasilitas berstatus 'Dalam Perbaikan' saat ditangani dan mengembalikan ke status aktif ('Dapat Digunakan') setelah tuntas beserta cascading cancellation.

---

## 1. DEKONSTRUKSI LAPISAN BLADE VIEW (`resources/views/`)

### A. Berkas Acuan
1. `resources/views/staff/reports/show.blade.php` (Detail Penanganan Laporan Kendala)
2. `resources/views/staff/reports/partials/decision-modal.blade.php` (Modal Resolusi & Penolakan Laporan)
3. `resources/views/staff/facilities/index.blade.php` (Katalog Status Operasional Petugas)
4. `resources/views/staff/facilities/maintenance.blade.php` (Daftar Khusus Fasilitas Dalam Perbaikan)
5. `resources/views/staff/facilities/partials/status-modal.blade.php` (Modal Pembaruan Status Operasional Fasilitas)

### B. Analisis Direktif Blade & Modal Disposisi
Pada `resources/views/staff/reports/show.blade.php`:
```blade
@extends('layouts.staff')

@section('title', 'Tindak Lanjut Laporan #' . $report->report_number . ' · Petugas')

@section('content')
<div class="max-w-[1000px] mx-auto px-4 py-8">
    <!-- Status Indicator -->
    @if($report->status->value === 'baru')
        <form method="POST" action="{{ route('staff.reports.start-process', $report->id) }}">
            @csrf
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-full ...">
                Mulai Proses Investigasi & Penanganan
            </button>
        </form>
    @elseif($report->status->value === 'diproses')
        <div class="flex items-center gap-3">
            <button type="button" onclick="openDecisionModal('resolve')" class="bg-green-600 hover:bg-green-700 text-white text-xs px-4 py-2 rounded-full ...">
                Tandai Selesai Diperbaiki
            </button>
            <button type="button" onclick="openDecisionModal('reject')" class="bg-red-600 hover:bg-red-700 text-white text-xs px-4 py-2 rounded-full ...">
                Tolak Laporan
            </button>
        </div>
    @endif
</div>
@include('staff.reports.partials.decision-modal')
@endsection
```

- Kontrol Transisi Status (*Finite State Machine*): Tombol aksi ditampilkan secara berjenjang. Dari `Baru`, petugas diwajibkan mengklik `Mulai Proses` terlebih dahulu. Opsi penyelesaian (`resolve`) atau penolakan (`reject`) hanya dimunculkan saat laporan berada pada status `Diproses`.
- `@include('staff.reports.partials.decision-modal')`: Menggunakan satu modal dinamis yang dapat berubah fungsi (mode 'resolve' atau mode 'reject') berdasarkan parameter JavaScript.

### C. Logika Client-Side JavaScript (Dynamic Decision Modal)
Pada `decision-modal.blade.php`:
```javascript
function openDecisionModal(actionType) {
    const modal = document.getElementById('decisionModal');
    const form = document.getElementById('decisionForm');
    const title = document.getElementById('decisionTitle');
    const label = document.getElementById('decisionLabel');
    const textarea = document.getElementById('decisionNotes');

    if (actionType === 'resolve') {
        title.textContent = 'Konfirmasi Penyelesaian Kendala';
        label.textContent = 'Catatan Resolusi / Tindakan Perbaikan *';
        textarea.placeholder = 'Tuliskan rincian perbaikan yang telah dilakukan (misal: Lampu proyektor telah diganti baru)...';
        form.action = "{{ route('staff.reports.resolve', $report->id) }}";
    } else {
        title.textContent = 'Tolak Laporan Kendala';
        label.textContent = 'Alasan Penolakan Laporan *';
        textarea.placeholder = 'Tuliskan alasan penolakan (misal: Kerusakan tidak ditemukan saat investigasi)...';
        form.action = "{{ route('staff.reports.reject', $report->id) }}";
    }

    modal.classList.remove('hidden');
}
```
JavaScript memanipulasi atribut `form.action`, judul dialog, dan placeholder secara dinamis sebelum menampilkan overlay modal, menghindari duplikasi elemen modal di DOM.

---

## 2. BEDAH MIKRO KODE BACKEND BARIS PER BARIS

### A. Alur Disposisi Laporan: `StaffReportService.php`
```php
85: public function startProcess(Report $report, string $staffId): Report
86: {
87:     return DB::transaction(function () use ($report, $staffId): Report {
88:         $lockedReport = Report::query()->where('id', $report->id)->lockForUpdate()->firstOrFail();
89: 
90:         if ($lockedReport->status !== ReportStatus::Baru) {
91:             throw ValidationException::withMessages([
92:                 'start_process' => 'Hanya laporan berstatus Baru yang dapat diproses.',
93:             ]);
94:         }
95: 
96:         $lockedReport->update([
97:             'status' => ReportStatus::Diproses,
98:             'processed_by_id' => $staffId,
99:             'processed_at' => now(),
100:         ]);
101: 
102:         return $lockedReport->fresh(['facility', 'reporter', 'processedBy']);
103:     });
104: }
```
```php
112: public function resolve(Report $report, string $resolutionNotes, string $staffId): Report
113: {
114:     return DB::transaction(function () use ($report, $resolutionNotes, $staffId): Report {
115:         $lockedReport = Report::query()->where('id', $report->id)->lockForUpdate()->firstOrFail();
116: 
117:         if ($lockedReport->status !== ReportStatus::Diproses) {
118:             throw ValidationException::withMessages([
119:                 'resolve' => 'Hanya laporan yang berstatus Diproses yang dapat diselesaikan.',
120:             ]);
121:         }
122: 
123:         $lockedReport->update([
124:             'status' => ReportStatus::Selesai,
125:             'resolution_notes' => trim($resolutionNotes),
126:             'resolved_by_id' => $staffId,
127:             'resolved_at' => now(),
128:         ]);
129: 
130:         return $lockedReport->fresh(['facility', 'reporter', 'resolvedBy']);
131:     });
132: }
```

#### Kartu Analisis Baris:
- **Baris 90–94 & 117–121 (State Guard Integrity):** Menegakkan urutan siklus hidup laporan: `Baru` $\rightarrow$ `Diproses` $\rightarrow$ `Selesai` atau `Ditolak`. Petugas tidak diperbolehkan langsung menandai `Selesai` dari status `Baru` tanpa melalui investigasi `Diproses`.
- **Baris 98–99 & 126–127 (Petugas Penanggung Jawab Audit):** Merekam akun staf penanggung jawab (`processed_by_id`, `resolved_by_id`) beserta waktu stempel audit, mewujudkan akuntabilitas operasional.

---

### B. Pengalihan Status 'Dalam Perbaikan' & Cascading: `StaffFacilityService.php`
Berkas: `app/Services/StaffFacilityService.php` (baris 53–142)

```php
60: return DB::transaction(function () use ($facility, $newStatus, $reason, $staffId, $reportId): Facility {
61:     $lockedFacility = Facility::query()->where('id', $facility->id)->lockForUpdate()->firstOrFail();
62: 
63:     if ($lockedFacility->operational_status === $newStatus) {
64:         throw ValidationException::withMessages([
65:             'operational_status' => 'Fasilitas sudah berada pada status operasional ' . $newStatus->label() . '.',
66:         ]);
67:     }
68: 
69:     $oldValue = $lockedFacility->operational_status->value;
70:     $newValue = $newStatus->value;
71:     $now = now();
72: 
73:     $lockedFacility->update([
74:         'operational_status' => $newStatus,
75:     ]);
76: 
77:     FacilityStatusLog::query()->create([
78:         'facility_id' => $lockedFacility->id,
79:         'changed_by_id' => $staffId,
80:         'change_type' => FacilityStatusChangeType::OperationalStatus,
81:         'old_value' => $oldValue,
82:         'new_value' => $newValue,
83:         'reason' => trim($reason),
84:         'report_id' => $reportId ?: null,
85:         'changed_at' => $now,
86:     ]);
87: 
88:     // Cascading effects when entering maintenance (FR-PTG-10)
89:     if ($newStatus === OperationalStatus::DalamPerbaikan) {
90:         // 1. Auto-reject future pending reservations; past pending becomes expired
91:         $pendingReservations = $lockedFacility->reservations()
92:             ->where('status', ReservationStatus::Menunggu)
93:             ->get();
94: 
95:         foreach ($pendingReservations as $pending) {
96:             if ($pending->start_date_time->isFuture()) {
97:                 $pending->update([
98:                     'status' => ReservationStatus::Ditolak,
99:                     'rejection_reason' => 'Fasilitas dialihkan ke status dalam perbaikan: ' . trim($reason),
100:                     'rejected_by_id' => $staffId,
101:                     'rejected_at' => $now,
102:                 ]);
103:             } else {
104:                 $pending->update([
105:                     'status' => ReservationStatus::Kedaluwarsa,
106:                     'expiration_reason' => 'Waktu mulai reservasi telah terlampaui sebelum disetujui petugas.',
107:                 ]);
108:             }
109:         }
110: 
111:         // 2. Auto-cancel future approved reservations
112:         $approvedReservations = $lockedFacility->reservations()
113:             ->where('status', ReservationStatus::Disetujui)
114:             ->get();
115: 
116:         foreach ($approvedReservations as $approved) {
117:             if ($approved->start_date_time->isFuture()) {
118:                 $approved->update([
119:                     'status' => ReservationStatus::DibatalkanOlehPetugas,
120:                     'cancellation_reason' => 'Fasilitas dialihkan ke status dalam perbaikan: ' . trim($reason),
121:                     'cancelled_by_id' => $staffId,
122:                     'cancelled_at' => $now,
123:                 ]);
124:             }
125:         }
126:     }
127: 
128:     return $lockedFacility->fresh(['photos', 'statusLogs']);
129: });
```

#### Kartu Analisis Baris:
- **Baris 77–86 (`FacilityStatusLog::create`):** Merekam setiap transisi operasional ke dalam tabel audit terpisah (`facility_status_logs`), mencatat `old_value`, `new_value`, alasan pemindahan, dan tautan ke ID laporan kerusakan (`report_id`) jika dipicu dari laporan kendala.
- **Baris 89–126 (`Cascading Maintenance Effects`):**
  - **Reservasi Menunggu:** Seluruh pengajuan permohonan masa depan yang masih mengantre otomatis di-reject dengan alasan fasilitas masuk perbaikan.
  - **Reservasi Disetujui:** Seluruh reservasi yang telah disetujui sebelumnya otomatis dibatalkan (`dibatalkan_oleh_petugas`), membebaskan jadwal dan mencegah pengguna mendatangi fasilitas yang sedang dibongkar/diperbaiki.

---

## 3. CODE DEFENSE & ORAL EXAMINATION SIMULATOR

### Uji 1: Dampak Kaskade Pengalihan Status 'Dalam Perbaikan'
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Apa yang terjadi pada peminjam ruangan yang reservasinya sudah disetujui untuk besok lusa jika hari ini terjadi kerusakan parah (misal pipa bocor) dan petugas menyetel fasilitas ke 'Dalam Perbaikan' (US-12)?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Sistem kami mengeksekusi **Cascading Emergency Cancellation** di dalam transaksi database atomik pada [app/Services/StaffFacilityService.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Services/StaffFacilityService.php#L89-L126):  
> Ketika status operasional fasilitas diubah menjadi `DalamPerbaikan`, service mengunci fasilitas dengan `lockForUpdate()`. Kemudian secara otomatis menelusuri seluruh reservasi fasilitas tersebut yang berstatus `Disetujui` dan memiliki jadwal masa depan (`start_date_time->isFuture()`).  
> Status reservasi tersebut secara kaskade diubah menjadi `dibatalkan_oleh_petugas` disertai alasan resmi sistem: 'Fasilitas dialihkan ke status dalam perbaikan: [Alasan Petugas]'. Hal ini menjamin tidak ada reservasi aktif yang menggantung pada fasilitas yang secara fisik tidak layak digunakan."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Cascading Emergency Cancellation]`, `[Atomic DB Transaction]`, `[Future Slot Guard (isFuture())]`, `[Preservation of Audit Trail]`.

---

### Uji 2: Penegakan Finite State Machine pada Laporan Kendala
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa petugas tidak bisa langsung mengklik tombol 'Selesaikan' saat laporan baru pertama kali masuk (status Baru)?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Ini adalah penerapan **Finite State Machine (FSM)** untuk menegakkan SOP penanganan sarpras kampus (aturan SRS BR-PTG-04).  
> Sebuah kerusakan sarpras wajib melalui fase investigasi/penanganan fisik (`Diproses`) terlebih dahulu agar ada petugas yang bertanggung jawab (`processed_by_id`). Jika sistem memperbolehkan jalan pintas langsung dari `Baru` ke `Selesai`, petugas dapat menutup laporan tanpa pernah menindaklanjuti secara nyata di lapangan. Di backend, metode `resolve()` pada [app/Services/StaffReportService.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Services/StaffReportService.php#L117-L121) memvalidasi `if ($lockedReport->status !== ReportStatus::Diproses) { throw ValidationException; }`."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Finite State Machine (FSM)]`, `[SOP Enforcement (BR-PTG-04)]`, `[State Transition Guard]`, `[Accountability Tracking]`.

---

### Uji 3: Peran Tabel `facility_status_logs`
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa Anda memerlukan tabel terpisah `facility_status_logs` alih-alih cukup mengupdate kolom `operational_status` di tabel `facilities`?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Tabel `facilities` hanya menyimpan *Current State* (status saat ini). Jika kita hanya memperbarui kolom tersebut, kita kehilangan seluruh histori operasional fasilitas.  
> Tabel `facility_status_logs` berfungsi sebagai **Audit Log & Analytics Ledger**. Tabel ini mencatat setiap perubahan status, siapa staf atau admin yang mengubah, tanggal perubahan, durasi dalam perbaikan, dan tautan ke ID laporan kerusakan (`report_id`). Data historis inilah yang menjadi fondasi utama perhitungan statistik pada **Modul Rekapitulasi (US-17)** untuk menghitung metrik frekuensi kerusakan fasilitas setiap bulannya."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Current State vs Historic Ledger]`, `[Audit Log Table]`, `[Forensic Traceability]`, `[Foundation for US-17 Recap Analytics]`.

