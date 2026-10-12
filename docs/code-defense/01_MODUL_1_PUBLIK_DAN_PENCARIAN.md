# MODUL 1: PUBLIK & PENCARIAN FASILITAS
**User Stories:**  
- **[US-01]** Melihat daftar fasilitas & ketersediaan per slot waktu (tanpa melihat identitas pemohon/tujuan privasi - Zero PII Leak).  
- **[US-02]** Pencarian & filter fasilitas berdasarkan tipe, lokasi, dan kapasitas.

---

## 1. DEKONSTRUKSI LAPISAN BLADE VIEW (`resources/views/`)

### A. Berkas Acuan
1. `resources/views/facilities/index.blade.php` (Katalog, Pencarian, & Filter)
2. `resources/views/facilities/show.blade.php` (Detail Fasilitas & Kalender / Form Cek Ketersediaan Real-Time)

### B. Analisis Direktif Blade
Pada `resources/views/facilities/index.blade.php`:
```blade
@extends('layouts.app')

@section('title', 'Katalog Fasilitas Kampus · Resvi')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-8">
    <form method="GET" action="{{ route('facilities.index') }}" class="...">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama fasilitas..." />
        ...
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($facilities as $facility)
            @include('facilities.partials.card', ['facility' => $facility])
        @empty
            <div class="col-span-full text-center py-12 text-[#888888]">
                Tidak ada fasilitas yang sesuai dengan kriteria pencarian.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $facilities->links() }}
    </div>
</div>
@endsection
```

- `@extends('layouts.app')`: Membungkus katalog ke layout publik utama, memuat CSS Tailwind, Alpine/Vanilla JS, header navigasi, dan tombol toggle tema (`resources/views/partials/theme-toggle.blade.php`).
- `value="{{ request('q') }}"`: Mempertahankan nilai input pencarian (*repopulating state*) setelah form dikirimkan via metode HTTP `GET`.
- `@forelse ... @empty ... @endforelse`: Konstruk Blade elegan yang mengompilasi pengecekan koleksi kosong dan perulangan sekaligus tanpa perlu sintaks ganda `if(count) + foreach`.
- `{{ $facilities->links() }}`: Merender pagination bar bawaan Laravel yang terintegrasi dengan Tailwind CSS.

Pada `resources/views/facilities/show.blade.php`:
```blade
<div id="availability-checker" class="...">
    <input type="date" id="check-date" min="{{ now()->toDateString() }}" class="..." />
    <input type="time" id="check-start" step="1800" class="..." />
    <input type="time" id="check-end" step="1800" class="..." />
    <button type="button" id="btn-check" class="...">Cek Ketersediaan</button>
    <div id="check-result" class="hidden p-4 rounded-[6px] text-xs"></div>
</div>
```

### C. Logika Client-Side JavaScript (Fetch API & Availability Check)
Pada script `facilities/show.blade.php`:
```javascript
document.getElementById('btn-check').addEventListener('click', async () => {
    const facilityId = "{{ $facility->id }}";
    const date = document.getElementById('check-date').value;
    const startTime = document.getElementById('check-start').value;
    const endTime = document.getElementById('check-end').value;
    const resultBox = document.getElementById('check-result');

    if (!date || !startTime || !endTime) {
        alert('Harap lengkapi tanggal dan rentang waktu.');
        return;
    }

    try {
        const response = await fetch("{{ route('reservations.check_availability') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                facility_id: facilityId,
                date: date,
                start_time: startTime,
                end_time: endTime
            })
        });

        const data = await response.json();
        resultBox.classList.remove('hidden', 'bg-[#002b11]', 'bg-[#2b0000]', 'text-[#50e3c2]', 'text-[#ff453a]');

        if (data.is_available) {
            resultBox.classList.add('bg-[#002b11]', 'text-[#50e3c2]');
            resultBox.textContent = data.message;
        } else {
            resultBox.classList.add('bg-[#2b0000]', 'text-[#ff453a]');
            resultBox.textContent = data.message;
        }
    } catch (err) {
        console.error('Galat komunikasi API:', err);
    }
});
```
- Menangkap input tanggal dan slot waktu, memvalidasi di sisi klien, lalu memancarkan request HTTP `POST` asynchronous via `fetch()`.
- Menyertakan header `X-CSRF-TOKEN` untuk lolos verifikasi middleware `ValidateCsrfToken`.
- Merespons tanpa merender ulang seluruh halaman, memperbarui class DOM warna status ketersediaan secara instan.

---

## 2. BEDAH MIKRO KODE BACKEND BARIS PER BARIS

