# 01 — Database Schema & Data Dictionary: Horizon UniStock

Sistem Horizon UniStock menggunakan database relasional **MySQL** (engine InnoDB dengan dukungan penuh transaksi ACID dan foreign keys). Berikut adalah kamus data (*data dictionary*) lengkap dari seluruh tabel inti sistem.

---

## 1. Modul Pengguna & Hak Akses

### 1.1 Tabel `users`
Menyimpan identitas akun pengguna (Super Admin, Admin, Staff Gudang, dan Mahasiswa).
- `id` (BIGINT UNSIGNED, PK, Auto Increment)
- `name` (VARCHAR 255): Nama lengkap pengguna.
- `email` (VARCHAR 255, UNIQUE): Alamat email utama untuk login.
- `email_verified_at` (TIMESTAMP, NULLABLE): Waktu verifikasi email.
- `password` (VARCHAR 255): Hash kata sandi bcrypt.
- `must_change_password` (BOOLEAN, DEFAULT TRUE): Flag wajib ganti password pada login pertama.
- `is_active` (BOOLEAN, DEFAULT TRUE): Flag status aktif akun.
- `remember_token` (VARCHAR 100, NULLABLE)
- `created_at`, `updated_at` (TIMESTAMP)

### 1.2 Tabel Spatie Permission
- `roles`: `id`, `name` (super_admin, admin, staff, student), `guard_name` (web).
- `permissions`: `id`, `name` (manage-students, manage-distributions, manage-finance), `guard_name`.
- `model_has_roles`: `role_id`, `model_type`, `model_id`.
- `model_has_permissions`: `permission_id`, `model_type`, `model_id`.
- `role_has_permissions`: `permission_id`, `role_id`.

---

## 2. Modul Master Akademik

### 2.1 Tabel `faculties`
- `id` (BIGINT UNSIGNED, PK)
- `code` (VARCHAR 50, UNIQUE): Kode fakultas (misal: `FK`, `FTI`).
- `name` (VARCHAR 255): Nama fakultas (misal: *Fakultas Ilmu Kesehatan*, *Fakultas Teknologi Informasi*).
- `created_at`, `updated_at` (TIMESTAMP)

### 2.2 Tabel `study_programs`
- `id` (BIGINT UNSIGNED, PK)
- `faculty_id` (BIGINT UNSIGNED, FK -> faculties.id, NULL ON DELETE)
- `code` (VARCHAR 50, UNIQUE): Kode prodi (misal: `S1-KEP`, `S1-IF`, `D3-KEB`).
- `name` (VARCHAR 255): Nama program studi.
- `created_at`, `updated_at` (TIMESTAMP)

### 2.3 Tabel `student_generations`
- `id` (BIGINT UNSIGNED, PK)
- `year` (YEAR / INT): Tahun angkatan (misal: `2024`, `2025`, `2026`).
- `name` (VARCHAR 100): Nama label angkatan (misal: *Angkatan 2026*).
- `created_at`, `updated_at` (TIMESTAMP)

### 2.4 Tabel `student_levels`
- `id` (BIGINT UNSIGNED, PK)
- `kode` (VARCHAR 50, UNIQUE): Kode jenjang semester (`Y1S1`, `Y1S2`, `Y2S1`, `Y2S2`, `Y3S1`, `Y3S2`, `Y4S1`, `Y4S2`, `graduated`).
- `deskripsi` (VARCHAR 255): Keterangan deskriptif (misal: *Year 1 Sem 1*).
- `status` (VARCHAR 50): Status kualifikasi (`Freshman`, `Continuing`, `Graduated`).
- `created_at`, `updated_at` (TIMESTAMP)

---

## 3. Modul Mahasiswa & Profil Ukuran

