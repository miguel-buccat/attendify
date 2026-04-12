#!/bin/sh
set -e

echo "Running production entrypoint..."

# Ensure storage directories exist (in case volume is mounted fresh)
mkdir -p \
    storage/app/public/avatars \
    storage/app/public/banners \
    storage/app/public/site-settings \
    storage/app/private/site-assets \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Fix ownership — volume mounts can reset root ownership
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Ensure site-settings.json is writable if it already exists
if [ -f storage/app/site-settings.json ]; then
    chown www-data:www-data storage/app/site-settings.json
    chmod 664 storage/app/site-settings.json
fi

# Ensure storage symlink
if [ ! -L public/storage ]; then
    ln -s /var/www/html/storage/app/public public/storage
fi

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run migrations
php artisan migrate --force

# Re-fix ownership — artisan commands above run as root and may
# have created files (e.g. site-settings.json) owned by root
chown -R www-data:www-data storage bootstrap/cache

exec "$@"
