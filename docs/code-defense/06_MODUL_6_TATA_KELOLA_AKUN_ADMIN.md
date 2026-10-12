# MODUL 6: TATA KELOLA AKUN ADMINISTRATOR
**User Stories:**  
- **[US-13]** Pendaftaran akun Petugas langsung secara administratif (tanpa registrasi publik).  
- **[US-14]** Pendaftaran akun Pengguna (Mahasiswa/Dosen/Tendik) langsung secara administratif.  
- **[US-15]** Audit akun, approval, dan reset kredensial langsung dengan pencatatan audit log.

---

## 1. DEKONSTRUKSI LAPISAN BLADE VIEW (`resources/views/`)

### A. Berkas Acuan
1. `resources/views/admin/accounts/create.blade.php` (Form Pembuatan Akun Administratif)
2. `resources/views/admin/accounts/created.blade.php` (Layar Tampil Kredensial Sekali Lihat / *One-Time Display*)
3. `resources/views/admin/accounts/index.blade.php` (Katalog Akun, Filter Role & Kategori)
4. `resources/views/admin/accounts/show.blade.php` (Detail Akun & Riwayat Aktivitas)
5. `resources/views/admin/accounts/partials/password-modal.blade.php` (Modal Reset Kata Sandi oleh Admin)

### B. Analisis Direktif Blade & Form Dynamic Behavior
Pada `resources/views/admin/accounts/create.blade.php`:
```blade
@extends('layouts.admin')

@section('title', 'Daftarkan Akun Baru · Administrator')

@section('content')
<div class="max-w-[700px] mx-auto px-4 py-8">
    <form method="POST" action="{{ route('admin.accounts.store') }}" class="space-y-6">
        @csrf

        <!-- Nama & Email -->
        <div>
            <label for="name" class="...">Nama Lengkap <span class="text-[#ff453a]">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="..." />
            @error('name') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="...">Alamat Email Resmi <span class="text-[#ff453a]">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="..." />
            @error('email') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Role Selector -->
        <div>
            <label for="role" class="...">Peran Akun <span class="text-[#ff453a]">*</span></label>
            <select name="role" id="role" required onchange="toggleCategoryField(this.value)" class="...">
                <option value="">Pilih Peran</option>
                <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas Sarpras</option>
                <option value="pengguna" {{ old('role') === 'pengguna' ? 'selected' : '' }}>Pengguna (Civitas)</option>
            </select>
            @error('role') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Conditional User Category -->
        <div id="category_wrapper" class="{{ old('role') === 'pengguna' ? '' : 'hidden' }}">
            <label for="user_category" class="...">Kategori Pengguna <span class="text-[#ff453a]">*</span></label>
            <select name="user_category" id="user_category" class="...">
                <option value="">Pilih Kategori</option>
                <option value="mahasiswa" {{ old('user_category') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                <option value="dosen" {{ old('user_category') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                <option value="tendik" {{ old('user_category') === 'tendik' ? 'selected' : '' }}>Tenaga Kependidikan (Tendik)</option>
            </select>
            @error('user_category') <p class="text-[11px] text-[#ff453a] mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="...">Terbitkan Akun</button>
    </form>
</div>
@endsection
```

- Kontrol Tampilan Kondisional: Dropdown `user_category` disembunyikan secara bawaan jika peran yang dipilih adalah `Petugas Sarpras`, dan dimunculkan jika peran yang dipilih adalah `Pengguna`.
- `old('role') === 'pengguna' ? '' : 'hidden'`: Menjaga visibilitas elemen tetap sinkron saat terjadi kegagalan validasi server-side (*flash session old input*).