### 3.1 Tabel `students`
Menyimpan profil induk biodata mahasiswa.
- `id` (BIGINT UNSIGNED, PK)
- `user_id` (BIGINT UNSIGNED, FK -> users.id, NULL ON DELETE): Akun login mahasiswa.
- `nim` (VARCHAR 255, UNIQUE, INDEX): Nomor Induk Mahasiswa resmi.
- `name` (VARCHAR 255, INDEX): Nama mahasiswa.
- `gender` (CHAR 1, NULLABLE): Jenis kelamin (`L` = Laki-laki, `P` = Perempuan).
- `email_kampus` (VARCHAR 255, UNIQUE, NULLABLE): Email resmi kampus (`@krw.horizon.ac.id`).
- `email_pribadi` (VARCHAR 255, NULLABLE): Email pribadi mahasiswa.
- `study_program_id` (BIGINT UNSIGNED, FK -> study_programs.id, NULL ON DELETE)
- `generation_id` (BIGINT UNSIGNED, FK -> student_generations.id, NULL ON DELETE)
- `student_level` (VARCHAR 50, DEFAULT 'Y1S1', INDEX): Jenjang mahasiswa saat ini.
- `status` (VARCHAR 20, DEFAULT 'active', INDEX): Status mahasiswa (`active`, `inactive`, `graduated`).
- `current_semester` (VARCHAR 20, DEFAULT 'Y1S1')
- `entitlement_code` (VARCHAR 255, NULLABLE, INDEX): Kode paket hak barang yang terikat.
- `email_verified_at` (TIMESTAMP, NULLABLE): Waktu verifikasi email.
- `created_at`, `updated_at` (TIMESTAMP)
- `deleted_at` (TIMESTAMP, NULLABLE, SOFT DELETES)

### 3.2 Tabel `student_size_profiles`
Menyimpan profil ukuran fisik yang aktif bagi mahasiswa.
- `id` (BIGINT UNSIGNED, PK)
- `student_id` (BIGINT UNSIGNED, FK -> students.id, CASCADE ON DELETE)
- `baju_size` (VARCHAR 10, NULLABLE): Ukuran baju yang dipilih (misal: `M`, `L`, `XL`).
- `sepatu_size` (VARCHAR 10, NULLABLE): Ukuran sepatu yang dipilih (misal: `40`, `41`, `42`).
- `is_active` (BOOLEAN, DEFAULT TRUE)
- `created_at`, `updated_at` (TIMESTAMP)

### 3.3 Tabel `student_size_histories`
Audit trail perubahan ukuran badan mahasiswa (baik saat pengisian mandiri maupun saat fitting di loket staf).
- `id` (BIGINT UNSIGNED, PK)
- `student_id` (BIGINT UNSIGNED, FK -> students.id, CASCADE ON DELETE)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `old_size` (VARCHAR 10): Ukuran sebelumnya.
- `new_size` (VARCHAR 10): Ukuran baru pengganti.
- `changed_by` (BIGINT UNSIGNED, FK -> users.id, NULL ON DELETE): User yang melakukan perubahan.
- `reason` (VARCHAR 255, NULLABLE): Alasan perubahan (misal: *Fitting di loket kesempitan*).
- `created_at`, `updated_at` (TIMESTAMP)

### 3.4 Tabel `eligibility_records`
Status kelayakan mahasiswa untuk menerima seragam dari Bagian Keuangan.
- `id` (BIGINT UNSIGNED, PK)
- `student_id` (BIGINT UNSIGNED, FK -> students.id, CASCADE ON DELETE)
- `is_eligible` (BOOLEAN, DEFAULT FALSE, INDEX): Layak ambil barang atau tidak.
- `payment_status` (VARCHAR 50, DEFAULT 'unpaid'): Status pembayaran (`paid`, `partial`, `unpaid`).
- `notes` (TEXT, NULLABLE)
- `verified_by` (BIGINT UNSIGNED, FK -> users.id, NULL ON DELETE)
- `verified_at` (TIMESTAMP, NULLABLE)
- `created_at`, `updated_at` (TIMESTAMP)

---

## 4. Modul Master Inventaris & Seragam

### 4.1 Tabel `item_categories`
- `id` (BIGINT UNSIGNED, PK)
- `code` (VARCHAR 50, UNIQUE): Kode kategori (`UNF` = Uniform, `SHO` = Shoes, `ACC` = Accessories).
- `name` (VARCHAR 255): Nama kategori.
- `created_at`, `updated_at` (TIMESTAMP)

