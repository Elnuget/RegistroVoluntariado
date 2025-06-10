Instalación: 
composer install 
cp .env.example .env 
php artisan key:generate 
php artisan migrate --seed 
npm install 
EN SHELL COMO ADMINISTRADOR: Get-ExecutionPolicy Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned
php artisan config:clear 
php artisan route:clear
php artisan view:clear
php artisan cache:clear 
php artisan migrate --seed