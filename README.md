# Readme dong

ini laravel template starter versi ke 2 buatan gw, ini pakai laravel 12 starter-kit react, udah pakai tailwind 4, shadcn dan reactJS, dan inertiaJS 2.0 juga. gw buat ini biar gw cepet ngerjain project2 kecil. sekali generate, tinggal ngatur2 table dan formnya aja. oiya disini udah pakai fitur RBAC (Role Base Access Controll) pakai [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6/introduction) dan udah pakai [spatie/laravel-medialibrary](https://spatie.be/docs/laravel-medialibrary/v11/introduction) biar setting media jadi lebih mudah

## Installation Steps

```bash
# Clone the repository
git clone git@github.com:iqbalfarhan/templatestarter-v2.git
cd templatestarter2
rm -rf .git

# Install PHP dependencies
composer install

# Install Node.js dependencies
pnpm install

# Copy environment file and configure your database
cp .env.example .env
php artisan key:generate

# Run database migrations and seeders
php artisan migrate --seed

# Build assets and start development server
npm run dev
php artisan serve

# Access the application at
http://127.0.0.1:8000

```

## Development Guide

### 1. Membuat model

ngebuat model di app starter ini kaya bikin model di laravel kaya biasa aja. pakai flag a biar cepet (digenerating semua sama laravel)

```
php artisan make:model NamaModel -a
```

> -a untuk buat semua perlengkapan model, seperti factory, seeder, migration, store request, update request, controller dan policy

terus tinggal ngatur migration, fillable, dan lainnya terus di migrate. dilanjutkan buat viewnya

### 2. Ngebuat react view

nah disini gw ada tambahin command artisan `php artisan generate:rview` untuk generate full view react:

- index berisi table
- halaman detail
- component dialog delete data
- component sheet untuk create dan edit
- component sheet untuk filter data
- component item
- type untuk model ini

cara ngejalaninnya tinggai jalanin command artisan

```
php artisan generate:rview {nama feature}
// contoh
php artisan generate:rview project
```

selanjutnya ngatur list menu yang ada di sidebar (file app-sidebar.tsx) cari filenya pakai `Ctrl+p` aja biar cepet dan setting route di web.php. di web.php biar cepet pakai `apiResource` aja biar langsung jadi

```
// contoh pakai apiResource
Route::apiResource('project', ProjectController::class);
```
