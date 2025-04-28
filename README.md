<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

## Instalasi Program

1. Masuk ke folder project
```
cd nama-folder-project
```
3. Install dependency dengan Composer
```
composer install
```
2. Install node modules
```
npm install
```
4. Copy file .env
```
cp .env.example .env
```
6. Generate application key
```
php artisan key:generate
```
8. Setting koneksi database di .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3388
DB_DATABASE=pencatatan_msin
DB_USERNAME=root
DB_PASSWORD=
```
9. Jalankan migrasi database
```
php artisan migrate
```
10. Build frontend menggunakan NPM
```
npm run build
```
11. Jalankan servernya 
```
php -S 192.168.1.4:8888 -t public
```
