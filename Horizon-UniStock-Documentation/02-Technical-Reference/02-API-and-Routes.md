# 02 — API & Web Routes Documentation: Horizon UniStock

Seluruh interaksi sistem UniStock dikendalikan melalui berkas rute terdefinisi di `routes/web.php` dan `routes/auth.php`. Rute-rute ini dilindungi oleh middleware hierarkis berbasis sesi, verifikasi perubahan password default, rate limiting (throttle), dan hak akses Spatie Role/Permission.

---

## 1. Rute Autentikasi (`routes/auth.php`)

| Method | URI | Nama Rute | Middleware | Keterangan & Aksi |
|--------|-----|-----------|------------|-------------------|
| `GET` | `/login` | `login` | `guest` | Form login multi-role |
| `POST` | `/login` | `login` | `guest`, `throttle:5,1` | Validasi kredensial (NIM/Email + Password) |
| `GET` | `/forgot-password` | `password.request` | `guest` | Permintaan kirim token reset password |
| `POST` | `/forgot-password` | `password.email` | `guest`, `throttle:3,1` | Pengiriman tautan reset ke email |
| `GET` | `/reset-password/{token}` | `password.reset` | `guest` | Formulir setel ulang password baru |
| `POST` | `/reset-password` | `password.store` | `guest`, `throttle:3,1` | Penyimpanan password baru |
| `GET` | `/password/change` | `password.change` | `auth` | Form pergantian password wajib login pertama |
| `POST` | `/password/change` | `password.change.store` | `auth`, `throttle:5,1` | Proses pergantian password awal |
| `POST` | `/logout` | `logout` | `auth` | Invalidate session & token CSRF |

---

## 2. Rute Profil & Verifikasi Email (`routes/web.php`)

| Method | URI | Nama Rute | Middleware | Keterangan & Aksi |
|--------|-----|-----------|------------|-------------------|
| `GET` | `/profile` | `profile.edit` | `auth`, `password.changed` | Tampilan kelola profil user |
| `PATCH` | `/profile` | `profile.update` | `auth`, `password.changed`, `throttle:5,1` | Update informasi profil pengguna |
| `DELETE` | `/profile` | `profile.destroy` | `auth`, `password.changed`, `throttle:5,1` | Hapus akun mandiri |
| `GET` | `/profile/email/change` | `profile.email.change` | `auth`, `password.changed` | Form ganti alamat email |
| `POST` | `/profile/email/verify-password` | `profile.email.verify-password` | `auth`, `throttle:5,1` | Re-autentikasi password sebelum ganti email |
| `GET` | `/profile/email/input-email` | `profile.email.input-email` | `auth`, `password.changed` | Input email baru |
| `POST` | `/profile/email/send-otp` | `profile.email.send-otp` | `auth`, `throttle:3,1` | Pengiriman 6-digit OTP ke email baru |
| `GET` | `/profile/email/verify` | `profile.email.verify-otp` | `auth`, `password.changed` | Form verifikasi OTP |
| `POST` | `/profile/email/verify` | `profile.email.verify-otp.post` | `auth`, `throttle:5,1` | Verifikasi OTP & pembaruan alamat email |

---

## 3. Rute Dashboard Terpadu (`DashboardController`)

| Method | URI | Nama Rute | Middleware | Keterangan & Aksi |
|--------|-----|-----------|------------|-------------------|
| `GET` | `/dashboard` | `dashboard` | `auth`, `password.changed` | Dispatched otomatis sesuai Role pengguna: `SuperAdmin/Admin` -> Sales & GPM Dashboard; `Staff` -> Loket Distribution Dashboard; `Student` -> Status Ukuran & Jadwal Mahasiswa. |

---

## 4. Rute Portal Mahasiswa (`role:student`)

| Method | URI | Nama Rute | Middleware | Keterangan & Aksi |
|--------|-----|-----------|------------|-------------------|
| `GET` | `/student/sizes` | `student.sizes.index` | `auth`, `password.changed`, `role:student` | Ringkasan profil ukuran baju & sepatu |
| `GET` | `/student/sizes/{event}/input` | `student.sizes.input` | `auth`, `password.changed`, `role:student` | Form input ukuran sesuai SizeChangeEvent aktif |
| `POST` | `/student/sizes` | `student.sizes.store` | `auth`, `password.changed`, `role:student` | Simpan ukuran & buat riwayat size profile |
| `GET` | `/student/qr` | `student.qr` | `auth`, `password.changed`, `role:student` | Tampilan QR Code resmi beresolusi tinggi (NIM) |
| `GET` | `/student/items` | `student.items.index` | `auth`, `password.changed`, `role:student` | Riwayat daftar seragam yang telah diterima |
| `POST` | `/student/email/send-otp` | `student.email.send-otp` | `auth`, `throttle:3,1` | Request OTP verifikasi email mahasiswa |
| `GET` | `/student/email/verify` | `student.email.verify-form` | `auth` | Layar form verifikasi email mahasiswa |
| `POST` | `/student/email/verify-otp` | `student.email.verify-otp` | `auth`, `throttle:5,1` | Validasi OTP email mahasiswa |