### 4.2 Tabel `item_types`
- `id` (BIGINT UNSIGNED, PK)
- `code` (VARCHAR 50, UNIQUE): Kode tipe (misal: `ALM` = Jas Almamater, `KPK` = Kemeja Praktik).
- `name` (VARCHAR 255): Nama tipe barang.
- `created_at`, `updated_at` (TIMESTAMP)

### 4.3 Tabel `item_departments`
- `id` (BIGINT UNSIGNED, PK)
- `code` (VARCHAR 50, UNIQUE): Departemen penanggung jawab (misal: `KEMHS` = Kemahasiswaan, `GUD` = Logistik).
- `name` (VARCHAR 255)
- `created_at`, `updated_at` (TIMESTAMP)

### 4.4 Tabel `item_sizes`
Daftar seluruh ukuran master dengan penandaan kategori.
- `id` (BIGINT UNSIGNED, PK)
- `name` (VARCHAR 50): Label ukuran (`S`, `M`, `L`, `XL`, `XXL`, `38`, `39`, `40`, `41`, `42`).
- `category_id` (BIGINT UNSIGNED, FK -> item_categories.id, NULL ON DELETE)
- `is_baju` (BOOLEAN, DEFAULT FALSE): Flag penanda kelompok baju.
- `is_sepatu` (BOOLEAN, DEFAULT FALSE): Flag penanda kelompok sepatu.
- `sort_order` (INT, DEFAULT 0): Urutan penataan dropdown.
- `created_at`, `updated_at` (TIMESTAMP)

### 4.5 Tabel `items`
Barang master level agregasi.
- `id` (BIGINT UNSIGNED, PK)
- `name` (VARCHAR 255, INDEX): Nama barang (misal: *Jas Almamater Horizon*, *Baju Putih Praktik*).
- `code` (VARCHAR 255, UNIQUE): Kode unik barang.
- `base_code` (VARCHAR 50, NULLABLE, INDEX): Kode dasar produk untuk menyatukan relasi entitlement.
- `gender` (CHAR 1, NULLABLE, INDEX): Peruntukan gender (`L`, `P`, atau `U` = Unisex).
- `category_id` (BIGINT UNSIGNED, FK -> item_categories.id, NULL ON DELETE)
- `type_id` (BIGINT UNSIGNED, FK -> item_types.id, NULL ON DELETE)
- `department_id` (BIGINT UNSIGNED, FK -> item_departments.id, NULL ON DELETE)
- `unit` (VARCHAR 50, DEFAULT 'pcs'): Satuan unit.
- `selling_price` (DECIMAL 15,2, DEFAULT 0): Harga jual standar penetapan universitas.
- `hpp` (DECIMAL 15,2, DEFAULT 0): Harga Pokok Penjualan rata-rata berjalan.
- `is_active` (BOOLEAN, DEFAULT TRUE, INDEX)
- `created_at`, `updated_at` (TIMESTAMP)

### 4.6 Tabel `item_variants`
Varian ukuran spesifik yang memiliki SKU gudang dan stok fisik.
- `id` (BIGINT UNSIGNED, PK)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `size_id` (BIGINT UNSIGNED, FK -> item_sizes.id, NULL ON DELETE)
- `size` (VARCHAR 50): Nilai string ukuran (misal: `M`, `L`, `42`).
- `size_label` (VARCHAR 100, NULLABLE)
- `sku` (VARCHAR 255, UNIQUE): Stock Keeping Unit fisik.
- `weight` (DECIMAL 8,2, NULLABLE): Berat item dalam gram/kg.
- `created_at`, `updated_at` (TIMESTAMP)
- Index gabungan: `(item_id, size)`

---

## 5. Modul Entitlement & Jadwal Distribusi

