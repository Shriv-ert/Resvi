# Keputusan Global — PPK Room Reservation

**Status dokumen:** Acuan revisi SRS

## 1. Tujuan

Dokumen ini mencatat keputusan yang berlaku lintas aktor sebelum SRS Pengunjung, Pengguna, Petugas, dan Admin direvisi. Jika isi SRS lama bertentangan dengan dokumen ini, keputusan dalam dokumen ini menjadi acuan revisi berikutnya.

Keputusan bersumber dari hasil diskusi tim dan konsultasi dengan dosen.

## 2. Istilah Umum

Istilah **objek reservasi** digunakan dalam dokumentasi ini untuk mencakup:

- Fasilitas tetap, seperti ruang kelas, aula, laboratorium, dan lapangan.
- Alat yang menetap di ruangan dan tidak dibawa keluar.

Pada antarmuka, seluruh objek tersebut disebut **Fasilitas**. Istilah **objek reservasi** hanya digunakan dalam dokumentasi ketika perlu membicarakan fasilitas dan alat secara umum. Penamaan model data akan ditentukan ketika rancangan basis data dibahas lebih rinci.

## 3. Keputusan yang Telah Dikonfirmasi

### 3.1 Fasilitas dan alat menggunakan logika reservasi yang sama

- Alat yang dikelola sistem diasumsikan menetap di ruangan dan tidak berpindah tempat.
- Fasilitas dan alat menggunakan alur pengajuan, pemeriksaan jadwal, bentrokan, persetujuan, pembatalan, dan perubahan status reservasi yang sama.
- Satu kartu mewakili satu objek reservasi tertentu.
- Satu objek hanya dapat digunakan oleh satu reservasi pada rentang waktu yang sama.
- Ketersediaan jadwal ditentukan berdasarkan bentrokan waktu pada objek yang sama.
- Tidak ada konsep jumlah stok dalam proses reservasi.
- Tidak ada pemilihan atau alokasi unit dari sekumpulan stok.
- Tidak ada lokasi pengambilan alat.
- Tidak ada proses penyerahan dan pengembalian alat.
- Tidak ada status transaksi khusus alat seperti `Dipinjam`, `Terlambat`, atau `Dikembalikan`.

Detail atribut dan implementasi teknis fasilitas serta alat akan dibahas pada tahap berikutnya. Penyamaan logika tidak mengharuskan seluruh atribut keduanya identik.

### 3.2 Tidak ada registrasi mandiri

- Sistem tidak menyediakan registrasi mandiri bagi Pengguna maupun Petugas.
- Akun dibuat langsung oleh Admin atau disediakan melalui seeding awal.
- Sistem tetap menyediakan login dan logout.
- Dosen telah mengonfirmasi bahwa tidak adanya registrasi diperbolehkan selama pembuatan akun ditangani oleh Admin.
- Keputusan ini menggantikan asumsi sebelumnya bahwa perbedaan dengan ketentuan registrasi pada `CASE.md` masih menunggu konfirmasi.

### 3.3 Pengunjung dapat melihat detail

- Pengunjung dapat membuka daftar objek reservasi tanpa login.
- Pengunjung dapat membuka halaman detail objek reservasi.
- Pengunjung dapat memeriksa ketersediaan jadwal tanpa login.
- Pengunjung tetap tidak dapat melihat identitas pemesan, tujuan penggunaan, atau detail reservasi yang menyebabkan bentrokan.
- Pengunjung harus login sebelum dapat mengajukan reservasi.
- Keputusan ini menggantikan asumsi sebelumnya bahwa Pengunjung tidak boleh membuka detail fasilitas.

### 3.4 Homepage langsung menampilkan daftar

- Sistem tidak menggunakan landing page yang hanya berisi informasi umum dan navigasi.
- Saat sistem diakses sebagai Pengunjung atau Pengguna, halaman pertama menampilkan daftar objek reservasi. Petugas dan Admin tetap masuk ke halaman kerja perannya masing-masing setelah login.
- Halaman tersebut tetap disebut **Homepage**.
- Homepage menjadi tempat untuk melihat kartu, mencari, dan memfilter objek reservasi.
- Fasilitas tetap dan alat ditampilkan dalam satu daftar yang sama.
- Perbedaan jenis objek ditangani melalui tipe fasilitas dan filter tipe.
- Tipe fasilitas mengikuti objek yang disebutkan pada user story dengan label antarmuka: `Kelas`, `Aula`, `Laboratorium`, `Alat`, dan `Lapangan`.
- Tidak ada halaman Daftar Fasilitas terpisah yang hanya menduplikasi isi Homepage.
- Perbedaan kemampuan berdasarkan aktor ditampilkan melalui aksi yang tersedia setelah pengguna login.

