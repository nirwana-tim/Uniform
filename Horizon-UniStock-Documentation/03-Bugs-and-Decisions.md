# 03 — Architecture Decisions, Known Bugs & Operational Cases: Horizon UniStock

Dokumen ini mencatat seluruh **Architecture Decision Records (ADR)**, status penanganan **kasus operasional bisnis**, serta rekam jejak **masalah teknis (*Known Bugs*)** yang telah diselesaikan maupun yang sedang dipantau pada sistem Horizon UniStock.

---

## 1. Architecture Decision Records (ADR)

### ADR-001: Pilihan Arsitektur Monolitik Modern (Blade + Tailwind + Alpine.js)
- **Status:** Diterima & Diterapkan.
- **Konteks:** Sistem membutuhkan antarmuka yang modern, reaktif, cepat dikembangkan, namun harus di-deploy pada lingkungan hosting cPanel bersama (*shared/managed cPanel*) tanpa akses SSH atau node runtime independen.
- **Keputusan:** Menggunakan stack monolitik Laravel Blade dipadukan dengan Tailwind CSS v3 untuk sistem desain dan Alpine.js v3 untuk interaktivitas reaktif klien (modal dialog, filter dinamis, kalkulasi form). Aset dikompilasi saat build melalui Vite.
- **Konsekuensi:** Menghilangkan kerumitan state management API terpisah (seperti React/Vue SPA), menyederhanakan alur autentikasi sesi, dan memungkinkan deployment langsung via file statis di folder `public/build/`.

### ADR-002: Metode Penilaian Persediaan FIFO Murni dengan Pelacakan Batch (`StockBatch`)
- **Status:** Diterima & Diterapkan.
- **Konteks:** Bagian Keuangan membutuhkan laporan laba kotor (GPM - Gross Profit Margin) yang akurat. Harga beli seragam dari vendor konveksi berfluktuasi dari satu surat jalan ke surat jalan berikutnya.
- **Keputusan:** Mengimplementasikan penilaian persediaan **First-In, First-Out (FIFO)** murni. Setiap penerimaan barang masuk dicatat sebagai entitas `StockBatch` dengan `quantity_remaining` dan `unit_hpp`. Saat distribusi, stok dipotong dari batch tertua.
- **Konsekuensi:** Menghasilkan nilai *Blended HPP* yang mencerminkan harga perolehan riil. Jika terjadi pengembalian atau pembatalan, pemulihan saldo batch membutuhkan penanganan khusus.

### ADR-003: Pembangkitan QR Code Mandiri Server-Side (`bacon/bacon-qr-code`)
- **Status:** Diterima & Diterapkan.
- **Konteks:** Pada saat intake ribuan mahasiswa di aula kampus, koneksi internet publik kerap kali lambat atau terputus. Sistem loket staf dan tampilan QR mahasiswa tidak boleh bergantung pada API generator QR publik eksternal (seperti Google Charts API).
- **Keputusan:** Menggunakan pustaka lokal PHP `bacon/bacon-qr-code` dengan driver `GDLibRenderer`. Payload QR Code berisi string **NIM Mahasiswa**. Gambar di-render langsung dalam format Base64 PNG.
- **Konsekuensi:** QR Code dapat di-generate seketika secara offline tanpa latensi jaringan eksternal. Tingkat koreksi kesalahan disetel ke Level H agar tahan terhadap goresan layar atau kertas yang kusut.

### ADR-004: Manajemen Hak Akses Peran Menggunakan Spatie Laravel Permission
- **Status:** Diterima & Diterapkan.
- **Konteks:** Aplikasi memiliki 4 aktor dengan batasan hak akses ketat: Super Admin (konfigurasi sistem), Admin/Finance (keuangan, laporan, master data), Staff Gudang (scan loket dan fitting), dan Mahasiswa (isi ukuran dan unduh QR).
- **Keputusan:** Menggunakan paket teruji industri `spatie/laravel-permission` yang dipadukan dengan PHP Enum `App\Enums\Role` untuk standarisasi pengecekan peran di seluruh Controller dan Blade directive (`@role`, `@can`).
- **Konsekuensi:** Menghilangkan inkonsistensi string literal peran (hardcoded string) dan memberikan fleksibilitas jika di masa depan dibutuhkan hak akses granular baru.

