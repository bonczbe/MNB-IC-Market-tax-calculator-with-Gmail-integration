@echo off
echo === Pulling latest changes ===
git pull origin main

echo === Maintenance mode ON ===
docker exec dailytax_app php artisan down --refresh=60 --retry=10

echo === Building new image ===
docker-compose build app horizon scheduler

echo === Swapping app container ===
docker-compose up -d --no-deps app

echo === Waiting for app to become healthy... ===
:wait_loop
for /f "tokens=*" %%i in ('docker inspect --format="{{.State.Health.Status}}" dailytax_app') do set STATUS=%%i
if "%STATUS%"=="healthy" goto app_ready
echo App not ready yet (status: %STATUS%), waiting 10s...
timeout /t 10 /nobreak >nul
goto wait_loop

:app_ready
echo App is healthy!

echo === Running migrations and clearing caches ===
docker exec dailytax_app php artisan migrate --force
docker exec dailytax_app php artisan config:clear
docker exec dailytax_app php artisan cache:clear
docker exec dailytax_app php artisan view:clear
docker exec dailytax_app php artisan route:clear

echo === Starting horizon and scheduler ===
docker-compose up -d --no-deps horizon scheduler

echo === Maintenance mode OFF ===
docker exec dailytax_app php artisan up

echo === Done! ===
docker-compose ps