### 3.5 Pencarian dan filter pada Homepage

- Pencarian dilakukan berdasarkan nama.
- Filter yang disediakan adalah tipe, lokasi, dan kapasitas.
- Kapasitas boleh bernilai `null`, terutama untuk alat yang tidak mempunyai konsep kapasitas pengguna.
- Ketika filter kapasitas digunakan, fasilitas dengan kapasitas `null` tidak termasuk dalam hasil filter kapasitas tersebut.
- Pencarian dan beberapa filter dapat digunakan bersamaan.
- Perubahan pencarian atau filter memperbarui daftar pada Homepage tanpa membuka halaman baru.
- Filter berdasarkan status operasional tidak disediakan.
- Status operasional tetap boleh ditampilkan pada kartu atau detail meskipun tidak dapat digunakan sebagai filter.

### 3.6 Status operasional dan ketersediaan jadwal tetap dipisahkan

Status operasional objek:

- `Dapat digunakan`
- `Dalam perbaikan`

Status hasil pemeriksaan jadwal:

- `Tersedia`
- `Tidak tersedia`

Status operasional `Dapat digunakan` berarti objek boleh dilanjutkan ke proses pengecekan atau reservasi. Status tersebut tidak menjamin jadwal yang dipilih tersedia karena masih dapat bertabrakan dengan reservasi lain.

Objek berstatus operasional `Dalam perbaikan` tidak dapat direservasi untuk tanggal apa pun, termasuk tanggal setelah masa sekarang. Antarmuka harus menonaktifkan atau menyembunyikan aksi reservasi pada objek tersebut. Server tetap wajib menolak pengajuan terhadap objek `Dalam perbaikan` meskipun permintaan dikirim dengan melewati antarmuka.

Status `Tidak tersedia` digunakan sebagai hasil negatif pengecekan ketersediaan. Penyebabnya dapat berupa bentrokan jadwal atau objek yang secara operasional tidak dapat digunakan. Sistem tidak memerlukan status hasil pengecekan ketiga bernama `Dalam perbaikan`.

Kode respons yang digunakan ketika server menolak pengajuan terhadap objek `Dalam perbaikan`, misalnya `409` atau `422`, belum ditentukan dan akan diputuskan pada tahap implementasi API atau controller.

### 3.7 Aturan waktu reservasi

- Jam operasional adalah 07.00–20.00.
- Waktu mulai dan selesai menggunakan kelipatan 30 menit.
- Waktu selesai harus lebih besar daripada waktu mulai.
- Waktu mulai dan selesai berada pada tanggal yang sama.
- Waktu mulai pengajuan baru harus lebih besar daripada waktu saat permintaan diproses di server; jadwal yang sudah mencapai waktu mulai tidak dapat diajukan.
- Validasi waktu dan bentrokan wajib dilakukan di sisi server.
- Pemeriksaan yang dilakukan sebelumnya oleh pengguna tidak menggantikan pemeriksaan ulang ketika pengajuan dikirim atau disetujui.
- Pengajuan yang masih `Menunggu` tepat saat waktu mulai tercapai menjadi `Kedaluwarsa`. Petugas hanya dapat menyetujui atau menolak secara manual sebelum waktu mulai. Aturan ini diperiksa terhadap waktu saat tindakan diproses di server, meskipun pembaruan tampilan/status terjadwal belum berjalan.

Form penting, termasuk login, pembuatan akun, pengelolaan fasilitas, pengajuan dan pembatalan reservasi, laporan, serta aksi perubahan status, harus memberi validasi dan umpan balik pada sisi klien. Pemeriksaan yang menentukan keabsahan tindakan tetap wajib diulang di sisi server. Ini mengikuti ketentuan validasi pada `CASE.md`.

### 3.8 Status laporan

Status laporan yang digunakan adalah:

- `Baru`: laporan telah dikirim dan belum mulai ditangani.
- `Diproses`: Petugas sudah mulai menangani laporan.
- `Selesai`: penanganan selesai dan catatan resolusi tersedia.
- `Ditolak`: laporan tidak dapat ditindaklanjuti dan alasan penolakan tersedia.

