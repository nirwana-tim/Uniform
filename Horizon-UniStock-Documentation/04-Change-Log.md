# 04 — System Change Log: Horizon UniStock

Catatan riwayat perubahan sistem (*Change Log*) mencatat seluruh pembaruan fitur, perbaikan bug, keputusan refaktor, dan pemeliharaan repositori Horizon UniStock dari awal rilis MVP hingga versi stabil saat ini.

---

## Versi 1.2.0 (Oktober 2026) — Stabilisasi & Penataan Pengetahuan Sistem
- **Chore:** Pembersihan file redundan root repositori dan restrukturisasi dokumentasi induk sistem (`Horizon-UniStock-Documentation/`).
- **Refactor:** Penyederhanaan penanganan validasi gender mismatch pada lembar serah terima loket staf (`commit 6a13c0d`).
- **Docs:** Pembangunan dokumentasi lengkap 90 bab berbasis bukti (*Evidence-Based System Documentation*) dalam format PDF A4 searchable dan markdown referensi teknis.
- **Security:** Audit penanganan kata sandi pengguna dan penghapusan penyimpanan plain-text password (`commit 50140f3`).

---

## Versi 1.1.5 (Agustus 2026) — Penyesuaian Skema & Pemisahan Flag Ukuran
- **Feat:** Mengubah kolom `session` menjadi `nullable` pada tabel `distribution_schedules` melalui migrasi `2026_08_28_185629`.
- **Feat:** Implementasi komponen visual input password terpadu pada form pembuatan dan edit pengguna (`commit 6ea29a2`).
- **Refactor:** Migrasi pengecekan peran dari string literal menjadi konstanta PHP Enum `App\Enums\Role` untuk meningkatkan pemeliharaan kode (`commit e3c1217`).
- **Feat:** Penambahan kolom boolean `is_baju` dan `is_sepatu` pada tabel `item_sizes` via migrasi backfill untuk mempercepat query opsi event pengisian ukuran.
- **Refactor:** Validasi unik pada seluruh form request diperbarui agar otomatis mengabaikan data yang berada dalam status *trashed* (soft-deleted).

---

## Versi 1.1.0 (Pertengahan Agustus 2026) — Penyempurnaan Inventaris FIFO & Stock Opname
- **Feat:** Integrasi modul Stock Opname bulanan dengan upload file Excel fisik, kalkulasi selisih otomatis (*variance calculation*), dan jurnal penyesuaian (*adjustment*).
- **Feat:** Penerapan algoritma pemotongan persediaan FIFO murni melalui tabel `stock_batches` dan pencatatan riwayat kartu stok `stock_movements`.
- **Feat:** Peluncuran Sales Dashboard terintegrasi dengan kalkulasi laba kotor (*Gross Profit Margin - GPM*) real-time bagi peran Super Admin dan Finance Admin.
- **Feat:** Modul penanganan kredensial mahasiswa (`StudentController::credentials`) dengan fitur generate massal, pengiriman email SMTP otomatis, dan tombol reset password acak.

---

## Versi 1.0.0 (Awal Agustus 2026) — Rilis MVP (Minimum Viable Product)
- **Feat:** Setup fondasi framework Laravel 12/13, autentikasi Laravel Breeze, dan permission Spatie.
- **Feat:** Pembangunan modul master data akademik (Fakultas, Prodi, Angkatan, Jenjang Mahasiswa).
- **Feat:** Pembangunan modul master inventaris (Kategori, Tipe, Departemen, Item, Varian Ukuran, Vendor).
- **Feat:** Alur pendaftaran ukuran mandiri mahasiswa melalui event pengisian ukuran (`SizeChangeEvent`).
- **Feat:** Pembangkitan QR Code identitas digital berbasis NIM mahasiswa menggunakan `bacon/bacon-qr-code`.
- **Feat:** Antarmuka pemindaian loket distribusi staf dengan dukungan scanner kamera `html5-qrcode` dan pencarian fallback manual.
- **Feat:** Integrasi transaksi distribusi dengan verifikasi status kelayakan keuangan (*EligibilityRecord*).
