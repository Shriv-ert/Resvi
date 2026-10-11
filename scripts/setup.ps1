# =============================================================================
# Resvi — Setup Script (Windows PowerShell)
# =============================================================================
# Menjalankan seluruh langkah setup awal proyek menggunakan Docker.
# Pastikan Docker Desktop sudah terinstal dan berjalan.
#
# Penggunaan:
#   Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
#   .\scripts\setup.ps1
# =============================================================================

$ErrorActionPreference = "Stop"

function Write-Step($msg)    { Write-Host "`n[STEP] $msg" -ForegroundColor Cyan }
function Write-Ok($msg)      { Write-Host "[OK] $msg" -ForegroundColor Green }
function Write-Warn($msg)    { Write-Host "[WARN] $msg" -ForegroundColor Yellow }
function Write-Err($msg)     { Write-Host "[ERROR] $msg" -ForegroundColor Red }

# Pindah ke root proyek (parent dari scripts/)
Set-Location (Split-Path -Parent $PSScriptRoot)
if (-not $PSScriptRoot) {
    Set-Location (Split-Path -Parent $MyInvocation.MyCommand.Path)
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Resvi - Setup"                          -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# --- 1. Cek Docker ---
Write-Step "Memeriksa Docker..."
try {
    docker info 2>&1 | Out-Null
    Write-Ok "Docker ditemukan dan berjalan."
} catch {
    Write-Err "Docker tidak ditemukan atau tidak berjalan."
    Write-Host "  -> https://docs.docker.com/desktop/install/windows-install/"
    exit 1
}

# --- 2. Cek Docker Compose ---
Write-Step "Memeriksa Docker Compose..."
try {
    docker compose version 2>&1 | Out-Null
    Write-Ok "Docker Compose ditemukan."
} catch {
    Write-Err "Docker Compose tidak ditemukan."
    Write-Host "  -> https://docs.docker.com/compose/install/"
    exit 1
}

# --- 3. Salin .env ---
Write-Step "Menyiapkan file .env..."
if (Test-Path ".env") {
    Write-Warn ".env sudah ada, dilewati."
} else {
    Copy-Item ".env.example" ".env"
    Write-Ok ".env berhasil dibuat dari .env.example."
}

# --- 4. Build container ---
Write-Step "Build container Docker..."
docker compose build
if ($LASTEXITCODE -ne 0) { Write-Err "Build gagal."; exit 1 }
Write-Ok "Build selesai."

# --- 5. Jalankan database terlebih dahulu ---
Write-Step "Menjalankan database..."
docker compose up -d db
if ($LASTEXITCODE -ne 0) { Write-Err "Gagal menjalankan database."; exit 1 }
Write-Ok "Database container berjalan."

# --- 6. Tunggu database siap ---
Write-Step "Menunggu database siap..."
$retries = 30
$dbReady = $false
while ($retries -gt 0) {
    $status = (docker inspect --format='{{.State.Health.Status}}' ppk_db 2>$null)
    if ($status -eq "healthy") {
        $dbReady = $true
        break
    }

    docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqladmin ping 2>/dev/null' | Out-Null
    if ($LASTEXITCODE -eq 0) {
        $dbReady = $true
        break
    }

    $retries--
    Start-Sleep -Seconds 2
}
if (-not $dbReady) {
    Write-Err "Database tidak merespons setelah timeout."
    exit 1
}
Write-Ok "Database siap."

# --- 7. Install dependensi Composer (sebelum app container hidup) ---
Write-Step "Menginstal dependensi Composer..."
docker compose run --rm --no-deps app composer install
if ($LASTEXITCODE -ne 0) { Write-Err "Composer install gagal."; exit 1 }
Write-Ok "Dependensi Composer terinstal."

# --- 8. Generate application key ---
Write-Step "Generate application key..."
docker compose run --rm --no-deps app php artisan key:generate
if ($LASTEXITCODE -ne 0) { Write-Err "Key generation gagal."; exit 1 }
Write-Ok "Application key berhasil di-generate."

# --- 9. Buat symbolic link storage ---
Write-Step "Membuat symbolic link storage..."
docker compose run --rm --no-deps app php artisan storage:link --force
if ($LASTEXITCODE -ne 0) { Write-Err "Storage link gagal."; exit 1 }
Write-Ok "Storage link berhasil dibuat."

# --- 10. Jalankan migrasi dan data feeder ---
Write-Step "Menjalankan migrasi dan data feeder..."
docker compose run --rm app php artisan migrate --seed --force
if ($LASTEXITCODE -ne 0) { Write-Err "Migrasi dan seeding gagal."; exit 1 }
Write-Ok "Migrasi dan data feeder berhasil dimasukkan."

# --- 11. Jalankan semua container ---
Write-Step "Menjalankan semua container..."
docker compose up -d
if ($LASTEXITCODE -ne 0) { Write-Err "Gagal menjalankan container."; exit 1 }
Write-Ok "Semua container berjalan."

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  Setup selesai! Sistem Siap Digunakan  " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "  Aplikasi    : http://localhost:8000"     -ForegroundColor Cyan
Write-Host "  phpMyAdmin  : http://localhost:8080"     -ForegroundColor Cyan
Write-Host "  MySQL       : localhost:3306"            -ForegroundColor Cyan
Write-Host ""
Write-Host "----------------------------------------" -ForegroundColor Yellow
Write-Host "  Akun Demo Siap Pakai (Password: password123)" -ForegroundColor Yellow
Write-Host "----------------------------------------" -ForegroundColor Yellow
Write-Host "  • Super Admin  : admin@ppk.test"         -ForegroundColor Cyan
Write-Host "  • Petugas      : petugas.demo@ppk.test"  -ForegroundColor Cyan
Write-Host "  • Mahasiswa    : mahasiswa.demo@ppk.test"-ForegroundColor Cyan
Write-Host "  • Dosen        : dosen.demo@ppk.test"    -ForegroundColor Cyan
Write-Host "  • Staf Civitas : staf.demo@ppk.test"     -ForegroundColor Cyan
Write-Host "----------------------------------------" -ForegroundColor Yellow
Write-Host ""