Transisi laporan yang diizinkan adalah `Baru → Diproses → Selesai` atau `Baru → Diproses → Ditolak`. Petugas harus memilih **Mulai Proses** sebelum dapat menyelesaikan atau menolak laporan. `Selesai` dan `Ditolak` adalah status final.

### 3.9 Form reservasi bertahap untuk Pengunjung dan Pengguna

- Pengunjung dan Pengguna membuka Form Reservasi yang sama dari halaman Detail Fasilitas.
- Form Reservasi dapat diakses tanpa login, tetapi Pengunjung tidak dapat mengirim pengajuan reservasi.
- Tahap awal form memuat fasilitas yang dipilih, tanggal, waktu mulai, waktu selesai, dan tombol **Cek Ketersediaan**.
- Jika hasil pengecekan `Tidak tersedia`, proses tidak dapat dilanjutkan. Pengunjung atau Pengguna dapat mengubah jadwal dan menjalankan pengecekan kembali pada form yang sama.
- Perubahan tanggal, waktu mulai, atau waktu selesai membatalkan hasil pengecekan sebelumnya.
- Jika hasil pengecekan `Tersedia`, Pengunjung melihat aksi **Login untuk Melanjutkan**.
- Jika hasil pengecekan `Tersedia`, Pengguna yang sudah login dapat melengkapi tujuan penggunaan dan mengirim pengajuan.
- Fasilitas berstatus operasional `Dalam perbaikan` tidak dapat dilanjutkan ke Form Reservasi.
- Sistem menyimpan draft sementara yang berisi fasilitas, tanggal, waktu mulai, dan waktu selesai ketika Pengunjung melanjutkan ke login.
- Setelah login berhasil, Pengguna dikembalikan ke Form Reservasi yang sama dengan data draft tetap terisi.
- Draft sementara bukan reservasi, tidak mempunyai status reservasi, dan tidak mengunci jadwal.
- Mekanisme penyimpanan draft dapat menggunakan session aplikasi.
- Sistem wajib memeriksa kembali status operasional, validitas waktu, dan bentrokan ketika pengajuan dikirim.
- Jika jadwal sudah tidak tersedia pada pemeriksaan terakhir, pengajuan tidak dibuat dan Pengguna tetap berada pada Form Reservasi untuk memilih jadwal lain.

### 3.10 Aturan tambahan Pengguna

- Tidak terdapat batas maksimal seberapa jauh tanggal reservasi dapat dipilih di masa depan.
- Satu Pengguna boleh mempunyai reservasi pada waktu yang bertabrakan apabila fasilitasnya berbeda.
- Pemeriksaan bentrokan dilakukan terhadap jadwal pada fasilitas yang sama, bukan terhadap seluruh jadwal milik Pengguna.
- Sistem menolak pengajuan duplikat dari Pengguna yang sama untuk fasilitas, tanggal, waktu mulai, dan waktu selesai yang sama selama pengajuan sebelumnya masih `Menunggu` atau `Disetujui`.
- Form Laporan dapat dibuka dari Homepage maupun Detail Fasilitas.
- Jika Form Laporan dibuka dari Detail Fasilitas, fasilitas otomatis terisi.
- Jika Form Laporan dibuka dari Homepage, Pengguna memilih satu fasilitas pada form.
- Kedua jalur menggunakan Form Laporan yang sama.
- Draft reservasi setelah login hanya dapat dilanjutkan oleh akun dengan peran Pengguna.
- Jika akun Petugas atau Admin login ketika terdapat draft reservasi, akun diarahkan ke halaman sesuai perannya dan tidak dapat melanjutkan pengajuan reservasi.

### 3.11 Keputusan Petugas

