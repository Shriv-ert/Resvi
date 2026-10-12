# MODUL 3: PELAPORAN KENDALA SARPRAS
**User Stories:**  
- **[US-06]** Melaporkan kerusakan/kendala fasilitas (kategori enum, deskripsi, foto bukti multipart).  
- **[US-07]** Melihat riwayat dan status pelaporan kendala pribadi beserta catatan penyelesaian.

---

## 1. DEKONSTRUKSI LAPISAN BLADE VIEW (`resources/views/`)

### A. Berkas Acuan
1. `resources/views/user/reports/create.blade.php` (Formulir Pelaporan Kendala & Upload Bukti)
2. `resources/views/user/reports/index.blade.php` (Daftar Riwayat Pelaporan Kendala)
3. `resources/views/user/reports/show.blade.php` (Detail Status Laporan & Catatan Resolusi Petugas)

### B. Analisis Direktif Blade & Form Attributes
Pada `resources/views/user/reports/create.blade.php`:
```blade
@extends('layouts.app')

@section('title', 'Laporkan Kerusakan Fasilitas · Resvi')

@section('content')
<div class="max-w-[760px] mx-auto px-4 py-8">
    <form method="POST" action="{{ route('user.reports.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Fasilitas Terkait -->
        <div>
            <label for="facility_id" class="...">Fasilitas Bermasalah <span class="text-[#ff453a]">*</span></label>
            <select name="facility_id" id="facility_id" required class="...">
                <option value="">Pilih Fasilitas</option>
                @foreach($facilities as $facility)
                    <option value="{{ $facility->id }}" {{ old('facility_id') == $facility->id ? 'selected' : '' }}>
                        {{ $facility->name }} ({{ $facility->location }})
                    </option>
                @endforeach
            </select>
            @error('facility_id') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Kategori Kerusakan (PHP Enum) -->
        <div>
            <label for="category" class="...">Kategori Kendala <span class="text-[#ff453a]">*</span></label>
            <select name="category" id="category" required class="...">
                <option value="">Pilih Kategori</option>
                @foreach(\App\Enums\ReportCategory::cases() as $cat)
                    <option value="{{ $cat->value }}" {{ old('category') == $cat->value ? 'selected' : '' }}>
                        {{ $cat->label() }}
                    </option>
                @endforeach
            </select>
            @error('category') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Deskripsi Kerusakan -->
        <div>
            <textarea name="description" rows="4" required placeholder="Jelaskan detail kerusakan sarpras...">{{ old('description') }}</textarea>
            @error('description') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Upload Foto Bukti Kerusakan -->
        <div>
            <label class="...">Bukti Foto Kerusakan (Opsional, Maks 2MB)</label>
            <div id="dropzone" class="border border-dashed border-[#333333] rounded-[6px] p-6 text-center cursor-pointer hover:border-[#666666] transition">
                <input type="file" name="photo" id="photo_input" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" />
                <p id="upload_prompt" class="text-xs text-[#888888]">Klik atau seret file gambar ke sini (JPEG, PNG, WebP)</p>
                <div id="preview_container" class="hidden mt-3">
                    <img id="image_preview" src="#" alt="Pratinjau" class="max-h-48 mx-auto rounded-[4px] border border-[#333333]" />
                    <button type="button" id="remove_btn" class="text-[11px] text-[#ff453a] mt-2 underline">Hapus Gambar</button>
                </div>
            </div>
            @error('photo') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="...">Kirim Laporan Kerusakan</button>
    </form>
</div>
@endsection
```

- `enctype="multipart/form-data"`: Atribut form wajib saat mengunggah berkas. Tanpa atribut ini, browser secara default menggunakan `application/x-www-form-urlencoded` yang hanya mengirim string teks nama berkas, bukan stream biner gambar.
- `\App\Enums\ReportCategory::cases()`: Mengiterasi kasus enum PHP 8.4 langsung di Blade. Nilai string (`value`) menjadi parameter input form, sementara method `label()` menyajikan teks ramah pengguna (misal: *Kebersihan, Kelistrikan, Kerusakan Fisik, Perangkat IT*).

