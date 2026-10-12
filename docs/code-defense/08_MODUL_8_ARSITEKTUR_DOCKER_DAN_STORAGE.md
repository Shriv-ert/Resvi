# MODUL 8: ARSITEKTUR DOCKER & STORAGE SUBSYSTEM
**Fokus Arsitektur:**  
- Topologi Jaringan & Service Discovery Docker Compose  
- Mekanisme Symlink Storage Engine (`ln -sfn`) & Mitigasi Host Volume Shadowing  
- Pemetaan Hak Akses Pengguna Linux (`UID:GID`) & Keamanan Non-Root Container  
- Penegakan Standar Operasi Kontainer (*Zero Host Execution Rule*)

---

## 1. TOPOLOGI MULTI-KONTAINER & JARINGAN TERISOLASI

Aplikasi Resvi dibangun menggunakan arsitektur modular yang diatur oleh berkas `docker-compose.yml`:

```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
      args:
        UID: ${UID:-1000}
        GID: ${GID:-1000}
        USER: ${USER:-developer}
    container_name: ppk_app
    restart: unless-stopped
    working_dir: /var/www
    environment:
      TZ: ${APP_TIMEZONE:-Asia/Jakarta}
    ports:
      - "${APP_PORT:-8000}:8000"
    volumes:
      - .:/var/www
    networks:
      - ppk_network
    command: sh -c "ln -sfn ../storage/app/public public/storage && php artisan serve --host=0.0.0.0 --port=8000"
    depends_on:
      db:
        condition: service_healthy

  db:
    image: mysql:8.0
    container_name: ppk_db
    restart: unless-stopped
    environment:
      TZ: ${APP_TIMEZONE:-Asia/Jakarta}
      MYSQL_DATABASE: ${DB_DATABASE:-ppk_room_reservation}
      MYSQL_USER: ${DB_USERNAME:-laravel_user}
      MYSQL_PASSWORD: ${DB_PASSWORD:-laravel_password}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-root_password}
    ports:
      - "${FORWARD_DB_PORT:-3306}:3306"
    command: --skip-name-resolve
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - ppk_network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-p${DB_ROOT_PASSWORD:-root_password}"]
      interval: 5s
      timeout: 5s
      retries: 10

  phpmyadmin:
    image: phpmyadmin/phpmyadmin:latest
    container_name: ppk_phpmyadmin
    ports:
      - "${PMA_PORT:-8080}:80"
    environment:
      PMA_HOST: db
      PMA_PORT: 3306
    networks:
      - ppk_network
    depends_on:
      - db

networks:
  ppk_network:
    driver: bridge

volumes:
  mysql_data:
    driver: local
```

### Karakteristik Jaringan & Service Discovery:
1. **Bridge Network (`ppk_network`):** Driver bridge internal menyediakan DNS Resolver otomatis. Nama layanan (`db`, `app`) bertindak sebagai hostname internal.
2. **Koneksi Database:** Konfigurasi `.env` mengatur `DB_HOST=db`. Ketika PDO PHP di `ppk_app` membuka koneksi TCP ke `db:3306`, Docker mengarahkannya ke IP privat kontainer `ppk_db` secara transparan.
3. **Healthcheck Dependency:** Layanan `app` tidak akan dinyalakan sebelum `ppk_db` siap melayani koneksi database melalui aturan:
   ```yaml
   depends_on:
     db:
       condition: service_healthy
   ```
   Ini mencegah aplikasi mengalami *PDOException: Connection Refused* saat kontainer pertama kali di-boot.

---

## 2. BEDAH MEKANISME STORAGE SYMLINK ENGINE (`ln -sfn`)

Pada `docker-compose.yml` baris 21:
```bash
command: sh -c "ln -sfn ../storage/app/public public/storage && php artisan serve --host=0.0.0.0 --port=8000"
```