- Setelah login, Petugas langsung masuk ke halaman Antrian Reservasi; tidak ada Homepage statistik khusus Petugas.
- Navigasi Petugas memisahkan Antrian Reservasi, Riwayat Reservasi, Laporan, Riwayat Laporan, Fasilitas, dan Dalam Perbaikan. Daftar Fasilitas diperlukan agar Petugas dapat membuka Detail Fasilitas yang masih `Dapat digunakan` dan menandainya `Dalam perbaikan` tanpa laporan sumber.
- Antrian Reservasi hanya berisi status aktif `Menunggu`, `Disetujui`, dan `Berlangsung`; status final `Selesai`, `Ditolak`, `Dibatalkan oleh pengguna`, `Dibatalkan oleh petugas`, dan `Kedaluwarsa` muncul di Riwayat Reservasi.
- Laporan kerja hanya berisi `Baru` dan `Diproses`; status final `Selesai` dan `Ditolak` muncul di Riwayat Laporan.
- Daftar kerja menyediakan satu pencarian di kiri atas: nama pemohon atau fasilitas untuk reservasi, nama pelapor atau fasilitas untuk laporan. Di kanan atas tersedia filter tipe fasilitas dan status transaksi/laporan. Halaman Fasilitas serta Dalam Perbaikan menyediakan pencarian nama fasilitas dan filter tipe; Dalam Perbaikan tidak memerlukan filter status karena seluruh entrinya mempunyai status operasional yang sama.
- Urutan default antrian memprioritaskan `Menunggu` untuk reservasi dan `Baru` untuk laporan. Di dalam setiap grup status, pengajuan atau laporan yang paling dahulu dibuat ditampilkan paling atas.
- Riwayat Reservasi diurutkan menurut waktu pengajuan tertua ke terbaru. Riwayat Laporan diurutkan menurut waktu pengiriman tertua ke terbaru.
- Pembatalan mendesak cukup meminta alasan dalam teks bebas; tidak ada kategori alasan yang wajib dipilih.
- Semua Petugas dapat mengakses dan menangani seluruh fasilitas, reservasi, dan laporan. Banyak Petugas dapat menangani fasilitas yang sama; tidak ada penugasan eksklusif atau relasi penugasan Petugas–fasilitas yang diwajibkan.
- Sistem mencatat Petugas pelaku setiap perubahan, tetapi catatan tersebut tidak membatasi Petugas lain untuk melanjutkan penanganan.
- Penolakan manual reservasi `Menunggu` wajib disertai alasan dan bersifat final. Reservasi yang ditolak tetap dapat dilihat, tetapi tidak dapat dikembalikan ke `Menunggu` atau disetujui kemudian.
- Pembatalan reservasi `Disetujui` oleh Petugas hanya untuk keadaan mendesak dan wajib disertai alasan.
- Laporan harus berstatus `Diproses` sebelum dapat menjadi `Selesai` atau `Ditolak`; penyelesaian wajib disertai catatan resolusi dan penolakan wajib disertai alasan.
- Petugas dapat mengubah status operasional fasilitas dari Detail Laporan atau Detail Fasilitas. Perubahan ke `Dalam perbaikan` wajib disertai alasan; perubahan ke `Dapat digunakan` wajib disertai catatan pemeriksaan atau perbaikan. Jika perubahan berasal dari laporan, laporan harus sudah `Diproses` dan dicatat sebagai sumber. Perubahan langsung dari Detail Fasilitas tidak mewajibkan laporan sumber.
- Ketika fasilitas menjadi `Dalam perbaikan`, pengajuan yang masih `Menunggu` sebelum waktu mulai otomatis menjadi `Ditolak`, reservasi `Disetujui` yang belum dimulai otomatis menjadi `Dibatalkan oleh petugas`, dan reservasi `Berlangsung` tidak dibatalkan otomatis. Pengajuan yang telah mencapai waktu mulai menjadi `Kedaluwarsa` menurut aturan waktu. Alasan perubahan status fasilitas digunakan sebagai alasan penolakan atau pembatalan otomatis.
- Petugas mengelola status operasional; Admin mengelola data master fasilitas seperti nama, tipe, lokasi, kapasitas, serta aktif/nonaktif data. Admin tidak otomatis memperoleh wewenang memproses reservasi atau laporan.
- Perubahan oleh dua Petugas terhadap record yang sama harus divalidasi terhadap status terbaru di server agar tidak menghasilkan transisi yang bertentangan.

### 3.12 Pembuatan akun dan penggantian kata sandi oleh Admin

- Akun Admin pertama disediakan melalui seeding awal agar Admin dapat mulai membuat akun lain.
- Admin membuat akun Pengguna dan Petugas secara langsung; tidak ada registrasi mandiri.
- Saat akun dibuat, sistem menghasilkan kata sandi awal dan menampilkannya satu kali kepada Admin untuk disampaikan kepada pemilik akun di luar sistem.
- Pengguna dan Petugas tidak diwajibkan mengganti kata sandi saat login pertama, karena alur tersebut tidak diminta dalam user story yang disepakati.
- Jika Pengguna atau Petugas lupa kata sandi, Admin dapat menetapkan kata sandi baru untuk akun tersebut.
- Admin tidak dapat melihat kata sandi lama yang tersimpan.
- Penonaktifan akun Pengguna atau Petugas tidak termasuk cakupan sistem yang disepakati. Penonaktifan **data fasilitas** tetap merupakan kewenangan Admin sesuai User Story 16.

