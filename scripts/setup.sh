#!/usr/bin/env bash
# =============================================================================
# Resvi — Setup Script (Linux/macOS)
# =============================================================================
# Menjalankan seluruh langkah setup awal proyek menggunakan Docker.
# Pastikan Docker dan Docker Compose sudah terinstal dan berjalan.
#
# Penggunaan:
#   chmod +x scripts/setup.sh
#   ./scripts/setup.sh
# =============================================================================

set -euo pipefail

# Warna output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

print_step() {
    echo -e "\n${CYAN}[STEP]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[OK]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Pindah ke root proyek (parent dari scripts/)
cd "$(dirname "$0")/.."

echo -e "${CYAN}========================================${NC}"
echo -e "${CYAN}  Resvi — Setup${NC}"
echo -e "${CYAN}========================================${NC}"

# --- 1. Cek Docker ---
print_step "Memeriksa Docker..."
if ! command -v docker &> /dev/null; then
    print_error "Docker tidak ditemukan. Silakan instal Docker terlebih dahulu."
    echo "  → https://docs.docker.com/get-docker/"
    exit 1
fi

if ! docker info &> /dev/null; then
    print_error "Docker daemon tidak berjalan. Silakan jalankan Docker terlebih dahulu."
    exit 1
fi
print_success "Docker ditemukan dan berjalan."

# --- 2. Cek Docker Compose ---
print_step "Memeriksa Docker Compose..."
if ! docker compose version &> /dev/null; then
    print_error "Docker Compose tidak ditemukan."
    echo "  → https://docs.docker.com/compose/install/"
    exit 1
fi
print_success "Docker Compose ditemukan."

# --- 3. Salin .env ---
print_step "Menyiapkan file .env..."
if [ -f .env ]; then
    print_warning ".env sudah ada, dilewati."
else
    cp .env.example .env
    print_success ".env berhasil dibuat dari .env.example."
fi

# --- 4. Build container ---
print_step "Build container Docker..."
docker compose build
print_success "Build selesai."

# --- 5. Jalankan database terlebih dahulu ---
print_step "Menjalankan database..."
docker compose up -d db
print_success "Database container berjalan."

# --- 6. Tunggu database siap ---
print_step "Menunggu database siap..."
RETRIES=30
DB_READY=false
while [ $RETRIES -gt 0 ]; do
    STATUS=$(docker inspect --format='{{.State.Health.Status}}' ppk_db 2>/dev/null || true)
    if [ "$STATUS" = "healthy" ]; then
        DB_READY=true
        break
    fi

    if docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqladmin ping 2>/dev/null' >/dev/null 2>&1; then
        DB_READY=true
        break
    fi

    RETRIES=$((RETRIES - 1))
    sleep 2
done

if [ "$DB_READY" != "true" ]; then
    print_error "Database tidak merespons setelah timeout."
    exit 1
fi
print_success "Database siap."

# --- 7. Install dependensi Composer (sebelum app container hidup) ---
print_step "Menginstal dependensi Composer..."
docker compose run --rm --no-deps app composer install
print_success "Dependensi Composer terinstal."

# --- 8. Generate application key ---
print_step "Generate application key..."
docker compose run --rm --no-deps app php artisan key:generate
print_success "Application key berhasil di-generate."

# --- 9. Buat symbolic link storage ---
print_step "Membuat symbolic link storage..."
docker compose run --rm --no-deps app php artisan storage:link --force
print_success "Storage link berhasil dibuat."

# --- 10. Jalankan migrasi dan data feeder ---
print_step "Menjalankan migrasi dan data feeder..."
docker compose run --rm app php artisan migrate --seed --force
print_success "Migrasi dan data feeder berhasil dimasukkan."

# --- 11. Jalankan semua container ---
print_step "Menjalankan semua container..."
docker compose up -d
print_success "Semua container berjalan."

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Setup selesai! Sistem Siap Digunakan  ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "  Aplikasi    : ${CYAN}http://localhost:8000${NC}"
echo -e "  phpMyAdmin  : ${CYAN}http://localhost:8080${NC}"
echo -e "  MySQL       : ${CYAN}localhost:3306${NC}"
echo ""
echo -e "${YELLOW}----------------------------------------${NC}"
echo -e "${YELLOW}  Akun Demo Siap Pakai (Password: password123)${NC}"
echo -e "${YELLOW}----------------------------------------${NC}"
echo -e "  • Super Admin  : ${CYAN}admin@ppk.test${NC}"
echo -e "  • Petugas      : ${CYAN}petugas.demo@ppk.test${NC}"
echo -e "  • Mahasiswa    : ${CYAN}mahasiswa.demo@ppk.test${NC}"
echo -e "  • Dosen        : ${CYAN}dosen.demo@ppk.test${NC}"
echo -e "  • Staf Civitas : ${CYAN}staf.demo@ppk.test${NC}"
echo -e "${YELLOW}----------------------------------------${NC}"
echo ""
