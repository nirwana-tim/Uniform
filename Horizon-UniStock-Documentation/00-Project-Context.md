# 00 — Horizon UniStock: Project Context & AI Knowledge Base

> **Dokumen Induk Konteks Sistem & Panduan Kolaborasi AI**  
> **Institusi:** Horizon University Indonesia (Universitas Horizon Indonesia / STMIK & STIKes Horizon Karawang)  
> **Sistem:** UniStock (Sistem Distribusi Seragam & Inventory Management Terpadu)  
> **Status Dokumen:** Canonical Reference (Dokumentasi Induk Terverifikasi Berbasis Bukti)  
> **Versi Sistem:** 1.2.0 (Laravel 12.63.0 / PHP 8.3+ / MySQL)  
> **URL Produksi:** [https://unistok.nirwana.biz.id](https://unistok.nirwana.biz.id)  
> **Repositori Resmi:** `nirwana-tim/Horizon-UniStock`

---

## 1. Lima Jawaban Pokok Mengenai Horizon UniStock

Dokumentasi ini dirancang agar setiap agen AI memahami konteks UniStock secara permanen dan tidak memperlakukan sistem ini sebagai proyek baru yang tidak dikenal.

### 1.1 UniStock itu Apa?
**Horizon UniStock** adalah platform web terpadu yang dirancang untuk mengelola seluruh rantai pasok dan siklus hidup distribusi seragam mahasiswa baru serta manajemen inventaris perlengkapan akademik di **Horizon University Indonesia**. Sistem ini menyatukan pendataan ukuran badan mandiri oleh mahasiswa, penerbitan tanda pengenal digital QR Code berbasis NIM, verifikasi kelayakan administrasi keuangan (*eligibility*), penjadwalan distribusi bertahap (*distribution schedule*), pencocokan hak jatah prodi (*entitlement*), proses fitting dan serah terima di loket staf gudang (*scan & verification*), hingga pencatatan mutasi stok gudang secara otomatis menggunakan penilaian persediaan **FIFO (First-In, First-Out)** dengan perhitungan blended HPP (*Harga Pokok Penjualan*) dan analisis laba kotor (**GPM - Gross Profit Margin**).

### 1.2 Kenapa UniStock Dibuat? (Latar Belakang & Akar Masalah)
Sebelum UniStock diimplementasikan, seluruh proses operasional distribusi seragam dikelola secara manual dan terfragmentasi menggunakan kombinasi Google Forms, Google Sheets, pesan WhatsApp, pencatatan fisik formulir kertas di loket, dan sebuah spreadsheet Excel induk raksasa (*terbukti secara fisik dalam repositori pada file `scripts/Dummy - Inventory Management.xlsx` berukuran ~4.5 MB dengan 19 lembar kerja kerja kompleks seperti `Sales Dashboard`, `Stock Opname`, `STO AUG 24`, `Loss STO`, dan `CEK GPM`*).

Praktik manual pra-digitalisasi tersebut menimbulkan sejumlah kerugian kritis bagi universitas:
1. **Penumpukan Antrean Masif Saat Intake Mahasiswa Baru:** Setiap bulan Agustus–September, ribuan mahasiswa baru datang bersamaan untuk mengambil jas almamater, baju kemeja, celana olahraga, dan sepatu dinas. Pencatatan manual di kertas menyebabkan waktu layanan mencapai 15–20 menit per mahasiswa.
2. **Klaim Ganda (*Double-Claim*):** Tidak adanya database transaksi terpusat yang terisolasi memungkinkan mahasiswa mengambil jatah seragam lebih dari satu kali di hari atau loket yang berbeda.
3. **Ketidaksesuaian Ukuran di Lapangan (*Fitting Mismatch*):** Data ukuran yang diisi berbulan-bulan sebelumnya saat pendaftaran PMB seringkali tidak pas saat dicoba secara fisik di loket, memaksa penukaran ukuran mendadak yang merusak keakuratan stok gudang.
4. **Kebocoran & Selisih Stok Gudang (*Stock Shrinkage*):** Selisih antara stok fisik gudang dan catatan rekapitulasi tidak dapat ditelusuri riwayatnya karena ketiadaan kartu stok (*stock movement card*) yang terikat langsung pada referensi transaksi penyerahan.
5. **Ketiadaan Pelacakan HPP & GPM Real-Time:** Bagian Keuangan (*Finance*) mengalami kesulitan menghitung laba kotor per item seragam karena pembelian dari vendor datang dalam beberapa gelombang harga yang berbeda, tanpa ada mekanisme penilaian batch persediaan FIFO.

### 1.3 Bagaimana Proses Bisnisnya Berjalan?
Proses bisnis UniStock terbagi menjadi 5 fase end-to-end yang saling mengunci:
1. **Fase 1: Pendataan & Manajemen Ukuran Mahasiswa (`SizeChangeEvent`)**
   Admin membuka event pengisian ukuran untuk angkatan, fakultas, atau prodi tertentu. Mahasiswa login menggunakan akun kampus (NIM), melakukan verifikasi email, dan mengisi profil ukuran baju serta sepatu sesuai pilihan opsi yang disediakan. Mahasiswa dibatasi dalam jumlah kesempatan ubah ukuran (*max_changes*).
2. **Fase 2: Verifikasi Kelayakan Keuangan & Penerbitan QR Code**
   Bagian Finance memperbarui status kelayakan (*EligibilityRecord*) mahasiswa (misal: pelunasan biaya kuliah/seragam). Mahasiswa yang berstatus *Eligible* diterbitkan identitas digital berupa QR Code permanen beresolusi tinggi (Error Correction Level H) yang mengenkapsulasi NIM mahasiswa.
3. **Fase 3: Penjadwalan & Filter Dinamis Hak Barang (`Entitlement`)**
   Admin membentuk Jadwal Distribusi berdasarkan kombinasi Angkatan, Fakultas, Prodi, tanggal, lokasi, dan sesi. Sistem mencocokkan jatah barang (*Entitlement Item*) yang berhak diterima mahasiswa berdasarkan jenjang (*Student Level*) dan program studinya.
4. **Fase 4: Pemindaian Loket & Serah Terima Barang Fisik (`Staff Scan`)**
   Mahasiswa menunjukkan QR Code di loket gudang. Staf memindai QR Code via webcam/kamera smartphone (menggunakan library `html5-qrcode`) atau memasukkan NIM manual. Sistem menampilkan identitas, status kelayakan, daftar barang yang berhak diambil, dan ukuran terdaftar. Jika dilakukan penukaran ukuran saat fitting, staf dapat mengubah dropdown *actual size* yang secara otomatis mencatat audit trail ke tabel `student_size_histories`.
5. **Fase 5: Pemotongan Stok FIFO & Rekonsiliasi Keuangan**
   Saat transaksi disimpan, database menjalankan transaksi ACID dengan pessimistic locking (`lockForUpdate`). Sistem memotong persediaan dari batch penerimaan vendor tertua (`stock_batches`) yang masih memiliki saldo (`quantity_remaining`), menghitung nilai blended HPP secara presisi, mencatat kartu mutasi stok (`stock_movements`), mengkalkulasi selisih margin terhadap harga jual (`selling_price`), serta memperbarui laporan GPM dan kartu stok secara instan.

### 1.4 Bagaimana Sistem Diimplementasikan Secara Teknis?
- **Backend:** Menggunakan **Laravel 12.63.0 / 13.x** di atas **PHP 8.3+** dengan pemisahan tanggung jawab yang tegas: Controller tipis (*Thin Controllers*), Domain Service layer yang kaya logika bisnis (`DistributionService`, `StockService`, `StudentSizeService`, `ReportService`, dll), Form Request untuk validasi input, dan Eloquent ORM.
- **Frontend:** Menggunakan arsitektur monolitik modern berbasis **Laravel Blade**, **Tailwind CSS v3**, dan **Alpine.js v3** yang dikompilasi melalui **Vite**. Pendekatan ini menghilangkan kompleksitas state management API eksternal dan memberikan pengalaman reaktif layaknya SPA melalui komponen Alpine.
- **Basis Data:** Menggunakan **MySQL** dengan 58 file migrasi terstruktur rapi, indeks komposit teroptimasi untuk pencarian volume tinggi, integritas foreign key dengan penanganan *nullOnDelete* atau *cascadeOnDelete*, dan isolasi transaksi InnoDB.
- **Manajemen Hak Akses:** Menggunakan **Spatie Laravel Permission** dengan 4 Role terdefinisi: `super_admin`, `admin`, `staff`, dan `student`.
- **Integrasi Utilitas:** `bacon/bacon-qr-code` untuk perenderan QR Code server-side secara lokal tanpa API pihak ketiga; `maatwebsite/excel` v4.0 untuk ekspor-impor Excel; `html5-qrcode` untuk scanning barcode kamera klien; dan SMTP terintegrasi database untuk pengiriman OTP kredensial akun.

### 1.5 Bagaimana AI Harus Membantu Pengembangannya?
Sebagai agen AI yang berpasangan dengan pengembang, AI harus:
1. **Mematuhi Arsitektur yang Sudah Ada:** Tidak mengusulkan penulisan ulang arsitektur (seperti mengganti Blade ke React/Vue SPA) kecuali diminta secara eksplisit.
2. **Mematuhi Konvensi Deployment Produksi:** Lingkungan produksi berada di **cPanel tanpa akses SSH** (`unistok.nirwana.biz.id`), di-deploy via `git pull` saja. Setiap perubahan file helper global atau autoload wajib diiringi instruksi `composer dump-autoload --optimize`.
3. **Mematuhi Aturan Integritas Inventaris:** Tidak boleh mengurangi atau menambah stok secara manual pada tabel `stock_balances` tanpa melalui `StockService` (wajib mencatat `stock_batches` dan `stock_movements` agar kartu stok dan HPP tidak korup).
4. **Mematuhi Aturan Frontend Alpine.js:** Semua tombol interaktif yang memancarkan event `$dispatch(...)` wajib berada di dalam scope `x-data`.
5. **Mematuhi Kontrak Interface Maatwebsite Excel:** Di versi 4.0, method `styles(Worksheet $sheet)` pada kelas ekspor wajib ber-return type `?array` (tidak boleh `void`).

---

## 2. Fakta-Fakta Kanonis Sistem (Canonical Facts)

Berikut adalah 15 fakta tidak terbantahkan (*ground-truth*) yang wajib dijadikan acuan konsisten:

| No | Fakta Kanonis | Keterangan & Rujukan Teknis |
|----|---------------|-----------------------------|
| 1 | **Identitas QR Code** | Konten QR Code adalah **NIM Mahasiswa murni** (*plaintext* string NIM), di-generate menggunakan `BaconQrCode\\Renderer\\GDLibRenderer` beresolusi 300x300 dengan Error Correction Level H. |
| 2 | **Role Pengguna** | Hanya terdapat 4 role resmi yang terdaftar via enum `App\\Enums\\Role`: `super_admin`, `admin`, `staff`, dan `student`. |
| 3 | **Akun Super Admin Bawaan** | Kredensial super admin diambil dari `.env` (`SUPERADMIN_EMAIL` dan `SUPERADMIN_PASSWORD`). Jika di-seed tanpa password di env, seeder membuat password acak. Akun selalu di-flag `must_change_password = true`. |
| 4 | **Pemisahan Kategori Ukuran** | Ukuran barang dikelompokkan ke dalam baju atau sepatu melalui flag boolean `is_baju` dan `is_sepatu` langsung pada tabel `item_sizes`. Controller mengkueri flag ini dan tidak membaca pivot kategori secara langsung. |
| 5 | **Metode Penilaian Persediaan** | Inventaris menggunakan metode **FIFO (First-In, First-Out)** murni yang dilacak melalui entitas `StockBatch` (`quantity_remaining` dan `unit_hpp`). HPP transaksi distribusi adalah rata-rata tertimbang (*blended HPP*) dari batch-batch yang terpotong. |
| 6 | **Pencegahan Ambil Ganda** | Transaksi diproteksi di dua tingkat: 1) Pessimistic row locking (`lockForUpdate`) pada tabel `students` dan `eligibility_records`; 2) Validasi saldo sisa hak (`remaining = entitlement_qty - already_distributed`). Mahasiswa tidak bisa mengambil barang yang saldonya sudah 0. |
| 7 | **Audit Perubahan Ukuran** | Jika staf loket menyerahkan seragam dengan ukuran berbeda dari yang diinput mahasiswa (`actual_size` != `expected_size`), sistem secara otomatis mencatat record riwayat ke tabel `student_size_histories`. |
| 8 | **Penanganan Barang Ditunda (*Deferred*)** | Jika stok barang habis saat hari distribusi, staf tidak mencentang barang tersebut. Sistem mencatat transaksi sebagian (*partial distribution*), dan barang yang belum diterima tetap tercatat sebagai sisa saldo hak yang dapat diambil pada jadwal berikutnya. |
| 9 | **Dua Email Mahasiswa** | Entitas `Student` menyimpan dua email: `email_kampus` (resmi kampus, unique, digunakan untuk login dan pengiriman akun) dan `email_pribadi` (opsional, diverifikasi via OTP jika diubah). |
| 10 | **Verifikasi OTP** | Pengubahan email dan verifikasi dilakukan melalui kode OTP 6-digit dengan masa kedaluwarsa 15 menit yang disimpan di tabel `otp_codes` dengan throttle proteksi brute-force. |
| 11 | **Tingkat Mahasiswa (*Student Levels*)** | Menggunakan format kode standar akademik: `Y1S1` (Year 1 Semester 1), `Y1S2`, `Y2S1`, `Y2S2`, `Y3S1`, `Y3S2`, `Y4S1`, `Y4S2`, dan `graduated`. |
| 12 | **Koleksi Barang Entitlement** | Pengelompokan barang seragam menggunakan `base_code`. Suatu `Item` mewakili satu barang spesifik (dengan kategori, tipe, gender), sedangkan varian ukuran diikat melalui `ItemVariant`. |
| 13 | **Jadwal Distribusi Dinamis** | Jadwal distribusi terikat pada relasi opsional `generation_id`, `faculty_id`, dan `study_program_id`. Kolom `session` berstatus nullable. Scope `forStudent($student)` menyaring apakah jadwal cocok untuk mahasiswa terkait. |
| 14 | **Deployment cPanel** | Server produksi tidak memiliki akses SSH. Deployment dilakukan via cPanel Git Version Control yang mengeksekusi `git pull`. File `app/helpers.php` didaftarkan di `composer.json` autoload; maka `composer dump-autoload` adalah keharusan mutlak setiap pembaruan. |
| 15 | **Stock Opname Bulanan** | Penyesuaian stok opname melalui alur: Draft Opname → Upload Fisik Excel → Kalkulasi Variance Otomatis → Approval Admin → Pembuatan Jurnal Penyesuaian `StockOpnameAdjustment` dan `StockMovement` (tipe `ADJUSTMENT`). |

