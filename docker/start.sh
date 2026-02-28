#!/bin/bash
set -e

cd /var/www/html

echo "[start.sh] Running Laravel production startup..."

# Run database migrations
echo "[start.sh] Running migrations..."
php artisan migrate --force

# Cache config, routes, events, views for performance
echo "[start.sh] Optimizing..."
php artisan optimize

# Create storage symlink (safe to run multiple times)
echo "[start.sh] Linking storage..."
php artisan storage:link 2>/dev/null || true

# Ensure correct permissions (important if storage/ is mounted as a volume)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "[start.sh] Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