Pada `resources/views/admin/accounts/created.blade.php`:
```blade
<div class="bg-[#111111] border border-[#333333] rounded-[6px] p-6 max-w-lg mx-auto text-center space-y-4">
    <h2 class="text-lg font-semibold text-white">Akun Berhasil Diterbitkan</h2>
    <p class="text-xs text-[#888888]">Salin kredensial berikut dan berikan kepada pemilik akun. Kata sandi ini hanya ditampilkan satu kali.</p>

    <div class="p-3 bg-[#0a0a0a] border border-[#333333] rounded-[6px] font-mono text-sm text-left space-y-1">
        <p class="text-[#888888]">Email: <span class="text-white">{{ session('created_email') }}</span></p>
        <p class="text-[#888888]">Password: <span id="pwd_val" class="text-[#50e3c2]">{{ session('created_password') }}</span></p>
    </div>

    <button onclick="navigator.clipboard.writeText(document.getElementById('pwd_val').textContent)" class="...">
        Salin Kata Sandi
    </button>
</div>
```
- Pola *One-Time Secret Display*: Kredensial kata sandi polos (*plaintext password*) hanya disimpan dalam session flash sekali pakai (`session()->flash(...)`), sehingga saat halaman di-refresh, kata sandi tersebut hilang dari memori web server.

---

## 2. BEDAH MIKRO KODE BACKEND BARIS PER BARIS

### A. Pembuatan Akun dengan Password Otomatis: `AccountService.php`
Berkas: `app/Services/AccountService.php` (baris 51–76)

```php
51: public function createAccount(array $data): array
52: {
53:     $plainPassword = Str::password(length: 12, letters: true, numbers: true, symbols: true, spaces: false);
54: 
55:     $role = $data['role'] instanceof UserRole ? $data['role'] : UserRole::from($data['role']);
56: 
57:     $category = null;
58:     if ($role === UserRole::Pengguna && ! empty($data['user_category'])) {
59:         $category = $data['user_category'] instanceof UserCategory
60:             ? $data['user_category']
61:             : UserCategory::from($data['user_category']);
62:     }
63: 
64:     $user = User::query()->create([
65:         'name' => trim($data['name']),
66:         'email' => Str::lower(trim($data['email'])),
67:         'password' => $plainPassword,
68:         'role' => $role,
69:         'user_category' => $category,
70:     ]);
71: 
72:     return [
73:         'user' => $user,
74:         'plain_password' => $plainPassword,
75:     ];
76: }
```

#### Kartu Analisis Baris:
- **Baris 53 (`Str::password(...)`):**
  - Menggunakan generator entropy acak CSPRNG (*Cryptographically Secure Pseudo-Random Number Generator*) bawaan Laravel.
  - Menghasilkan string 12 karakter berkekuatan tinggi kombinasi huruf besar/kecil, angka, dan simbol tanpa spasi, mengeliminasi risiko pembuatan kata sandi lemah (*weak default password*).
- **Baris 67 (`'password' => $plainPassword`):**
  - Pada Model [User.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Models/User.php), kolom `password` telah dipasang casting otomatis: `'password' => 'hashed'`.
  - Secara otomatis, Eloquent mengalirkan nilai `$plainPassword` melalui algoritma hashing **Bcrypt** (cost factor 12) sebelum dieksekusi ke klausa SQL `INSERT INTO users`. Kata sandi polos tidak pernah tersimpan di tabel database.
- **Baris 72–75:** Mengembalikan array yang memuat model `$user` dan `$plainPassword` murni untuk di-flash ke session admin.

---

### B. Reset Kata Sandi & Audit Log: `AccountService::resetPassword`
Berkas: `app/Services/AccountService.php` (baris 81–94)

```php
81: public function resetPassword(User $user, string $newPassword, string $adminId): void
82: {
83:     $user->update([
84:         'password' => $newPassword,
85:     ]);
86: 
87:     Log::info(sprintf(
88:         '[AUDIT] Admin ID %s reset password for User ID %s (%s) at %s',
89:         $adminId,
90:         $user->id,
91:         $user->email,
92:         now()->toIso8601String(),
93:     ));
94: }
```

