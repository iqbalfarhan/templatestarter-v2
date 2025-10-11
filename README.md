# Laravel + React Template Starter v2

Starter kit **Laravel 12 + React (Inertia v2, Tailwind v4, Shadcn UI)** buat bikin project kecil–menengah dengan cepat.
Sekali generate, langsung dapet **Model + CRUD API + React View** — tinggal atur field dan form aja.

---

## Fitur Utama

- **Auto Generate CRUD**
  Model, Migration, Controller, Request, dan React View otomatis.
- **Role Based Access Control (RBAC)**
  Menggunakan [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6/introduction)
- **Media Handling**
  Support [spatie/laravel-medialibrary](https://spatie.be/docs/laravel-medialibrary/v11/introduction)
- **Database Management**
  Built-in [laravel-adminer](https://github.com/onecentlin/laravel-adminer)
- **Modern Frontend Stack**
  Tailwind CSS v4, Shadcn, React, dan Inertia v2 sudah siap pakai.

---

## Instalasi

### 1. Buat Project Baru

```bash
composer create-project iqbalfarhan/templatestarter-v2 nama_project
cd nama_project
```

> Ganti `nama_project` sesuai nama project kamu.

### 2. Setup Awal

```bash
# Install dependencies
composer install
pnpm install

# Copy env & generate key
cp .env.example .env
php artisan key:generate

# Setup database & seed data
php artisan migrate
php artisan db:seed

# Build assets & jalankan dev server
composer dev
```

Akses aplikasi di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Konfigurasi

File konfigurasi utama ada di: `config/template-starter.php`

| Key                          | Fungsi                                                    |
| ---------------------------- | --------------------------------------------------------- |
| `default-roles`              | Role default aplikasi (`['superadmin', 'admin', 'user']`) |
| `default-role`               | Role default user baru                                    |
| `with-landingpage`           | Aktif/nonaktif landing page                               |
| `generated-react-files-path` | Path hasil generate file React                            |

> Pastikan `default-role` ada di daftar `default-roles`.

Kalau ada perubahan konfigurasi di tengah development:

```bash
php artisan migrate:fresh --seed
```

---

## Panduan Development

### 1. Generate Model

```bash
php artisan generate:amodel NamaModel
```

Akan membuat otomatis:

- Model (fillable ready)
- Factory dan Seeder
- StoreRequest dan UpdateRequest
- Controller API full CRUD
- Route::apiResource di `web.php`

Gunakan **PascalCase** untuk nama model (contoh: `Project`, `UserProfile`).

---

### 2. Generate React View

```bash
php artisan generate:rview nama_fitur
# contoh
php artisan generate:rview project
```

Yang dibuat:

- Index (tabel)
- Detail page
- Dialog delete
- Sheet create/edit
- Sheet filter
- Item component
- Type definition (TypeScript)

Tambahkan route di `web.php` dan menu di `app-sidebar.tsx`:

```php
Route::apiResource('project', ProjectController::class);
```

---

### 3. Generate Model + View Sekaligus

```bash
php artisan generate:rmodel NamaFitur
```

CLI akan menanyakan:

- SoftDelete?
- Media?

Lalu masukkan field model seperti berikut:

```txt
name:string
content:text
published:boolean
published_at:datetime
category_id:fk
```

> Jangan tekan enter kosong di akhir input.

| Datatype | TypeScript | Migration | Keterangan        |
| -------- | ---------- | --------- | ----------------- |
| integer  | number     | integer   | Angka             |
| string   | string     | string    | Teks pendek       |
| text     | string     | text      | Teks panjang      |
| date     | string     | date      | Tanggal           |
| datetime | string     | datetime  | Tanggal dan waktu |
| boolean  | boolean    | boolean   | True/False        |
| fk       | Object     | foreignId | Relasi wajib      |
| nfk      | Object     | foreignId | Relasi nullable   |

---

### 4. Generate Permission

```bash
php artisan generate:permission user
```

Atau generate semua sekaligus:

```bash
php artisan generate:permission --all --softDelete
```

Perintah ini otomatis membuat permission untuk semua model di `app/Models`, termasuk yang menggunakan SoftDeletes.

---

### 5. Login dengan Socialite (Google)

Sudah mendukung login via Google menggunakan Laravel Socialite.
Cara mengaktifkan:

1. Ubah `enable_socialite` menjadi `true` di `config/template-starter.php`
2. Tambahkan di `.env`:

   ```env
   GOOGLE_CLIENT_ID=
   GOOGLE_CLIENT_SECRET=
   ```

3. Rebuild project, lalu buka halaman login. Tombol “Sign in with Google” akan muncul.

Untuk menambahkan platform lain, sesuaikan di `config/services.php` dan `SocialiteController.php`.

---

## Roadmap

- [x] Auto generate model, view, rmodel
- [x] RBAC dengan Spatie
- [x] Login via Socialite
- [ ] Auto generate menu sidebar
- [ ] API Pagination + Search

---

## Tips

- Kalau ubah konfigurasi role → jalankan `php artisan migrate:fresh --seed`
- Untuk development cepat → tambahkan menu langsung di `app-sidebar.tsx` setelah generate

---

## Lisensi

MIT License © [Iqbal Farhan](https://github.com/iqbalfarhan)

---

## Dukung Pengembangan

Kalau template ini bermanfaat buat kamu, dukung pengembangannya dengan donasi seikhlasnya.
Setiap dukungan sangat berarti agar project ini bisa terus dikembangkan.

[![Donate via Saweria](https://img.shields.io/badge/Donate-Saweria-yellow?style=for-the-badge)](https://saweria.co/iqbalfarhan08)
[![Donate via DANA](https://img.shields.io/badge/Donate-DANA-blue?style=for-the-badge)](https://link.dana.id/minta?full_url=https://qr.dana.id/v1/281012012022050160996242)