### A. Anatomi Perintah & Flag Unix:
- `ln`: Utilitas POSIX standard untuk membuat tautan (*link*).
- `-s` (*symbolic*): Membuat tautan simbolik (*soft link*) alih-alih tautan fisik (*hard link*). Tautan ini menghubungkan file pointer tanpa menduplikasi blok inode storage.
- `-f` (*force*): Memaksa penghapusan tautan target jika telah ada sebelumnya, mencegah galat *File exists*.
- `-n` (*no-dereference*): Jika target `public/storage` sudah ada dan merupakan symlink ke sebuah direktori, perlakukan sebagai file biasa tanpa masuk ke dalam direktori tersebut (*prevents nested link recursion bug*).
- Relative Path `../storage/app/public`: Ditautkan dari sudut pandang direktori `public/`.

### B. Mengapa Tidak Dijalankan Saat Build di Dockerfile?
Pada pengembangan berbasis Docker, kita menggunakan *Bind Mount* host:
```yaml
volumes:
  - .:/var/www
```
Jika kita membuat symlink di Dockerfile (`RUN php artisan storage:link`), maka saat kontainer dijalankan di mesin host, volume mount `.:/var/www` dari host akan menimpa (*shadow/overlay*) seluruh isi direktori `/var/www` di dalam kontainer. Seluruh perubahan file atau symlink yang dibuat saat fase build Docker image akan hilang tertimpa oleh state folder lokal host.

Dengan meletakkan perintah `ln -sfn` di parameter `command` runtime:
1. Kontainer terlebih dahulu selesai memasang volume bind mount `.:/var/www`.
2. Symlink dibuat secara *fresh* dan atomik di folder aktif.
3. Web server `php artisan serve` baru kemudian dijalankan, menjamin file bukti laporan (US-06) dan foto fasilitas (US-16) selalu dapat diakses peramban di URL `/storage/...`.

---

## 3. MANAJEMEN HAK AKSES PENGGUNA LINUX (UID:GID MAPPING)

Pada `Dockerfile`:
```dockerfile
ARG UID=1000
ARG GID=1000
ARG USER=developer

RUN groupadd -g ${GID} ${USER} && \
    useradd -u ${UID} -g ${USER} -m ${USER}

USER ${USER}
```

### Analisis Masalah Hak Akses (*Permission Denied Issue*):
Pada sistem operasi Linux, hak akses file dikontrol oleh nilai angka `UID` (*User ID*) dan `GID` (*Group ID*).
- Secara default, proses di dalam kontainer Docker berjalan sebagai user `root` (UID 0).
- Jika kontainer berjalan sebagai root, setiap file yang dibuat oleh Laravel (seperti log di `storage/logs/laravel.log`, session di `storage/framework/sessions/`, atau cache di `bootstrap/cache/`) akan dimiliki oleh `root:root` di mesin host.
- Akibatnya, developer di mesin host tidak dapat mengedit, menghapus, atau melakukan `git checkout` tanpa hak akses `sudo`. Sebaliknya, jika hak akses folder tidak sinkron, Laravel akan melempar galat fatal `The stream or file ".../storage/logs/laravel.log" could not be opened: failed to open stream: Permission denied`.

### Solusi Pemetaan Dinamis:
Melalui argumen build `UID=${UID:-1000}` dan `GID=${GID:-1000}`, image dikompilasi dengan akun non-root yang memiliki ID persis sama dengan pengguna aktif mesin Linux host. Proses PHP berjalan sebagai user `developer`, sehingga seluruh operasi file sharing antara host dan kontainer berlangsung mulus tanpa konflik perizinan.

---

## 4. STANDAR PENGOPERASIAN KONTAINER (*ZERO HOST EXECUTION*)

Seluruh pengembang dan penguji dilarang menjalankan perintah PHP atau Composer langsung di terminal host. Seluruh perintah diagnostik dijalankan di dalam lingkungan runtime kontainer:

### Diagnostic Cheat-Sheet (Container Level):
```bash
# 1. Jalankan migrasi dan seeder database
docker compose exec app php artisan migrate --seed

# 2. Cek status rute aplikasi
docker compose exec app php artisan route:list

# 3. Jalankan unit & feature testing
docker compose exec app php artisan test

# 4. Sinkronisasi status reservasi berbasis waktu
docker compose exec app php artisan reservations:sync-statuses

# 5. Masuk ke terminal interaktif kontainer
docker compose exec app bash

# 6. Monitor log runtime Laravel secara real-time
docker compose exec app tail -f storage/logs/laravel.log
```

---