### 5.1 Tabel `entitlements`
Paket hak jatah seragam per program studi dan tingkat.
- `id` (BIGINT UNSIGNED, PK)
- `code` (VARCHAR 255): Kode unik entitlement (misal: `ENT-S1KEP-Y1S1`).
- `student_level` (VARCHAR 20, NULLABLE): Jenjang peruntukan.
- `description` (VARCHAR 255, NULLABLE)
- `is_active` (BOOLEAN, DEFAULT TRUE, INDEX)
- `created_at`, `updated_at` (TIMESTAMP)
- Unique: `(code, student_level)`

### 5.2 Tabel `entitlement_items`
Daftar rincian barang dan kuota hak per paket entitlement.
- `id` (BIGINT UNSIGNED, PK)
- `entitlement_id` (BIGINT UNSIGNED, FK -> entitlements.id, CASCADE ON DELETE)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `quantity` (INT, DEFAULT 1): Jumlah hak barang per mahasiswa.
- Unique: `(entitlement_id, item_id)`
- `created_at`, `updated_at` (TIMESTAMP)

### 5.3 Tabel `distribution_schedules`
Jadwal operasional pembukaan loket distribusi.
- `id` (BIGINT UNSIGNED, PK)
- `name` (VARCHAR 255): Nama agenda jadwal (misal: *Distribusi Tahap 1 FTI Angkatan 2026*).
- `period` (VARCHAR 255, NULLABLE, INDEX): Periode akademik (misal: *Ganjil 2026/2027*).
- `student_level` (VARCHAR 255, NULLABLE, INDEX): Target jenjang mahasiswa.
- `date` (DATE, INDEX): Tanggal pelaksanaan.
- `location` (VARCHAR 255): Lokasi gedung/ruang loket (misal: *Gedung C Lantai 1*).
- `session` (VARCHAR 255, NULLABLE): Nama sesi pembagian.
- `start_time`, `end_time` (TIME, NULLABLE): Rentang jam operasional.
- `generation_id` (BIGINT UNSIGNED, FK -> student_generations.id, NULL ON DELETE)
- `faculty_id` (BIGINT UNSIGNED, FK -> faculties.id, NULL ON DELETE)
- `study_program_id` (BIGINT UNSIGNED, FK -> study_programs.id, NULL ON DELETE)
- `is_active` (BOOLEAN, DEFAULT TRUE, INDEX)
- `created_at`, `updated_at` (TIMESTAMP)
- `deleted_at` (TIMESTAMP, NULLABLE, SOFT DELETES)

### 5.4 Tabel `dist_schedule_items`
Filter barang-barang yang dibuka pada suatu jadwal tertentu.
- `id` (BIGINT UNSIGNED, PK)
- `schedule_id` (BIGINT UNSIGNED, FK -> distribution_schedules.id, CASCADE ON DELETE)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `created_at`, `updated_at` (TIMESTAMP)

---

## 6. Modul Transaksi Distribusi & Loket Staf

### 6.1 Tabel `distribution_transactions`
Rekam transaksi penyerahan barang di loket.
- `id` (BIGINT UNSIGNED, PK)
- `student_id` (BIGINT UNSIGNED, FK -> students.id, NULL ON DELETE)
- `schedule_id` (BIGINT UNSIGNED, FK -> distribution_schedules.id, NULL ON DELETE)
- `staff_id` (BIGINT UNSIGNED, FK -> users.id, NULL ON DELETE): Staf petugas loket.
- `status` (VARCHAR 20, DEFAULT 'completed', INDEX): Status transaksi (`completed`, `cancelled`).
- `pickup_time` (TIMESTAMP, INDEX): Waktu aktual penyerahan barang.
- `notes` (TEXT, NULLABLE): Catatan manual staf.
- `created_at`, `updated_at` (TIMESTAMP)
- `deleted_at` (TIMESTAMP, NULLABLE, SOFT DELETES)
- Index: `(student_id, schedule_id, status)`