### 3.13 Pengelolaan fasilitas dan rekap Admin

- Fasilitas baru langsung berstatus data master aktif dan status operasional `Dapat digunakan`.
- Admin dapat mengaktifkan kembali data fasilitas yang pernah dinonaktifkan. Pengaktifan data master tidak otomatis mengubah status operasional; fasilitas yang masih `Dalam perbaikan` tetap tidak dapat direservasi.
- Admin hanya dapat menonaktifkan fasilitas jika tidak ada reservasi `Disetujui` atau `Berlangsung` pada fasilitas tersebut. Pemeriksaan harus diulang di server saat konfirmasi.
- Jika penonaktifan berhasil, semua pengajuan yang masih `Menunggu` dan belum mencapai waktu mulai pada fasilitas tersebut otomatis menjadi `Ditolak` dengan alasan fasilitas dinonaktifkan. Pengajuan yang telah mencapai waktu mulai menjadi `Kedaluwarsa` menurut aturan waktu. Penonaktifan dan penolakan otomatis harus diproses konsisten terhadap tindakan bersamaan.
- Persetujuan Petugas harus memastikan data master fasilitas masih aktif. Pemeriksaan persetujuan dan penonaktifan Admin harus saling aman terhadap tindakan bersamaan sehingga fasilitas tidak dapat dinonaktifkan tepat saat reservasinya disetujui.
- Rekap Admin menggunakan periode bulanan, dipilih berdasarkan bulan dan tahun; periode bawaan adalah bulan berjalan.
- Okupansi bulanan per fasilitas adalah total durasi reservasi `Selesai` dibagi total durasi slot jam operasional saat data master fasilitas aktif pada bulan itu, dikalikan 100%. Jam operasional 07.00–20.00 berarti 13 jam atau 26 slot per hari. Slot ketika status operasional `Dalam perbaikan` tetapi data master masih aktif tetap masuk penyebut; slot ketika data master nonaktif tidak masuk.
- Okupansi per lokasi menggunakan jumlah durasi reservasi `Selesai` dan jumlah slot aktif seluruh fasilitas di lokasi tersebut, bukan rata-rata persentase fasilitas. Jika penyebut nol, persentase tidak dapat dihitung. Ukuran ini mencerminkan jadwal reservasi yang selesai, bukan penggunaan fisik yang diverifikasi terpisah.
- Frekuensi kerusakan bulanan dihitung dari banyaknya perubahan status operasional `Dapat digunakan → Dalam perbaikan` pada bulan tersebut, termasuk perubahan tanpa laporan sumber. Satu perubahan adalah satu kejadian; durasi perbaikan dan jumlah laporan tidak menambah hitungan. Frekuensi per lokasi adalah jumlah kejadian fasilitas di lokasi tersebut.
- Sistem mempertahankan riwayat perubahan status aktif/nonaktif dan status operasional agar rekap bulan lalu dapat dihitung konsisten.

### 3.14 Penamaan data dan struktur dasar laporan

- Nama entitas dan kode menggunakan bahasa Inggris mengikuti konvensi Laravel: model berbentuk tunggal PascalCase dan tabel berbentuk jamak `snake_case`.
- Entitas utama menggunakan model `User`, `Facility`, `Reservation`, dan `Report`, dengan tabel `users`, `facilities`, `reservations`, dan `reports`.
- Kolom menggunakan bahasa Inggris dan `snake_case`, misalnya `facility_id`, `reporter_id`, `start_time`, `end_time`, `purpose`, `category`, `description`, `photo_path`, `status`, `resolution_notes`, dan `rejection_reason`.
- Struktur laporan umum berlaku untuk semua tipe fasilitas. Laporan minimal menyimpan pelapor, satu fasilitas, kategori, deskripsi, foto opsional, status, catatan resolusi atau alasan penolakan, serta waktu pencatatan.
- Tidak ada field laporan khusus berdasarkan tipe fasilitas pada cakupan awal. Nomor inventaris, titik kerusakan, tingkat urgensi, atau detail khusus lain dapat ditulis dalam deskripsi bila diperlukan.
- Kategori laporan tetap wajib karena disebutkan pada User Story 6. Daftar nilai kategorinya masih perlu ditetapkan; kategori merupakan pilihan tetap dan bukan data yang dikelola Admin pada cakupan saat ini.

