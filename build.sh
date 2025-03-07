#!/bin/bash

echo "Starting build process..."

# Copy production environment file
cp env.production .env

# Install PHP dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate application key if not set
php artisan key:generate --force

# Create SQLite database directory and file
mkdir -p database
touch database/database.sqlite
chmod -R 777 database

# Run database migrations
php artisan migrate --force

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Clear and cache views
php artisan view:clear
php artisan view:cache

# Install NPM dependencies and build assets
npm install && npm run build

# Ensure storage directory is writable
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chmod -R 777 public

echo "Build process completed. Starting server..."