### 6.2 Tabel `distribution_items`
Rincian fisik barang, ukuran, harga jual, dan HPP batch saat didistribusikan.
- `id` (BIGINT UNSIGNED, PK)
- `transaction_id` (BIGINT UNSIGNED, FK -> distribution_transactions.id, CASCADE ON DELETE)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `variant_id` (BIGINT UNSIGNED, FK -> item_variants.id, NULL ON DELETE)
- `expected_size` (VARCHAR 255): Ukuran yang sebelumnya dipesan oleh mahasiswa.
- `actual_size` (VARCHAR 255, NULLABLE): Ukuran fisik yang benar-benar diserahkan.
- `quantity` (INT, DEFAULT 1): Jumlah unit diserahkan.
- `hpp` (DECIMAL 15,2, DEFAULT 0): Blended HPP hasil pemotongan batch FIFO.
- `selling_price_at_distribution` (DECIMAL 15,2, DEFAULT 0): Harga jual patokan saat transaksi.
- `unit_price` (DECIMAL 15,2, DEFAULT 0)
- `created_at`, `updated_at` (TIMESTAMP)
- Index: `(transaction_id, item_id)`, `(item_id, variant_id)`

---

## 7. Modul Inventaris & Penilaian Persediaan FIFO

### 7.1 Tabel `stock_receives`
Dokumen penerimaan barang masuk dari vendor konveksi/pabrik.
- `id` (BIGINT UNSIGNED, PK)
- `reference_number` (VARCHAR 255, UNIQUE): Nomor Surat Jalan / Bukti Terima (misal: `RCV-202609-001`).
- `vendor_id` (BIGINT UNSIGNED, FK -> vendors.id, NULL ON DELETE)
- `receive_date` (DATE, INDEX): Tanggal penerimaan barang.
- `status` (VARCHAR 20, DEFAULT 'pending', INDEX): Status dokumen (`received`, `pending`, `cancelled`).
- `notes` (TEXT, NULLABLE)
- `created_at`, `updated_at` (TIMESTAMP)
- `deleted_at` (TIMESTAMP, NULLABLE, SOFT DELETES)

### 7.2 Tabel `stock_receive_items`
Item rincian dari dokumen penerimaan.
- `id` (BIGINT UNSIGNED, PK)
- `stock_receive_id` (BIGINT UNSIGNED, FK -> stock_receives.id, CASCADE ON DELETE)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `variant_id` (BIGINT UNSIGNED, FK -> item_variants.id, NULL ON DELETE)
- `quantity` (INT): Kuantitas barang yang diterima.
- `unit_price` (DECIMAL 15,2, DEFAULT 0): Harga beli per unit dari vendor.
- `hpp` (DECIMAL 15,2, DEFAULT 0): Nilai HPP modal unit.
- `created_at`, `updated_at` (TIMESTAMP)

### 7.3 Tabel `stock_batches`
Fondasi kalkulasi **FIFO**: Melacak sisa kuantitas pada masing-masing gelombang penerimaan.
- `id` (BIGINT UNSIGNED, PK)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `variant_id` (BIGINT UNSIGNED, FK -> item_variants.id, NULL ON DELETE)
- `quantity_remaining` (INT, DEFAULT 0): Sisa kuantitas pada batch ini yang belum terpotong.
- `unit_hpp` (DECIMAL 15,2, DEFAULT 0): Biaya HPP per unit pada batch terkait.
- `received_date` (DATE): Tanggal kedatangan batch (kunci pengurutan FIFO).
- `stock_receive_item_id` (BIGINT UNSIGNED, FK -> stock_receive_items.id, NULL ON DELETE)
- `created_at`, `updated_at` (TIMESTAMP)
- `deleted_at` (TIMESTAMP, NULLABLE, SOFT DELETES)
- Index: `(item_id, variant_id, received_date)`

