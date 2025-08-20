# Membaca sebelum ngoding pakai templatestarter-v2

Laravel templatestarter versi ke 2 buatan gw ini, pakai laravel 12 starter-kit react, udah pakai tailwind 4, shadcn dan reactJS, dan inertiaJS 2.0 juga (bawaan dari sononye).

Gw buat ini biar gw cepet ngerjain project2 kecil. sekali generate, tinggal ngatur2 table dan formnya aja. Oiya disini udah pakai fitur:

- RBAC (Role Base Access Controll) pakai [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- Udah pakai [spatie/laravel-medialibrary](https://spatie.be/docs/laravel-medialibrary/v11/introduction) biar setting media jadi lebih gampang
- Udah ada internal database, pakai [laravel adminer](https://github.com/onecentlin/laravel-adminer)

## Installation Steps

### 1. Cloning repo

```bash
# Clone the repository
git clone git@github.com:iqbalfarhan/templatestarter-v2.git nama_project
cd nama_project
rm -rf .git
```

> ganti nama_project jadi nama project lu

### 2. Config awal

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
pnpm install

# Copy environment file and configure your database
cp .env.example .env
php artisan key:generate

# Setting database dan env lain di project lu
# baru lanjut ke bikin database

# Run database migrations and seeders
php artisan migrate
php artisan db:seed

# Build assets and start development server
composer dev

# Access the application at http://127.0.0.1:8000

```

### 3. Migrate ulang

kalo lu udah punya databasenya, lu bisa jalanin ini untuk reset semua database biar mulai ulang dari 0

```bash
php artisan migrate:fresh --seed
```

## Development Guide

### 1. Membuat model

Ngebuat model di templatestarter-v2 ini gak kaya bikin model di laravel kaya biasa. Gw udah buatin command khusus untuk buat model, factory, seeder, migrateion, store-request, update-request, controller dan nambahin baris Route::apiResource ke file web.php. pake command ini untuk generate file phpnya.

```
php artisan generate:amodel NamaModel
```

> Penting! jangan lupa pakai PascalCase untuk nama modelnya ya.

Ini yang lo dapat kalo udah jalanin command ini:

- Model dengan fillable
- Factory dan seeder
- Store dan Update request
- Controller api yang udah diisi methodnya
- Otomatis nambahin Route::apiResource ke web.php

Kalu udah kelar, lo tinggal ngatur migration, fillable, dan lainnya sesuai dengan kebutuhan fitur, terus di migrate dan jalanin seedernya pakai `php artisan db:seed {FiturSeeder}`. Terus lo tinggal lanjutin buat viewnya.

### 2. Ngebuat react view

Nah disini gw juga nambahin command artisan `php artisan generate:rview` untuk generate full view react:

- index berisi table
- halaman detail
- component dialog delete data
- component sheet untuk create dan edit
- component sheet untuk filter data
- component item
- type untuk model ini

cara ngejalaninnya tinggai jalanin command artisan

```
php artisan generate:rview {nama fitur}
// contoh
php artisan generate:rview project
```

selanjutnya ngatur list menu yang ada di sidebar (file app-sidebar.tsx) cari filenya pakai `Ctrl+p` aja biar cepet dan setting route di web.php. di web.php biar cepet pakai `apiResource` aja biar langsung jadi

```
// contoh pakai apiResource
Route::apiResource('project', ProjectController::class);
```

### 3. Generate semuanya sekaligus

Nah ini fitur yang gw suka banget, bikin CRUD cuma 2 menit, sekali jalanin commandnya, magic!! lu langsung dapat file php yang digenerate dari amodel dan juga dari rview. jalaninnya gampang tinggal jalanin command:

```bash
php artisan generate:rmodel NamaFitur
```

> Penting! jangan lupa pakai PascalCase untuk nama fiturnya ya.

kalu udah lu dapat semua filenya tinggal atur migration, fillable dan relasi table, store dan update request, type di file .d.ts dan tambahin menunya di file app-sidebar.tsx

## Pengembangan

Nantinya gw bakalan nambahin :

- View dan route untuk softDelete
- Setting Media untuk model
- View dan control untuk bulk action fiturnya
- Improvement di view filter data
- Beberapa perubahan controller yang udah implement permission
- Login pakai socialite
