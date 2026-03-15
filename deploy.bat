@echo off
echo === Pulling latest changes ===
git pull origin main

echo === Building new image (the old version is still running!) ===
docker-compose build app queue scheduler

echo === Running migrations ===
docker-compose run --rm app php artisan migrate

echo === Clearing caches ===
docker-compose run --rm app php artisan config:clear
docker-compose run --rm app php artisan cache:clear
docker-compose run --rm app php artisan view:clear
docker-compose run --rm app php artisan route:clear

echo === Swapping containers ===
docker-compose up -d --no-deps app queue scheduler

echo === Done! ===
docker-compose ps