### 7.4 Tabel `stock_movements`
Buku besar mutasi persediaan (Kartu Stok Elektronik).
- `id` (BIGINT UNSIGNED, PK)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `variant_id` (BIGINT UNSIGNED, FK -> item_variants.id, NULL ON DELETE)
- `type` (VARCHAR 3, INDEX): Jenis mutasi (`IN` = Masuk, `OUT` = Keluar, `ADJ` = Penyesuaian Stock Opname).
- `quantity` (INT): Jumlah barang yang bermutasi.
- `hpp` (DECIMAL 15,2, DEFAULT 0): Nilai HPP saat mutasi berlangsung.
- `stock_batch_id` (BIGINT UNSIGNED, FK -> stock_batches.id, NULL ON DELETE)
- `reference_type` (VARCHAR 255): Nama model sumber (`StockReceive`, `DistributionTransaction`, `StockOpname`).
- `reference_id` (BIGINT UNSIGNED): ID entitas sumber.
- `notes` (TEXT, NULLABLE)
- `created_at`, `updated_at` (TIMESTAMP)
- `deleted_at` (TIMESTAMP, NULLABLE, SOFT DELETES)
- Index: `(item_id, variant_id, type)`, `(reference_type, reference_id)`, `created_at`

### 7.5 Tabel `stock_balances`
Saldo posisi persediaan fisik saat ini (Real-time snapshot).
- `id` (BIGINT UNSIGNED, PK)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `variant_id` (BIGINT UNSIGNED, FK -> item_variants.id, NULL ON DELETE)
- `quantity` (INT, DEFAULT 0, INDEX): Saldo fisik on-hand saat ini.
- `reserved` (INT, DEFAULT 0): Saldo ter-alokasi/terpesan.
- `last_hpp` (DECIMAL 15,2, DEFAULT 0): HPP rata-rata berjalan terkini.
- Unique: `(item_id, variant_id)`
- `created_at`, `updated_at` (TIMESTAMP)

---

## 8. Modul Stock Opname & Penyesuaian Selisih Fisik

### 8.1 Tabel `stock_opnames`
Sesi penghitungan fisik persediaan berkala.
- `id` (BIGINT UNSIGNED, PK)
- `reference_number` (VARCHAR 255, UNIQUE): Nomor register sesi opname.
- `opname_date` (DATE, INDEX): Tanggal pelaksanaan.
- `period` (VARCHAR 255, INDEX): Periode audit bulanan (misal: *Agustus 2026*).
- `notes` (TEXT, NULLABLE)
- `status` (VARCHAR 20, DEFAULT 'draft', INDEX): Status dokumen (`draft`, `approved`, `rejected`).
- `created_by` (BIGINT UNSIGNED, FK -> users.id)
- `created_at`, `updated_at` (TIMESTAMP)

### 8.2 Tabel `stock_opname_items`
Perbandingan kuantitas sistem dengan hasil penghitungan fisik lapangan.
- `id` (BIGINT UNSIGNED, PK)
- `stock_opname_id` (BIGINT UNSIGNED, FK -> stock_opnames.id, CASCADE ON DELETE)
- `item_id` (BIGINT UNSIGNED, FK -> items.id, NULL ON DELETE)
- `variant_id` (BIGINT UNSIGNED, FK -> item_variants.id, NULL ON DELETE)
- `system_quantity` (INT): Saldo menurut catatan sistem saat opname dibuka.
- `physical_quantity` (INT): Saldo fisik hasil penghitungan riil di rak gudang.
- `notes` (TEXT, NULLABLE)
- Unique: `(stock_opname_id, item_id, variant_id)`
- `created_at`, `updated_at` (TIMESTAMP)

### 8.3 Tabel `stock_opname_adjustments`
Jurnal koreksi penyesuaian yang disetujui untuk menyamakan saldo buku dengan saldo fisik.
- `id` (BIGINT UNSIGNED, PK)
- `stock_opname_id` (BIGINT UNSIGNED, FK -> stock_opnames.id)
- `stock_movement_id` (BIGINT UNSIGNED, FK -> stock_movements.id, NULL ON DELETE)
- `type` (VARCHAR 20): Tipe selisih (`surplus` = fisik lebih banyak, `shortage` = fisik kurang / susut).
- `quantity` (INT): Besaran selisih kuantitas.
- `reason` (TEXT, NULLABLE): Justifikasi alasan selisih.
- `approved_by` (BIGINT UNSIGNED, FK -> users.id, NULL ON DELETE)
- `approved_at` (TIMESTAMP, NULLABLE)
- `created_at`, `updated_at` (TIMESTAMP)
