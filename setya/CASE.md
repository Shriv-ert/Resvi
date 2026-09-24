# Pengembangan Platform Khusus 2026 (Sebelum UTS)

## Ketentuan Umum

### 1. Durasi & Tim
* Project dikerjakan hingga Ujian Tengah Semester (UTS).
* Satu tim terdiri dari 4–5 mahasiswa.
* Setiap anggota tim wajib berkontribusi secara aktif dalam pengerjaan project.

### 2. Aturan Implementasi
* **Authentication**: Sistem wajib memiliki fitur registrasi, login, dan logout.
* **Struktur Kode**: Kode program minimal dipisahkan menjadi bagian koneksi database, tampilan (HTML), dan logika proses.
* **Validasi Data**: Validasi dilakukan pada sisi server (*server-side*) dan sisi client (*client-side*) untuk form-form penting.
* **UI/UX**: Tampilan sistem harus mudah digunakan (*user-friendly*) dan memiliki antarmuka yang jelas.

### 3. Kolaborasi & Version Control
* Setiap tim wajib menggunakan repository GitHub/GitLab bersama.
* Setiap anggota wajib melakukan commit dengan pesan commit yang jelas dan deskriptif.
* Minimal terdapat pembagian struktur folder sebagai berikut:
  * `/public`
  * `/app` (model/controller)
  * `/views`
  * `/config`

---

## Ketentuan Khusus

### 1. Rancangan Data
Mahasiswa diperbolehkan menambahkan asumsi, tabel, maupun atribut pada rancangan data apabila diperlukan untuk mendukung implementasi sistem.

### 2. Pengumpulan Tugas
* **Batas Waktu**: Maksimal tanggal **11 Oktober 2026 pukul 12.00 WIB** melalui Kulon.

### 3. Berkas yang Dikumpulkan
Tugas dikumpulkan dalam bentuk **satu file Word** yang berisi:
* Nama dan NIM seluruh anggota kelompok.
* Pembagian tugas masing-masing anggota kelompok.
* Link file program di Google Drive, yang mencakup:
  * Source code
  * File SQL
  * File-file lain yang diperlukan untuk menjalankan program
  * Informasi konfigurasi/setting yang diperlukan untuk menjalankan program
  * Informasi login untuk masing-masing actor/pengguna
  * Screenshot antarmuka dan penjelasan singkat untuk masing-masing fitur

### 4. Presentasi
* Tugas akan dipresentasikan sebagai bentuk UTS.
* **Jadwal Presentasi**: TBA (*To Be Announced*).
* **Materi Presentasi**:
  * Latar belakang
  * Fitur utama
  * Demo sistem
  * Kendala yang dihadapi selama proses pengembangan
* **Alokasi Waktu**:
  * 10 menit untuk presentasi
  * 10–15 menit untuk sesi tanya jawab

---

## Deskripsi Sistem

Sebuah aplikasi web untuk mengelola penggunaan fasilitas kampus (ruang kelas, aula, laboratorium, alat, dan lapangan). Pengguna dapat mengecek ketersediaan dan mengajukan reservasi, serta melaporkan kerusakan atau masalah pada fasilitas yang sama. Petugas dan admin memproses kedua alur (reservasi dan laporan) secara terpusat dalam satu sistem.

### Ketentuan Waktu Reservasi
* **Jam Operasional**: 07.00–20.00.
* **Slot Waktu**: Menggunakan slot waktu tetap berdurasi **30 menit** (mis. 07.00–07.30, 07.30–08.00, dst.).
* **Validasi Waktu**: `start_time` dan `end_time` pada reservasi wajib berada dalam rentang jam operasional dan merupakan kelipatan slot 30 menit; validasi ini **wajib dilakukan di sisi server**, bukan hanya di tampilan kalender.

