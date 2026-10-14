# 04 — Deployment & Operations Guide: Horizon UniStock

Panduan ini mendokumentasikan prosedur instalasi, konfigurasi server, manajemen dependensi, dan alur pemeliharaan operasional sistem Horizon UniStock pada lingkungan pengembangan lokal maupun server produksi.

---

## 1. Spesifikasi Kebutuhan Lingkungan

### 1.1 Persyaratan Perangkat Lunak
- **PHP:** Versi 8.3 atau 8.4 (Ekstensi wajib: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`, `xml`, `curl`, `bcmath`, `fileinfo`).
- **Database:** MySQL 8.0+ atau MariaDB 10.5+ (Engine InnoDB, utf8mb4 collation).
- **Web Server:** Nginx 1.20+ atau Apache 2.4+ (dengan modul `mod_rewrite`).
- **Node.js / Package Manager:** Node.js v18+ dan NPM / Bun v1.2+.
- **Composer:** Composer v2.5+.

---

## 2. Instalasi Lingkungan Pengembangan Lokal (Local Setup)

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/nirwana-tim/Horizon-UniStock.git
   cd Horizon-UniStock
   ```
2. **Konfigurasi Environment:**
   Salin berkas `.env.example` ke `.env`:
   ```bash
   copy .env.example .env
   ```
   Buka berkas `.env` dan sesuaikan koneksi database MySQL:
   ```ini
   APP_NAME="UniStock"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=horizon_unistock
   DB_USERNAME=root
   DB_PASSWORD=

   SUPERADMIN_EMAIL=admin@horizon-unistock.ac.id
   SUPERADMIN_PASSWORD=AdminPassword2026!
   ```
3. **Instal Dependensi PHP:**
   ```bash
   composer install
   ```
4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```
5. **Jalankan Migrasi & Database Seeder:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Catatan: Perintah di atas akan membangun 58 tabel dan men-seed role Spatie serta akun Super Admin.*
6. **Hubungkan Symlink Storage:**
   ```bash
   php artisan storage:link
   ```
7. **Kompilasi Aset Frontend:**
   ```bash
   npm install && npm run build
   # Atau jika menggunakan bun:
   bun install && bun run build
   ```
8. **Jalankan Web Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka peramban di `http://127.0.0.1:8000`.

---

## 3. Alur Deployment Server Produksi (cPanel Git Deployment)

Server produksi UniStock beralamat di `https://unistok.nirwana.biz.id` di-hosting pada lingkungan **cPanel tanpa hak akses SSH**. Oleh karena itu, strategi pembaruan sistem sepenuhnya mengandalkan antarmuka **cPanel Git Version Control** atau script automasi berbasis webhook.

### 3.1 Prosedur Standar Setiap Pembaruan (`Git Pull`)
Setiap kali perubahan kode ditarik (*pull*) ke server produksi, operator atau webhook WAJIB menjalankan urutan perintah berikut:

```bash
# 1. Menarik commit terbaru dari branch utama
git pull origin main

# 2. Re-dump autoloader Composer (WAJIB MUTLAK)
# Kritis: File app/helpers.php didaftarkan di autoload.files. 
# Jika tidak dijalankan, fungsi global seperti escapeLike() hilang -> Fatal 500!
composer dump-autoload --optimize --no-interaction

# 3. Membersihkan seluruh cache lama Laravel
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Menjalankan migrasi database baru jika ada
php artisan migrate --force

# 5. Membangun ulang cache konfigurasi produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3.2 Script Deployer Otomatis
Repositori telah dilengkapi dengan script deployer otomatis:
- `deploy.sh` (Untuk server berbasis Linux/Bash):
  ```bash
  #!/bin/bash
  git pull origin main
  composer dump-autoload --optimize --no-interaction
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  php artisan cache:clear
  php artisan migrate --force
  php artisan optimize
  echo "Deployment UniStock Sukses!"
  ```
- `deploy.bat` (Untuk pengujian lingkungan Windows).

---

## 4. Pemecahan Masalah Operasional (Troubleshooting)

| Gejala Error | Akar Masalah | Solusi Terverifikasi |
|--------------|--------------|----------------------|
| **Error 500: Call to undefined function escapeLike()** | Autoloader Composer di server produksi belum diperbarui setelah git pull. | Jalankan `composer dump-autoload --optimize`. Jangan panggil helper global secara manual di kode baru, gunakan method trait atau inline replace. |
| **Fatal Error: Method WithStyles::styles() must return ?array** | Versi Maatwebsite Excel 4.0 mengharuskan return type `?array` bukan `void`. | Ubah deklarasi method pada kelas ekspor menjadi `public function styles(Worksheet $sheet): ?array` dan akhiri dengan `return null;`. |
| **Tombol Modal / $dispatch Tidak Berfungsi** | Elemen HTML pemanggil berada di luar lingkup reaktivitas Alpine.js. | Tambahkan atribut `x-data=""` kosong pada elemen button atau container terdekat. |
| **QR Code Rusak / Tidak Muncul di Layar Mahasiswa** | Ekstensi PHP `gd` belum diaktifkan di server, atau ukuran gambar melebihi buffer. | Pastikan ekstensi `php-gd` aktif pada modul cPanel PHP Selector. Periksa `QrCodeService` memastikan output Base64 terbentuk sempurna. |
| **Peringatan: Mahasiswa Sudah Mengambil Barang** | Terdapat record transaksi aktif pada jadwal yang sama, atau sisa saldo hak entitlement bernilai 0. | Periksa rute `/student/{id}/transactions` untuk melihat rekam jejak serah terima sebelumnya. Jika barang tertunda, jadwalkan pada agenda distribusi berikutnya. |