### C. Logika Client-Side JavaScript (File Preview & Validation)
Pada script form `create.blade.php`:
```javascript
const photoInput = document.getElementById('photo_input');
const dropzone = document.getElementById('dropzone');
const previewContainer = document.getElementById('preview_container');
const imagePreview = document.getElementById('image_preview');
const uploadPrompt = document.getElementById('upload_prompt');
const removeBtn = document.getElementById('remove_btn');

dropzone.addEventListener('click', () => photoInput.click());

photoInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        // Validasi ukuran di sisi klien (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran berkas melebihi batas maksimal 2MB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            previewContainer.classList.remove('hidden');
            uploadPrompt.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
});

removeBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    photoInput.value = '';
    previewContainer.classList.add('hidden');
    uploadPrompt.classList.remove('hidden');
});
```
- Menggunakan `FileReader` API untuk membaca data biner lokal menjadi string base64 (`DataURL`) secara asinkron tanpa mengunggah ke server terlebih dahulu.
- Pengecekan ukuran file 2MB secara langsung di browser menghemat bandwidth pengguna sebelum form di-submit.

---

## 2. BEDAH MIKRO KODE BACKEND BARIS PER BARIS

### A. Validasi Upload: `StoreReportRequest.php`
```php
public function rules(): array
{
    return [
        'facility_id' => ['required', 'exists:facilities,id'],
        'category' => ['required', new Enum(ReportCategory::class)],
        'description' => ['required', 'string', 'min:10', 'max:2000'],
        'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
    ];
}
```

#### Kartu Analisis Baris:
- **`new Enum(ReportCategory::class)`:** Memastikan nilai yang dikirim klien cocok dengan nilai valid enum PHP (`kebersihan`, `kelistrikan`, `kerusakan_fisik`, `perangkat_it`, dll.). Jika penyerang menyuntikkan string sembarangan, Laravel otomatis melempar error validasi.
- **`'image'`:** Memvalidasi bahwa file yang diunggah benar-benar berkas gambar nyata menggunakan ekstensi PHP `fileinfo` (bukan sekadar file `.exe` atau `.php` yang diganti ekstensinya).
- **`'max:2048'`:** Membatasi ukuran maksimal berkas sebesar 2048 Kilobyte (2 Megabyte) di tingkat framework sebelum dialokasikan ke storage.

---

### B. Pembuatan Laporan & Penyimpanan Berkas: `UserReportService.php`
```php
19: public function createReport(User $user, array $data, ?UploadedFile $photo = null): Report
20: {
21:     return DB::transaction(function () use ($user, $data, $photo) {
22:         $facility = Facility::where('id', $data['facility_id'])->first();
23: 
24:         if (! $facility || ! $facility->is_active) {
25:             throw ValidationException::withMessages([
26:                 'facility_id' => ['Fasilitas yang dilaporkan tidak valid atau tidak aktif.'],
27:             ]);
28:         }
29: 
30:         $photoPath = null;
31:         if ($photo) {
32:             $photoPath = $photo->store('reports', 'public');
33:         }
34: 
35:         // Generate Nomor Laporan Unik: LAP-YYYYMMDD-XXXX
36:         $datePrefix = date('Ymd');
37:         $countToday = Report::whereDate('created_at', today())->count();
38:         $uniqueCode = str_pad((string) ($countToday + 1), 4, '0', STR_PAD_LEFT);
39:         $reportNumber = "LAP-{$datePrefix}-{$uniqueCode}";
40: 
41:         while (Report::where('report_number', $reportNumber)->exists()) {
42:             $uniqueCode = str_pad((string) (random_int(1000, 9999)), 4, '0', STR_PAD_LEFT);
43:             $reportNumber = "LAP-{$datePrefix}-{$uniqueCode}";
44:         }
45: 
46:         return Report::create([
47:             'report_number' => $reportNumber,
48:             'reporter_id' => $user->id,
49:             'facility_id' => $facility->id,
50:             'category' => $data['category'],
51:             'description' => $data['description'],
52:             'photo_path' => $photoPath,
53:             'status' => ReportStatus::Baru,
54:         ]);
55:     });
56: }
```

#### Kartu Analisis Baris:
- **Baris 32 (`$photo->store('reports', 'public')`):**
  - Menginstruksikan Laravel Storage subsystem untuk memindahkan file temporer di `/tmp` ke disk `public` pada path `storage/app/public/reports/`.
  - Meng-generate nama file hash acak 40 karakter berbasis SHA1 (misal: `reports/k8H3z...png`), mencegah penyerang menimpa file orang lain (*File Overwrite Vulnerability*) atau menyisipkan karakter *Directory Traversal* (`../../malicious.php`).
