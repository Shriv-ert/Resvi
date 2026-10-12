# Panduan Instalasi Docker & Docker Compose

Panduan resmi untuk menginstal **Docker** dan **Docker Compose** pada sistem operasi Windows, Linux (Ubuntu/Debian), dan macOS.

Proyek **Resvi** berjalan sepenuhnya di dalam container Docker. Anda **Tidak perlu** menginstal PHP, Composer, atau MySQL secara manual di sistem lokal Anda. Cukup pastikan Docker dan Docker Compose telah terpasang dan berjalan aktif.

---

## Daftar Isi
1. [Windows (Docker Desktop)](#1-windows-docker-desktop)
2. [Linux: Ubuntu / Debian (Docker Engine + Compose Plugin)](#2-linux-ubuntu--debian-docker-engine--compose-plugin)
3. [Linux: Arch Linux (Docker + Docker Compose)](#3-linux-arch-linux-docker--docker-compose)
4. [macOS (Docker Desktop)](#4-macos-docker-desktop)
5. [Verifikasi Instalasi](#5-verifikasi-instalasi)
6. [Langkah Selanjutnya: Menjalankan Resvi](#6-langkah-selanjutnya-menjalankan-resvi)

---

## 1. Windows (Docker Desktop)

Metode yang direkomendasikan untuk pengguna Windows adalah menggunakan **Docker Desktop** dengan backend **WSL 2 (Windows Subsystem for Linux)**.

### Prasyarat
- Windows 10 64-bit (Home / Pro / Enterprise versi 2004 atau lebih baru) atau Windows 11.
- Fitur virtualisasi CPU aktif di sistem.

### Langkah Instalasi
1. **Aktifkan WSL 2:**
   Buka **PowerShell** sebagai Administrator (*Run as Administrator*), lalu jalankan:
   ```powershell
   wsl --install
   ```
   > Jika WSL sudah terpasang sebelumnya, pastikan versinya telah diperbarui:
   > ```powershell
   > wsl --update
   > ```

2. **Unduh Docker Desktop:**
   Unduh installer resmi dari tautan berikut:
   - [Unduh Docker Desktop untuk Windows (.exe)](https://desktop.docker.com/win/main/amd64/Docker%20Desktop%20Installer.exe)
   - Atau kunjungi [docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop/)

3. **Jalankan Installer:**
   - Buka file `Docker Desktop Installer.exe`.
   - Pastikan opsi **"Use WSL 2 instead of Hyper-V (recommended)"** dicentang.
   - Ikuti proses instalasi hingga selesai, lalu klik **Close and restart** jika sistem meminta restart komputer.

4. **Jalankan Docker Desktop:**
   - Setelah komputer menyala, buka **Docker Desktop** dari Start Menu.
   - Setujui syarat dan ketentuan (*Docker Subscription Service Agreement*).
   - Tunggu beberapa saat sampai ikon paus di pojok kiri bawah jendela Docker Desktop berwarna hijau bertuliskan **Engine running**.

---

## 2. Linux: Ubuntu / Debian (Docker Engine + Compose Plugin)

Untuk lingkungan Linux, disarankan menginstal **Docker Engine** resmi dan **Docker Compose Plugin** via repositori APT karena jauh lebih ringan dan efisien dibandingkan Docker Desktop.

### Langkah Instalasi

1. **Hapus paket lama / konflik (jika ada):**
   ```bash
   for pkg in docker.io docker-doc docker-compose docker-compose-v2 podman-docker containerd runc; do
     sudo apt-get remove -y $pkg 2>/dev/null
   done
   ```

2. **Pasang repositori resmi Docker:**
   ```bash
   # Update indeks paket dan pasang utilitas pendukung
   sudo apt-get update
   sudo apt-get install -y ca-certificates curl

   # Tambahkan GPG key resmi Docker
   sudo install -m 0755 -d /etc/apt/keyrings
   sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
   sudo chmod a+r /etc/apt/keyrings/docker.asc

   # Daftarkan repositori Docker ke APT sources
   echo \
     "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
     $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}") stable" | \
     sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

   sudo apt-get update
   ```
   > **Catatan untuk Debian:** Jika Anda menggunakan Debian murni, ganti URL repositori dan GPG key dari `https://download.docker.com/linux/ubuntu` menjadi `https://download.docker.com/linux/debian`.

3. **Instal Docker Engine dan Compose Plugin:**
   ```bash
   sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
   ```

4. **Konfigurasi Hak Akses Non-Root (Sangat Disarankan):**
   Agar dapat menjalankan perintah `docker` tanpa harus selalu mengetikkan `sudo`:
   ```bash
   sudo usermod -aG docker $USER
   newgrp docker
   ```

5. **Pastikan Layanan Docker Aktif:**
   ```bash
   sudo systemctl enable --now docker
   ```

---

## 3. Linux: Arch Linux (Docker + Docker Compose)

Arch Linux dan distribusi turunannya (seperti Manjaro, EndeavourOS, dll.) telah menyediakan paket resmi Docker dan Docker Compose di repositori resmi (`extra`).

### Langkah Instalasi

1. **Instal paket Docker & Docker Compose:**
   Buka terminal dan jalankan:
   ```bash
   sudo pacman -Syu docker docker-compose docker-buildx
   ```

2. **Aktifkan dan Jalankan Layanan Docker:**
   Gunakan systemd untuk mengaktifkan daemon Docker secara otomatis saat booting dan langsung menyalakannya:
   ```bash
   sudo systemctl enable --now docker.service
   ```

3. **Konfigurasi Hak Akses Non-Root (Sangat Disarankan):**
   Tambahkan user aktif Anda ke grup `docker` agar tidak perlu mengetikkan `sudo` setiap kali menjalankan Docker:
   ```bash
   sudo usermod -aG docker $USER
   newgrp docker
   ```
   > **Tip:** Anda juga bisa logout dan login kembali ke sesi desktop/shell agar perubahan grup berlaku menyeluruh.

---

## 4. macOS (Docker Desktop)

### Langkah Instalasi

1. **Unduh Docker Desktop for Mac:**
   Pilih file installer sesuai jenis prosesor Mac Anda:
   - **Apple Silicon (M1 / M2 / M3 / M4):** [Unduh Docker Desktop (ARM64 .dmg)](https://desktop.docker.com/mac/main/arm64/Docker.dmg)
   - **Intel Chip:** [Unduh Docker Desktop (Intel .dmg)](https://desktop.docker.com/mac/main/amd64/Docker.dmg)
   - Atau kunjungi [docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop/)

2. **Pasang Aplikasi:**
   - Buka file `Docker.dmg` yang telah diunduh.
   - Seret (*drag & drop*) ikon **Docker** ke folder **Applications**.
   - Buka aplikasi **Docker** dari Spotlight atau Launchpad.
   - Ikuti panduan di layar (*privileged helper installation*) dan setujui lisensi.

3. **Tunggu Hingga Siap:**
   - Perhatikan ikon Docker di status bar (menu bar kanan atas).
   - Tunggu hingga status menunjukkan **Docker Desktop is running**.

---

## 5. Verifikasi Instalasi

Untuk memastikan Docker dan Docker Compose sudah terpasang dengan benar dan dapat digunakan di terminal, jalankan perintah-perintah berikut:

```bash
# 1. Cek versi Docker CLI & Engine
docker --version

# 2. Cek versi Docker Compose (V2 menggunakan spasi, bukan tanda minus)
docker compose version

# 3. (Opsional) Uji coba eksekusi container
docker run --rm hello-world
```

### Output yang Diharapkan:
- `Docker version 24.x.x` (atau versi lebih baru)
- `Docker Compose version v2.x.x` (atau versi lebih baru)
- Container `hello-world` menampilkan pesan: *"Hello from Docker! This message shows that your installation appears to be working correctly."*

---

## 6. Langkah Selanjutnya: Menjalankan Resvi

Setelah verifikasi berhasil dan Docker berstatus aktif (*running*), masuk ke direktori proyek `PPK-Room-Reservation` di terminal dan jalankan script otomatis yang telah disiapkan:

### Untuk Windows (PowerShell):
```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\scripts\setup.ps1
```

### Untuk Linux / macOS (Terminal):
```bash
chmod +x scripts/setup.sh
./scripts/setup.sh
```

Script di atas akan secara otomatis:
1. Memeriksa ketersediaan Docker & Docker Compose.
2. Menyiapkan file konfigurasi `.env`.
3. Membangun (*build*) dan menyalakan seluruh container (`app`, `db`, `phpmyadmin`).
4. Menginstal dependensi Composer, generate App Key, dan menghubungkan storage link.
5. Menjalankan migrasi database serta menginjeksi data seeder awal (feeder data & akun pengguna).
6. Menampilkan URL akses aplikasi dan daftar akun default untuk login.

