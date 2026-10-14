# 03 — System Architecture & Components: Horizon UniStock

Arsitektur sistem Horizon UniStock dirancang menggunakan pola **Monolitik Modular Berbasis Layanan (Service-Layer Pattern)** di atas framework Laravel. Pendekatan ini memisahkan secara tegas antara lapisan presentasi (*presentation layer*), lapisan koordinasi HTTP (*controllers*), lapisan logika domain (*domain services*), dan lapisan persistensi data (*data access layer*).

---

## 1. Diagram Arsitektur Lapisan (Layered Architecture)

```
┌────────────────────────────────────────────────────────────────────────┐
│                      PRESENTATION LAYER (Client)                       │
│  - Blade Views (Modular components, partials, dashboards)              │
│  - Tailwind CSS v3 (Utility design system, Horizon Green palette)      │
│  - Alpine.js v3 (Reactive modals, dynamic checklists, scanning state)   │
│  - HTML5-QRCode Scanner Library (Camera access & barcode reader)       │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ HTTP Requests / Form Submissions
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                      TRANSPORT & APPLICATION LAYER                     │
│  - Routes (routes/web.php, routes/auth.php)                            │
│  - Middleware Pipeline (Auth, Spatie Role/Permission, PasswordCheck)   │
│  - Form Requests (Validation rules, authorization logic)               │
│  - Thin Controllers (Auth, Master, Staff, Student, Finance, Inventory)│
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Invokes Domain Operations
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                        DOMAIN SERVICES LAYER                           │
│  - DistributionService     : Business rules, eligibility, FIFO link    │
│  - StockService            : FIFO deduction, batch balances, movements │
│  - StudentSizeService      : Size resolution, options, events, history │
│  - EntitlementService      : Program study quotas & bundle mapping     │
│  - ReportService           : GPM calculation, financial summaries      │
│  - QrCodeService           : BaconQrCode GDLib PNG generation          │
│  - NotificationService     : Credentials emailing, OTP dispatching     │
│  - StockOpnameService      : Variance calculation & adjustment journal │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Atomic Operations (ACID Transactions)
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                       DATA & PERSISTENCE LAYER                         │
│  - 42 Eloquent Models (Rich relationships, scopes, accessors)         │
│  - MySQL 8.0 / InnoDB Engine (Pessimistic row locking: lockForUpdate) │
│  - 58 Migration Schemas with Composite Indexes                         │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ File Storage & Output
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                       EXTERNAL & FILE SERVICES                         │
│  - Local Public Symlink (storage/app/public/qrcodes)                   │
│  - Maatwebsite Excel Engine (Spreadsheet imports/exports)              │
│  - SMTP Mail Server (Dynamic configuration stored in smtp_settings)    │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Rincian Mesin Inventaris FIFO (FIFO Inventory Engine)

Salah satu keunggulan teknis UniStock adalah pelacakan Harga Pokok Penjualan (HPP) secara nyata menggunakan metode **First-In, First-Out (FIFO)**.

### Mekanisme Pemotongan Batch (`StockService::deductStockFifo`)
1. Ketika staf loket memvalidasi serah terima seragam sebanyak kuantitas $Q$, sistem mengkueri tabel `stock_batches` untuk varian item tersebut:
   ```sql
   SELECT * FROM stock_batches
   WHERE item_id = :item_id AND variant_id = :variant_id AND quantity_remaining > 0
   ORDER BY received_date ASC, id ASC
   FOR UPDATE;
   ```
2. Sistem mengiterasi batch dari yang tertua:
   - Jika batch saat ini memiliki $B_1 \ge Q$, maka sisa batch dikurangi $Q$, dan seluruh unit dihitung menggunakan $HPP_1$.
   - Jika batch saat ini hanya memiliki $B_1 < Q$, maka batch tersebut dihabiskan ($B_1 	o 0$), sisa kebutuhan $Q' = Q - B_1$ dipotong dari batch berikutnya ($B_2$), dan seterusnya.
3. **Perhitungan Blended HPP:**
   $$	ext{Blended HPP} = rac{\sum (q_i 	imes 	ext{unit\_hpp}_i)}{Q}$$
4. Sistem secara bersamaan menulis ke tabel `stock_movements` (tipe `OUT`) dengan mencantumkan ID transaksi distribusi sebagai `reference_id` dan blended HPP tersebut.
5. Saldo di tabel `stock_balances` dikurangi secara instan.

---

## 3. Jalur Pembuatan & Pemindaian QR Code

### 3.1 Pembangkitan Server-Side (`QrCodeService`)
Untuk menjamin kecepatan dan keandalan di lingkungan offline (tanpa koneksi internet publik), UniStock tidak menggunakan API generator QR Code eksternal.
- Sistem menggunakan pustaka `bacon/bacon-qr-code`.
- Engine: `BaconQrCode\Renderer\GDLibRenderer(300, 4, 'png')`.
- Payload: **NIM Mahasiswa Murni** (misal: `20260001`).
- Error Correction Level: **Level H** (mampu dipindai dengan sempurna meskipun permukaan kertas atau layar ponsel tergores/rusak hingga 30%).
- Output: Dikirim langsung ke browser dalam format string Base64 Data URL (`data:image/png;base64,...`), menghilangkan kebutuhan penyimpanan file sementara di disk.

### 3.2 Pemindaian Client-Side (`html5-qrcode`)
- Pada antarmuka staf loket (`distribution/scan.blade.php`), script menginisialisasi kamera melalui library JavaScript `html5-qrcode`.
- Begitu barcode/QR Code terdeteksi, library mengekstrak string NIM dan mengirimkan request AJAX ke rute `/distribution/search`.
- Jika mahasiswa ditemukan dan memiliki jadwal aktif, layar langsung berpindah ke lembar verifikasi barang.

---

## 4. Reaktivitas Frontend dengan Alpine.js

UniStock memanfaatkan **Alpine.js v3** untuk memberikan reaktivitas tinggi tanpa overhead arsitektur SPA yang rumit:
1. **Dynamic Form Filtering pada Jadwal Distribusi:**
   Saat admin memilih Fakultas dan Prodi pada dropdown form jadwal, event Alpine memicu penyaringan baris barang di tabel checklist barang secara instan, menyembunyikan barang yang bukan hak prodi tersebut.
2. **Modal & Dialog Konfirmasi:**
   Pemicuan modal menggunakan event global Alpine `$dispatch('open-modal', 'modal-name')`. Komponen modal mendengarkan event dan mengatur transisi CSS tampilan.
3. **Pencarian Real-Time di Tabel Master Data:**
   Input pencarian di halaman mahasiswa memicu debounce query AJAX yang memperbarui tabel secara asinkron tanpa me-refresh seluruh halaman web.