### A. Query Filter Katalog: `FacilityCatalogService.php`
```php
16: public function getPaginatedFacilities(array $filters, int $perPage = 12): LengthAwarePaginator
17: {
18:     return Facility::query()
19:         ->active()
20:         ->with(['primaryPhoto'])
21:         ->when(! empty($filters['q']), function ($query) use ($filters) {
22:             $keyword = trim((string) $filters['q']);
23:             $query->where('name', 'like', "%{$keyword}%");
24:         })
25:         ->when(! empty($filters['type']), function ($query) use ($filters) {
26:             $query->where('type', $filters['type']);
27:         })
28:         ->when(! empty($filters['location']), function ($query) use ($filters) {
29:             $query->where('location', $filters['location']);
30:         })
31:         ->when(! empty($filters['capacity']), function ($query) use ($filters) {
32:             $query->whereNotNull('capacity')
33:                 ->where('capacity', '>=', (int) $filters['capacity']);
34:         })
35:         ->orderBy('name')
36:         ->paginate($perPage)
37:         ->withQueryString();
38: }
```

#### Kartu Analisis Baris:
- **Baris 19 (`->active()`):** Memanggil *Local Eloquent Scope* pada Model `Facility` (`scopeActive($query) { $query->where('is_active', true); }`). Menjamin fasilitas yang telah dinonaktifkan oleh administrator tidak pernah bocor ke tampilan publik.
- **Baris 20 (`->with(['primaryPhoto'])`):** *Eager Loading* relasi foto utama. Mencegah permasalahan performa klasik **N+1 Query Problem**. Tanpa baris ini, merender 12 kartu fasilitas akan memicu 13 query terpisah (1 query induk + 12 query foto). Dengan eager loading, data foto ditarik hanya dalam 1 query relasi tambahan (`WHERE facility_id IN (...)`).
- **Baris 21–34 (`->when(...)`):** Eksekusi kondisional Fluent Builder. Jika parameter filter tidak dikirim oleh pengguna, closure di dalamnya diabaikan, menjaga SQL query tetap ramping tanpa tumpukan klausa `WHERE 1=1` manual.
- **Baris 37 (`->withQueryString()`):** Memastikan seluruh query string parameter (`q`, `type`, `location`, `capacity`) tetap dipertahankan saat pengunjung mengklik tombol pagination halaman 2, 3, dst., sehingga filter tidak ter-reset secara tidak sengaja.

---

### B. Validasi Ketersediaan & Zero PII Leak: `FacilityAvailabilityService.php`
```php
18: public function check(
19:     Facility $facility,
20:     CarbonImmutable|string $date,
21:     string $startTime,
22:     string $endTime
23: ): AvailabilityResultDTO {
24:     // 1. Validasi Status Operasional Fasilitas
25:     if ($facility->operational_status === OperationalStatus::DalamPerbaikan) {
26:         return new AvailabilityResultDTO(
27:             isAvailable: false,
28:             message: 'Fasilitas sedang dalam perbaikan dan tidak dapat direservasi untuk tanggal apa pun.',
29:             reason: 'dalam_perbaikan'
30:         );
31:     }
32: 
33:     if (! $facility->is_active) {
34:         return new AvailabilityResultDTO(
35:             isAvailable: false,
36:             message: 'Fasilitas tidak aktif atau dinonaktifkan oleh administrator.',
37:             reason: 'non_aktif'
38:         );
39:     }
40: 
41:     $formattedStartTime = strlen($startTime) === 5 ? "{$startTime}:00" : $startTime;
42:     $formattedEndTime = strlen($endTime) === 5 ? "{$endTime}:00" : $endTime;
43:     $dateString = is_string($date) ? $date : $date->toDateString();
44: 
45:     // 2. Deteksi Bentrokan Waktu Terhadap Status Terkunci
46:     $lockedStatuses = [
47:         ReservationStatus::Disetujui->value,
48:         ReservationStatus::Berlangsung->value,
49:     ];
50: 
51:     $hasConflict = Reservation::query()
52:         ->where('facility_id', $facility->id)
53:         ->whereDate('date', $dateString)
54:         ->whereIn('status', $lockedStatuses)
55:         ->where('start_time', '<', $formattedEndTime)
56:         ->where('end_time', '>', $formattedStartTime)
57:         ->exists();
58: 
59:     if ($hasConflict) {
60:         return new AvailabilityResultDTO(
61:             isAvailable: false,
62:             message: 'Jadwal yang Anda pilih tidak tersedia karena telah terisi oleh kegiatan lain. Silakan pilih waktu atau tanggal lain.',
63:             reason: 'bentrok'
64:         );
65:     }
66: 
67:     return new AvailabilityResultDTO(
68:         isAvailable: true,
69:         message: 'Jadwal yang Anda pilih tersedia untuk digunakan.'
70:     );
71: }
```