### Aktor
* **Pengunjung**: Melihat daftar fasilitas dan ketersediaan jadwal (tersedia/tidak tersedia), tanpa detail, tanpa login.
* **Pengguna (Mahasiswa/Dosen/Staf, Login)**: Mengajukan reservasi dan melaporkan kerusakan fasilitas.
* **Petugas**: Memproses antrian reservasi & laporan yang masuk (*approve*/*reject*/*cancel* reservasi, dan resolusi laporan kerusakan fasilitas), serta memperbarui status ketersediaan fasilitas (termasuk menandai fasilitas dalam perbaikan) berdasarkan laporan yang ditangani.
* **Admin**: Mengelola data master fasilitas, melihat rekap lintas fasilitas, mendaftarkan akun petugas dan (jika diperlukan) akun pengguna secara langsung, serta memverifikasi akun pengguna hasil registrasi mandiri.

---

## User Stories

| No | Aktor | User Story |
| :---: | :--- | :--- |
| **1** | Pengunjung / Pengguna | Sebagai pengunjung/pengguna, saya bisa melihat daftar fasilitas beserta status ketersediaannya per slot waktu (tersedia/tidak tersedia), tanpa melihat detail pemohon atau tujuan penggunaan. |
| **2** | Pengunjung / Pengguna | Sebagai pengunjung/pengguna, saya bisa mencari fasilitas berdasarkan tipe/lokasi/kapasitas. |
| **3** | Pengguna | Sebagai pengguna, saya bisa mengajukan reservasi pada rentang waktu tertentu dengan menyebutkan tujuan penggunaan. |
| **4** | Pengguna | Sebagai pengguna, saya bisa membatalkan reservasi saya sendiri sebelum batas waktu tertentu. |
| **5** | Pengguna | Sebagai pengguna, saya bisa melihat riwayat dan status reservasi saya, termasuk detail lengkap reservasi tersebut. |
| **6** | Pengguna | Sebagai pengguna, saya bisa melaporkan kerusakan/masalah pada fasilitas tertentu (kategori, deskripsi, foto). |
| **7** | Pengguna | Sebagai pengguna, saya bisa melihat status laporan saya. |
| **8** | Petugas | Sebagai petugas, saya bisa melihat dashboard/antrian reservasi dan laporan yang masih menunggu diproses, agar tidak ada yang terlewat. |
| **9** | Petugas | Sebagai petugas, saya bisa menyetujui/menolak reservasi yang masuk secara manual; sistem mencegah persetujuan reservasi yang bentrok jadwal pada fasilitas yang sama. |
| **10** | Petugas | Sebagai petugas, saya bisa membatalkan reservasi yang sudah disetujui dalam kondisi mendesak (mis. fasilitas mendadak tidak bisa dipakai), dengan mencantumkan alasan pembatalan. |
| **11** | Petugas | Sebagai petugas, saya bisa mengubah status laporan (baru/diproses/selesai/ditolak) beserta catatan resolusi saat laporan ditutup. |
| **12** | Petugas | Sebagai petugas, saya bisa menandai fasilitas berstatus 'dalam perbaikan' terkait laporan kerusakan yang sedang ditangani, dan mengembalikannya ke status aktif setelah selesai diperbaiki. |
| **13** | Admin | Sebagai admin, saya bisa mendaftarkan akun petugas secara langsung (petugas tidak melakukan registrasi mandiri dalam kondisi apapun). |
| **14** | Admin | Sebagai admin, saya bisa mendaftarkan akun pengguna (mahasiswa/dosen/staf) secara langsung tanpa melalui form registrasi mandiri. |
| **15** | Admin | Sebagai admin, saya bisa memverifikasi atau menolak akun pengguna hasil registrasi mandiri (jika diimplementasikan) sebelum akun tersebut dapat digunakan untuk login. |
| **16** | Admin | Sebagai admin, saya bisa mengelola data fasilitas (tambah/edit/nonaktifkan). |
| **17** | Admin | Sebagai admin, saya bisa melihat dan mengekspor (CSV/Excel/PDF) rekap okupansi fasilitas dan frekuensi kerusakan per fasilitas/lokasi. |

---

## Hint Rancangan Database

* **Users**: `nama`, `email`, `password`, `role`
* **Facilities**: `nama fasilitas`, `tipe`, `lokasi`, `kapasitas`, `deskripsi`
* **Reservations**: `nama pemesan`, `fasilitas yang dipesan`, `waktu penggunaan`, `status`
* **Reports**: `pelapor`, `fasilitas yang dilaporkan`, `kategori laporan`, `deskripsi`, `foto`, `status laporan`