## 4. Arah Flow yang Telah Disepakati

### 4.1 Pengunjung

1. Pengunjung membuka Homepage yang langsung menampilkan daftar objek reservasi.
2. Pengunjung mencari atau memfilter daftar jika diperlukan.
3. Pengunjung mengeklik kartu untuk membuka detail.
4. Pengunjung membuka Form Reservasi dari halaman detail.
5. Pengunjung mengisi tanggal dan waktu lalu menjalankan pengecekan ketersediaan.
6. Jika jadwal tersedia, Pengunjung menekan **Login untuk Melanjutkan**.
7. Sistem menyimpan draft sementara dan mengarahkan Pengunjung ke login.
8. Setelah login berhasil, Pengguna kembali ke Form Reservasi dengan data draft tetap terisi.
9. Pengguna melengkapi tujuan penggunaan dan mengirim pengajuan.

### 4.2 Pengguna

1. Pengguna membuka Homepage yang langsung menampilkan daftar objek reservasi.
2. Pengguna mencari atau memfilter daftar jika diperlukan.
3. Pengguna mengeklik kartu untuk membuka detail.
4. Pengguna membuka Form Reservasi dari halaman detail.
5. Pengguna mengisi tanggal dan waktu lalu menjalankan pengecekan ketersediaan di dalam form.
6. Jika jadwal tersedia, Pengguna melengkapi tujuan penggunaan.
7. Pengguna hanya dapat mengirim form jika jadwal yang sedang terisi telah diperiksa dan hasilnya `Tersedia`.
8. Sistem memeriksa ulang ketersediaan ketika pengajuan dikirim.

## 5. Perbedaan yang disadari terhadap `CASE.md`

- **Ketentuan umum autentikasi dan User Story 15:** `CASE.md` menyebut registrasi, sedangkan sistem tidak menyediakan registrasi mandiri maupun verifikasi akun hasil registrasi. Dosen telah mengonfirmasi bahwa akun boleh dibuat Admin; User Story 15 sendiri berlaku hanya jika registrasi mandiri diimplementasikan.
- **Deskripsi aktor Pengunjung:** `CASE.md` menyebut Pengunjung melihat daftar dan ketersediaan tanpa detail. Setelah klarifikasi dosen, Pengunjung boleh membuka Detail Fasilitas, tetapi tetap tidak melihat detail pemesan atau reservasi orang lain.
- **User Story 1:** `CASE.md` menyebut status ketersediaan per slot bersama daftar fasilitas. Keputusan antarmuka menampilkan status operasional pada kartu dan menempatkan pemeriksaan ketersediaan jadwal setelah Detail Fasilitas, pada Form Reservasi bersama. Kemampuan melihat ketersediaan per pilihan slot tetap ada bagi Pengunjung dan Pengguna, tetapi lokasi penyajiannya berbeda dari bunyi awal user story.
- **User Story 4:** batas pembatalan diperjelas menjadi sebelum waktu mulai, tanpa jeda minimal, dengan alasan wajib.
- **User Story 8:** pilihan `dashboard/antrian` dipenuhi melalui halaman antrian kerja terpisah, bukan dashboard statistik.
- **User Story 12:** istilah kembali ke status `aktif` ditafsirkan sebagai status operasional `Dapat digunakan`, bukan status aktif/nonaktif data master milik Admin. Petugas dapat mengubah status operasional dari Detail Laporan yang sedang `Diproses` atau langsung dari Detail Fasilitas. Jalur kedua tidak mewajibkan laporan sumber dan merupakan keputusan tambahan tim.
- **Deskripsi umum Admin:** teks `CASE.md` menyebut Admin memproses kedua alur, tetapi User Story 13–17 hanya memberikan pengelolaan akun, master fasilitas, dan rekap. SRS mengikuti rincian user story; persetujuan reservasi serta penanganan laporan tetap milik Petugas.

## 6. Hal yang Akan Dibahas Lebih Lanjut

Hal berikut belum diputuskan dalam dokumen ini:

- Daftar nilai kategori laporan.
- Rancangan ERD terperinci, termasuk primary key, foreign key, indeks, constraint, dan tabel riwayat/audit yang diperlukan.

Pilihan kategori perlu ditetapkan sebelum Form Laporan difinalkan. Detail ERD dapat dibahas pada tahap rancangan basis data tanpa mengubah alur aktor yang telah disepakati.