#### Kartu Analisis Baris:
- **Baris 83–85:** Memperbarui kolom password dengan kata sandi baru (kembali di-hash otomatis oleh model cast).
- **Baris 87–93 (`Log::info`):** Penulisan catatan audit forensik ke sistem log monolog Laravel (`storage/logs/laravel.log`). Merekam identitas administrator yang melakukan tindakan reset, ID target, email target, dan stempel waktu ISO-8601, mencegah penyalahgunaan wewenang superuser (*Admin Privilege Abuse*).

---

## 3. CODE DEFENSE & ORAL EXAMINATION SIMULATOR

### Uji 1: Kebijakan Tanpa Registrasi Publik (Closed Provisioning Model)
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa sistem Resvi tidak menyediakan tombol 'Daftar Akun Baru' di halaman login publik dan mewajibkan seluruh akun didaftarkan oleh Administrator (US-13 & US-14)?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Sistem Resvi mengadopsi model **Closed Administrative Provisioning** sesuai standar keamanan tata kelola sarana prasarana kampus:  
> 1. Fasilitas kampus (laboratorium, aula, peralatan khusus) adalah aset fisik institusi dengan nilai risiko tinggi. Membuka registrasi publik memungkinkan pihak luar atau bot internet membuat akun tanpa verifikasi identitas civitas akademika (*Sybil Attack / Fake User Spam*).  
> 2. Pendaftaran terpusat oleh Administrator menjamin bahwa setiap akun yang diterbitkan telah terverifikasi sebagai mahasiswa aktif, dosen, tendik, atau petugas sarpras resmi dengan alamat email kampus valid."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Closed Administrative Provisioning]`, `[Asset Risk Mitigation]`, `[Sybil Attack Prevention]`, `[Identity Verification Governance]`.

---

### Uji 2: Manajemen Plaintext Password & One-Time Display Pattern
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Bagaimana Anda menjamin kata sandi yang dibuat di `AccountService::createAccount` tidak bocor ke pihak yang tidak berhak jika admin lupa menutup halamannya?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Kami menerapkan pola **One-Time Secret Flashing**:  
> Di controller [app/Http/Controllers/Admin/AccountController.php](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/app/Http/Controllers/Admin/AccountController.php#L40-L46), kata sandi polos `$result['plain_password']` disimpan sementara melalui `session()->flash('created_password', ...)`. Session flash di Laravel memiliki sifat langsung terhapus dari server session storage setelah satu siklus permintaan HTTP berikutnya (*destroyed on next request*).  
> Jika halaman di-refresh atau admin meninggalkan browser, data kata sandi polos langsung hilang dari memori server dan tidak pernah dapat diambil kembali karena di database hanya tersimpan hash Bcrypt satu arah."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[One-Time Secret Flashing]`, `[Ephemeral Session Storage]`, `[One-Way Hashing (Bcrypt)]`, `[Zero Plaintext Persistence]`.

---

### Uji 3: Pengawasan Akuntabilitas Reset Kredensial Superuser
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Jika seorang Admin mereset password akun Petugas lain untuk melakukan tindakan tidak sah, bagaimana sistem Anda melacak kejadian tersebut?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Melalui pencatatan **Forensic Audit Logging** di `AccountService::resetPassword` baris 87–93.  
> Setiap eksekusi reset kata sandi tidak hanya memutasi kolom password, melainkan memancarkan log level `INFO` ke channel audit: `[AUDIT] Admin ID %s reset password for User ID %s (%s) at %s`. Log ini merekam jejak tak terhapuskan (*immutable stream*) mengenai siapa akun admin yang mengeksekusi, target yang terkena dampak, dan waktu kejadian presisi. Berkas log ini dapat dianalisis oleh auditor sistem untuk membuktikan pertanggungjawaban forensik."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Forensic Audit Logging]`, `[Monolog Logging Pipeline]`, `[Non-repudiation]`, `[Privileged Action Accountability]`.

