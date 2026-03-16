#!/bin/bash
set -e

echo "Running composer install..."
composer install --no-dev --optimize-autoloader
echo "Running npm install..."
npm install
npm run build

echo "Waiting for database connection..."
until php -r "
  \$conn = @mysqli_connect('${DB_HOST}', '${DB_USERNAME}', '${DB_PASSWORD}', '${DB_DATABASE}', ${DB_PORT});
  if (\$conn) { mysqli_close(\$conn); exit(0); }
  exit(1);
"; do
  echo "DB not ready, retrying in 3s..."
  sleep 5
done

echo "Database is ready!"

php artisan config:clear
php artisan key:generate
php artisan storage:link 2>/dev/null || true
php artisan migrate
php artisan cache:clear
php artisan serve --host=0.0.0.0 --port=8000