### ADR-005: Pemisahan Kategori Ukuran Menggunakan Boolean Flag pada `item_sizes`
- **Status:** Diterima & Diterapkan.
- **Konteks:** Kueri relasi pivot antara kategori barang dan ukuran fisik memicu perlambatan query dan kompleksitas logika saat mengisi opsi dropdown ukuran baju dan sepatu di event size.
- **Keputusan:** Menambahkan dua kolom boolean `is_baju` dan `is_sepatu` langsung pada tabel `item_sizes`. Disediakan toggle cepat pada antarmuka Master Data Item Size.
- **Konsekuensi:** Pengambilan opsi ukuran di `SizeChangeEventController` menjadi kueri tunggal yang sangat cepat (`ItemSize::where('is_baju', true)`).

### ADR-006: Relasi One-to-One Opsional antara `Student` dan `User`
- **Status:** Diterima & Diterapkan.
- **Konteks:** Data calon mahasiswa baru diimpor dari file Excel PMB jauh sebelum akun login mereka diterbitkan.
- **Keputusan:** Tabel `students` memiliki kolom `user_id` yang bernilai `nullable`. Proses generate akun dilakukan secara bertahap melalui modul `StudentController::generateAll`.
- **Konsekuensi:** Data akademik mahasiswa dapat diintegrasikan dan diverifikasi kelayakannya terlebih dahulu tanpa mewajibkan pembentukan akun sistem secara terburu-buru.

### ADR-007: Isolasi Transaksi Loket Menggunakan Pessimistic Locking (`lockForUpdate`)
- **Status:** Diterima & Diterapkan.
- **Konteks:** Kemungkinan terjadinya antrean ganda di dua loket yang berbeda dalam detik yang sama untuk satu mahasiswa yang berniat mengambil barang berulang kali (*race condition*).
- **Keputusan:** Membungkus seluruh eksekusi serah terima barang di `DistributionService::processDistribution` di dalam `DB::transaction()` dengan mengunci baris mahasiswa dan kelayakan melalui `Student::whereKey($id)->lockForUpdate()`.
- **Konsekuensi:** Menjamin integritas data 100% dan mencegah risiko double-claim.

### ADR-008: Deployment Produksi via cPanel Git Version Control
- **Status:** Diterima & Diterapkan.
- **Konteks:** Lingkungan hosting universitas berada di cPanel tanpa akses command line SSH langsung ke server.
- **Keputusan:** Deployment menggunakan alur Git Pull dari cPanel dengan automasi pembersihan cache dan eksekusi composer autoloader.
- **Konsekuensi:** Menuntut disiplin ketat dalam pembaruan dependensi dan keharusan menguji build aset secara lokal sebelum commit ke branch `main`.

---

## 2. Rekam Keputusan Kasus Operasional Bisnis

### Kasus Operasional yang Telah Diselesaikan (Resolved Cases)
1. **Kasus 1 — Mahasiswa Mengambil Barang di Luar Jadwal:**
   - *Solusi:* Sistem menyediakan antarmuka `selectSchedule` yang memvalidasi apakah ada jadwal aktif yang sesuai. Jika jadwal telah lewat, staf loket dapat memilih jadwal terdahulu selama mahasiswa tersebut belum pernah mengambil jatahnya.
2. **Kasus 2 — Ketidaksesuaian Ukuran Saat Fitting di Loket:**
   - *Solusi:* Layar staf loket menyediakan dropdown *Actual Size* yang dapat disesuaikan secara fisik. Setiap pergantian ukuran dari yang terdaftar di sistem otomatis memicu pencatatan audit log ke `student_size_histories`.
3. **Kasus 3 — Ketidaksesuaian Gender Barang vs Mahasiswa:**
   - *Solusi:* Sistem memeriksa kecocokan gender barang (`gender`: L/P/U) dengan gender mahasiswa. Komit `6a13c0d` menyederhanakan penanganan ini dengan memberikan peringatan jelas jika staf mencoba menyerahkan seragam lawan jenis tanpa otorisasi.
4. **Kasus 4 — Stok Habis Saat Hari Pembagian (*Partial Distribution*):**
   - *Solusi:* Staf hanya mencentang barang yang fisiknya tersedia di loket. Barang yang tidak dicentang otomatis berstatus *Deferred* (ditunda) dan saldo sisa haknya tetap tersimpan di database untuk diambil pada gelombang berikutnya.
5. **Kasus 5 — Batas Frekuensi Pengubahan Ukuran oleh Mahasiswa:**
   - *Solusi:* Kolom `max_changes` pada tabel `size_change_events` membatasi berapa kali mahasiswa boleh mengedit ukurannya (default 1x atau 2x). Setelah batas tercapai, form input ukuran terkunci secara otomatis.