## 5. CODE DEFENSE & ORAL EXAMINATION SIMULATOR

### Uji 1: Mitigasi Host Volume Shadowing pada Storage Symlink
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa Anda tidak memasukkan perintah `RUN php artisan storage:link` ke dalam `Dockerfile`? Mengapa harus menggunakan perintah shell `ln -sfn` di parameter `command` `docker-compose.yml`?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Karena konfigurasi Docker Compose kami menggunakan **Host Bind Mount (`.:/var/www`)** untuk mendukung live-code reload selama pengembangan.  
> Jika symlink dibuat saat tahap build image di `Dockerfile`, sistem file kontainer akan tertimpa (*shadowed*) oleh direktori host begitu kontainer dinyalakan. Jika mesin host belum memiliki folder symlink yang valid, symlink dari image akan hilang atau rusak (*broken/dangling symlink*).  
> Dengan mengeksekusi `ln -sfn ../storage/app/public public/storage` di dalam parameter `command` runtime saat boot kontainer, symlink dibuat secara idempoten dan atomik setelah volume mount host berhasil dipasang, menjamin akses file statis publik selalu valid."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Host Bind Mount Shadowing]`, `[Build-time vs Run-time Execution]`, `[Idempotent ln -sfn Flags]`, `[Dangling Symlink Mitigation]`.

---

### Uji 2: Keamanan Non-Root & Pemetaan UID:GID Linux
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa kontainer aplikasi Anda tidak dibiarkan berjalan sebagai pengguna `root` bawaan Docker? Apa bahayanya dan bagaimana konfigurasi Anda menyelesaikannya?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Menjalankan aplikasi web sebagai `root` melanggar prinsip keamanan **Least Privilege**. Jika terjadi kerentanan *Remote Code Execution (RCE)* pada aplikasi, penyerang langsung mendapatkan hak akses penuh sebagai root di dalam kontainer yang berpotensi memicu *Container Escape*.  
> Selain faktor keamanan, menjalankan kontainer sebagai root pada Linux menyebabkan masalah kepemilikan berkas (*file permission issues*): file log atau file upload yang dibuat kontainer akan dimiliki oleh root di host machine sehingga tidak bisa diedit oleh developer.  
> Kami menyelesaikannya melalui Dockerfile ARG `UID: ${UID:-1000}` dan `GID: ${GID:-1000}` yang memetakan user non-root `developer` secara identik dengan akun pengguna Linux di host machine."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Principle of Least Privilege]`, `[Container Escape Prevention]`, `[Linux File Ownership / Permission Denied Prevention]`, `[Dynamic UID:GID Build Mapping]`.

---

### Uji 3: Service Discovery & Healthcheck Orchestration
> **Tanya (Dosen Penguji / Tech Lead):**  
> *"Mengapa di berkas `.env` nilai `DB_HOST` disetel ke `db` bukan `localhost`, dan apa fungsi konfigurasi `condition: service_healthy` pada blok `depends_on`?"*  
>  
> **Jawab (Rekomendasi Jawaban Anda):**  
> *"Dalam arsitektur Docker Compose, setiap kontainer memiliki *Network Namespace* dan loopback interface (`localhost / 127.0.0.1`) terisolasi sendiri. Jika `DB_HOST` disetel ke `localhost`, aplikasi di `ppk_app` akan mencari server MySQL di dalam kontainer `ppk_app` sendiri, bukan di `ppk_db`. Melalui Docker Bridge Network, nama service `db` secara otomatis di-resolve oleh internal DNS Docker ke alamat IP kontainer MySQL.  
> Sementara itu, `depends_on` standar hanya menunggu kontainer dinyalakan, bukan menunggu database siap menerima koneksi. Dengan menyematkan `condition: service_healthy` dan healthcheck `mysqladmin ping`, kontainer Laravel dipastikan tidak akan melayani request sampai database MySQL benar-benar selesai melakukan inisialisasi buffer pool dan siap menerima kueri PDO."*  
>  
> **Poin Kunci Wajib Sebut:**  
> `[Network Namespace Isolation]`, `[Docker Embedded DNS Service Discovery]`, `[Race Condition at Startup]`, `[mysqladmin ping Healthcheck Guard]`.

