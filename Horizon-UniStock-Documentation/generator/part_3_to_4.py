# -*- coding: utf-8 -*-
"""
Bagian III & Bagian IV: Bab 13 s/d Bab 36 (Expanded Edition)
Format: Enterprise Proposal & Technical Master Specification
- Detailed breakdown of all 49 Functional Requirements (FR-01 to FR-49)
- Standard 14-point documentation format for every core feature module
- Deep technical architecture, data flow, and API payload schemas
"""

def get_parts_3_and_4_html():
    return r"""
<!-- ============================================== -->
<!-- BAGIAN III: SPESIFIKASI PRODUK DAN SELURUH FITUR -->
<!-- ============================================== -->
<div class="part-divider">
  <div class="part-number">Bagian III</div>
  <h1 class="part-title">Spesifikasi Produk dan Seluruh Fitur</h1>
  <div class="part-subtitle">Dekomposisi Modul Fungsional, Katalog 49 Kebutuhan, Matriks Hak Akses, dan Spesifikasi Fitur 14-Poin</div>
</div>

<div class="chapter" id="bab-13">
  <h2 class="chapter-title">Bab 13 — Product Overview &amp; Dekomposisi Modul Fungsional</h2>
  <p>Sistem <strong>Horizon UniStock</strong> dirancang sebagai solusi *enterprise resource planning* (ERP) mini yang secara spesifik mengorkestrasi mata rantai distribusi perlengkapan mahasiswa. Secara garis besar, sistem membagi domain bisnisnya ke dalam 8 modul fungsional yang beroperasi secara modular namun terikat dalam integritas data yang ketat:</p>
  
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 20%;">Nama Modul</th>
        <th style="width: 25%;">Domain Tanggung Jawab</th>
        <th style="width: 25%;">Pengguna Utama</th>
        <th style="width: 30%;">Entitas Kunci yang Dikelola</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Student Portal</strong></td>
        <td>Pendataan ukuran mandiri, informasi jadwal, dan penerbitan QR Code.</td>
        <td>Mahasiswa Baru &amp; Lama</td>
        <td><code>students</code>, <code>student_size_profiles</code>, <code>size_event_submissions</code></td>
      </tr>
      <tr>
        <td><strong>Distribution Loket</strong></td>
        <td>Pemindaian barcode kamera, fitting fisik, checklist serah terima, dan potong stok.</td>
        <td>Staf Logistik &amp; Gudang</td>
        <td><code>distribution_transactions</code>, <code>distribution_items</code>, <code>student_size_histories</code></td>
      </tr>
      <tr>
        <td><strong>Academic Master</strong></td>
        <td>Struktur organisasi akademik, fakultas, prodi, angkatan, dan biodata mahasiswa.</td>
        <td>Admin BAAK &amp; Kemahasiswaan</td>
        <td><code>faculties</code>, <code>study_programs</code>, <code>student_generations</code>, <code>student_levels</code></td>
      </tr>
      <tr>
        <td><strong>Inventory Core</strong></td>
        <td>Penerimaan barang vendor, lot batch kedatangan, kartu mutasi stok, dan saldo rak.</td>
        <td>Admin Gudang &amp; Logistik</td>
        <td><code>stock_receives</code>, <code>stock_batches</code>, <code>stock_movements</code>, <code>stock_balances</code></td>
      </tr>
      <tr>
        <td><strong>Entitlement &amp; Schedule</strong></td>
        <td>Paket jatah barang per prodi dan pembukaan sesi waktu loket pembagian.</td>
        <td>Admin Logistik &amp; Distribusi</td>
        <td><code>entitlements</code>, <code>entitlement_items</code>, <code>distribution_schedules</code></td>
      </tr>
      <tr>
        <td><strong>Stock Opname</strong></td>
        <td>Audit fisik bulanan, komparasi variance, approval berjenjang, dan penyesuaian stok.</td>
        <td>Manajer Logistik &amp; Finance</td>
        <td><code>stock_opnames</code>, <code>stock_opname_items</code>, <code>stock_opname_adjustments</code></td>
      </tr>
      <tr>
        <td><strong>Finance &amp; GPM</strong></td>
        <td>Verifikasi kelayakan pembayaran, Sales Dashboard, dan analisis margin laba kotor.</td>
        <td>Direktorat Keuangan (Finance)</td>
        <td><code>eligibility_records</code>, <code>item_prices</code>, Laporan GPM Real-Time</td>
      </tr>
      <tr>
        <td><strong>System &amp; Security</strong></td>
        <td>Manajemen peran Spatie, konfigurasi SMTP, audit logging, dan manajemen akun.</td>
        <td>Administrator TI (Super Admin)</td>
        <td><code>users</code>, <code>roles</code>, <code>permissions</code>, <code>smtp_settings</code>, <code>otp_codes</code></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-14">
  <h2 class="chapter-title">Bab 14 — Functional Requirements (Katalog Lengkap FR-01 s/d FR-49)</h2>
  <p>Berikut adalah katalog lengkap seluruh 49 Kebutuhan Fungsional (*Functional Requirements*) sistem Horizon UniStock yang diuraikan satu per satu secara individual:</p>

  <h3 class="subchapter-title">14.1 Kebutuhan Fungsional Manajemen Mahasiswa &amp; Kredensial</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 10%;">ID</th>
        <th style="width: 25%;">Nama Fitur</th>
        <th style="width: 15%;">Aktor</th>
        <th style="width: 50%;">Deskripsi Fungsional &amp; Kriteria Keberhasilan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>FR-01</strong></td>
        <td>Impor Excel Mahasiswa</td>
        <td>Admin / Finance</td>
        <td>Mengunggah file Excel biodata mahasiswa PMB dengan pemetaan kolom NIM, Nama, Gender, Prodi, dan Angkatan. Sistem menolak berkas jika terdapat duplikasi NIM.</td>
      </tr>
      <tr>
        <td><strong>FR-02</strong></td>
        <td>Pencarian Data Mahasiswa</td>
        <td>Admin / Staff</td>
        <td>Menyediakan kolom pencarian debounced pada tabel mahasiswa dengan kriteria Nama, NIM, Email Kampus, serta filter dropdown Prodi dan Angkatan.</td>
      </tr>
      <tr>
        <td><strong>FR-03</strong></td>
        <td>Toggle Status Kelayakan</td>
        <td>Finance Admin</td>
        <td>Menyediakan tombol aksi cepat satu-klik untuk mengubah status mahasiswa antara Eligible (Layak) dan Ineligible (Belum Layak) pada tabel kelayakan keuangan.</td>
      </tr>
      <tr>
        <td><strong>FR-04</strong></td>
        <td>Generate Akun Massal</td>
        <td>Super Admin</td>
        <td>Membentuk akun <code>users</code> secara massal bagi mahasiswa yang belum memiliki akun, membuat kata sandi acak aman, dan mengaktifkan flag <code>must_change_password</code>.</td>
      </tr>
      <tr>
        <td><strong>FR-05</strong></td>
        <td>Distribusi Kredensial SMTP</td>
        <td>Sistem Otomatis</td>
        <td>Mengirimkan surat elektronik resmi berisi NIM dan kata sandi sementara ke alamat email kampus mahasiswa melalui protokol SMTP kampus terenkripsi.</td>
      </tr>
      <tr>
        <td><strong>FR-06</strong></td>
        <td>Reset Kata Sandi Terpusat</td>
        <td>Admin / Staf</td>
        <td>Admin dapat mereset kata sandi mahasiswa yang lupa password melalui tombol reset di antarmuka kredensial, yang menghasilkan password baru dan mengirimkannya via email.</td>
      </tr>
      <tr>
        <td><strong>FR-07</strong></td>
        <td>Verifikasi Email Kampus OTP</td>
        <td>Mahasiswa</td>
        <td>Memverifikasi kepemilikan email baru mahasiswa menggunakan kode One-Time Password (OTP) 6-digit dengan masa kedaluwarsa 15 menit.</td>
      </tr>
    </tbody>
  </table>

  <h3 class="subchapter-title">14.2 Kebutuhan Fungsional Penerimaan Barang &amp; Penjadwalan</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 10%;">ID</th>
        <th style="width: 25%;">Nama Fitur</th>
        <th style="width: 15%;">Aktor</th>
        <th style="width: 50%;">Deskripsi Fungsional &amp; Kriteria Keberhasilan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>FR-08</strong></td>
        <td>Penerimaan Barang Vendor</td>
        <td>Admin Gudang</td>
        <td>Mencatat surat jalan kiriman konveksi, nomor referensi, tanggal kedatangan, vendor rekanan, kuantitas per SKU ukuran, dan harga beli perolehan (HPP).</td>
      </tr>
      <tr>
        <td><strong>FR-09</strong></td>
        <td>Penyusunan Jadwal Distribusi</td>
        <td>Admin Distribusi</td>
        <td>Membuat sesi waktu pembagian seragam di aula kampus dengan menentukan tanggal, lokasi loket, jam sesi, target prodi, dan mencentang barang yang dibuka.</td>
      </tr>
      <tr>
        <td><strong>FR-10</strong></td>
        <td>Size Monitor Rekapitulasi</td>
        <td>Admin / Finance</td>
        <td>Menyajikan grafik dan tabel distribusi ukuran baju/sepatu mahasiswa per prodi untuk memantau sebaran kebutuhan fisik sebelum PO vendor diterbitkan.</td>
      </tr>
      <tr>
        <td><strong>FR-11</strong></td>
        <td>Registrasi Stock Opname</td>
        <td>Admin Logistik</td>
        <td>Membuka agenda audit penghitungan fisik persediaan bulanan dengan membekukan snapshot saldo sistem (*system quantity*) saat opname dimulai.</td>
      </tr>
      <tr>
        <td><strong>FR-12</strong></td>
        <td>Kalkulasi GPM &amp; Biaya</td>
        <td>Finance / Eksekutif</td>
        <td>Mengkalkulasi selisih laba kotor secara otomatis per transaksi: <code>(Harga Jual Standar - Blended HPP FIFO) x Jumlah Unit</code>.</td>
      </tr>
      <tr>
        <td><strong>FR-13</strong></td>
        <td>Ekspor Laporan Distribusi</td>
        <td>Admin / Finance</td>
        <td>Mengekspor seluruh rekapitulasi penyerahan seragam (status Sudah Ambil, Belum Ambil, dan Parsial) ke dalam file spreadsheet Excel (.xlsx).</td>
      </tr>
      <tr>
        <td><strong>FR-14</strong></td>
        <td>Ekspor Laporan Stok &amp; Mutasi</td>
        <td>Admin Gudang</td>
        <td>Mengekspor posisi saldo persediaan saat ini dan buku besar kartu stok elektronik ke format Excel resmi.</td>
      </tr>
      <tr>
        <td><strong>FR-15</strong></td>
        <td>Ekspor Laporan Finansial GPM</td>
        <td>Direktur Keuangan</td>
        <td>Mengekspor rekapitulasi laba kotor per jenis seragam, per kategori, dan per fakultas lengkap dengan persentase margin keuntungan.</td>
      </tr>
    </tbody>
  </table>

  <h3 class="subchapter-title">14.3 Kebutuhan Fungsional Loket Staf &amp; Pemindaian Barcode</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 10%;">ID</th>
        <th style="width: 25%;">Nama Fitur</th>
        <th style="width: 15%;">Aktor</th>
        <th style="width: 50%;">Deskripsi Fungsional &amp; Kriteria Keberhasilan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>FR-16</strong></td>
        <td>Otentikasi Staf Loket</td>
        <td>Staf Gudang</td>
        <td>Staf login menggunakan akun kredensial resmi yang memiliki permission <code>manage-students</code> atau role <code>staff</code>.</td>
      </tr>
      <tr>
        <td><strong>FR-17</strong></td>
        <td>Scan Barcode Kamera</td>
        <td>Staf Loket</td>
        <td>Memanfaatkan kamera peramban (webcam/smartphone) via library <code>html5-qrcode</code> untuk mendeteksi QR Code layar ponsel mahasiswa secara instan.</td>
      </tr>
      <tr>
        <td><strong>FR-18</strong></td>
        <td>Pencarian NIM Fallback</td>
        <td>Staf Loket</td>
        <td>Menyediakan form input pengetikan NIM manual berkecepatan tinggi jika kamera pemindai terganggu atau layar ponsel mahasiswa rusak.</td>
      </tr>
      <tr>
        <td><strong>FR-19</strong></td>
        <td>Verifikasi Data Mahasiswa</td>
        <td>Staf Loket</td>
        <td>Menampilkan biodata lengkap mahasiswa, foto identitas, program studi, serta status kelayakan pembayaran (Eligible / Ineligible).</td>
      </tr>
      <tr>
        <td><strong>FR-20</strong></td>
        <td>Checklist Hak Entitlement</td>
        <td>Staf Loket</td>
        <td>Menampilkan daftar seragam yang berhak diambil mahasiswa pada jadwal aktif sesuai paket kuota entitlement program studinya.</td>
      </tr>
      <tr>
        <td><strong>FR-21</strong></td>
        <td>Penyesuaian Actual Size</td>
        <td>Staf Loket</td>
        <td>Mengizinkan staf mengubah ukuran baju/sepatu aktual yang diserahkan jika hasil fitting fisik mahasiswa di meja loket tidak sesuai pesanan semula.</td>
      </tr>
      <tr>
        <td><strong>FR-22</strong></td>
        <td>Validasi Stok Fisik Real-Time</td>
        <td>Staf Loket</td>
        <td>Sistem memeriksa apakah saldo persediaan varian ukuran yang dipilih masih tersedia di rak gudang sebelum form disubmit.</td>
      </tr>
      <tr>
        <td><strong>FR-23</strong></td>
        <td>Penyerahan Sebagian (Partial)</td>
        <td>Staf Loket</td>
        <td>Mengizinkan staf hanya mencentang barang yang tersedia fisiknya jika ada salah satu item seragam yang habis (item kosong berstatus Deferred).</td>
      </tr>
      <tr>
        <td><strong>FR-24</strong></td>
        <td>Submit Transaksi &amp; Potong FIFO</td>
        <td>Staf Loket</td>
        <td>Menyimpan transaksi penyerahan, mengunci data mahasiswa via <code>lockForUpdate</code>, memotong kuota batch tertua FIFO, dan mencatat audit kartu stok.</td>
      </tr>
    </tbody>
  </table>

  <h3 class="subchapter-title">14.4 Kebutuhan Fungsional Portal Mandiri Mahasiswa</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 10%;">ID</th>
        <th style="width: 25%;">Nama Fitur</th>
        <th style="width: 15%;">Aktor</th>
        <th style="width: 50%;">Deskripsi Fungsional &amp; Kriteria Keberhasilan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>FR-25</strong></td>
        <td>Login Portal Mahasiswa</td>
        <td>Mahasiswa</td>
        <td>Mahasiswa masuk ke portal menggunakan NIM resmi sebagai identifier dan kata sandi yang telah dibagikan admin.</td>
      </tr>
      <tr>
        <td><strong>FR-26</strong></td>
        <td>Wajib Ganti Password Awal</td>
        <td>Mahasiswa</td>
        <td>Memaksa mahasiswa mengganti kata sandi bawaan pada saat login pertama kali sebelum diizinkan mengakses fitur portal lainnya.</td>
      </tr>
      <tr>
        <td><strong>FR-27</strong></td>
        <td>Dashboard Status Mahasiswa</td>
        <td>Mahasiswa</td>
        <td>Menyajikan ringkasan profil akademik, status kelayakan pembayaran, event ukuran yang sedang aktif, dan agenda jadwal distribusi aula.</td>
      </tr>
      <tr>
        <td><strong>FR-28</strong></td>
        <td>Pengisian Ukuran Mandiri</td>
        <td>Mahasiswa</td>
        <td>Mahasiswa memilih ukuran baju dan sepatu dinas dari opsi dropdown yang disediakan pada event pengisian ukuran yang aktif.</td>
      </tr>
      <tr>
        <td><strong>FR-29</strong></td>
        <td>Panduan Ukuran (Size Chart)</td>
        <td>Mahasiswa</td>
        <td>Menampilkan modal panduan spesifikasi ukuran fisik resmi (lingkar dada, panjang lengan, ukuran sol sepatu) dari vendor konveksi rekanan.</td>
      </tr>
      <tr>
        <td><strong>FR-30</strong></td>
        <td>Pembatasan Edit Ukuran</td>
        <td>Mahasiswa</td>
        <td>Sistem membatasi frekuensi pengubahan ukuran mandiri maksimal sesuai angka <code>max_changes</code> pada event (setelah itu form terkunci otomatis).</td>
      </tr>
      <tr>
        <td><strong>FR-31</strong></td>
        <td>Tampilan QR Code Digital</td>
        <td>Mahasiswa</td>
        <td>Menghasilkan gambar tanda pengenal QR Code digital beresolusi tinggi (Error Correction Level H) yang mengenkapsulasi string NIM mahasiswa.</td>
      </tr>
      <tr>
        <td><strong>FR-32</strong></td>
        <td>Pemeriksaan Jadwal Distribusi</td>
        <td>Mahasiswa</td>
        <td>Mahasiswa dapat melihat tanggal, jam sesi, dan lokasi loket pengambilan seragam yang secara khusus dialokasikan untuk program studinya.</td>
      </tr>
      <tr>
        <td><strong>FR-33</strong></td>
        <td>Permohonan Reset Password</td>
        <td>Mahasiswa</td>
        <td>Mahasiswa yang lupa kata sandi dapat meminta tautan reset password yang dikirimkan ke email kampus resmi mereka.</td>
      </tr>
    </tbody>
  </table>

  <h3 class="subchapter-title">14.5 Kebutuhan Fungsional Administrasi Sistem &amp; Keamanan</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 10%;">ID</th>
        <th style="width: 25%;">Nama Fitur</th>
        <th style="width: 15%;">Aktor</th>
        <th style="width: 50%;">Deskripsi Fungsional &amp; Kriteria Keberhasilan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>FR-34</strong></td>
        <td>Manajemen Pengguna Staf/Admin</td>
        <td>Super Admin</td>
        <td>CRUD pengguna sistem lengkap dengan penetapan peran Spatie (Super Admin, Admin, Staff) dan toggle pengaktifan status akun.</td>
      </tr>
      <tr>
        <td><strong>FR-35</strong></td>
        <td>Pengaturan Server SMTP Dinamis</td>
        <td>Super Admin</td>
        <td>Mengonfigurasi parameter host, port, username, password, dan enkripsi server surat elektronik SMTP kampus yang disimpan di database.</td>
      </tr>
      <tr>
        <td><strong>FR-36</strong></td>
        <td>Uji Koneksi Soket SMTP</td>
        <td>Super Admin</td>
        <td>Melakukan pengujian konektivitas soket jaringan ke server mail kampus dan mengirimkan email tes simulasi untuk memastikan kesiapan sistem.</td>
      </tr>
      <tr>
        <td><strong>FR-37</strong></td>
        <td>Pusat Monitoring Dashboard TI</td>
        <td>Super Admin</td>
        <td>Menyajikan metrik kesehatan sistem, jumlah pengguna aktif, volume transaksi loket hari ini, dan sisa kuota kapasitas memori.</td>
      </tr>
      <tr>
        <td><strong>FR-38</strong></td>
        <td>Manajemen Varian SKU Barang</td>
        <td>Admin Master</td>
        <td>Mendaftarkan varian ukuran fisik (S, M, L, XL, 39, 40, dst) untuk setiap master seragam lengkap dengan kode unik SKU gudang.</td>
      </tr>
    </tbody>
  </table>

  <h3 class="subchapter-title">14.6 Kebutuhan Fungsional Stock Opname &amp; Akuntansi Finansial GPM</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 10%;">ID</th>
        <th style="width: 25%;">Nama Fitur</th>
        <th style="width: 15%;">Aktor</th>
        <th style="width: 50%;">Deskripsi Fungsional &amp; Kriteria Keberhasilan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>FR-39</strong></td>
        <td>Inisiasi Batch Stock Opname</td>
        <td>Admin Logistik</td>
        <td>Membentuk sesi dokumen audit fisik dengan nomor register unik, penetapan tanggal audit, dan penentuan periode buku tahunan.</td>
      </tr>
      <tr>
        <td><strong>FR-40</strong></td>
        <td>Unggah Rekap Fisik Excel</td>
        <td>Admin Logistik</td>
        <td>Mengunggah berkas Excel hasil penghitungan fisik tim pemeriksa gudang untuk dipetakan ke dalam tabel rincian opname.</td>
      </tr>
      <tr>
        <td><strong>FR-41</strong></td>
        <td>Kalkulasi Otomatis Selisih (Variance)</td>
        <td>Sistem Otomatis</td>
        <td>Membandingkan angka fisik lapangan terhadap angka saldo buku sistem secara otomatis dan mengkategorikan selisih surplus atau shortage.</td>
      </tr>
      <tr>
        <td><strong>FR-42</strong></td>
        <td>Persetujuan Jurnal Penyesuaian</td>
        <td>Manajer Logistik / Finance</td>
        <td>Otorisasi pimpinan untuk menyetujui hasil audit, yang secara otomatis membentuk mutasi stok tipe 'ADJ' guna menyamakan saldo buku ke saldo fisik riil.</td>
      </tr>
      <tr>
        <td><strong>FR-43</strong></td>
        <td>Histori Audit Persediaan per Item</td>
        <td>Auditor Internal</td>
        <td>Menelusuri riwayat seluruh sesi stock opname yang pernah dilakukan pada suatu SKU barang tertentu sepanjang tahun buku.</td>
      </tr>
      <tr>
        <td><strong>FR-44</strong></td>
        <td>Ekspor Hasil Rekonsiliasi Opname</td>
        <td>Admin Logistik</td>
        <td>Mengekspor lembar perbandingan saldo sistem vs saldo fisik serta daftar penyesuaian yang disetujui ke format spreadsheet Excel.</td>
      </tr>
      <tr>
        <td><strong>FR-45</strong></td>
        <td>Pelacakan HPP per Lot Kedatangan</td>
        <td>Sistem FIFO</td>
        <td>Sistem secara otomatis memelihara riwayat harga perolehan unit pada tabel <code>stock_batches</code> untuk setiap transaksi barang masuk vendor.</td>
      </tr>
      <tr>
        <td><strong>FR-46</strong></td>
        <td>Penetapan Harga Jual Patokan</td>
        <td>Direktorat Keuangan</td>
        <td>Mengonfigurasi harga patokan seragam universitas pada tabel <code>item_prices</code> berdasarkan tahun akademik dan jenjang program studi.</td>
      </tr>
      <tr>
        <td><strong>FR-47</strong></td>
        <td>Pelaporan Real-Time Margin GPM</td>
        <td>Finance / Eksekutif</td>
        <td>Menghitung akumulasi laba kotor kotor penjualan seragam secara seketika berdasarkan data riil seragam yang telah diserahkan di loket.</td>
      </tr>
      <tr>
        <td><strong>FR-48</strong></td>
        <td>Laporan Rugi Kerusakan &amp; Kehilangan</td>
        <td>Finance / Auditor</td>
        <td>Menghitung total kerugian finansial akibat barang susut, rusak di gudang, atau selisih opname negatif berdasarkan nilai modal HPP batch terkait.</td>
      </tr>
      <tr>
        <td><strong>FR-49</strong></td>
        <td>Ekspor Komprehensif Laporan GPM</td>
        <td>Direktur Keuangan</td>
        <td>Mengekspor laporan analisis biaya dan laba kotor lengkap ke format Excel dengan formula laba rugi standar akuntansi perguruan tinggi.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-15">
  <h2 class="chapter-title">Bab 15 — Role and Permission Matrix Terperinci</h2>
  <p>Pemberian hak akses pada sistem UniStock dikelola melalui paket <code>spatie/laravel-permission</code> yang terikat pada PHP Enum <code>App\Enums\Role</code>. Matriks berikut merinci izin operasional masing-masing peran:</p>

  <table class="data-table">
    <thead>
      <tr>
        <th>Fitur / Aksi Operasional</th>
        <th>Super Admin</th>
        <th>Finance / Admin</th>
        <th>Staf Gudang</th>
        <th>Mahasiswa</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Konfigurasi Server SMTP &amp; Tes Email</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
      <tr>
        <td>Manajemen Pengguna Staf &amp; Admin</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-warning">Hanya Lihat</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
      <tr>
        <td>Kelola Master Data (Fakultas, Prodi, Item)</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
      <tr>
        <td>Atur Paket Jatah Barang (Entitlement)</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
      <tr>
        <td>Verifikasi &amp; Toggle Kelayakan Pembayaran</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
      <tr>
        <td>Penerimaan Barang Masuk Vendor (Stock Receive)</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-warning">Hanya Lihat</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
      <tr>
        <td>Approval &amp; Koreksi Saldo Stock Opname</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
      <tr>
        <td>Akses Loket Pemindaian Barcode (Scan QR)</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
      <tr>
        <td>Eksekusi Transaksi Penyerahan Seragam</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
      <tr>
        <td>Pengisian Ukuran &amp; Unduh QR Code Pribadi</td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-success">Milik Sendiri</span></td>
      </tr>
      <tr>
        <td>Akses Sales Dashboard &amp; Laporan Finansial GPM</td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-success">Akses Penuh</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
        <td><span class="badge badge-danger">Ditolak</span></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-16">
  <h2 class="chapter-title">Bab 16 — Student Module (Spesifikasi Fitur Lengkap 14-Poin)</h2>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">FITUR STU-01</span>
      <span class="feature-name">Pengisian Profil Ukuran Seragam Mandiri Mahasiswa</span>
    </div>
    <table class="data-table">
      <tbody>
        <tr><td style="width: 25%;"><strong>Tujuan</strong></td><td>Mengumpulkan data ukuran baju dan sepatu dinas mahasiswa baru secara terstruktur dan terstandarisasi sebelum jadwal distribusi aula dibuka.</td></tr>
        <tr><td><strong>Aktor</strong></td><td>Mahasiswa Baru / Mahasiswa Aktif (Role: <code>student</code>).</td></tr>
        <tr><td><strong>Prasyarat</strong></td><td>Mahasiswa telah login ke sistem, telah mengubah kata sandi awal (<code>must_change_password = false</code>), dan terdapat agenda <code>SizeChangeEvent</code> yang aktif untuk prodi/angkatannya.</td></tr>
        <tr><td><strong>Input</strong></td><td>Pilihan dropdown ukuran baju (misal: S, M, L, XL, XXL) dan ukuran sepatu (misal: 38, 39, 40, 41, 42).</td></tr>
        <tr><td><strong>Alur Normal</strong></td><td>1. Mahasiswa membuka rute <code>/student/sizes</code>; 2. Klik "Input Ukuran"; 3. Membuka modal panduan size chart; 4. Memilih opsi ukuran baju dan sepatu; 5. Klik "Simpan Ukuran"; 6. Sistem menyimpan data ke <code>student_size_profiles</code> dan menampilkan notifikasi sukses.</td></tr>
        <tr><td><strong>Alur Alternatif</strong></td><td>Jika event ukuran belum dimulai atau sudah kedaluwarsa (lewat <code>end_date</code>), sistem menampilkan banner peringatan oranye dan mengunci form menjadi read-only.</td></tr>
        <tr><td><strong>Validasi</strong></td><td>Opsi ukuran wajib dipilih dari daftar master yang memiliki flag <code>is_baju = true</code> dan <code>is_sepatu = true</code> pada tabel <code>item_sizes</code>. Mahasiswa tidak boleh mengisi nilai acak.</td></tr>
        <tr><td><strong>Output</strong></td><td>Profil ukuran aktif tersimpan di tabel <code>student_size_profiles</code> dengan flag <code>is_active = true</code>.</td></tr>
        <tr><td><strong>Data yang Terdampak</strong></td><td>Tabel <code>student_size_profiles</code> (INSERT/UPDATE), tabel <code>size_event_submissions</code> (penambahan counter submission).</td></tr>
        <tr><td><strong>Hak Akses</strong></td><td>Hanya pengguna dengan peran <code>student</code> yang dapat mengisi profil miliknya sendiri.</td></tr>
        <tr><td><strong>Kegagalan &amp; Error</strong></td><td>Pesan error: "Event pengisian ukuran telah ditutup" atau "Anda telah melebihi batas maksimal pengubahan ukuran".</td></tr>
        <tr><td><strong>Test Case</strong></td><td>Uji coba mahasiswa mengisi ukuran L dan 41 pada event aktif; verifikasi data tersimpan di basis data dan counter bertambah 1.</td></tr>
        <tr><td><strong>Status Implementasi</strong></td><td><span class="badge badge-success">Terverifikasi Selesai (100% Implemented)</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="chapter" id="bab-17">
  <h2 class="chapter-title">Bab 17 — Staff Module (Spesifikasi Fitur Lengkap 14-Poin)</h2>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">FITUR STF-01</span>
      <span class="feature-name">Pemindaian Barcode Loket &amp; Verifikasi Kelayakan Mahasiswa</span>
    </div>
    <table class="data-table">
      <tbody>
        <tr><td style="width: 25%;"><strong>Tujuan</strong></td><td>Mengidentifikasi mahasiswa di loket gudang secara kilat menggunakan kamera scanner, memeriksa status pelunasan keuangan, dan memuat hak paket barang.</td></tr>
        <tr><td><strong>Aktor</strong></td><td>Staf Logistik Loket Gudang (Role: <code>staff</code>, <code>admin</code>, <code>super_admin</code>).</td></tr>
        <tr><td><strong>Prasyarat</strong></td><td>Staf login ke sistem dan membuka antarmuka <code>/distribution/scan</code> dengan izin akses kamera peramban aktif.</td></tr>
        <tr><td><strong>Input</strong></td><td>Citra optik barcode QR Code dari layar ponsel mahasiswa, atau input teks 8 digit NIM manual.</td></tr>
        <tr><td><strong>Alur Normal</strong></td><td>1. Staf mengarahkan kamera ke layar ponsel mahasiswa; 2. Library <code>html5-qrcode</code> menangkap barcode dan mengekstrak string NIM; 3. Permintaan AJAX dikirim ke <code>POST /distribution/search</code>; 4. Sistem merespons redirect ke lembar penyerahan <code>/distribution/student/{nim}</code>; 5. Layar menampilkan profil mahasiswa, status kelayakan hijau (*Paid*), dan grid barang.</td></tr>
        <tr><td><strong>Alur Alternatif</strong></td><td>Jika mahasiswa belum lunas (status *Ineligible*), sistem menampilkan banner merah peringatan dan menonaktifkan tombol submit serah terima barang.</td></tr>
        <tr><td><strong>Validasi</strong></td><td>NIM wajib terdaftar di basis data <code>students</code>. Terdapat rate limiting 30 request per menit pada endpoint pencarian.</td></tr>
        <tr><td><strong>Output</strong></td><td>Tampilan lembar kerja verifikasi dan checklist barang seragam mahasiswa di loket.</td></tr>
        <tr><td><strong>Data yang Terdampak</strong></td><td>Log pembacaan scanner dan sesi staf.</td></tr>
        <tr><td><strong>Hak Akses</strong></td><td>Role <code>staff</code>, <code>admin</code>, dan <code>super_admin</code>. Mahasiswa dilarang keras mengakses antarmuka ini.</td></tr>
        <tr><td><strong>Kegagalan &amp; Error</strong></td><td>Pesan error: "Mahasiswa dengan NIM tersebut tidak ditemukan" atau "Kamera peramban tidak diizinkan".</td></tr>
        <tr><td><strong>Test Case</strong></td><td>Pindai QR Code mahasiswa NIM 20260001; verifikasi halaman berpindah ke rute penyerahan dalam waktu &lt; 1 detik.</td></tr>
        <tr><td><strong>Status Implementasi</strong></td><td><span class="badge badge-success">Terverifikasi Selesai (100% Implemented)</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="chapter" id="bab-18">
  <h2 class="chapter-title">Bab 18 — Admin Module (Spesifikasi Fitur Lengkap 14-Poin)</h2>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">FITUR ADM-01</span>
      <span class="feature-name">Penerbitan Kredensial Akun Mahasiswa Massal &amp; Pengiriman Email</span>
    </div>
    <table class="data-table">
      <tbody>
        <tr><td style="width: 25%;"><strong>Tujuan</strong></td><td>Menerbitkan akun login sistem untuk seluruh mahasiswa baru yang belum memiliki akun serta mendistribusikan kredensial melalui email kampus.</td></tr>
        <tr><td><strong>Aktor</strong></td><td>Super Admin / Admin Kemahasiswaan.</td></tr>
        <tr><td><strong>Prasyarat</strong></td><td>Data master mahasiswa telah diimpor ke tabel <code>students</code> dan konfigurasi server SMTP telah diverifikasi aktif.</td></tr>
        <tr><td><strong>Input</strong></td><td>Tombol pemicu "Generate All Accounts" pada antarmuka <code>/student/generate</code>.</td></tr>
        <tr><td><strong>Alur Normal</strong></td><td>1. Admin membuka halaman <code>/student/generate</code>; 2. Melihat statistik mahasiswa tanpa akun; 3. Klik tombol "Generate All"; 4. Sistem mengiterasi mahasiswa, membuat record <code>users</code> baru dengan password acak, mengisi <code>students.user_id</code>, dan mengirim email kredensial via SMTP; 5. Menampilkan rekapitulasi akun sukses dibuat.</td></tr>
        <tr><td><strong>Alur Alternatif</strong></td><td>Jika server SMTP kampus gagal terhubung, akun tetap berhasil dibuat di database dan sistem menandai status notifikasi sebagai 'failed' agar dapat dikirim ulang secara massal via tombol "Resend All Failed".</td></tr>
        <tr><td><strong>Validasi</strong></td><td>Mahasiswa yang telah memiliki <code>user_id</code> tidak akan di-generate ulang (idempotent).</td></tr>
        <tr><td><strong>Output</strong></td><td>Record baru pada tabel <code>users</code> dengan role 'student' dan record riwayat di <code>email_notifications</code>.</td></tr>
        <tr><td><strong>Data yang Terdampak</strong></td><td>Tabel <code>users</code> (INSERT), tabel <code>students</code> (UPDATE user_id), tabel <code>email_notifications</code> (INSERT).</td></tr>
        <tr><td><strong>Hak Akses</strong></td><td>Role <code>super_admin</code> dan <code>admin</code>.</td></tr>
        <tr><td><strong>Kegagalan &amp; Error</strong></td><td>Pesan error: "Koneksi SMTP timeout saat mengirimkan kredensial akun".</td></tr>
        <tr><td><strong>Test Case</strong></td><td>Eksekusi generate pada 10 mahasiswa uji; verifikasi akun terbentuk dan password acak terenkripsi bcrypt.</td></tr>
        <tr><td><strong>Status Implementasi</strong></td><td><span class="badge badge-success">Terverifikasi Selesai (100% Implemented)</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="chapter" id="bab-19">
  <h2 class="chapter-title">Bab 19 — QR Code Module (Spesifikasi Fitur Lengkap 14-Poin)</h2>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">FITUR QRC-01</span>
      <span class="feature-name">Pembangkitan Gambar QR Code Digital Offline Resolusi Tinggi</span>
    </div>
    <table class="data-table">
      <tbody>
        <tr><td style="width: 25%;"><strong>Tujuan</strong></td><td>Menghasilkan gambar barcode QR Code resmi beresolusi tinggi secara offline di sisi server untuk digunakan sebagai tanda pengenal saat pengambilan seragam.</td></tr>
        <tr><td><strong>Aktor</strong></td><td>Mahasiswa (Role: <code>student</code>).</td></tr>
        <tr><td><strong>Prasyarat</strong></td><td>Mahasiswa login dengan akun valid dan memiliki data NIM terdaftar.</td></tr>
        <tr><td><strong>Input</strong></td><td>Permintaan HTTP GET ke rute <code>/student/qr</code>.</td></tr>
        <tr><td><strong>Alur Normal</strong></td><td>1. Mahasiswa mengklik menu "QR Code" di portal; 2. <code>SizeController::qr()</code> memanggil <code>QrCodeService::getQrPngDataUrl()</code>; 3. Library BaconQrCode me-render barcode ukuran 300x300 piksel dengan Error Correction Level H berisi string NIM murni; 4. Citra dikonversi ke format string Base64 Data URL; 5. Layar menampilkan QR Code dengan tombol cetak/screenshot.</td></tr>
        <tr><td><strong>Alur Alternatif</strong></td><td>Jika mahasiswa belum mengisi profil ukuran, sistem tetap menampilkan QR Code disertai catatan peringatan oranye bahwa ukuran belum lengkap.</td></tr>
        <tr><td><strong>Validasi</strong></td><td>String NIM wajib valid dan terikat pada record model <code>Student</code> yang sedang login.</td></tr>
        <tr><td><strong>Output</strong></td><td>String Base64 Data URL berformat <code>data:image/png;base64,...</code> yang disematkan pada tag citra HTML.</td></tr>
        <tr><td><strong>Data yang Terdampak</strong></td><td>Tidak ada mutasi basis data (operasi murni pembacaan dan perenderan di RAM).</td></tr>
        <tr><td><strong>Hak Akses</strong></td><td>Role <code>student</code> untuk melihat QR pribadinya.</td></tr>
        <tr><td><strong>Kegagalan &amp; Error</strong></td><td>Pesan error: "Ekstensi PHP GD Lib belum aktif di server".</td></tr>
        <tr><td><strong>Test Case</strong></td><td>Panggil method <code>getQrPngDataUrl()</code>; verifikasi string diawali dengan header Base64 PNG valid dan dapat dipindai menghasilkan NIM yang tepat.</td></tr>
        <tr><td><strong>Status Implementasi</strong></td><td><span class="badge badge-success">Terverifikasi Selesai (100% Implemented)</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="chapter" id="bab-20">
  <h2 class="chapter-title">Bab 20 — Distribution Module (Spesifikasi Fitur Lengkap 14-Poin)</h2>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">FITUR DST-01</span>
      <span class="feature-name">Eksekusi Penyerahan Seragam &amp; Pemotongan Stok Persediaan FIFO</span>
    </div>
    <table class="data-table">
      <tbody>
        <tr><td style="width: 25%;"><strong>Tujuan</strong></td><td>Menyerahkan seragam secara fisik di loket, menyesuaikan ukuran hasil fitting, mengunci transaksi dari klaim ganda, dan memotong persediaan batch tertua FIFO.</td></tr>
        <tr><td><strong>Aktor</strong></td><td>Staf Logistik Loket Gudang (Role: <code>staff</code>, <code>admin</code>, <code>super_admin</code>).</td></tr>
        <tr><td><strong>Prasyarat</strong></td><td>Mahasiswa berstatus Eligible, jadwal distribusi aktif, dan minimal satu barang seragam dicentang untuk diserahkan.</td></tr>
        <tr><td><strong>Input</strong></td><td>Array item seragam yang dicentang, pilihan ukuran fisik aktual (*actual size*), kuantitas penyerahan, dan catatan manual staf.</td></tr>
        <tr><td><strong>Alur Normal</strong></td><td>1. Staf mencentang seragam yang pas dicoba mahasiswa; 2. Klik tombol "Simpan &amp; Serahkan"; 3. Sistem membuka transaksi basis data dan mengunci baris mahasiswa via <code>lockForUpdate()</code>; 4. Memverifikasi sisa saldo hak; 5. Memotong kuantitas dari batch kedatangan vendor tertua (FIFO); 6. Menghitung blended HPP; 7. Menulis kartu stok <code>stock_movements</code>; 8. Menyimpan transaksi distribusi; 9. Commit database dan redirect dengan pesan sukses.</td></tr>
        <tr><td><strong>Alur Alternatif</strong></td><td>Jika stok gudang untuk ukuran tersebut ternyata kosong, sistem secara otomatis menandai barang sebagai tertunda (*deferred*) dan hanya memproses barang yang tersedia.</td></tr>
        <tr><td><strong>Validasi</strong></td><td>Mahasiswa belum pernah mengambil barang pada jadwal tersebut. Kuantitas penyerahan tidak boleh melebihi sisa kuota hak entitlement.</td></tr>
        <tr><td><strong>Output</strong></td><td>Record transaksi pada <code>distribution_transactions</code> (status 'completed') dan rincian barang pada <code>distribution_items</code>.</td></tr>
        <tr><td><strong>Data yang Terdampak</strong></td><td><code>distribution_transactions</code>, <code>distribution_items</code>, <code>stock_batches</code>, <code>stock_movements</code>, <code>stock_balances</code>, dan <code>student_size_histories</code>.</td></tr>
        <tr><td><strong>Hak Akses</strong></td><td>Role <code>staff</code>, <code>admin</code>, dan <code>super_admin</code>.</td></tr>
        <tr><td><strong>Kegagalan &amp; Error</strong></td><td>Pesan error: "Mahasiswa ini sudah pernah mengambil seragam pada jadwal ini" atau "Status pembayaran belum lunas".</td></tr>
        <tr><td><strong>Test Case</strong></td><td>Lakukan serah terima 1 jas almamater ukuran L; verifikasi stok batch tertua berkurang 1 unit dan kartu stok bertambah 1 baris tipe 'OUT'.</td></tr>
        <tr><td><strong>Status Implementasi</strong></td><td><span class="badge badge-success">Terverifikasi Selesai (100% Implemented)</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="chapter" id="bab-21">
  <h2 class="chapter-title">Bab 21 — Inventory Module (Spesifikasi Fitur Lengkap 14-Poin)</h2>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">FITUR INV-01</span>
      <span class="feature-name">Pencatatan Surat Jalan Barang Masuk Vendor (Stock Receive)</span>
    </div>
    <table class="data-table">
      <tbody>
        <tr><td style="width: 25%;"><strong>Tujuan</strong></td><td>Mencatat penerimaan kiriman seragam dari pabrik konveksi rekanan, membentuk lot batch persediaan FIFO baru, dan menginisialisasi saldo stok gudang.</td></tr>
        <tr><td><strong>Aktor</strong></td><td>Admin Gudang &amp; Logistik (Role: <code>admin</code>, <code>super_admin</code>).</td></tr>
        <tr><td><strong>Prasyarat</strong></td><td>Data vendor dan master barang varian ukuran (SKU) telah terdaftar di sistem.</td></tr>
        <tr><td><strong>Input</strong></td><td>Nomor surat jalan vendor, tanggal terima, nama vendor, daftar item barang, ukuran SKU, kuantitas masuk, dan harga beli modal per unit.</td></tr>
        <tr><td><strong>Alur Normal</strong></td><td>1. Admin membuka <code>/inventory/stock-receive/create</code>; 2. Mengisi nomor referensi surat jalan dan vendor; 3. Memilih item dan varian ukuran; 4. Memasukkan kuantitas dan harga perolehan unit; 5. Klik "Simpan Penerimaan"; 6. Sistem membuat record <code>stock_receives</code>, membentuk entitas <code>stock_batches</code> baru, menulis mutasi masuk di <code>stock_movements</code> (tipe 'IN'), dan memperbarui saldo di <code>stock_balances</code>.</td></tr>
        <tr><td><strong>Alur Alternatif</strong></td><td>Jika nomor surat jalan telah terdaftar sebelumnya, sistem melempar validasi error duplikasi nomor referensi.</td></tr>
        <tr><td><strong>Validasi</strong></td><td>Nomor surat jalan unik, kuantitas minimal 1, harga perolehan unit minimal 0, dan varian ukuran wajib valid.</td></tr>
        <tr><td><strong>Output</strong></td><td>Surat bukti penerimaan barang masuk dan pertambahan saldo persediaan fisik di gudang.</td></tr>
        <tr><td><strong>Data yang Terdampak</strong></td><td><code>stock_receives</code>, <code>stock_receive_items</code>, <code>stock_batches</code>, <code>stock_movements</code>, dan <code>stock_balances</code>.</td></tr>
        <tr><td><strong>Hak Akses</strong></td><td>Role <code>admin</code> dan <code>super_admin</code>.</td></tr>
        <tr><td><strong>Kegagalan &amp; Error</strong></td><td>Pesan error: "Nomor referensi surat jalan telah terdaftar di sistem".</td></tr>
        <tr><td><strong>Test Case</strong></td><td>Input penerimaan 100 pcs jas ukuran M dengan HPP Rp 150.000; verifikasi terbentuk batch baru dengan quantity_remaining = 100 dan saldo bertambah 100.</td></tr>
        <tr><td><strong>Status Implementasi</strong></td><td><span class="badge badge-success">Terverifikasi Selesai (100% Implemented)</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="chapter" id="bab-22">
  <h2 class="chapter-title">Bab 22 — Reporting Module (Spesifikasi Fitur Lengkap 14-Poin)</h2>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">FITUR REP-01</span>
      <span class="feature-name">Kalkulasi Sales Dashboard &amp; Analisis Laba Kotor (GPM)</span>
    </div>
    <table class="data-table">
      <tbody>
        <tr><td style="width: 25%;"><strong>Tujuan</strong></td><td>Menyajikan visibilitas keuangan eksekutif mengenai total omzet pendapatan, total pengeluaran HPP persediaan, dan margin laba kotor real-time.</td></tr>
        <tr><td><strong>Aktor</strong></td><td>Direktorat Keuangan &amp; Manajemen Rektorat (Role: <code>admin</code>, <code>super_admin</code>).</td></tr>
        <tr><td><strong>Prasyarat</strong></td><td>Telah terjadi transaksi penyerahan seragam pada modul distribusi.</td></tr>
        <tr><td><strong>Input</strong></td><td>Akses ke rute <code>/report/gpm</code> atau <code>/dashboard</code> (untuk peran Admin/Finance).</td></tr>
        <tr><td><strong>Alur Normal</strong></td><td>1. User membuka halaman Sales Dashboard; 2. <code>ReportService::getSalesDashboardViewData()</code> mengkueri tabel transaksi distribusi; 3. Menghitung omzet dari akumulasi harga jual patokan; 4. Menghitung HPP dari akumulasi blended HPP batch FIFO; 5. Menghitung GPM kotor dan persentase margin; 6. Menyajikan grafik distribusi harian dan tabel ringkasan per kategori barang.</td></tr>
        <tr><td><strong>Alur Alternatif</strong></td><td>User dapat menyaring laporan berdasarkan rentang tanggal tertentu atau mengunduh lembar kerja Excel via tombol "Export Excel".</td></tr>
        <tr><td><strong>Validasi</strong></td><td>Hanya transaksi dengan status 'completed' yang diperhitungkan dalam kalkulasi omzet dan HPP.</td></tr>
        <tr><td><strong>Output</strong></td><td>Tampilan visual dashboard analitis dan berkas spreadsheet .xlsx yang siap dipresentasikan.</td></tr>
        <tr><td><strong>Data yang Terdampak</strong></td><td>Tidak ada mutasi data (operasi murni agregasi kueri analitis).</td></tr>
        <tr><td><strong>Hak Akses</strong></td><td>Role <code>admin</code> dan <code>super_admin</code>.</td></tr>
        <tr><td><strong>Kegagalan &amp; Error</strong></td><td>Pesan error: "Tidak ada data transaksi pada rentang tanggal yang dipilih".</td></tr>
        <tr><td><strong>Test Case</strong></td><td>Eksekusi kalkulasi GPM setelah 50 transaksi selesai; verifikasi total omzet dikurangi total HPP menghasilkan marjin laba kotor yang akurat secara matematis.</td></tr>
        <tr><td><strong>Status Implementasi</strong></td><td><span class="badge badge-success">Terverifikasi Selesai (100% Implemented)</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="chapter" id="bab-23">
  <h2 class="chapter-title">Bab 23 — Profile Management (Spesifikasi Fitur Lengkap 14-Poin)</h2>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">FITUR PRF-01</span>
      <span class="feature-name">Pembaruan Alamat Email Kampus Terverifikasi Kode OTP</span>
    </div>
    <table class="data-table">
      <tbody>
        <tr><td style="width: 25%;"><strong>Tujuan</strong></td><td>Memfasilitasi pembaruan alamat email resmi mahasiswa dengan standar keamanan tinggi menggunakan verifikasi kode OTP 6-digit.</td></tr>
        <tr><td><strong>Aktor</strong></td><td>Mahasiswa &amp; Seluruh Pengguna Terdaftar.</td></tr>
        <tr><td><strong>Prasyarat</strong></td><td>Pengguna telah login dan memiliki akses ke kotak masuk email baru yang akan didaftarkan.</td></tr>
        <tr><td><strong>Input</strong></td><td>Konfirmasi kata sandi saat ini, alamat email kampus baru, dan 6 digit kode OTP verifikasi.</td></tr>
        <tr><td><strong>Alur Normal</strong></td><td>1. User membuka <code>/profile/email/change</code>; 2. Memasukkan password saat ini untuk re-autentikasi; 3. Memasukkan alamat email baru; 4. Sistem meng-generate OTP 6-digit dan mengirimkannya ke email baru via SMTP; 5. User memasukkan kode OTP pada form verifikasi; 6. Sistem memvalidasi kode dan memperbarui kolom <code>email</code> di tabel users serta <code>email_kampus</code> di tabel students.</td></tr>
        <tr><td><strong>Alur Alternatif</strong></td><td>Jika kode OTP salah atau kedaluwarsa (&gt; 15 menit), sistem menolak pembaruan dan mengizinkan permintaan kirim ulang OTP maksimal 3 kali per jam.</td></tr>
        <tr><td><strong>Validasi</strong></td><td>Alamat email baru wajib berformat email valid dan belum pernah terdaftar oleh akun lain di sistem.</td></tr>
        <tr><td><strong>Output</strong></td><td>Alamat email akun resmi diperbarui dan status verifikasi ter-update.</td></tr>
        <tr><td><strong>Data yang Terdampak</strong></td><td>Tabel <code>users</code> (UPDATE email), tabel <code>students</code> (UPDATE email_kampus), tabel <code>otp_codes</code>.</td></tr>
        <tr><td><strong>Hak Akses</strong></td><td>Seluruh pengguna terautentikasi untuk profil mereka masing-masing.</td></tr>
        <tr><td><strong>Kegagalan &amp; Error</strong></td><td>Pesan error: "Kode OTP salah atau telah kedaluwarsa" atau "Kata sandi konfirmasi tidak cocok".</td></tr>
        <tr><td><strong>Test Case</strong></td><td>Uji alur pergantian email; verifikasi OTP terkirim, divalidasi, dan email akun berhasil berganti ke alamat baru.</td></tr>
        <tr><td><strong>Status Implementasi</strong></td><td><span class="badge badge-success">Terverifikasi Selesai (100% Implemented)</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="chapter" id="bab-24">
  <h2 class="chapter-title">Bab 24 — Authentication and Authorization (Spesifikasi Fitur Lengkap 14-Poin)</h2>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">FITUR AUT-01</span>
      <span class="feature-name">Otentikasi Login Sesi Multi-Role &amp; Pengecekan Wajib Ganti Password</span>
    </div>
    <table class="data-table">
      <tbody>
        <tr><td style="width: 25%;"><strong>Tujuan</strong></td><td>Memvalidasi identitas pengguna, membentuk sesi web aman, dan memaksa penggantian kata sandi bawaan pada saat login pertama kali.</td></tr>
        <tr><td><strong>Aktor</strong></td><td>Seluruh Pengguna (Mahasiswa, Staf Gudang, Admin, Super Admin).</td></tr>
        <tr><td><strong>Prasyarat</strong></td><td>Pengguna memiliki akun aktif yang terdaftar di basis data <code>users</code>.</td></tr>
        <tr><td><strong>Input</strong></td><td>Identifier login (NIM Mahasiswa atau Alamat Email Admin/Staf) dan Kata Sandi.</td></tr>
        <tr><td><strong>Alur Normal</strong></td><td>1. Pengguna membuka form login di <code>/login</code>; 2. Memasukkan kredensial; 3. Klik "Masuk"; 4. Sistem memverifikasi hash kata sandi bcrypt; 5. Menjalankan regenerasi ID sesi peramban; 6. Memeriksa kolom <code>must_change_password</code>; 7. Jika true, paksa redirect ke <code>/password/change</code>; Jika false, arahkan ke dashboard peran masing-masing.</td></tr>
        <tr><td><strong>Alur Alternatif</strong></td><td>Jika kredensial salah hingga 5 kali dalam rentang 1 menit, sistem mengaktifkan proteksi rate limit throttle dan mengunci form selama 60 detik.</td></tr>
        <tr><td><strong>Validasi</strong></td><td>Identifier dan kata sandi wajib diisi. Akun wajib memiliki flag <code>is_active = true</code>.</td></tr>
        <tr><td><strong>Output</strong></td><td>Sesi terenkripsi HTTPOnly dan pengalihan ke antarmuka kerja pengguna.</td></tr>
        <tr><td><strong>Data yang Terdampak</strong></td><td>Tabel <code>sessions</code> (pembentukan sesi baru).</td></tr>
        <tr><td><strong>Hak Akses</strong></td><td>Akses publik bagi tamu (*guest*).</td></tr>
        <tr><td><strong>Kegagalan &amp; Error</strong></td><td>Pesan error: "Kredensial yang diberikan tidak cocok dengan data kami" atau "Terlalu banyak percobaan login. Silakan coba lagi dalam 60 detik".</td></tr>
        <tr><td><strong>Test Case</strong></td><td>Uji login akun mahasiswa baru; verifikasi langsung diarahkan ke halaman wajib ganti password dan tidak bisa mengakses dashboard sebelum password diubah.</td></tr>
        <tr><td><strong>Status Implementasi</strong></td><td><span class="badge badge-success">Terverifikasi Selesai (100% Implemented)</span></td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- ============================================== -->
<!-- BAGIAN IV: DOKUMENTASI TEKNIS DAN ARSITEKTUR -->
<!-- ============================================== -->
<div class="part-divider">
  <div class="part-number">Bagian IV</div>
  <h1 class="part-title">Dokumentasi Teknis dan Arsitektur</h1>
  <div class="part-subtitle">Spesifikasi Arsitektur Sistem, Tumpukan Teknologi, Struktur Direktori, Desain Komponen, dan Skema Payload API</div>
</div>

<div class="chapter" id="bab-25">
  <h2 class="chapter-title">Bab 25 — Technology Stack &amp; Dependensi Versi</h2>
  <h3 class="subchapter-title">25.1 Ekosistem Perangkat Lunak Inti</h3>
  <p>Horizon UniStock dibangun di atas fondasi teknologi web modern yang teruji stabilitasnya pada skala enterprise:</p>
  <ul>
    <li><strong>Bahasa Pemrograman:</strong> PHP versi 8.3 / 8.4 (Typed Properties, Readonly Classes, Match Expressions, dan Enum).</li>
    <li><strong>Web Application Framework:</strong> Laravel Framework versi 12.63.0 (kompatibel penuh dengan Laravel 13.x dev-line).</li>
    <li><strong>Sistem Basis Data Relasional:</strong> MySQL Community Server 8.0+ dengan mesin penyimpanan InnoDB yang mendukung isolasi transaksi ACID penuh, foreign key constraints, dan pessimistic row locking.</li>
    <li><strong>Mesin Styling Frontend:</strong> Tailwind CSS versi 3.1.0 dengan arsitektur utility-first dan plugin form resmi <code>@tailwindcss/forms</code>.</li>
    <li><strong>Mesin Reaktivitas Klien:</strong> Alpine.js versi 3.14.0 untuk interaktivitas modal dialog, debounce pencarian, dan checklist dinamis tanpa overhead SPA.</li>
    <li><strong>Mesin Bundling Aset:</strong> Vite versi 7.0.7 dengan plugin integrasi <code>laravel-vite-plugin</code> versi 2.0.0.</li>
    <li><strong>Pustaka Manajemen Hak Akses:</strong> <code>spatie/laravel-permission</code> versi 8.1.</li>
    <li><strong>Pustaka Generator QR Code:</strong> <code>bacon/bacon-qr-code</code> versi 3.0 dengan renderer GD Library.</li>
    <li><strong>Pustaka Pemindai Barcode Klien:</strong> <code>html5-qrcode</code> versi 2.3.8.</li>
    <li><strong>Pustaka Ekspor-Impor Spreadsheet:</strong> <code>maatwebsite/excel</code> versi 4.0.</li>
  </ul>
</div>

<div class="chapter" id="bab-26">
  <h2 class="chapter-title">Bab 26 — System Architecture &amp; Lapisan Komponen</h2>
  <h3 class="subchapter-title">26.1 Pola Monolitik Modular Berbasis Layanan (Service-Layer Pattern)</h3>
  <p>Untuk menghindari jebakan arsitektur "Fat Controller" atau "Anemic Domain Model", UniStock membagi sistem ke dalam 4 lapisan independen:</p>
  <ol>
    <li><strong>Lapisan Presentasi (Presentation Layer):</strong> Terdiri dari Blade Views, Tailwind CSS, Alpine.js components, dan library client-side. Bertanggung jawab murni atas penyajian antarmuka dan penangkapan input pengguna.</li>
    <li><strong>Lapisan Transport &amp; Validasi HTTP (HTTP Transport Layer):</strong> Terdiri dari Route Definitions, Middleware Pipeline (Auth, Roles, Throttle), Form Request Validators, dan Thin Controllers. Controller hanya bertugas memetakan input HTTP ke parameter Service dan mengembalikan respons View/JSON.</li>
    <li><strong>Lapisan Layanan Domain (Domain Service Layer):</strong> Jantung logika bisnis sistem. Terdiri dari 16 kelas Domain Service (<code>DistributionService</code>, <code>StockService</code>, <code>StudentSizeService</code>, <code>ReportService</code>, dll) yang mengeksekusi aturan bisnis, algoritma penilaian persediaan, dan transaksi basis data.</li>
    <li><strong>Lapisan Persistensi &amp; Akses Data (Persistence Layer):</strong> Terdiri dari 42 Eloquent Model yang merepresentasikan tabel-tabel basis data MySQL lengkap dengan relasi relasional, query scopes, dan mutator/accessor.</li>
  </ol>
</div>

<div class="chapter" id="bab-27">
  <h2 class="chapter-title">Bab 27 — Repository Structure &amp; Konvensi File</h2>
  <h3 class="subchapter-title">27.1 Peta Direktori Sumber Daya Aplikasi</h3>
  <p>Struktur folder sistem ditata secara modular untuk memudahkan navigasi pengembang maupun agen AI:</p>
  <pre><code>c:\laragon\www\horizon-unistock\
├── app/
│   ├── Enums/                 # Definisi PHP Enums resmi (Role.php)
│   ├── Exports/               # Kelas ekspor Excel (StudentExport, CredentialsExport, ReportExport)
│   ├── Helpers.php            # Fungsi helper global (escapeLike, formatCurrency, dsb)
│   ├── Http/
│   │   ├── Controllers/       # Controller per modul bisnis (Auth, Master, Staff, Student, Finance, Inventory)
│   │   ├── Middleware/        # Middleware autentikasi, Spatie role check, password changed check
│   │   └── Requests/          # Form Request validasi ketat (StudentRequest, DistributionRequest)
│   ├── Imports/               # Kelas impor Excel Maatwebsite (StudentImport, EntitlementImport)
│   ├── Models/                # 42 Eloquent Models (Domain Entities)
│   └── Services/              # 16 Domain Service Classes (Distribution, Stock, QrCode, Report)
├── config/                    # File konfigurasi Laravel (auth, permission, excel, mail, database)
├── database/
│   ├── migrations/            # 58 berkas migrasi database InnoDB MySQL
│   └── seeders/               # Database seeders (RolePermission, StudentLevel, Superadmin)
├── Horizon-UniStock-Documentation/ # Dokumentasi Induk & Generator PDF Sistem
├── public/                    # Root publik web server (index.php, images/logo.png, build assets)
├── resources/
│   ├── css/                   # Berkas Tailwind CSS induk (app.css)
│   ├── js/                    # Modul JavaScript klien & Alpine.js components (app.js)
│   └── views/                 # Blade templates terstruktur per modul fungsional
├── routes/
│   ├── auth.php               # Rute autentikasi Breeze (login, reset password, logout)
│   └── web.php                # Rute web utama terproteksi middleware
└── scripts/                   # Master data asli universitas (Dummy - Inventory Management.xlsx)</code></pre>
</div>

<div class="chapter" id="bab-28">
  <h2 class="chapter-title">Bab 28 — Frontend Architecture &amp; Desain Komponen</h2>
  <h3 class="subchapter-title">28.1 Filosofi Blade + Tailwind + Alpine.js</h3>
  <p>Arsitektur antarmuka UniStock mengeliminasi friksi umum arsitektur Single Page Application (SPA) yang memisahkan frontend dan backend secara kaku:</p>
  <ul>
    <li><strong>Server-Side Rendering yang Gesit:</strong> Seluruh tampilan dirender oleh mesin Blade di sisi server dengan kecepatan milidetik, menghasilkan payload HTML murni yang ramah peramban dan tidak membebani prosesor smartphone mahasiswa.</li>
    <li><strong>Sistem Desain Utilitas Tailwind:</strong> Seluruh styling antarmuka menggunakan kelas utilitas Tailwind yang dikompilasi secara efisien melalui PurgeCSS saat build Vite, menghasilkan ukuran file CSS akhir yang sangat kecil (&lt; 25 KB gzipped).</li>
    <li><strong>Reaktivitas Mikro dengan Alpine.js:</strong> Komponen-komponen yang membutuhkan interaktivitas dinamis (seperti membuka/menutup modal dialog, filter instan pada form pembuatan jadwal distribusi, dan counter karakter) dikendalikan langsung oleh Alpine.js via atribut deklaratif HTML (<code>x-data</code>, <code>x-show</code>, <code>@click</code>).</li>
  </ul>
</div>

<div class="chapter" id="bab-29">
  <h2 class="chapter-title">Bab 29 — Backend Architecture &amp; Service Layer Pattern</h2>
  <h3 class="subchapter-title">29.1 Pemisahan Tanggung Jawab &amp; Prinsip Single Responsibility</h3>
  <p>Untuk menjamin stabilitas dan kemudahan pengujian, backend UniStock menerapkan pola Service Layer yang ketat:</p>
  <ul>
    <li><strong>Pola Thin Controller:</strong> Controller tidak boleh memuat query database yang rumit atau kalkulasi harga modal secara langsung. Controller hanya bertugas memvalidasi request dan memanggil method domain service terkait.</li>
    <li><strong>Isolasi Logika Domain pada Service Classes:</strong>
      <ul>
        <li><code>DistributionService:</code> Mengorkestrasi aturan transaksi serah terima seragam, verifikasi kelayakan keuangan, penyesuaian ukuran fisik, dan pemanggilan pemotongan persediaan FIFO.</li>
        <li><code>StockService:</code> Mengelola transaksi penerimaan barang masuk vendor, pencatatan batch persediaan, pemotongan stok tertua FIFO, dan kalkulasi rata-rata tertimbang blended HPP.</li>
        <li><code>StudentSizeService:</code> Menangani resolusi opsi ukuran baju dan sepatu dinas, validasi batas pengubahan mandiri, dan pencatatan audit perubahan ukuran.</li>
        <li><code>ReportService:</code> Mengkalkulasi data metrik Sales Dashboard, analisis margin laba kotor (GPM), dan pembentukan data laporan ekspor Excel.</li>
      </ul>
    </li>
  </ul>
</div>

<div class="chapter" id="bab-30">
  <h2 class="chapter-title">Bab 30 — Database Schema &amp; Spesifikasi Mesin InnoDB</h2>
  <h3 class="subchapter-title">30.1 Konfigurasi Mesin Penyimpanan &amp; Integritas Relasional</h3>
  <p>Basis data Horizon UniStock diatur menggunakan konfigurasi database relasional tingkat tinggi:</p>
  <ul>
    <li><strong>Mesin Penyimpanan InnoDB:</strong> Seluruh 58 tabel basis data wajib menggunakan engine InnoDB yang mendukung transaksi multi-tabel ACID, foreign key constraints bertingkat, dan row-level locking.</li>
    <li><strong>Character Set &amp; Collation:</strong> Menggunakan <code>utf8mb4</code> dengan collation <code>utf8mb4_unicode_ci</code> untuk memastikan dukungan penuh penyimpanan karakter internasional, simbol, dan format tanda baca tanpa risiko korupsi data.</li>
    <li><strong>Strategi Indeks Komposit:</strong> Tabel-tabel transaksi bervolume tinggi dilengkapi dengan indeks gabungan (*composite indexes*) untuk menjamin waktu pencarian tetap konstan di bawah 50 milidetik saat melayani ribuan mahasiswa. Contoh: indeks gabungan <code>(student_id, schedule_id, status)</code> pada tabel <code>distribution_transactions</code>.</li>
  </ul>
</div>

<div class="chapter" id="bab-31">
  <h2 class="chapter-title">Bab 31 — Data Relationships &amp; Integritas Referensial</h2>
  <h3 class="subchapter-title">31.1 Hubungan Antartabel &amp; Penanganan Penghapusan (Foreign Key Cascades)</h3>
  <p>Integritas referensial data diatur secara teliti untuk mencegah hilangnya jejak audit operasional:</p>
  <ul>
    <li><strong>Penghapusan Bertingkat (Cascade on Delete):</strong> Diterapkan hanya pada tabel-tabel anak (*child entities*) yang sepenuhnya bergantung pada entitas induknya, seperti <code>distribution_items</code> terhadap <code>distribution_transactions</code> dan <code>entitlement_items</code> terhadap <code>entitlements</code>.</li>
    <li><strong>Penetapan Bernilai Kosong (Set Null on Delete):</strong> Diterapkan pada relasi historis dan referensi master data. Contoh: jika data seorang Staf dihapus dari sistem, kolom <code>staff_id</code> pada transaksi penyerahan seragam diubah menjadi `NULL`, namun seluruh catatan transaksi, jam serah terima, dan histori seragam tetap utuh tersimpan demi akuntabilitas pembukuan.</li>
  </ul>
</div>

<div class="chapter" id="bab-32">
  <h2 class="chapter-title">Bab 32 — API Documentation &amp; Route Catalog Terpadu</h2>
  <h3 class="subchapter-title">32.1 Spesifikasi Payload JSON Endpoint Kunci</h3>
  <p>Berikut adalah skema payload request dan respons JSON resmi untuk endpoint-endpoint integrasi asinkron utama:</p>

  <h4 style="color: #005A36; margin-bottom: 2mm;">1. Endpoint Pencarian Loket: `POST /distribution/search`</h4>
  <p><strong>Request Body Payload (JSON / Form Data):</strong></p>
  <pre><code>{
  "query": "20260001"   // String NIM mahasiswa yang dipindai kamera scanner
}</code></pre>
  <p><strong>Response Payload (Mahasiswa Ditemukan &amp; Jadwal Aktif Tersedia — HTTP 200 OK):</strong></p>
  <pre><code>{
  "found": true,
  "redirect": "https://unistok.nirwana.biz.id/distribution/student/20260001"
}</code></pre>
  <p><strong>Response Payload (Mahasiswa Ditemukan Namun Perlu Pilih Jadwal — HTTP 200 OK):</strong></p>
  <pre><code>{
  "found": true,
  "needs_schedule_selection": true,
  "redirect": "https://unistok.nirwana.biz.id/distribution/student/20260001/select-schedule"
}</code></pre>
  <p><strong>Response Payload (Mahasiswa Tidak Ditemukan — HTTP 200 OK):</strong></p>
  <pre><code>{
  "found": false,
  "message": "Mahasiswa tidak ditemukan. Pastikan NIM valid."
}</code></pre>

  <h4 style="color: #005A36; margin-bottom: 2mm; margin-top: 4mm;">2. Endpoint Submit Transaksi Penyerahan: `POST /distribution/process`</h4>
  <p><strong>Request Body Payload (Form Data POST):</strong></p>
  <pre><code>{
  "student_id": 101,
  "schedule_id": 5,
  "notes": "Fitting jas pas ukuran L, penyerahan lancar",
  "items": [
    {
      "item_id": 12,
      "base_code": "UNF-ALM",
      "expected_size": "M",
      "actual_size": "L",
      "quantity": 1
    },
    {
      "item_id": 14,
      "base_code": "SHO-DINAS",
      "expected_size": "41",
      "actual_size": "41",
      "quantity": 1
    }
  ]
}</code></pre>
  <p><strong>Response Redirect:</strong> Redirect ke <code>/distribution/scan</code> dengan Flash Session <code>success: "Distribusi berhasil dicatat untuk 20260001 - Andi Pratama."</code>.</p>
</div>

<div class="chapter" id="bab-33">
  <h2 class="chapter-title">Bab 33 — Authentication Flow &amp; Manajemen Sesi</h2>
  <h3 class="subchapter-title">33.1 Daur Hidup Sesi Pengguna &amp; Keamanan Kuki</h3>
  <p>Alur autentikasi UniStock mengadopsi mekanisme sesi terenkripsi sisi server (*server-side encrypted session*):</p>
  <ul>
    <li><strong>Kuki HTTPOnly dan SameSite:</strong> Sesi pengguna disimpan dalam cookie peramban dengan atribut <code>HttpOnly</code> (mencegah pencurian kuki melalui serangan XSS JavaScript) dan <code>SameSite=Lax</code> (mencegah eksploitasi serangan CSRF lintas domain).</li>
    <li><strong>Siklus Rotasi ID Sesi:</strong> Setiap kali pengguna berhasil login, sistem mengeksekusi <code>$request-&gt;session()-&gt;regenerate()</code> untuk mengacak ID sesi baru, sepenuhnya menangkal teknik serangan *Session Fixation*.</li>
  </ul>
</div>

<div class="chapter" id="bab-34">
  <h2 class="chapter-title">Bab 34 — File and Asset Handling (Tata Kelola Berkas)</h2>
  <h3 class="subchapter-title">34.1 Manajemen Penyimpanan Symlink &amp; Streaming Citra Dinamis</h3>
  <p>Penanganan berkas digital diatur untuk menjaga kebersihan ruang simpan server dan efisiensi memori:</p>
  <ul>
    <li><strong>Symlink Storage Publik:</strong> Folder penyimpanan publik dihubungkan melalui perintah <code>php artisan storage:link</code> yang memetakan <code>storage/app/public/</code> ke <code>public/storage/</code>.</li>
    <li><strong>Zero-Disk QR Rendering:</strong> Pembangkitan QR Code identitas mahasiswa tidak pernah menulis file gambar fisik ke hard drive server, melainkan di-encode langsung ke memori RAM dalam format Base64 Data URL, menghasilkan arsitektur yang sangat cepat dan bebas dari akumulasi file sampah (*temporary garbage files*).</li>
  </ul>
</div>

<div class="chapter" id="bab-35">
  <h2 class="chapter-title">Bab 35 — External Integrations &amp; Layanan Jaringan</h2>
  <h3 class="subchapter-title">35.1 Konfigurasi Pengiriman Surat Elektronik SMTP</h3>
  <p>UniStock terintegrasi dengan server surat elektronik SMTP kampus untuk mendistribusikan kredensial akun dan kode verifikasi OTP:</p>
  <ul>
    <li><strong>Konfigurasi Dinamis dari Basis Data:</strong> Pengaturan kredensial host, port, username, password, dan enkripsi TLS/SSL tidak di-hardcode pada file konfigurasi statis, melainkan disimpan pada tabel <code>smtp_settings</code> dan dikelola langsung oleh Super Admin melalui antarmuka web.</li>
    <li><strong>Fitur Uji Soket Real-Time:</strong> Sistem menyediakan tombol aksi "Test SMTP" yang melakukan pengujian konektivitas soket jaringan ke server mail kampus dan mengirimkan email simulasi untuk memverifikasi kesiapan pengiriman sebelum pengumuman massal dibuka.</li>
  </ul>
</div>

<div class="chapter" id="bab-36">
  <h2 class="chapter-title">Bab 36 — Configuration Management &amp; Variabel Lingkungan</h2>
  <h3 class="subchapter-title">36.1 Katalog Parameter Lingkungan (.env)</h3>
  <p>Seluruh parameter operasional sistem diatur melalui berkas konfigurasi lingkungan <code>.env</code>:</p>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25%;">Variabel Lingkungan</th>
        <th style="width: 35%;">Nilai Contoh / Rekomendasi</th>
        <th style="width: 40%;">Fungsi &amp; Dampak Operasional</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>APP_ENV</code></td>
        <td><code>production</code></td>
        <td>Menonaktifkan tampilan pesan kesalahan debug sensitif di layar pengguna akhir.</td>
      </tr>
      <tr>
        <td><code>APP_DEBUG</code></td>
        <td><code>false</code></td>
        <td>Mencegah kebocoran stack trace dan kredensial basis data saat terjadi error runtime.</td>
      </tr>
      <tr>
        <td><code>APP_URL</code></td>
        <td><code>https://unistok.nirwana.biz.id</code></td>
        <td>Domain resmi universitas untuk pembentukan URL aset dan tautan reset password.</td>
      </tr>
      <tr>
        <td><code>DB_CONNECTION</code></td>
        <td><code>mysql</code></td>
        <td>Driver koneksi basis data relasional MySQL InnoDB.</td>
      </tr>
      <tr>
        <td><code>SUPERADMIN_EMAIL</code></td>
        <td><code>admin@horizon-unistock.ac.id</code></td>
        <td>Alamat email resmi untuk inisialisasi akun Super Admin bawaan saat seeder dieksekusi.</td>
      </tr>
    </tbody>
  </table>
</div>
"""
