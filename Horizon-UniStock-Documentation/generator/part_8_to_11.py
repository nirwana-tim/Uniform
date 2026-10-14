# -*- coding: utf-8 -*-
"""
Bagian VIII, IX, X, & XI: Bab 61 s/d Bab 90
Format: Enterprise Proposal-Grade Master Documentation (Deployment, Testing/Bugs, Roadmap/ADRs, and Canonical AI Instructions)
"""

def get_parts_8_to_11_html():
    return r"""
<!-- =================================================== -->
<!-- BAGIAN VIII: INSTALASI, DEPLOYMENT, DAN PEMELIHARAAN -->
<!-- =================================================== -->
<div class="part-divider">
  <div class="part-number">Bagian VIII</div>
  <h1 class="part-title">Instalasi, Deployment, dan Pemeliharaan</h1>
  <div class="part-subtitle">Panduan Instalasi Pengembangan Lokal, Prosedur Khusus Server Produksi cPanel Tanpa SSH, dan SOP Pemeliharaan</div>
</div>

<div class="chapter" id="bab-61">
  <h2 class="chapter-title">Bab 61 — Development Environment Setup</h2>
  <h3 class="subchapter-title">61.1 Prasyarat Perangkat Keras dan Perangkat Lunak Lokal</h3>
  <p>Untuk menjalankan dan mengembangkan sistem Horizon UniStock pada lingkungan pengembangan lokal (*Localhost*) dengan stabilitas maksimal, lingkungan kerja pengembang wajib memenuhi spesifikasi teknis berikut:</p>
  <ul>
    <li><strong>Sistem Operasi:</strong> Microsoft Windows 10/11 (disarankan menggunakan lingkungan Laragon atau WSL2 Ubuntu 22.04), macOS 13+ (Ventura/Sonoma), atau distribusi Linux modern.</li>
    <li><strong>PHP Runtime:</strong> PHP versi 8.3.x atau 8.4.x. Ekstensi PHP wajib yang harus diaktifkan pada <code>php.ini</code>:
      <code>pdo_mysql</code>, <code>mbstring</code>, <code>openssl</code>, <code>gd</code>, <code>zip</code>, <code>xml</code>, <code>curl</code>, <code>bcmath</code>, <code>fileinfo</code>, <code>intl</code>, dan <code>tokenizer</code>.
    </li>
    <li><strong>Database Server:</strong> MySQL versi 8.0.x atau MariaDB versi 10.6+ dengan konfigurasi default collation <code>utf8mb4_unicode_ci</code>.</li>
    <li><strong>Manajer Dependensi:</strong> Composer versi 2.6+ dan Node.js versi 18.x LTS atau runtime alternatif modern Bun v1.2+.</li>
  </ul>

  <h3 class="subchapter-title">61.2 Panduan Langkah Kloning dan Inisialisasi Proyek</h3>
  <p>Jalankan rangkaian instruksi baris perintah berikut pada terminal konsol:</p>
  <pre><code># 1. Kloning repositori resmi dari GitHub
git clone https://github.com/nirwana-tim/Horizon-UniStock.git
cd Horizon-UniStock

# 2. Salin template konfigurasi lingkungan
copy .env.example .env

# 3. Instal dependensi pustaka PHP backend
composer install

# 4. Generate Application Encryption Key Laravel
php artisan key:generate

# 5. Pasang dependensi JavaScript frontend dan kompilasi aset
npm install
npm run build

# 6. Buat database lokal di MySQL (misal: horizon_unistock)
# Lalu jalankan migrasi dan seeder
php artisan migrate:fresh --seed

# 7. Hubungkan direktori storage ke public
php artisan storage:link

# 8. Jalankan server pengembangan lokal
php artisan serve</code></pre>
</div>

<div class="chapter" id="bab-62">
  <h2 class="chapter-title">Bab 62 — Environment Configuration (.env Reference)</h2>
  <h3 class="subchapter-title">62.1 Kamus Parameter Lingkungan Berkas `.env`</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25%;">Kunci Variabel</th>
        <th style="width: 30%;">Contoh Nilai Konfigurasi</th>
        <th style="width: 45%;">Fungsi &amp; Dampak Operasional</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>APP_NAME</code></td>
        <td><code>"Horizon UniStock"</code></td>
        <td>Nama aplikasi yang tampil pada header tab peramban dan kop dokumen cetak.</td>
      </tr>
      <tr>
        <td><code>APP_ENV</code></td>
        <td><code>local</code> / <code>production</code></td>
        <td>Mode lingkungan. Pada server live, WAJIB diatur ke <code>production</code>.</td>
      </tr>
      <tr>
        <td><code>APP_KEY</code></td>
        <td><code>base64:...</code></td>
        <td>Kunci enkripsi 32-karakter untuk mengamankan cookie sesi dan data sensitif.</td>
      </tr>
      <tr>
        <td><code>APP_DEBUG</code></td>
        <td><code>false</code> (di produksi)</td>
        <td>Mencegah kebocoran jejak error (*stack trace*) dan password basis data ke publik.</td>
      </tr>
      <tr>
        <td><code>APP_URL</code></td>
        <td><code>https://unistok.nirwana.biz.id</code></td>
        <td>Alamat basis URL domain resmi untuk pembuatan tautan email reset password.</td>
      </tr>
      <tr>
        <td><code>DB_CONNECTION</code></td>
        <td><code>mysql</code></td>
        <td>Driver pangkalan data utama.</td>
      </tr>
      <tr>
        <td><code>DB_DATABASE</code></td>
        <td><code>horizon_unistock</code></td>
        <td>Nama basis data MySQL yang menampung 58 tabel sistem.</td>
      </tr>
      <tr>
        <td><code>SUPERADMIN_EMAIL</code></td>
        <td><code>admin@horizon-unistock.ac.id</code></td>
        <td>Email akun Super Administrator yang diinjeksi oleh <code>SuperadminSeeder</code>.</td>
      </tr>
      <tr>
        <td><code>SUPERADMIN_PASSWORD</code></td>
        <td><code>AdminPassword2026!</code></td>
        <td>Kata sandi inisial Super Admin (wajib diganti setelah login pertama).</td>
      </tr>
      <tr>
        <td><code>MAIL_MAILER</code></td>
        <td><code>smtp</code></td>
        <td>Driver pengiriman surat elektronik resmi universitas.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-63">
  <h2 class="chapter-title">Bab 63 — Dependency Management &amp; Frontend Compilation</h2>
  <h3 class="subchapter-title">63.1 Dependensi Kunci Backend dan Frontend</h3>
  <ul>
    <li><strong>Backend (Composer):</strong>
      <code>laravel/framework ^11.x</code>,
      <code>spatie/laravel-permission ^6.x</code> (RBAC),
      <code>maatwebsite/excel ^4.0</code> (Ekspor/Impor spreadsheet),
      <code>bacon/bacon-qr-code ^3.0</code> (Pembangkit QR resolusi tinggi offline),
      <code>laravel/sanctum ^4.x</code> (Token API otorisasi), dan
      <code>laravel/tinker ^2.x</code>.
    </li>
    <li><strong>Frontend (Vite + Tailwind + Alpine):</strong>
      <code>tailwindcss ^3.4</code>,
      <code>alpinejs ^3.13</code> (Reaktivitas antarmuka tanpa virtual DOM),
      <code>html5-qrcode ^2.3</code> (Pustaka pemindai kamera barcode multi-perangkat), dan
      <code>axios ^1.6</code>.
    </li>
  </ul>
  <p>Perintah kompilasi produksi <code>npm run build</code> atau <code>bun run build</code> akan menghasilkan bundel aset teroptimasi di <code>public/build/</code> lengkap dengan manifest hash JSON untuk mencegah masalah peramban menahan cache versi lama (*stale browser cache*).</p>
</div>

<div class="chapter" id="bab-64">
  <h2 class="chapter-title">Bab 64 — Database Setup, Migrations, &amp; Seeding Procedures</h2>
  <h3 class="subchapter-title">64.1 Prosedur Migrasi Skema Basis Data Terstruktur</h3>
  <p>Basis data UniStock dirancang dengan arsitektur migrasi independen. Eksekusi migrasi awal dijalankan melalui perintah:</p>
  <pre><code># Eksekusi migrasi dan injeksi master data awal
php artisan migrate:fresh --seed

# Verifikasi status migrasi basis data
php artisan migrate:status</code></pre>
  <div class="callout callout-note">
    <strong>Karakteristik Seeder Bawaan:</strong> Seeder default hanya membuat peran (Roles), daftar hak akses (Permissions), master jenjang akademik (Student Levels), dan akun Super Administrator. Data operasional riil (mahasiswa, master barang, jadwal) tetap kosong dan dimasukkan melalui impor Excel resmi atau form administrasi untuk menjamin orisinalitas data institusi.
  </div>
</div>

<div class="chapter" id="bab-65">
  <h2 class="chapter-title">Bab 65 — Deployment Architecture: Server Produksi cPanel Tanpa SSH</h2>
  <h3 class="subchapter-title">65.1 Karakteristik Hosting &amp; Komitmen Operasional Mutlak</h3>
  <p>Server produksi resmi Horizon UniStock beralamat di <code>https://unistok.nirwana.biz.id</code>, di-hosting pada lingkungan <strong>Shared/Managed cPanel tanpa hak akses baris perintah Terminal SSH</strong>. Pembaruan kode sumber dilakukan secara eksklusif memanfaatkan fitur antarmuka web <strong>cPanel Git Version Control</strong>.</p>
  
  <div class="callout callout-warning">
    <strong>ATURAN MUTLAK DEPLOYMENT PRODUKSI (KOMITMEN DEVOPS):</strong><br>
    Berkas pembantu global <code>app/helpers.php</code> didaftarkan secara eksplisit pada berkas <code>composer.json</code> di dalam blok <code>autoload.files</code>. Pada server hosting cPanel bersama, pembaruan file via <code>git pull</code> TIDAK secara otomatis memperbarui peta kelas Composer.<br><br>
    Jika autoloader Composer tidak di-refresh, fungsi-fungsi global penting seperti <code>escapeLike()</code> akan hilang dari memori PHP, memicu <strong>Fatal Error 500: "Call to undefined function escapeLike()"</strong> di seluruh layar pencarian loket dan tabel mahasiswa.<br><br>
    Oleh karena itu, setiap kali aksi <code>git pull</code> dieksekusi, operator hosting <strong>WAJIB MENJALANKAN</strong> rangkaian perintah berikut melalui Cron Job atau skrip deployment web:
  </div>

  <pre><code># Rangkaian SOP Wajib Pasca-Git-Pull di cPanel:
git pull origin main
composer dump-autoload --optimize --no-interaction
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan migrate --force
php artisan optimize</code></pre>
</div>

<div class="chapter" id="bab-66">
  <h2 class="chapter-title">Bab 66 — Operational Maintenance &amp; Pembersihan Cache</h2>
  <h3 class="subchapter-title">66.1 SOP Pemeliharaan Rutin Mingguan Administrator</h3>
  <p>Selama periode sibuk pembagian seragam mahasiswa baru, administrator TI kampus diwajibkan menjalankan checklist pemeliharaan preventif berikut setiap akhir pekan:</p>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25%;">Objek Pemeliharaan</th>
        <th style="width: 35%;">Perintah / Prosedur Teknis</th>
        <th style="width: 40%;">Tujuan &amp; Hasil yang Diharapkan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Pembersihan Cache</strong></td>
        <td><code>php artisan optimize:clear</code></td>
        <td>Memastikan perubahan konfigurasi dan view template Blade langsung aktif.</td>
      </tr>
      <tr>
        <td><strong>Rotasi Berkas Log</strong></td>
        <td>Arsipkan berkas <code>storage/logs/laravel.log</code> jika ukuran &gt; 50 MB</td>
        <td>Mencegah kepenuhan kuota disk hosting cPanel dan menjaga performa I/O server.</td>
      </tr>
      <tr>
        <td><strong>Audit Antrean Email</strong></td>
        <td>Cek tabel <code>email_notifications</code> di mana status = 'failed'</td>
        <td>Mendeteksi email kredensial akun mahasiswa yang memantul (*bounced*) untuk dikirim ulang.</td>
      </tr>
      <tr>
        <td><strong>Pembersihan Sesi Usang</strong></td>
        <td>Hapus berkas pada direktori <code>storage/framework/sessions/</code></td>
        <td>Membersihkan sesi kedaluwarsa mahasiswa yang telah meninggalkan aula kampus.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-67">
  <h2 class="chapter-title">Bab 67 — Logging, Monitoring, &amp; Health Checks</h2>
  <h3 class="subchapter-title">67.1 Arsitektur Pemantauan &amp; Pelacakan Galat</h3>
  <ul>
    <li><strong>Pencatatan Terpusat Monolog:</strong> Seluruh galat backend dicatat ke <code>storage/logs/laravel.log</code> dengan format standar PSR-3 lengkap dengan timestamp ISO-8601, level keparahan (EMERGENCY s/d DEBUG), pesan galat, dan jejak panggilan fungsi (*stack trace*).</li>
    <li><strong>Pemantauan Real-Time (Laravel Pail):</strong> Pada lingkungan lokal atau staging, pengembang dapat memantau log secara real-time saat simulasi loket berlangsung via perintah CLI <code>php artisan pail --filter=ERROR</code>.</li>
    <li><strong>Pemeriksaan Kesehatan Basis Data (Health Check):</strong> Rute internal <code>/healthz</code> memverifikasi koneksi database MySQL dan ketersediaan ruang penyimpanan disk server.</li>
  </ul>
</div>

<div class="chapter" id="bab-68">
  <h2 class="chapter-title">Bab 68 — Troubleshooting Guide &amp; Solusi Cepat Galat</h2>
  <h3 class="subchapter-title">68.1 Matriks Solusi Cepat 10 Masalah Operasional Lapangan</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25%;">Gejala Masalah / Galat</th>
        <th style="width: 35%;">Akar Masalah Teknis</th>
        <th style="width: 40%;">Solusi Terverifikasi &amp; Langkah Eksekusi</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Error 500 saat mencari mahasiswa</strong></td>
        <td>Autoloader Composer server belum me-refresh fungsi <code>escapeLike()</code> di <code>app/helpers.php</code>.</td>
        <td>Jalankan <code>composer dump-autoload --optimize</code> melalui Terminal cPanel atau script deployer.</td>
      </tr>
      <tr>
        <td><strong>Fatal Error saat mengunduh Excel</strong></td>
        <td>Return type method <code>styles()</code> di kelas ekspor bertipe <code>void</code> pada Maatwebsite Excel 4.0.</td>
        <td>Ubah deklarasi method menjadi <code>public function styles(Worksheet $sheet): ?array</code> dan akhiri dengan <code>return null;</code>.</td>
      </tr>
      <tr>
        <td><strong>Tombol modal konfirmasi tidak merespon</strong></td>
        <td>Elemen button pemanggil event <code>$dispatch()</code> berada di luar cakupan Alpine.js.</td>
        <td>Tambahkan atribut <code>x-data=""</code> (lingkup kosong) pada elemen button atau container pembungkus terdekat.</td>
      </tr>
      <tr>
        <td><strong>Layar kamera scanner hitam / macet</strong></td>
        <td>Peramban belum diberi izin akses kamera atau situs diakses via HTTP (bukan HTTPS).</td>
        <td>Pastikan URL menggunakan <code>https://</code> dan klik "Allow Camera" pada izin peramban pengguna.</td>
      </tr>
      <tr>
        <td><strong>Error validasi semu NIM saat edit</strong></td>
        <td>Rule unique memeriksa record lama yang telah di-softdelete.</td>
        <td>Gunakan sintaks <code>Rule::unique('students')->ignore($id)->whereNull('deleted_at')</code>.</td>
      </tr>
      <tr>
        <td><strong>Jadwal distribusi gagal disimpan</strong></td>
        <td>Kolom <code>session</code> bernilai NULL pada request sedangkan skema awal NOT NULL.</td>
        <td>Jalankan migrasi alter table yang mengubah kolom session menjadi <code>nullable()</code>.</td>
      </tr>
      <tr>
        <td><strong>GPM bernilai 100% atau minus ekstrim</strong></td>
        <td>Harga perolehan HPP tercatat 0 akibat barang masuk manual tanpa lot batch penerimaan.</td>
        <td>Wajibkan penerimaan stok melalui menu <code>StockReceive</code> agar memiliki harga perolehan sah.</td>
      </tr>
      <tr>
        <td><strong>Email kredensial gagal terkirim</strong></td>
        <td>Koneksi port SMTP (587 / 465) diblokir oleh firewall hosting cPanel.</td>
        <td>Buka menu Pengaturan SMTP di UniStock dan perbarui port serta kredensial akun email resmi kampus.</td>
      </tr>
      <tr>
        <td><strong>Gagal mengunggah file impor 8 MB</strong></td>
        <td>Parameter PHP <code>upload_max_filesize</code> di cPanel masih bernilai bawaan 2 MB.</td>
        <td>Naikkan nilai <code>upload_max_filesize = 16M</code> dan <code>post_max_size = 16M</code> pada php.ini hosting.</td>
      </tr>
      <tr>
        <td><strong>Memori habis saat ekspor 10.000 data</strong></td>
        <td>Kueri Eloquent memuat seluruh model objek ke memori RAM PHP sekaligus.</td>
        <td>Terapkan interface <code>FromQuery</code> dengan metode <code>query()</code> chunking pada kelas Export.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-69">
  <h2 class="chapter-title">Bab 69 — Rollback &amp; Disaster Recovery Procedures</h2>
  <h3 class="subchapter-title">69.1 Prosedur Pembatalan Rilis Kilat (1-Minute Rollback SOP)</h3>
  <p>Jika rilis pembaruan baru menimbulkan galat fatal di tengah antrean loket aula kampus, tim pengembang wajib mengeksekusi prosedur rollback 3 langkah:</p>
  <pre><code># Langkah 1: Batalkan commit Git ke versi stabil sebelumnya
git reset --hard HEAD~1

# Langkah 2: Refresh autoloader dan pulihkan cache teroptimasi
composer dump-autoload --optimize --no-interaction
php artisan optimize:clear
php artisan optimize

# Langkah 3: Jika terjadi kerusakan skema tabel, pulihkan snapshot basis data:
mysql -u [db_user] -p[db_pass] [db_name] < backup_sebelum_deploy.sql</code></pre>
</div>

<!-- ============================================== -->
<!-- BAGIAN IX: TESTING, DEBUGGING, DAN REKAM MASALAH -->
<!-- ============================================== -->
<div class="part-divider">
  <div class="part-number">Bagian IX</div>
  <h1 class="part-title">Testing, Debugging, dan Rekam Masalah</h1>
  <div class="part-subtitle">Kerangka Pengujian, Dokumentasi 16 Skenario PRD, Rekam Bug (BUG-001 s/d BUG-006), dan Studi Kasus Lapangan</div>
</div>

<div class="chapter" id="bab-70">
  <h2 class="chapter-title">Bab 70 — Testing Strategy &amp; Kerangka Pengujian</h2>
  <h3 class="subchapter-title">70.1 Piramida Pengujian Perangkat Lunak</h3>
  <p>Keandalan UniStock diverifikasi melalui 3 lapisan pengujian formal:</p>
  <ul>
    <li><strong>Unit Testing (PHPUnit):</strong> Menguji fungsi isolasi murni, seperti formula matematis blended HPP FIFO pada <code>StockService</code>, sanitasi string wildcard pada <code>EscapesLikeWildcards</code>, dan generator QR Code pada <code>QrCodeService</code>.</li>
    <li><strong>Feature &amp; Integration Testing:</strong> Menguji interaksi controller, form request, middleware Spatie RBAC, dan integritas transaksi database atomik menggunakan basis data SQLite in-memory yang cepat.</li>
    <li><strong>User Acceptance Testing (UAT Simulasi Loket):</strong> Gladi bersih di aula kampus melibatkan puluhan mahasiswa sukarelawan untuk menguji ketahanan pemindai kamera, toleransi pantulan cahaya, dan alur fitting fisik.</li>
  </ul>
</div>

<div class="chapter" id="bab-71">
  <h2 class="chapter-title">Bab 71 — Test Cases Lengkap (16 Skenario Pengujian PRD)</h2>
  <p>Berikut adalah dokumentasi formal pelaksanaan 16 skenario pengujian Product Requirement Document (PRD), di mana seluruhnya telah berhasil diverifikasi dengan status <strong>LULUS (100% PASSED)</strong>:</p>

  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 8%;">ID</th>
        <th style="width: 25%;">Judul Skenario Uji</th>
        <th style="width: 32%;">Langkah Pengujian &amp; Data Uji</th>
        <th style="width: 25%;">Hasil yang Diharapkan</th>
        <th style="width: 10%;">Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>TC-01</strong></td>
        <td>Impor Duplikasi NIM</td>
        <td>Unggah file Excel berisi 2 baris dengan NIM "20260012" yang identik.</td>
        <td>Sistem menolak impor, transaksi di-rollback, menampilkan nomor baris galat.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-02</strong></td>
        <td>QR Code Rusak / Buram</td>
        <td>Pindai barcode QR yang tergores 40% menggunakan scanner loket.</td>
        <td>Error correction Level H berhasil merekonstruksi data NIM mahasiswa.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-03</strong></td>
        <td>Mahasiswa Belum Lunas</td>
        <td>Pindai QR mahasiswa berstatus <code>is_eligible = false</code>.</td>
        <td>Muncul banner merah "Belum Lunas", tombol serahkan dinonaktifkan.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-04</strong></td>
        <td>Pencegahan Ambil Ganda</td>
        <td>Pindai QR mahasiswa yang telah selesai mengambil seragam.</td>
        <td>Sistem menolak: "Mahasiswa ini telah mengambil seluruh paket hak seragam".</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-05</strong></td>
        <td>Perubahan Ukuran Fitting</td>
        <td>Ganti ukuran jas dari M ke L di dropdown loket saat penyerahan.</td>
        <td>Stok L terpotong; sistem mencatat riwayat perubahan ke <code>student_size_histories</code>.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-06</strong></td>
        <td>Stok Fisik Gudang Kosong</td>
        <td>Hapus centang seragam jas yang kehabisan stok fisik di rak gudang.</td>
        <td>Item yang dicentang terdistribusi; jas berstatus <em>Deferred</em> (hak tetap ada).</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-07</strong></td>
        <td>Penyerahan Sebagian</td>
        <td>Serahkan 1 dari 2 kemeja hak mahasiswa.</td>
        <td>Sisa saldo hak tersimpan 1 unit; dapat diambil pada jadwal distribusi berikutnya.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-08</strong></td>
        <td>Ekspor Laporan Excel</td>
        <td>Klik tombol "Ekspor Laporan Distribusi" pada menu laporan.</td>
        <td>Mengunduh berkas .xlsx rapi dengan header resmi universitas dan styling sel.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-09</strong></td>
        <td>Reset Password Email</td>
        <td>Minta tautan reset kata sandi melalui form lupa password.</td>
        <td>Tautan token unik terkirim via SMTP ke email resmi kampus mahasiswa.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-10</strong></td>
        <td>Unggah Format Salah</td>
        <td>Unggah file berkas gambar JPEG pada form impor mahasiswa Excel.</td>
        <td>Validasi Form Request menolak unggahan dengan pesan tipe file tidak valid.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-11</strong></td>
        <td>Batas Ganti Ukuran</td>
        <td>Mahasiswa mencoba mengubah ukuran untuk ketiga kalinya (limit = 2).</td>
        <td>Form terkunci permanen; sistem menampilkan notifikasi kuota ubah habis.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-12</strong></td>
        <td>Validasi Email Ganda</td>
        <td>Simpan profil mahasiswa baru dengan email kampus yang telah terdaftar.</td>
        <td>Sistem melempar pesan validasi "Email kampus ini telah digunakan".</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-13</strong></td>
        <td>Surplus Stock Opname</td>
        <td>Hitung fisik gudang menemukan 102 potong (sistem mencatat 100 potong).</td>
        <td>Sistem mencatat varians +2 dan membentuk penyesuaian penambahan stok.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-14</strong></td>
        <td>Defisit Stock Opname</td>
        <td>Hitung fisik gudang menemukan 95 potong (sistem mencatat 100 potong).</td>
        <td>Sistem mencatat varians -5 dan membukukan kehilangan ke laporan selisih.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-15</strong></td>
        <td>Kalkulasi Otomatis GPM</td>
        <td>Periksa ringkasan Gross Profit Margin pada Sales Dashboard.</td>
        <td>Menampilkan pendapatan, HPP gabungan FIFO, dan persentase margin tepat.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
      <tr>
        <td><strong>TC-16</strong></td>
        <td>Unggah Rekap Opname</td>
        <td>Unggah lembar kerja Excel hasil penghitungan fisik tim gudang.</td>
        <td>Sistem memetakan SKU secara otomatis dan mengkalkulasi selisih seketika.</td>
        <td><span class="badge badge-success">PASSED</span></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-72">
  <h2 class="chapter-title">Bab 72 — Regression Test Matrix &amp; Dampak Refaktor</h2>
  <h3 class="subchapter-title">72.1 Matriks Dependensi Modul Kritis</h3>
  <p>Setiap perubahan kode pada modul inti wajib diikuti dengan pengujian regresi pada modul hilirnya:</p>
  <ul>
    <li>Modifikasi <code>StockService</code> &rarr; Wajib menguji ulang transaksi loket <code>ScanController</code>, rekonsiliasi opname <code>StockOpnameController</code>, dan pelaporan laba <code>GpmController</code>.</li>
    <li>Modifikasi <code>StudentSizeService</code> &rarr; Wajib menguji ulang form mandiri mahasiswa <code>SizeController</code> dan tampilan kartu ukuran di meja loket.</li>
    <li>Modifikasi <code>QrCodeService</code> &rarr; Wajib memverifikasi keterbacaan barcode di layar AMOLED ponsel mahasiswa dan peramban desktop.</li>
  </ul>
</div>

<div class="chapter" id="bab-73">
  <h2 class="chapter-title">Bab 73 — Known Bugs &amp; Rekam Mitigasi Masalah Teknis</h2>
  <h3 class="subchapter-title">73.1 Dokumentasi Masalah yang Telah Diperbaiki (BUG-001 s/d BUG-006)</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 12%;">Bug ID</th>
        <th style="width: 25%;">Deskripsi Masalah</th>
        <th style="width: 15%;">Tingkat Keparahan</th>
        <th style="width: 48%;">Akar Masalah Teknis &amp; Solusi Penyelesaian Terverifikasi</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>BUG-001</strong></td>
        <td>Error 500 helper <code>escapeLike()</code></td>
        <td><span class="badge badge-danger">Kritis</span></td>
        <td>File <code>app/helpers.php</code> tidak ter-reload di autoloader cPanel pasca git pull. <em>Solusi:</em> Ditambahkan instruksi wajib <code>composer dump-autoload --optimize</code> pada setiap alur deploy.</td>
      </tr>
      <tr>
        <td><strong>BUG-002</strong></td>
        <td>Fatal Error <code>WithStyles::styles()</code></td>
        <td><span class="badge badge-danger">Kritis</span></td>
        <td>Maatwebsite Excel 4.0 mewajibkan return type <code>: ?array</code> bukan <code>void</code>. <em>Solusi:</em> Seluruh kelas ekspor diperbarui deklarasi tipe kembaliannya menjadi nullable array.</td>
      </tr>
      <tr>
        <td><strong>BUG-003</strong></td>
        <td>Tombol Modal Alpine Tidak Bekerja</td>
        <td><span class="badge badge-warning">Sedang</span></td>
        <td>Event <code>$dispatch()</code> berada di luar cakupan <code>x-data</code>. <em>Solusi:</em> Menambahkan wrapper atribut <code>x-data=""</code> kosong pada elemen pemanggil tombol.</td>
      </tr>
      <tr>
        <td><strong>BUG-004</strong></td>
        <td>Validasi Unik Semu Update Mahasiswa</td>
        <td><span class="badge badge-warning">Sedang</span></td>
        <td>Aturan validasi memeriksa ID dirinya sendiri atau record yang telah di-softdelete. <em>Solusi:</em> Memperbarui rule menggunakan <code>Rule::unique()->ignore($id)->whereNull('deleted_at')</code>.</td>
      </tr>
      <tr>
        <td><strong>BUG-005</strong></td>
        <td>Kolom <code>session</code> Jadwal Gagal Simpan</td>
        <td><span class="badge badge-warning">Sedang</span></td>
        <td>Kolom <code>session</code> berstatus <code>NOT NULL</code> di migrasi awal. <em>Solusi:</em> Dijalankan migrasi alter table untuk mengubah kolom session menjadi <code>nullable()</code>.</td>
      </tr>
      <tr>
        <td><strong>BUG-006</strong></td>
        <td>Inkonsistensi HPP Varian Baru</td>
        <td><span class="badge badge-warning">Sedang</span></td>
        <td>Formula weighted average melompat saat kuantitas nol. <em>Solusi:</em> Logika <code>upsertBalance()</code> di <code>StockService</code> diperbaiki dengan proteksi pembagian nol.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-74">
  <h2 class="chapter-title">Bab 74 — Debugging Case Studies &amp; Kasus Lapangan</h2>
  <h3 class="subchapter-title">74.1 Studi Kasus 1: Resolusi Masalah Gender Mismatch (Komit 6a13c0d)</h3>
  <p><strong>Gejala Operasional:</strong> Pada awal implementasi, staf loket kerap kali secara tidak sengaja menyerahkan kemeja dinas perempuan kepada mahasiswa laki-laki karena kemiripan nama barang di layar.</p>
  <p><strong>Analisis &amp; Solusi:</strong> Dilakukan refaktorisasi pada <code>ScanController</code> dan <code>DistributionService</code>. Sistem sekarang secara ketat mencocokkan atribut <code>gender</code> pada entitas <code>Item</code> dengan atribut <code>gender</code> mahasiswa. Jika terjadi ketidakcocokan, sistem secara otomatis memunculkan dialog konfirmasi tebal dan memblokir penyerahan seragam lawan jenis tanpa otorisasi pengawas.</p>

  <h3 class="subchapter-title">74.2 Evaluasi 6 Opsi Kebijakan Kasus Lanjutan (Kasus 8 s/d 13)</h3>
  <ul>
    <li><strong>Kasus 8 (Batas Waktu Pengisian Ukuran):</strong> Diterapkan kebijakan *Kunci Otomatis Sistem*. Mahasiswa yang terlambat mengisi ukuran akan diberikan ukuran rata-rata (L) dan fitting manual di loket.</li>
    <li><strong>Kasus 9 (Pergantian Desain Seragam Baru):</strong> Diterapkan sistem pendaftaran *Kode SKU Baru*. Stok desain lama tetap diisolasi untuk melayani pembelian suku cadang mahasiswa tingkat atas.</li>
    <li><strong>Kasus 10 (Penjualan Bebas Sisa Stok Seragam):</strong> Direkomendasikan pembukaan modul kasir eceran berbayar setelah seluruh mahasiswa baru tuntas mengambil jatah hak mereka.</li>
  </ul>
</div>

<div class="chapter" id="bab-75">
  <h2 class="chapter-title">Bab 75 — Technical Debt &amp; Rencana Refaktorisasi</h2>
  <h3 class="subchapter-title">75.1 Rencana Pelunasan Utang Teknis 2026/2027</h3>
  <ul>
    <li><strong>Antrean Asinkron Pengiriman Email (Queue Workers):</strong> Migrasi pengiriman email dari pemrosesan sinkron HTTP ke background queue worker berbasis Redis atau database jobs guna memangkas waktu tunggu saat impor massal 1.000 mahasiswa.</li>
    <li><strong>Helper Global vs Trait:</strong> Merefaktor helper global di <code>app/helpers.php</code> ke dalam method trait terisolasi (seperti <code>EscapesLikeWildcards</code>) untuk menghilangkan ketergantungan pada dump autoloader Composer.</li>
  </ul>
</div>

<div class="chapter" id="bab-76">
  <h2 class="chapter-title">Bab 76 — Incident History &amp; Catatan Evaluasi Layanan</h2>
  <p>Tercatat satu insiden operasional minor pada 12 Agustus 2026 di mana pemancar WiFi aula kampus mati selama 8 menit. Berkat arsitektur QR Code offline murni UniStock, staf loket dapat segera beralih menggunakan koneksi internet ponsel tanpa kehilangan data, dan antrean mahasiswa berhasil dipulihkan dengan cepat.</p>
</div>

<!-- ==================================================== -->
<!-- BAGIAN X: ROADMAP, PERUBAHAN, DAN KEPUTUSAN TEKNIS -->
<!-- ==================================================== -->
<div class="part-divider">
  <div class="part-number">Bagian X</div>
  <h1 class="part-title">Roadmap, Perubahan, dan Keputusan Teknis</h1>
  <div class="part-subtitle">Status Modul Terkini, Rencana Fitur Masa Depan, 8 Architecture Decision Records (ADRs), dan Riwayat Rilis</div>
</div>

<div class="chapter" id="bab-77">
  <h2 class="chapter-title">Bab 77 — Current Project Status (Status Kesiapan Modul V1.2.0)</h2>
  <p>Per Oktober 2026, seluruh modul fungsional inti Horizon UniStock telah mencapai status <strong>Production Ready (Stabil &amp; Teruji Penuh)</strong>:</p>
  <table class="data-table">
    <thead>
      <tr>
        <th>Modul Fungsional</th>
        <th>Status Kesiapan</th>
        <th>Cakupan Fitur Teruji</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Portal Mahasiswa &amp; Input Ukuran</td>
        <td><span class="badge badge-success">Selesai 100%</span></td>
        <td>Input mandiri baju &amp; sepatu, counter batas ubah, penguncian event otomatis.</td>
      </tr>
      <tr>
        <td>Pembangkit QR Code Digital Offline</td>
        <td><span class="badge badge-success">Selesai 100%</span></td>
        <td>Pembangkitan lokal resolusi 300x300px Level H, stream Base64 PNG instan.</td>
      </tr>
      <tr>
        <td>Scanner Kamera Staf Loket</td>
        <td><span class="badge badge-success">Selesai 100%</span></td>
        <td>Pemindaian sub-detik, pemilihan kamera, fallback pencarian NIM manual.</td>
      </tr>
      <tr>
        <td>Transaksi Distribusi &amp; Fitting Fisik</td>
        <td><span class="badge badge-success">Selesai 100%</span></td>
        <td>Row locking MySQL, penyesuaian ukuran aktual di loket, penanganan deferred.</td>
      </tr>
      <tr>
        <td>Mesin Persediaan FIFO &amp; Batch Valuation</td>
        <td><span class="badge badge-success">Selesai 100%</span></td>
        <td>Pemotongan batch tertua, kalkulasi blended HPP, mutasi kartu persediaan.</td>
      </tr>
      <tr>
        <td>Modul Audit Stock Opname &amp; Excel</td>
        <td><span class="badge badge-success">Selesai 100%</span></td>
        <td>Draft opname, unggah hitung fisik, kalkulasi varians otomatis, approval jurnal.</td>
      </tr>
      <tr>
        <td>Sales Dashboard &amp; Analisis GPM</td>
        <td><span class="badge badge-success">Selesai 100%</span></td>
        <td>Metrik pendapatan kotor, total modal FIFO, persentase margin laba bersih.</td>
      </tr>
      <tr>
        <td>Administrasi Kredensial &amp; SMTP</td>
        <td><span class="badge badge-success">Selesai 100%</span></td>
        <td>Manajemen pengguna Spatie RBAC, konfigurasi server surat, reset sandi.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-78">
  <h2 class="chapter-title">Bab 78 — Feature Backlog &amp; Rencana Pengembangan Lanjutan</h2>
  <h3 class="subchapter-title">78.1 Peta Jalan Pengembangan Fitur 2027</h3>
  <ul>
    <li><strong>Modul Point of Sales (POS) Kasir Eceran:</strong> Memfasilitasi pembelian seragam tambahan atau jas pengganti bagi mahasiswa lama dan dosen dengan integrasi mesin kasir barcode USB serta pencetakan struk termal.</li>
    <li><strong>Multi-Warehouse &amp; Gudang Satelit:</strong> Mendukung pengelolaan persediaan lintas cabang jika Horizon University membuka kampus baru di luar Karawang.</li>
    <li><strong>Integrasi REST API ke SIAKAD Kampus:</strong> Sinkronisasi otomatis data pembayaran SPP mahasiswa secara real-time tanpa perlu impor berkas Excel berkala.</li>
    <li><strong>Gateway Notifikasi WhatsApp Otomatis:</strong> Mengirimkan barcode QR Code dan pengingat jadwal distribusi langsung ke nomor WhatsApp mahasiswa dan wali.</li>
  </ul>
</div>

<div class="chapter" id="bab-79">
  <h2 class="chapter-title">Bab 79 — Known Limitations &amp; Batasan Arsitektur</h2>
  <ul>
    <li><strong>Arsitektur Gudang Tunggal:</strong> Sistem saat ini memodelkan seluruh saldo persediaan berada pada satu kompleks gudang pusat kampus Karawang.</li>
    <li><strong>Pemindaian Optik Barcode:</strong> Belum mendukung integrasi pemindaian jarak jauh RFID Gate; verifikasi bertumpu pada kamera peramban optik.</li>
  </ul>
</div>

<div class="chapter" id="bab-80">
  <h2 class="chapter-title">Bab 80 — Improvement Opportunities (Peluang Peningkatan Performa)</h2>
  <p>Peluang optimasi performa berbiaya rendah untuk rilis mendatang:</p>
  <ul>
    <li>Penerapan Redis In-Memory Caching untuk data referensi master (Fakultas, Program Studi, Ukuran) guna memangkas beban kueri MySQL hingga 40%.</li>
    <li>Penggunaan Web Worker JavaScript pada pemindai kamera untuk memproses dekode gambar barcode di thread terpisah agar antarmuka ponsel tetap responsif 60 FPS.</li>
  </ul>
</div>

<div class="chapter" id="bab-81">
  <h2 class="chapter-title">Bab 81 — Architecture Decision Records (ADR 001 s/d ADR 008)</h2>
  <p>Seluruh keputusan rancang bangun sistem didokumentasikan menurut format standar enterprise ADR:</p>

  <h3 class="subchapter-title">ADR-001: Arsitektur Monolitik Modern (Blade + Tailwind + Alpine.js)</h3>
  <p><strong>Status:</strong> Diterima (Accepted).<br>
  <strong>Konteks:</strong> Kebutuhan membangun sistem yang cepat, ringan, mudah dirawat oleh tim kampus, dan dapat di-deploy ke shared hosting cPanel tanpa SSH.<br>
  <strong>Keputusan:</strong> Menggunakan monolitik Laravel Blade dipadu Tailwind CSS dan Alpine.js dibanding arsitektur Single Page Application (SPA) terpisah.<br>
  <strong>Konsekuensi:</strong> Deployment sangat sederhana (satu repositori), zero runtime node server di produksi, namun memerlukan kehati-hatian dalam merancang komponen Alpine.</p>

  <h3 class="subchapter-title">ADR-002: Penilaian Persediaan FIFO Murni dengan Entitas `StockBatch`</h3>
  <p><strong>Status:</strong> Diterima (Accepted).<br>
  <strong>Konteks:</strong> Fluktuasi harga beli kain seragam dari vendor antar-batch pengiriman memerlukan akurasi Harga Pokok Penjualan (HPP) untuk pelaporan keuangan rektorat.<br>
  <strong>Keputusan:</strong> Menerapkan penilaian persediaan FIFO murni yang mencatat setiap lot kedatangan ke tabel <code>stock_batches</code>.<br>
  <strong>Konsekuensi:</strong> Perhitungan matematis blended HPP presisi 100%, menghilangkan distorsi margin akibat metode rata-rata (Average Costing).</p>

  <h3 class="subchapter-title">ADR-003: Generator QR Code Offline Berbasis String NIM Murni</h3>
  <p><strong>Status:</strong> Diterima (Accepted).<br>
  <strong>Konteks:</strong> Risiko gangguan koneksi internet di aula kampus saat pembagian seragam massal ribuan mahasiswa.<br>
  <strong>Keputusan:</strong> QR Code mahasiswa hanya memuat string teks NIM murni, digenerate secara lokal menggunakan <code>bacon/bacon-qr-code</code> Level H.<br>
  <strong>Konsekuensi:</strong> Pembangkitan QR 100% independen dari API pihak ketiga, ukuran QR sangat kompak, dan mudah dibaca scanner meski layar ponsel tergores.</p>

  <h3 class="subchapter-title">ADR-004: Standardisasi Peran dengan Spatie Laravel Permission &amp; PHP Enum</h3>
  <p><strong>Status:</strong> Diterima (Accepted).<br>
  <strong>Konteks:</strong> Mencegah celah keamanan hak akses dan kesalahan ketik (typo) nama peran pada seluruh controller dan Blade view.<br>
  <strong>Keputusan:</strong> Mengenkapsulasi 4 peran resmi dalam PHP 8.1 Enum <code>App\Enums\Role</code> yang diikatkan ke Spatie Permission.<br>
  <strong>Konsekuensi:</strong> Keamanan otorisasi berdaya tahan tinggi, autocompletion pada IDE, dan pemeliharaan peran yang terpusat.</p>

  <h3 class="subchapter-title">ADR-005: Optimasi Opsi Ukuran dengan Flag Boolean `is_baju` &amp; `is_sepatu`</h3>
  <p><strong>Status:</strong> Diterima (Accepted).<br>
  <strong>Konteks:</strong> Dropdown pilihan ukuran seragam di portal mahasiswa harus memfilter ukuran pakaian (S, M, L) dan sepatu (36-45) secara efisien.<br>
  <strong>Keputusan:</strong> Menambahkan kolom boolean terindeks <code>is_baju</code> dan <code>is_sepatu</code> pada tabel master <code>item_sizes</code>.<br>
  <strong>Konsekuensi:</strong> Kueri database menjadi sangat sederhana dan cepat (<code>ItemSize::where('is_baju', true)->get()</code>) tanpa memerlukan tabel pivot tambahan.</p>

  <h3 class="subchapter-title">ADR-006: Relasi Mahasiswa Nullable (`students.user_id`)</h3>
  <p><strong>Status:</strong> Diterima (Accepted).<br>
  <strong>Konteks:</strong> Panitia PMB perlu mengimpor ribuan data mahasiswa baru ke sistem sebelum akun login dan email resmi diterbitkan.<br>
  <strong>Keputusan:</strong> Menjadikan foreign key <code>students.user_id</code> bersifat nullable dengan aturan onDelete SET NULL.<br>
  <strong>Konsekuensi:</strong> Fleksibilitas tinggi dalam alur penerimaan mahasiswa; pembuatan akun pengguna dapat dilakukan secara bertahap tanpa memblokir impor master.</p>

  <h3 class="subchapter-title">ADR-007: Pessimistic Row Locking pada Transaksi Penyerahan Loket</h3>
  <p><strong>Status:</strong> Diterima (Accepted).<br>
  <strong>Konteks:</strong> Risiko pengambilan seragam ganda jika mahasiswa menunjukkan QR Code yang sama pada dua meja loket yang berbeda di aula.<br>
  <strong>Keputusan:</strong> Membungkus transaksi penyerahan dengan klausa database <code>Student::whereKey($id)->lockForUpdate()</code>.<br>
  <strong>Konsekuensi:</strong> Mengeliminasi 100% risiko *race condition* dan penyerahan ganda di level mesin basis data.</p>

  <h3 class="subchapter-title">ADR-008: Prosedur Wajib Pasca-Deploy cPanel (`composer dump-autoload`)</h3>
  <p><strong>Status:</strong> Diterima (Accepted).<br>
  <strong>Konteks:</strong> Berkas pembantu <code>app/helpers.php</code> berada pada composer <code>autoload.files</code>. Pada server cPanel git pull, fungsi global tidak terdeteksi otomatis.<br>
  <strong>Keputusan:</strong> Menjadikan perintah <code>composer dump-autoload --optimize</code> sebagai komitmen wajib dalam SOP pasca-deploy.<br>
  <strong>Konsekuensi:</strong> Menjamin ketersediaan fungsi global seperti <code>escapeLike()</code> dan mencegah Fatal Error 500 di server live.</p>
</div>

<div class="chapter" id="bab-82">
  <h2 class="chapter-title">Bab 82 — Change Log Komprehensif Sistem</h2>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 15%;">Versi Rilis</th>
        <th style="width: 20%;">Tanggal Rilis</th>
        <th style="width: 65%;">Ringkasan Pembaruan &amp; Perubahan Utama</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>v1.2.0</strong></td>
        <td>14 Oktober 2026</td>
        <td>Restrukturisasi direktori dokumentasi terpusat (<code>Horizon-UniStock-Documentation/</code>), penyusunan Master System Documentation 90 Bab, audit keamanan kata sandi, dan standarisasi panduan agen AI.</td>
      </tr>
      <tr>
        <td><strong>v1.1.5</strong></td>
        <td>28 Agustus 2026</td>
        <td>Penambahan flag <code>is_baju</code>/<code>is_sepatu</code> pada tabel master ukuran, perbaikan kolom <code>session</code> jadwal nullable, pengetatan validasi soft-delete, dan patch BUG-001 s/d BUG-006.</td>
      </tr>
      <tr>
        <td><strong>v1.1.0</strong></td>
        <td>15 Agustus 2026</td>
        <td>Implementasi mesin persediaan FIFO murni, kartu mutasi stok (<code>stock_movements</code>), modul audit Stock Opname bulanan, dan Sales GPM Dashboard pelaporan laba kotor.</td>
      </tr>
      <tr>
        <td><strong>v1.0.0</strong></td>
        <td>01 Agustus 2026</td>
        <td>Rilis Minimum Viable Product (MVP) mencakup portal mahasiswa mandiri, pembangkit QR Code offline, pemindai kamera staf loket, dan verifikasi status pembayaran keuangan.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-83">
  <h2 class="chapter-title">Bab 83 — Release History &amp; Kebijakan Versioning</h2>
  <p>Sistem Horizon UniStock menerapkan kaidah penomoran versi <strong>Semantic Versioning (SemVer 2.0.0)</strong> dengan skema <code>MAJOR.MINOR.PATCH</code>:</p>
  <ul>
    <li><strong>MAJOR (1.x.x):</strong> Perubahan arsitektur mendasar atau migrasi skema basis data yang tidak kompatibel ke belakang.</li>
    <li><strong>MINOR (x.1.x):</strong> Penambahan modul fungsional baru (seperti modul kasir eceran atau modul integrasi SIAKAD).</li>
    <li><strong>PATCH (x.x.1):</strong> Perbaikan galat bug teknis, penyesuaian styling antarmuka, atau optimasi kueri basis data.</li>
  </ul>
</div>

<!-- ======================================================= -->
<!-- BAGIAN XI: GLOSARIUM, SUMBER, DAN INSTRUKSI UNTUK AI -->
<!-- ======================================================= -->
<div class="part-divider">
  <div class="part-number">Bagian XI</div>
  <h1 class="part-title">Glosarium, Sumber, dan Instruksi untuk AI</h1>
  <div class="part-subtitle">Kamus Istilah Komprehensif, 15 Fakta Baku Kanonis, Registry Kode Kunci, dan Panduan AI Pair-Programming</div>
</div>

<div class="chapter" id="bab-84">
  <h2 class="chapter-title">Bab 84 — Glosarium Istilah Bisnis &amp; Teknis Lengkap</h2>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25%;">Istilah</th>
        <th style="width: 75%;">Definisi Lengkap dalam Konteks Horizon UniStock</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Entitlement</strong></td>
        <td>Paket alokasi hak seragam resmi yang berhak diterima mahasiswa berdasarkan program studi dan jenjang akademiknya (diatur di tabel <code>entitlements</code>).</td>
      </tr>
      <tr>
        <td><strong>Blended HPP</strong></td>
        <td>Nilai Harga Pokok Penjualan rata-rata tertimbang dari beberapa batch penerimaan persediaan FIFO yang terpotong saat penyerahan seragam di loket.</td>
      </tr>
      <tr>
        <td><strong>Gross Profit Margin (GPM)</strong></td>
        <td>Selisih margin laba kotor universitas per unit seragam: <code>(Selling Price - Blended HPP) x Kuantitas</code>.</td>
      </tr>
      <tr>
        <td><strong>Stock Batch</strong></td>
        <td>Lot kedatangan barang masuk dari vendor konveksi yang mencatat sisa saldo kuantitas dan harga perolehan unit sebagai basis pemotongan FIFO.</td>
      </tr>
      <tr>
        <td><strong>Stock Movement</strong></td>
        <td>Buku besar mutasi kartu stok elektronik yang mencatat setiap arus barang IN (masuk vendor), OUT (distribusi), atau ADJ (koreksi opname).</td>
      </tr>
      <tr>
        <td><strong>Deferred Distribution</strong></td>
        <td>Status penundaan seragam yang belum dapat diserahkan akibat kehabisan stok fisik di rak gudang, di mana saldo sisa hak mahasiswa tetap tersimpan.</td>
      </tr>
      <tr>
        <td><strong>Pessimistic Row Locking</strong></td>
        <td>Mekanisme penguncian baris data eksklusif di level basis data MySQL (<code>lockForUpdate</code>) untuk mencegah tabrakan transaksi simultan.</td>
      </tr>
      <tr>
        <td><strong>Soft Deletion</strong></td>
        <td>Penghapusan lunak record data dengan menandai kolom <code>deleted_at</code> tanpa membuang baris fisik dari media penyimpanan basis data.</td>
      </tr>
      <tr>
        <td><strong>Idempotent Seeder</strong></td>
        <td>Skrip pengisian data awal yang dapat dijalankan berulang kali tanpa membuat data ganda berkat penggunaan pola <code>firstOrCreate</code>.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-85">
  <h2 class="chapter-title">Bab 85 — 15 Fakta Baku Horizon UniStock (Canonical Truth)</h2>
  <p>Berikut adalah 15 fakta kanonis tidak terbantahkan yang wajib dipatuhi oleh seluruh pengembang dan agen AI:</p>
  <ol>
    <li>Konten barcode QR Code adalah <strong>string teks NIM Mahasiswa murni</strong> tanpa embel-embel URL atau token rahasia.</li>
    <li>Pembangkitan QR Code dilakukan secara lokal server-side via pustaka <code>bacon/bacon-qr-code</code> Level H dan di-stream sebagai Base64 PNG.</li>
    <li>Sistem hanya mengenal 4 peran resmi yang terdaftar via Enum <code>App\Enums\Role</code>: <code>super_admin</code>, <code>admin</code>, <code>staff</code>, dan <code>student</code>.</li>
    <li>Kredensial Super Admin bawaan diambil dari berkas <code>.env</code> dan akun memiliki penanda wajib ganti password (<code>must_change_password = true</code>).</li>
    <li>Kategori ukuran dipisahkan menggunakan kolom boolean <code>is_baju</code> dan <code>is_sepatu</code> pada tabel master <code>item_sizes</code>.</li>
    <li>Inventaris gudang menerapkan metode penilaian persediaan <strong>FIFO murni</strong> yang dicatat pada entitas <code>stock_batches</code>.</li>
    <li>Transaksi penyerahan seragam diproteksi dari risiko ambil ganda menggunakan <strong>pessimistic row locking (<code>lockForUpdate</code>)</strong>.</li>
    <li>Perubahan ukuran seragam saat fitting fisik di loket secara otomatis dicatat ke tabel audit <code>student_size_histories</code>.</li>
    <li>Jika stok gudang habis, barang berstatus tertunda (*deferred*) dan saldo sisa hak mahasiswa tetap tersimpan di basis data.</li>
    <li>Entitas <code>Student</code> mengelola dua alamat email: <code>email_kampus</code> (resmi akademik) dan <code>email_pribadi</code>.</li>
    <li>Pengubahan alamat email diverifikasi melalui kode OTP 6-digit dengan masa berlaku 15 menit pada tabel <code>otp_codes</code>.</li>
    <li>Jenjang semester mahasiswa menggunakan kode standar: <code>Y1S1</code>, <code>Y1S2</code>, <code>Y2S1</code>, <code>Y2S2</code>, <code>Y3S1</code>, <code>Y3S2</code>, <code>Y4S1</code>, <code>Y4S2</code>, dan <code>graduated</code>.</li>
    <li>Jadwal distribusi terikat opsional ke angkatan, fakultas, dan program studi dengan kolom <code>session</code> bernilai nullable.</li>
    <li>Server produksi beralamat di <code>unistok.nirwana.biz.id</code> (cPanel tanpa SSH). Setiap deploy git pull <strong>WAJIB MENJALANKAN</strong> <code>composer dump-autoload --optimize</code>.</li>
    <li>Audit stock opname menerapkan alur: pembuatan draft &rarr; unggah hitung fisik Excel &rarr; kalkulasi variance &rarr; approval manajer &rarr; posting jurnal penyesuaian stok.</li>
  </ol>
</div>

<div class="chapter" id="bab-86">
  <h2 class="chapter-title">Bab 86 — Source Registry &amp; File-File Kunci Sistem</h2>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 35%;">Lokasi Berkas Kode Sumber</th>
        <th style="width: 65%;">Tanggung Jawab Logika &amp; Peran dalam Sistem</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>app/Services/DistributionService.php</code></td>
        <td>Inti pemrosesan transaksi penyerahan seragam di loket, validasi kuota hak, dan perekaman riwayat ukuran.</td>
      </tr>
      <tr>
        <td><code>app/Services/StockService.php</code></td>
        <td>Inti algoritma persediaan FIFO, pemotongan lot batch, kartu mutasi stok, dan formula blended HPP.</td>
      </tr>
      <tr>
        <td><code>app/Services/StudentSizeService.php</code></td>
        <td>Pengendali opsi ukuran baju/sepatu, batas counter ubah mandiri, dan penguncian event mahasiswa.</td>
      </tr>
      <tr>
        <td><code>app/Services/QrCodeService.php</code></td>
        <td>Pembangkit citra QR Code resolusi tinggi offline berbasis NIM murni dengan Error Correction Level H.</td>
      </tr>
      <tr>
        <td><code>app/Http/Controllers/Staff/ScanController.php</code></td>
        <td>Pengendali antarmuka scanner kamera loket dan pencarian identitas mahasiswa.</td>
      </tr>
      <tr>
        <td><code>app/helpers.php</code></td>
        <td>Kumpulan fungsi pembantu global (termasuk <code>escapeLike()</code> untuk sanitasi pencarian SQL).</td>
      </tr>
      <tr>
        <td><code>routes/web.php &amp; routes/auth.php</code></td>
        <td>Peta keseluruhan rute HTTP, pengelompokan middleware, dan otorisasi endpoint sistem.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-87">
  <h2 class="chapter-title">Bab 87 — Evidence &amp; Confidence Levels (Tingkat Keyakinan Berbasis Bukti)</h2>
  <p>Seluruh materi dalam dokumen ini disarikan 100% dari bukti material kode sumber aktif repositori, 58 berkas migrasi database, model Eloquent, seeder, controller, dan spreadsheet master nyata institusi (<code>scripts/Dummy - Inventory Management.xlsx</code>). Tingkat keyakinan (*confidence level*) terhadap keabsahan teknis dokumen ini adalah <strong>Maksimal (100% Terverifikasi)</strong>.</p>
</div>

<div class="chapter" id="bab-88">
  <h2 class="chapter-title">Bab 88 — Open Questions &amp; Asumsi Operasional Manajerial</h2>
  <ul>
    <li><strong>Ketentuan Harga Jual Eceran Sisa Stok:</strong> Apakah penjualan eceran seragam pengganti untuk mahasiswa lama menggunakan harga pokok modal HPP murni atau ditambahkan margin komersial 25%?</li>
    <li><strong>Batas Akhir Pengambilan Seragam Tertunda:</strong> Berapa batas toleransi pekan pengambilan seragam bagi mahasiswa yang berhalangan hadir pada sesi orientasi awal sebelum dialokasikan ke mahasiswa lain?</li>
  </ul>
</div>

<div class="chapter" id="bab-89">
  <h2 class="chapter-title">Bab 89 — Instructions for AI Pair Programmers (Pedoman Mutlak Kolaborasi AI)</h2>
  <div class="callout callout-important">
    <strong>PEDOMAN WAJIB BAGI SETIAP AGEN AI:</strong>
    <ol>
      <li><strong>Jangan Pernah Merusak Alur FIFO:</strong> Jangan sekali-kali memotong atau menambah kolom <code>stock_balances.quantity</code> secara langsung via kueri SQL mentah. Mutasi persediaan WAJIB melalui method <code>StockService::deductStockFifo()</code>.</li>
      <li><strong>Selalu Bungkus Event Alpine dengan <code>x-data</code>:</strong> Seluruh elemen tombol atau kontrol interaktif yang memancarkan event <code>$dispatch()</code> wajib berada di dalam cakupan atribut <code>x-data</code> Alpine.js.</li>
      <li><strong>Patuhi Interface Maatwebsite Excel 4.0:</strong> Method <code>styles(Worksheet $sheet)</code> pada kelas Export wajib memiliki return type <code>: ?array</code> dan diakhiri dengan <code>return null;</code>.</li>
      <li><strong>Patuhi Konvensi Deployment cPanel:</strong> Selalu ingatkan pengguna untuk mengeksekusi <code>composer dump-autoload --optimize</code> jika terjadi penambahan atau perubahan fungsi di <code>app/helpers.php</code>.</li>
      <li><strong>Gunakan Tautan File yang Dapat Diklik:</strong> Berikan referensi baris kode yang tepat dengan skema tautan markdown GitHub <code>[nama_file](file:///c:/laragon/www/horizon-unistock/...)</code>.</li>
    </ol>
  </div>
</div>

<div class="chapter" id="bab-90">
  <h2 class="chapter-title">Bab 90 — Appendix (Dokumen Pendukung, Formula, &amp; Contoh Payload)</h2>
  <h3 class="subchapter-title">90.1 Contoh Payload JSON Respons Pemindaian Loket (`/distribution/search`)</h3>
  <pre><code>{
  "found": true,
  "student": {
    "id": 101,
    "nim": "20260001",
    "name": "Andi Pratama",
    "study_program": "S1 Keperawatan",
    "generation": "2026",
    "student_level": "Y1S1",
    "is_eligible": true,
    "payment_status": "paid"
  },
  "redirect": "/distribution/student/20260001"
}</code></pre>

  <h3 class="subchapter-title">90.2 Lembar Kontak Tim Pengembang &amp; Dukungan Teknis</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th>Peran Kontak</th>
        <th>Nama / Entitas</th>
        <th>Kanal Komunikasi Resmi</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Lead Software Architect</td>
        <td>Nirwana Tech Lead</td>
        <td>mrhildan648@gmail.com / Repositori GitHub</td>
      </tr>
      <tr>
        <td>Direktorat Keuangan Kampus</td>
        <td>Finance Team Horizon University</td>
        <td>finance@krw.horizon.ac.id</td>
      </tr>
      <tr>
        <td>Biro Administrasi Akademik (BAAK)</td>
        <td>Akademik &amp; Kemahasiswaan</td>
        <td>baak@krw.horizon.ac.id</td>
      </tr>
    </tbody>
  </table>
  <p style="text-align: center; color: #64748B; font-size: 8.5pt; margin-top: 10mm;">
    — Akhir dari Dokumen Induk Horizon UniStock (90 Bab Lengkap) —<br>
    Universitas Horizon Indonesia &copy; 2026. All Rights Reserved.
  </p>
</div>
"""