#### Kartu Analisis Baris:
- **Baris 23 (`-> AvailabilityResultDTO`):** Mengembalikan objek DTO (*Data Transfer Object*) biner terisolasi. Objek ini hanya membawa properti `isAvailable`, `message`, dan `reason`. Tidak ada informasi identitas pemohon (`user_id`, nama mahasiswa, NIM/NIP, kontak) maupun tujuan kegiatan yang diikutsertakan.
- **Baris 46–49 (`$lockedStatuses`):** Hanya status `Disetujui` dan `Berlangsung` yang mengunci slot waktu secara absolut. Status `Menunggu` sengaja tidak mengunci slot publik karena belum diverifikasi oleh petugas sarpras.
- **Baris 55–56 (`start_time < end_time AND end_time > start_time`):** Menerapkan Teorema Perpotongan Interval (*Interval Intersection Theorem*). Query menghasilkan respon boolean tanpa memuat baris data reservasi ke memori PHP (`exists()` menjalankan `SELECT 1 ... LIMIT 1`).

---

## 3. CODE DEFENSE & ORAL EXAMINATION SIMULATOR

### Uji 1: Kebijakan Privasi & Pencegahan Kebocoran PII (Zero PII Leak)
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Pada US-01, pengunjung publik dapat melihat slot ketersediaan fasilitas. Mengapa API Anda tidak mengembalikan daftar nama organisasi atau nama mahasiswa yang sedang meminjam ruangan pada jam tersebut agar jadwal terlihat transparan?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Secara arsitektural, sistem kami menerapkan prinsip **Privacy by Design** dan mematuhi aturan bisnis SRS BR-PG-08 (*Zero Personally Identifiable Information Leak*). Menampilkan identitas pemohon atau agenda kegiatan pada endpoint publik (`/reservations/check-availability`) membuka celah ancaman *Data Scraping* dan pelanggaran privasi civitas akademika.  
> Oleh karena itu, di [app/Services/FacilityAvailabilityService.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Services/FacilityAvailabilityService.php#L60-L72), respon API dibatasi strictly menggunakan `AvailabilityResultDTO` yang hanya mengembalikan status biner ketersediaan (`isAvailable: true/false`) dan pesan generik tanpa pernah menyertakan atribut Eloquent `user_id`, `name`, `purpose`, maupun kontak pemohon."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Privacy by Design]`, `[Zero PII Leak Principle (BR-PG-08)]`, `[Data Transfer Object (DTO) Boundary]`, `[Data Scraping Attack Mitigation]`.

---

### Uji 2: Pengendalian N+1 Query Problem pada Katalog
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Bagaimana Anda menjamin halaman katalog fasilitas (`facilities.index`) tetap berkinerja tinggi saat database memuat ratusan fasilitas beserta foto-fotonya?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Kami memitigasi bottleneck database melalui dua teknik pada [app/Services/FacilityCatalogService.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Services/FacilityCatalogService.php#L18-L37):  
> 1. **Eager Loading Relasi:** Pada baris 20, kami memanggil `->with(['primaryPhoto'])`. Tanpa eager loading, perulangan 12 fasilitas di Blade akan memicu 1 query induk ditambah 12 query foto anak (*N+1 Problem*). Dengan eager loading, Laravel hanya mengeksekusi tepat 2 query SQL terindeks ke MySQL.  
> 2. **Database-level Pagination:** Kami menggunakan metode `->paginate($perPage)`, yang menerjemahkan batasan ke klausa SQL `LIMIT 12 OFFSET X`. Server hanya memproses 12 record di memori dalam satu waktu, bukan memuat ribuan baris model ke RAM PHP."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[N+1 Query Problem]`, `[Eager Loading (with())]`, `[Database-level SQL LIMIT/OFFSET]`, `[Memory Buffer Optimization]`.

---

### Uji 3: Penanganan Slot Pending vs Approved pada Pengecekan Ketersediaan
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa pada pengecekan ketersediaan publik (`FacilityAvailabilityService`), pengajuan reservasi yang berstatus 'Menunggu' (*pending*) tidak dianggap sebagai bentrok dan fasilitas tetap dinyatakan tersedia?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Karena status `Menunggu` belum memiliki kekuatan legalitas operasional dan belum tentu disetujui oleh Petugas Sarpras (bisa saja ditolak karena berkas tidak lengkap atau alasan prioritas).  
> Jika status `Menunggu` langsung mengunci slot publik, maka sistem rentan terhadap serangan penolakan layanan reservasi (*Reservation Denial of Service*), di mana pengguna jahat dapat mengajukan banyak permohonan reservasi palsu untuk memblokir seluruh slot waktu kampus tanpa verifikasi. Slot baru terkunci secara definitif setelah Petugas melakukan persetujuan resmi (`Disetujui`), di mana saat itu sistem secara otomatis mengeksekusi cascading reject terhadap pengajuan pending lainnya (US-09)."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Reservation Denial of Service Prevention]`, `[Optimistic Queueing]`, `[Authority Boundary]`, `[Pemisahan Pending vs Committed State]`.

