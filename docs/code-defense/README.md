# RESVI — SUITE DOKUMENTASI CODE DEFENSE LENGKAP
**Sistem Reservasi Fasilitas & Pelaporan Kendala Sarpras Kampus**  
*Framework:* Laravel 13 (PHP 8.4-cli) | MySQL 8.0 | Docker Compose  
*Lokasi Dokumen:* `/home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/`

---

## 📌 Peta Navigasi Dokumen Code Defense

Seluruh analisis mendalam, bedah kode baris demi baris (*view-first methodology*), dan simulasi pertanyaan ujian sidang telah diorganisasi ke dalam 9 berkas markdown terpisah di folder ini:

| Berkas Dokumentasi | Modul & Ruang Lingkup | Cakupan User Stories | Aktor Sistem Terkait |
| :--- | :--- | :--- | :--- |
| [**00_MASTER_TRACEABILITY_MATRIX.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/00_MASTER_TRACEABILITY_MATRIX.md) | **Arsitektur Global & Matriks 17 User Story** | US-01 s/d US-17 Lengkap | Pengunjung, Pengguna, Petugas, Administrator |
| [**01_MODUL_1_PUBLIK_DAN_PENCARIAN.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/01_MODUL_1_PUBLIK_DAN_PENCARIAN.md) | **Modul 1: Publik & Pencarian Fasilitas** | **US-01, US-02** | Pengunjung & Pengguna Publik |
| [**02_MODUL_2_SIKLUS_RESERVASI_PENGGUNA.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/02_MODUL_2_SIKLUS_RESERVASI_PENGGUNA.md) | **Modul 2: Siklus Reservasi Pengguna** | **US-03, US-04, US-05** | Pengguna (Civitas Akademika) |
| [**03_MODUL_3_PELAPORAN_KENDALA_SARPRAS.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/03_MODUL_3_PELAPORAN_KENDALA_SARPRAS.md) | **Modul 3: Pelaporan Kendala Sarpras** | **US-06, US-07** | Pengguna (Civitas Akademika) |
| [**04_MODUL_4_TRIAGE_DAN_VERIFIKASI_PETUGAS.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/04_MODUL_4_TRIAGE_DAN_VERIFIKASI_PETUGAS.md) | **Modul 4: Triage & Verifikasi Petugas** | **US-08, US-09, US-10** | Petugas Sarpras (Operator) |
| [**05_MODUL_5_PEMELIHARAAN_DAN_STATUS_FASILITAS.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/05_MODUL_5_PEMELIHARAAN_DAN_STATUS_FASILITAS.md) | **Modul 5: Pemeliharaan & Status Fasilitas** | **US-11, US-12** | Petugas Sarpras (Operator) |
| [**06_MODUL_6_TATA_KELOLA_AKUN_ADMIN.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/06_MODUL_6_TATA_KELOLA_AKUN_ADMIN.md) | **Modul 6: Tata Kelola Akun Administrator** | **US-13, US-14, US-15** | Administrator Sistem |
| [**07_MODUL_7_MASTER_DATA_DAN_ANALYTICS.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/07_MODUL_7_MASTER_DATA_DAN_ANALYTICS.md) | **Modul 7: Master Data & Analytics** | **US-16, US-17** | Administrator Sistem |
| [**08_MODUL_8_ARSITEKTUR_DOCKER_DAN_STORAGE.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/08_MODUL_8_ARSITEKTUR_DOCKER_DAN_STORAGE.md) | **Modul 8: Arsitektur Docker & Storage** | Topologi, Symlink, Networking, UID/GID | Infrastruktur & DevOps |
| [**09_KAMUS_SINTAKS_DAN_POLA_VIEW_LAYER.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/09_KAMUS_SINTAKS_DAN_POLA_VIEW_LAYER.md) | **Kamus & Ensiklopedia Sintaks View Layer** | Seluruh Pola Sintaks, Direktif, HTML, JS, SVG, PHP | Semua Aktor & Penguji Sidang |
| [**10_KAMUS_SINTAKS_DAN_POLA_HTTP_LAYER.md**](file:///home/rafrusth/Attic/Programming/PPK/PPK-Room-Reservation/docs/code-defense/10_KAMUS_SINTAKS_DAN_POLA_HTTP_LAYER.md) | **Kamus & Ensiklopedia Sintaks HTTP Layer** | Controller Lifecycle, FormRequest, DI, Middleware, Responses | Semua Aktor & Penguji Sidang |

---

## 🎯 Struktur Standar Setiap Berkas Modul
Setiap modul di atas disusun menggunakan format metodologi *View-First*:

1. **Bagian 1: Dekonstruksi Lapisan Blade View (`resources/views/`)**
   - Bedah direktif Blade (`@extends`, `@section`, `@csrf`, `@if`, `@forelse`, `@error`, dll.).
   - Analisis atribut form HTML5 (`enctype="multipart/form-data"`, `step="1800"`, `min`).
   - Logika interaktif JavaScript peramban klien (AJAX fetch ketersediaan, preview FileReader, modal toggling, filter tabel DOM instan).
2. **Bagian 2: Bedah Mikro Kode Backend Baris per Baris**
   - Potongan kode riil dari repositori disertai nomor baris asli.
   - Analisis token sintaks, justifikasi arsitektur, dan perilaku mesin database di runtime (`DB::transaction`, `lockForUpdate()`, formula bentrok waktu, dll.).
3. **Bagian 3: Simulasi Ujian Sidang Kode (*Oral Defense Simulator*)**
   - 3 pertanyaan kritis dan menjebak tingkat dosen penguji / lead architect.
   - Rekomendasi jawaban teknis komprehensif.
   - Poin kunci wajib sebut untuk mendapatkan nilai maksimal dalam pengujian kode.

---

## ⚡ Perintah Penting (Zero Host Execution Rule)
Seluruh instruksi dijalankan di dalam kontainer Docker:
```bash
# Uji coba fungsional & fitur
docker compose exec app php artisan test

# Periksa seluruh rute web
docker compose exec app php artisan route:list

# Status kontainer
docker compose ps
```

