#!/bin/sh
cd /var/www/html

# ── Composer vendor ───────────────────────────────────────────────────────────
if [ ! -f vendor/autoload.php ]; then
    echo "→ [1/3] Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Ensure permissions on storage and bootstrap/cache after bind mount
echo "→ Fixing permissions for storage and bootstrap/cache..."
chown -R nobody:nobody storage bootstrap/cache

# ── Node modules + Vite build ─────────────────────────────────────────────────
if [ ! -f public/build/manifest.json ]; then
    echo "→ [2/3] Installing Node dependencies..."
    npm ci

    echo "→ [3/3] Building Vite assets..."
    npm run build
fi

# ── Laravel first-boot setup ─────────────────────────────────────────────────
if [ -f .env ]; then
    grep -q "^APP_KEY=base64:" .env || php artisan key:generate --force
    [ -L public/storage ] || php artisan storage:link 2>/dev/null || true
fi

echo "✓ Starting PHP-FPM..."
exec "$@"