6. **Kasus 6 — Pencegahan Klaim Ganda Lintas Transaksi (*Remaining Balance Tracking*):**
   - *Solusi:* Sistem menghitung saldo hak real-time: `Sisa Saldo = Kuota Entitlement - Total Yang Sudah Pernah Diterima`. Jika saldo bernilai 0, baris barang di layar staf otomatis terkunci (*disabled*) dengan label "Sudah Diambil".
7. **Kasus 7 — Filter Dinamis Pembuatan Jadwal Distribusi:**
   - *Solusi:* Komponen Alpine.js pada form pembuatan jadwal secara dinamis menyaring daftar barang yang tampil agar hanya mencantumkan item yang sesuai dengan Fakultas dan Prodi yang dipilih.

### Kasus Ambigu & Rekomendasi Kebijakan Operasional
- **Kasus 8 (Batas Akhir Input Ukuran):** *Rekomendasi:* Kunci total form input setelah `end_date` event berakhir. Perubahan setelah deadline wajib melalui permohonan ke admin.
- **Kasus 9 (Perubahan Desain Seragam vs Stok Lama):** *Rekomendasi:* Daftarkan seragam dengan desain baru sebagai `Item` baru (`code` dan `base_code` baru). Stok lama diisolasi untuk melayani pembelian seragam eceran pengganti bagi mahasiswa angkatan atas.
- **Kasus 10 (Penjualan Sisa Stok / POS Eceran):** *Rekomendasi:* Sisa stok di akhir periode distribusi yang tidak terpakai dapat dialokasikan ke modul POS Eceran dengan harga jual standar universitas.

---

## 3. Catatan Masalah Teknis (Known Bugs & Bug Tracking)

| ID | Deskripsi Masalah | Dampak | Status | Akar Masalah & Solusi yang Diterapkan |
|----|-------------------|--------|--------|---------------------------------------|
| **BUG-001** | **Fatal Error: Call to undefined function escapeLike()** | Error 500 pada seluruh pencarian tabel di server cPanel. | **Resolved** | Helper `app/helpers.php` baru belum terdaftar di autoloader PHP server. *Solusi:* Ditambahkan aturan wajib `composer dump-autoload --optimize` setiap deploy, serta refaktor kode menggunakan trait `EscapesLikeWildcards`. |
| **BUG-002** | **Fatal Error pada Export Excel: WithStyles::styles() must return ?array** | Seluruh unduhan template Excel dan ekspor laporan mengalami crash. | **Resolved** | Pustaka `maatwebsite/excel` v4.0 memperketat kontrak interface `WithStyles` yang mewajibkan return type `?array`. *Solusi:* Seluruh kelas ekspor diperbarui deklarasi method-nya menjadi `: ?array` dan mengembalikan `null`. |
| **BUG-003** | **Tombol Modal Alpine.js Tidak Merespons Klik** | Modal konfirmasi hapus / submit tidak terbuka saat diklik. | **Resolved** | Elemen HTML pemanggil berada di luar cakupan `x-data`. *Solusi:* Menambahkan atribut `x-data=""` pada elemen button atau parent terdekat. |
| **BUG-004** | **Pembaruan Profil Mahasiswa Mengalami Validasi Unik Semu** | Mahasiswa gagal menyimpan profil saat tidak mengubah email/NIM. | **Resolved** | Aturan validasi `unique` memeriksa record itu sendiri. *Solusi:* Memperbarui rule validasi menjadi `Rule::unique('students')->ignore($student->id)` dan mengabaikan record yang telah di-softdelete. |
| **BUG-005** | **Session Kolom Nullable pada Distribution Schedules** | Jadwal distribusi gagal disimpan jika sesi dikosongkan. | **Resolved** | Kolom `session` di migrasi awal berstatus `NOT NULL`. *Solusi:* Dijalankan migrasi `2026_08_28_185629_alter_session_nullable_in_distribution_schedules_table` untuk mengubah kolom menjadi `nullable()`. |
| **BUG-006** | **Inkonsistensi Nilai HPP Saat Multiple Variant Ditambahkan Bersamaan** | Rata-rata tertimbang HPP bergeser pada varian baru. | **Resolved** | Method `upsertBalance` di `StockService` diperbarui menggunakan formula weighted average berbasis total stok sebelum mutasi. |
