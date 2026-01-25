#!/bin/sh

# Exit immediately if a command exits with a non-zero status
set -e

# Run Migrations
echo "Running migrations..."
php artisan migrate --force

# Filament Optimization
echo "Optimizing Filament assets..."
php artisan filament:optimize

# Cache Configuration
echo "Caching configuration..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Start Supervisor
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf