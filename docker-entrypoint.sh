#!/bin/bash
set -e

cd /var/www

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Seed if database is empty
php artisan db:seed --force 2>/dev/null || true

# Start server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
