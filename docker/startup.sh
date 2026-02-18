#!/bin/bash
 
# Exit on error
set -e
 
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
 
# Run migrations (force for production)
echo "Running migrations..."
php artisan migrate --force
 
# Start PHP-FPM in standard background mode
echo "Starting PHP-FPM..."
php-fpm -D
 
# Start Nginx in foreground
echo "Starting Nginx..."
nginx -g "daemon off;"