- **Baris 35–44 (Penomoran Tiket `LAP-YYYYMMDD-XXXX`):** Menjamin nomor unik urut harian dengan fallback acak bila terjadi konkurensi pembuatan tiket laporan di waktu bersamaan.
- **Baris 53 (`'status' => ReportStatus::Baru`):** Mengeset siklus awal laporan kendala pada status `Baru`, siap masuk ke antrean triage petugas (US-08 & US-11).

---

## 3. CODE DEFENSE & ORAL EXAMINATION SIMULATOR

### Uji 1: Protokol HTTP Multipart Form Data
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa pada form upload di Blade wajib menyertakan atribut `enctype="multipart/form-data"`? Apa yang terjadi di level paket HTTP bila atribut ini tidak ditulis?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Secara spesifikasi RFC 7578, form HTTP standar menggunakan `application/x-www-form-urlencoded` yang mengonversi seluruh input menjadi pasangan key-value string ASCII yang di-URL-encode (misal: `photo=foto_rusak.png`). Jika atribut `enctype="multipart/form-data"` tidak disertakan, browser hanya akan mengirimkan string nama file teks tersebut, bukan stream data biner berkas gambar.  
> Dengan `multipart/form-data`, payload HTTP dipecah menjadi beberapa bagian (*parts*) yang dipisahkan oleh *Boundary Delimiter*. Bagian teks dikirim sebagai form-data biasa, sedangkan berkas gambar dikirim sebagai stream biner dengan header `Content-Type` tersendiri. Ini memungkinkan PHP di dalam kontainer `ppk_app` memetakan file ke superglobal `$_FILES` dan memindahkannya ke direktori temporer `/tmp` sebelum divalidasi Laravel."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[RFC 7578 Multipart Protocol]`, `[Boundary Delimiter]`, `[Binary Stream vs URL-encoded String]`, `[Superglobal $_FILES Buffer]`.

---

### Uji 2: Mitigasi Serangan Upload Berkas Berbahaya (RCE Mitigation)
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Bagaimana sistem Anda mencegah penyerang mengunggah skrip PHP backdoor berbahaya yang disamarkan dengan nama file `exploit.php.jpg`?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Sistem kami menerapkan pertahanan berlapis (*Defense-in-Depth*):  
> 1. **Inspeksi Magic Bytes:** Aturan `'image'` pada `StoreReportRequest` tidak hanya melihat ekstensi nama berkas, melainkan memanggil ekstensi internal PHP `fileinfo` yang membaca header heksadesimal berkas (*magic bytes/numbers*). Jika isi berkas adalah teks script PHP, validasi akan langsung menggagalkannya dengan kode 422.  
> 2. **Hashing Nama Berkas:** Pemanggilan `$photo->store('reports', 'public')` tidak pernah menggunakan nama asli file kiriman klien (`getClientOriginalName()`), melainkan meng-generate hash acak unik. Ini mensterilkan serangan *Null Byte Injection* dan *Directory Traversal*.  
> 3. **Penyimpanan Terisolasi:** Berkas disimpan di disk terpisah `storage/app/public/reports/` yang hanya dapat diakses melalui symlink publik tanpa kemampuan mengeksekusi script PHP langsung melalui web server."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Magic Bytes / Magic Numbers Inspection]`, `[PHP Fileinfo Extension]`, `[Random Hash Renaming ($photo->store())]`, `[Directory Traversal Mitigation]`.

---

### Uji 3: Akses Publik Berkas & Symlink Engine
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Di database, kolom `photo_path` hanya berisi string seperti `reports/abcdef123.jpg`. Bagaimana gambar tersebut akhirnya bisa diakses dan dirender dengan aman oleh browser di `user/reports/show.blade.php`?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Di Blade template [resources/views/user/reports/show.blade.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/resources/views/user/reports/show.blade.php), gambar dirender menggunakan helper `{{ Storage::url($report->photo_path) }}` atau `{{ asset('storage/' . $report->photo_path) }}`.  
> Helper ini menerjemahkan path internal storage menjadi URL publik `/storage/reports/abcdef123.jpg`. Permintaan ini kemudian dilayani oleh web server melalui *Symbolic Link* yang telah dipasang antara `public/storage` dan `../storage/app/public`. Karena symlink dibuat secara otomatis saat kontainer `ppk_app` dinyalakan (`ln -sfn`), file biner gambar dapat di-stream langsung ke browser klien tanpa membebani runtime PHP framework."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Storage::url() Helper]`, `[Symbolic Link Engine (public/storage)]`, `[Zero Framework Overhead on Static File Streaming]`.