---

## 3. Peta Direktori & Titik Masuk (Repository Map)

```
c:\laragon\www\horizon-unistock\
├── app/
│   ├── Enums/                 # Enum resmi (Role.php)
│   ├── Exports/               # Kelas ekspor Excel (StudentExport, CredentialsExport, ReportExport)
│   ├── Helpers.php            # Helper fungsi global (escapeLike, formatCurrency, dsb)
│   ├── Http/
│   │   ├── Controllers/       # Controller per modul (Auth, Master, Staff, Student, Finance, Inventory, Report)
│   │   ├── Middleware/        # Middleware autentikasi, Spatie role/permission, password changed check
│   │   └── Requests/          # Form Request validasi ketat
│   ├── Imports/               # Kelas impor Excel Maatwebsite
│   ├── Models/                # 42 Eloquent Model (Domain Entities)
│   └── Services/              # 16 Domain Service Classes (Distribution, Stock, QrCode, Report, dsb)
├── config/                    # File konfigurasi Laravel (auth, permission, excel, mail, dsb)
├── database/
│   ├── factories/             # Factory untuk testing
│   ├── migrations/            # 58 migrasi database InnoDB MySQL
│   └── seeders/               # Seeder role, permission, student level, superadmin
├── public/                    # Web root publik (index.php, images/logo.png, favicon, build assets)
├── resources/
│   ├── css/                   # Tailwind CSS styling induk
│   ├── js/                    # JavaScript & Alpine.js components
│   └── views/                 # Blade templates (layouts, dashboards, master, distribution, inventory, report)
├── routes/
│   ├── auth.php               # Rute autentikasi Breeze (login, forgot-password, reset, logout)
│   └── web.php                # Rute aplikasi utama terproteksi middleware
├── scripts/                   # Script pendukung & master data Excel asli (Dummy - Inventory Management.xlsx)
└── storage/                   # File log, cache, dan symlink file publik (QR codes)
```

---

## 4. Instruksi Kerja Agen AI (AI Working Guidelines)

1. **Selalu Baca File Aturan `.ai/rules/` Sebelum Mengedit:**
   - Aturan ekspor: `.ai/rules/exports.md` (wajib `: ?array` pada method `styles()`).
   - Aturan keuangan: `.ai/rules/finance.md` (selalu baca flag `is_baju`/`is_sepatu`).
   - Aturan view: `.ai/rules/views.md` (elemen ber-`@click="$dispatch()"` wajib dibungkus `x-data`).
   - Aturan deployment: `.ai/rules/general.md` (jangan gunakan helper global baru tanpa dump-autoload).
2. **Jangan Mengubah Perilaku FIFO:**
   - Segala pengurangan inventaris wajib memanggil `StockService::deductStockFifo()`.
   - Jangan pernah meng-update kolom `stock_balances.quantity` secara langsung melalui raw SQL atau Eloquent biasa.
3. **Pemberian Tanggapan & Pembuatan Solusi:**
   - Selalu berikan referensi baris kode yang tepat dengan link format markdown `[nama_file](file:///c:/laragon/www/horizon-unistock/...)`.
   - Gunakan bahasa Indonesia profesional yang jelas dengan istilah teknis bahasa Inggris baku.