---

## 5. Rute Loket & Pemindaian Staf (`role:staff|admin|super_admin`)

| Method | URI | Nama Rute | Middleware | Keterangan & Aksi |
|--------|-----|-----------|------------|-------------------|
| `GET` | `/distribution/scan` | `distribution.scan.index` | `auth`, `password.changed`, `role:...` | Antarmuka kamera scanner QR Code |
| `POST` | `/distribution/search` | `distribution.search` | `auth`, `throttle:30,1` | Pencarian mahasiswa via scanner / input AJAX |
| `GET` | `/distribution/search` | `distribution.search.get` | `auth` | Fallback pencarian manual via query string |
| `GET` | `/distribution/student/{nim}` | `distribution.scan.student` | `auth`, `password.changed` | Tampilan verifikasi kelayakan & checklist barang |
| `GET` | `/distribution/student/{nim}/select-schedule` | `distribution.scan.select-schedule` | `auth`, `password.changed` | Pemilihan jadwal jika ada jadwal kadaluarsa/ganda |
| `POST` | `/distribution/process` | `distribution.process` | `auth`, `throttle:15,1` | **Submit Transaksi**: Potong stok FIFO & serahkan barang |

---

## 6. Rute Master Data & Distribusi Admin (`role:admin|super_admin`)

### 6.1 Master Data Dasar (`/master-data`)
- `faculty` (Resource: index, create, store, edit, update, destroy)
- `study-program` (Resource)
- `student-generation` (Resource)
- `item-category` (Resource)
- `item-type` (Resource)
- `item-department` (Resource)
- `item-size` (Resource)
- `PUT /master-data/item-size/{item_size}/tag`: Toggle flag `is_baju` atau `is_sepatu`.
- `item` (Resource) + `item.sizes-types-by-category` (Filter dinamis JSON)
- `POST /master-data/item/{item}/variant`: Tambah varian ukuran SKU.
- `DELETE /master-data/item/{item}/variant/{variant}`: Hapus varian ukuran.
- `vendor` (Resource)
- `item-price` (Resource)
- `student-level` (Resource: only index, show)

### 6.2 Modul Pengelolaan Mahasiswa & Kredensial (`/student`)
- `GET /student/students-data`: Katalog seluruh mahasiswa dengan pagination & filter.
- `POST /student/students-data`: Tambah mahasiswa manual.
- `GET /student/students-data/{student}`: Detail lengkap mahasiswa (profil, hak, transaksi).
- `GET /student/generate`: Antarmuka generate akun mahasiswa massal.
- `POST /student/generate`: Generate batch akun mahasiswa.
- `POST /student/generate-all`: Generate seluruh akun mahasiswa yang belum memiliki akun.
- `GET /student/export`: Ekspor seluruh master mahasiswa ke Excel.
- `GET /student/credentials`: Daftar status akun, password sementara, dan pengiriman email.
- `GET /student/credentials/export`: Ekspor kredensial mahasiswa ke Excel terproteksi.
- `POST /student/credentials/{student}/reset-password`: Reset kata sandi mahasiswa acak.
- `POST /student/credentials/{student}/resend-email`: Kirim ulang email kredensial ke mahasiswa.
- `POST /student/credentials/resend-all-failed`: Kirim ulang seluruh email berstatus gagal.
- `POST /student/credentials/destroy`: Hapus kredensial mahasiswa.
- `GET /student/eligibility`: Rekap kelayakan administrasi keuangan mahasiswa.
- `POST /student/eligibility/{student}/toggle`: Toggle status kelayakan (`Eligible`/`Ineligible`).

