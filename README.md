<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<h1 align="center">UniStock</h1>

<p align="center">
  Sistem Distribusi Seragam & Inventory Management — Berbasis Web untuk Admin Universitas
</p>

---

**UniStock** adalah sistem berbasis web untuk mengelola proses distribusi seragam mahasiswa. Menggantikan proses manual (Google Form, Google Sheet, barcode manual, checklist manual, rekap Excel) dengan sistem terintegrasi.

**Alur utama:** Student Data → Size Management → QR Identity → Staff Distribution → Inventory Movement → Admin Report

# Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 12 |
| Database | MySQL |
| Frontend | Blade + Tailwind CSS + Vite |
| Auth | Laravel Breeze |
| Permission | Spatie Laravel Permission |
| Excel | Maatwebsite Laravel Excel |
| QR Code | Simple QR Code (SVG/PNG) |
| QR Scanner | HTML5 QR Scanner |
| Email | Laravel Mail + SMTP |

# Quick Start

## 1. Clone project
Clone repositori ini ke komputer Anda:
```bash
git clone https://github.com/nirwana-tim/Horizon-UniStock.git
```

## 2. Masuk folder project
```bash
cd Horizon-UniStock
```

## 3. Copy environment & atur database
Salin `.env.example` menjadi `.env`, lalu edit `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`:
```bash
copy .env.example .env
```

## 4. Install PHP dependencies
```bash
composer install
```

## 5. Generate app key
```bash
php artisan key:generate
```

## 6. Jalankan migrasi & seeder
```bash
php artisan migrate:fresh --seed
```

Seeder bawaan hanya membuat **role/permission + akun super admin** (data bisnis kosong). Master data (fakultas, prodi, item, dll) dari Excel ditambahkan terpisah:
```bash
php artisan db:seed --class="Database\Seeders\Master\MasterDataSeeder"
```

> **Catatan:** Seeder master ada di `database/seeders/Master/`, masing-masing 1 record. Bisa dihapus file-nya kapan aja jika tidak dibutuhkan.

## 7. Install frontend dependencies

**npm:**
```bash
npm install && npm run build
```

**bun (alternatif, jika sudah install bun):**
```bash
bun install && bun run build
```

## 8. Buat storage link
Untuk QR code dan upload file:
```bash
php artisan storage:link
```

## 9. Jalankan server
```bash
php artisan serve
```
Buka http://127.0.0.1:8000

# After Pull (Setelah `git pull`)

Setiap kali menarik perubahan terbaru dari repositori, jalankan perintah berikut secara berurutan:

```bash
php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear
```

**npm:**
```bash
npm install && npm run build
```

**bun (alternatif):**
```bash
bun install && bun run build
```

> **Catatan:** `npm install && npm run build` (atau `bun install && bun run build`) wajib dijalankan jika ada perubahan pada file JavaScript, CSS, atau dependensi frontend. Keempat `php artisan ...:clear` membersihkan cache Laravel yang mungkin masih menyimpan versi lama.

### Login Super Admin

Setelah `migrate:fresh --seed`, hanya ada satu akun bawaan (super admin). Kredensial diambil dari `.env`:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `SUPERADMIN_EMAIL` (default `admin@horizon-unistock.ac.id`) | `SUPERADMIN_PASSWORD` |

> **Setup super admin:**
> 1. Set `SUPERADMIN_EMAIL` & `SUPERADMIN_PASSWORD` di `.env` (lihat `.env.example`)
> 2. Jalankan: `php artisan db:seed --class=SuperadminSeeder`
> 3. Jika `SUPERADMIN_PASSWORD` kosong, seeder membuat password acak & mencetaknya di console
> 4. Akun dibuat dengan `must_change_password=true` — ganti password saat login pertama
>
> ⚠️ Seeder memakai `firstOrCreate` (idempotent) — aman dijalankan ulang, tapi **tidak mengubah** user yang sudah ada. Untuk menerapkan password baru ke akun lama, update manual di database.

### Master Data Seeder

Seeder master data ada di `database/seeders/Master/`, masing-masing 1 record dari data Excel. **Tidak otomatis di-seed** oleh `migrate:fresh --seed`.

**Jalankan semua:**
```bash
php artisan db:seed --class="Database\Seeders\Master\MasterDataSeeder"
```

**Hapus semua master data:**
```bash
Remove-Item -Recurse -Force database/seeders/Master/
php artisan migrate:fresh --seed
```

## Dokumentasi

Dokumentasi lengkap sistem berbasis bukti tersedia di folder [`Horizon-UniStock-Documentation/`](Horizon-UniStock-Documentation/):

| Dokumen | Keterangan & Format |
|---------|---------------------|
| [00-Project-Context.md](Horizon-UniStock-Documentation/00-Project-Context.md) | Konteks inti proyek, identitas sistem, 15 fakta kanonis, dan pedoman kerja AI |
| [01-UniStock-Master-Documentation.pdf](Horizon-UniStock-Documentation/01-UniStock-Master-Documentation.pdf) | **Master Documentation PDF (21 Halaman A4 Searchable, 90 Bab)** |
| [02-Technical-Reference/](Horizon-UniStock-Documentation/02-Technical-Reference/) | Kamus database (58 tabel), katalog API & rute, diagram arsitektur, dan panduan deployment cPanel |
| [03-Bugs-and-Decisions.md](Horizon-UniStock-Documentation/03-Bugs-and-Decisions.md) | 8 Architecture Decision Records (ADR), resolusi kasus operasional, dan tracking bug |
| [04-Change-Log.md](Horizon-UniStock-Documentation/04-Change-Log.md) | Catatan riwayat versi dan pembaruan sistem dari MVP hingga V1.2.0 |

## Lisensi

[MIT License](https://opensource.org/licenses/MIT)
.
