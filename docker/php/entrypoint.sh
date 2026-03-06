#!/bin/bash
set -e

echo "──────────────────────────────────────────────"
echo "  Immobilier Platform — Container bootstrap"
echo "──────────────────────────────────────────────"

# ── Ensure required directories exist (host bind-mount may not have them) ────
echo "[0/6] Ensuring directory structure..."
mkdir -p /var/www/bootstrap/cache
mkdir -p /var/www/storage/app/public
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/logs
chown -R www-data:www-data /var/www/bootstrap/cache /var/www/storage
chmod -R 775 /var/www/bootstrap/cache /var/www/storage

# ── Generate application key if not set ───────────────────────────────────────
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "[1/6] Generating APP_KEY..."
    php artisan key:generate --force --ansi
else
    echo "[1/6] APP_KEY already set — skipping."
fi

# ── Wait for the database to accept connections ───────────────────────────────
echo "[2/6] Waiting for database ($DB_HOST:$DB_PORT)..."
until php -r "
new PDO(
    'mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306};dbname=${DB_DATABASE:-immobilier}',
    '${DB_USERNAME:-immobilier}',
    '${DB_PASSWORD:-secret}'
);
" 2>/dev/null; do
    echo "  → DB not ready yet, retrying in 2s..."
    sleep 2
done
echo "  → Database is up."

# ── Run migrations ────────────────────────────────────────────────────────────
echo "[3/6] Running migrations..."
php artisan migrate --force --ansi

# ── Create storage symlink ────────────────────────────────────────────────────
echo "[4/6] Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# ── Clear & warm caches ───────────────────────────────────────────────────────
echo "[5/6] Warming caches..."
php artisan config:cache --ansi
php artisan route:cache  --ansi

# ── Set file permissions ───────────────────────────────────────────────────────
echo "[6/6] Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ── Background: re-apply www-data ownership on storage/app/public ─────────────
# Needed because `docker exec php artisan ...` runs as root and may create
# root-owned directories that PHP-FPM (www-data) cannot write into.
(while true; do
    sleep 5
    chown -R www-data:www-data /var/www/storage/app/public 2>/dev/null
done) &

echo "──────────────────────────────────────────────"
echo "  Bootstrap complete — starting PHP-FPM..."
echo "──────────────────────────────────────────────"

exec "$@"