### 6.3 Modul Distribusi & Size Monitoring (`/distribution`)
- `entitlement` (Resource): Atur kuota jatah barang per prodi/jenjang.
- `distribution-schedule` (Resource): Buat agenda jadwal distribusi.
- `GET /distribution/distribution-schedule/fetch-items`: Ambil item sesuai prodi terpilih (AJAX).
- `GET /distribution/distribution-schedule/{id}/transactions`: Log transaksi pada jadwal terkait.
- `GET /distribution/size-monitor`: Rekapitulasi sebaran ukuran baju/sepatu mahasiswa per prodi.
- `size-events` (Resource): Buka jadwal event pengisian/perubahan ukuran mahasiswa.

---

## 7. Rute Inventaris Gudang (`/inventory`)

| Method | URI | Nama Rute | Keterangan & Aksi |
|--------|-----|-----------|-------------------|
| `GET` | `/inventory/stock-receive` | `inventory.stock-receive.index` | Daftar surat jalan barang masuk vendor |
| `POST` | `/inventory/stock-receive` | `inventory.stock-receive.store` | Simpan penerimaan barang, bentuk `StockBatch`, update `StockBalance` |
| `GET` | `/inventory/stock-receive/search-items` | `inventory.stock-receive.search-items` | AJAX autocomplete pencarian barang masuk |
| `GET` | `/inventory/stock-balance` | `inventory.stock-balance.index` | Posisi saldo persediaan terkini per item dan ukuran |
| `GET` | `/inventory/stock-movement` | `inventory.stock-movement.index` | Kartu mutasi stok terperinci (IN, OUT, ADJ) |
| `GET` | `/inventory/stock-opname` | `inventory.stock-opname.index` | Daftar agenda stock opname berkala |
| `POST` | `/inventory/stock-opname` | `inventory.stock-opname.store` | Buat draft register stock opname baru |
| `POST` | `/inventory/stock-opname/{id}/upload` | `inventory.stock-opname.upload` | Unggah hasil hitung fisik Excel |
| `POST` | `/inventory/stock-opname/{id}/approve` | `inventory.stock-opname.approve` | **Approval Opname**: Rekonsiliasi saldo & posting jurnal penyesuaian |

---

## 8. Rute Laporan Keuangan & Audit (`/report`)

| Method | URI | Nama Rute | Keterangan & Aksi |
|--------|-----|-----------|-------------------|
| `GET` | `/report` | `report.index` | Beranda pusat pelaporan |
| `GET` | `/report/distribution` | `report.distribution` | Laporan distribusi mahasiswa (Sudah, Belum, Parsial) |
| `GET` | `/report/distribution-recap` | `report.distribution-recap` | Rekapitulasi persentase penyerahan seragam |
| `GET` | `/report/inventory` | `report.inventory` | Laporan posisi stok barang |
| `GET` | `/report/stock-card` | `report.stock-card` | Kartu stok fisik per item dan varian |
| `GET` | `/report/gpm` | `report.gpm` | Laporan analisis Gross Profit Margin real-time |
| `GET` | `/report/gpm-cost` | `report.gpm-cost` | Analisis komparasi HPP batch vs Harga Jual |
| `GET` | `/report/stock-opname` | `report.stock-opname` | Riwayat selisih dan penyesuaian opname |
| `GET` | `/report/loss` | `report.loss` | Laporan kerugian susut barang rusak/hilang |
| `GET` | `/report/size-recap` | `report.size-recap` | Rekapitulasi pesanan ukuran ke konveksi vendor |

---

## 9. Rute Administrasi Sistem & Super Admin (`/system` & `/admin/users`)

| Method | URI | Nama Rute | Middleware | Keterangan & Aksi |
|--------|-----|-----------|------------|-------------------|
| `GET` | `/system/smtp` | `system.smtp.show` | `role:super_admin` | Konfigurasi server SMTP dari database |
| `PUT` | `/system/smtp` | `system.smtp.store` | `role:super_admin` | Simpan setting SMTP dinamis |
| `POST` | `/system/smtp/test` | `system.smtp.test` | `role:super_admin` | Uji coba kirim email tes |
| `POST` | `/system/smtp/verify` | `system.smtp.verify` | `role:super_admin` | Verifikasi konektivitas soket SMTP |
| `GET` | `/admin/users` | `admin.user.index` | `role:super_admin\|admin` | Manajemen akun staf dan admin |
| `POST` | `/admin/users` | `admin.user.store` | `role:super_admin\|admin` | Daftarkan akun staf baru |
| `PUT` | `/admin/users/{user}/active` | `admin.user.active` | `role:super_admin\|admin` | Toggle status aktif pengguna |
