#!/bin/sh
set -e

# Pastikan symbolic link storage tersedia
echo "Ensuring storage symlink exists..."
php artisan storage:link --force || true

# Jalankan migrasi database (default: true)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# Jalankan seeder database jika diaktifkan (default: false)
if [ "${RUN_SEEDER:-false}" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force || true
fi

# Optimasi caching saat mode production dan APP_KEY terdefinisi
if [ "$APP_ENV" = "production" ] && [ -n "$APP_KEY" ]; then
    echo "Optimizing configuration, routes, and views for production..."
    php artisan config:clear || true
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Gunakan port dinamis dari Railway ($PORT), fallback ke 8000
PORT="${PORT:-8000}"
echo "Starting Laravel server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port="$PORT"
