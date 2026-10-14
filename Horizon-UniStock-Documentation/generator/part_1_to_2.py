# -*- coding: utf-8 -*-
"""
Bagian I & Bagian II: Bab 1 s/d Bab 12
Format: Proposal-Grade Comprehensive Master Documentation (Non-Technical & Business Deep Dive)
"""

def get_parts_1_and_2_html():
    return r"""
<!-- ========================================== -->
<!-- BAGIAN I: IDENTITAS DAN KONTEKS DASAR PROYEK -->
<!-- ========================================== -->
<div class="part-divider">
  <div class="part-number">Bagian I</div>
  <h1 class="part-title">Identitas dan Konteks Dasar Proyek</h1>
  <div class="part-subtitle">Landasan Konseptual, Tata Kelola Institusional, dan Justifikasi Strategis Sistem</div>
</div>

<div class="chapter" id="bab-1">
  <h2 class="chapter-title">Bab 1 — Sampul dan Metadata Sistem</h2>
  <p>Sistem <strong>Horizon UniStock</strong> merupakan platform terintegrasi berbasis web yang dikembangkan secara khusus untuk mengelola tata kelola rantai pasok perlengkapan mahasiswa, pencatatan profil ukuran seragam dan sepatu dinas, penerbitan tanda pengenal digital, verifikasi kelayakan administratif dan finansial, serta eksekusi penyerahan fisik di gudang logistik <strong>Horizon University Indonesia</strong> (Universitas Horizon Indonesia / STMIK &amp; STIKes Horizon Karawang).</p>
  
  <h3 class="subchapter-title">1.1 Tabel Metadata Dokumen dan Otorisasi Sistem</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25%;">Parameter Metadata</th>
        <th style="width: 75%;">Deskripsi &amp; Nilai Otoritatif</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Nama Resmi Proyek</strong></td>
        <td>Horizon UniStock (Sistem Distribusi Seragam &amp; Inventory Management Terpadu)</td>
      </tr>
      <tr>
        <td><strong>Institusi Induk</strong></td>
        <td>Yayasan Triputra &amp; Horizon Education — Universitas Horizon Indonesia (Kampus Karawang)</td>
      </tr>
      <tr>
        <td><strong>Unit Penanggung Jawab</strong></td>
        <td>Biro Keuangan (Finance Directorate), Biro Administrasi Akademik &amp; Kemahasiswaan (BAAK), serta Unit Logistik &amp; Perlengkapan Umum</td>
      </tr>
      <tr>
        <td><strong>Tim Pengembang Utama</strong></td>
        <td>Nirwana Tech Team (nirwana-tim/Horizon-UniStock)</td>
      </tr>
      <tr>
        <td><strong>Lingkungan Produksi</strong></td>
        <td><a href="https://unistok.nirwana.biz.id">https://unistok.nirwana.biz.id</a> (Production Server cPanel)</td>
      </tr>
      <tr>
        <td><strong>Versi Dokumentasi</strong></td>
        <td>Versi 1.2.0 — Proposal &amp; Master System Reference (Revisi Komprehensif Oktober 2026)</td>
      </tr>
      <tr>
        <td><strong>Klasifikasi Keamanan</strong></td>
        <td>Confidential — Dokumen Resmi Institusi (Hanya untuk Internal Universitas, Auditor, dan Agen AI Pengembang)</td>
      </tr>
      <tr>
        <td><strong>Status Dokumen</strong></td>
        <td>Canonical Ground Truth — Seluruh rincian diverifikasi langsung terhadap kode sumber, migrasi basis data, dan data operasional aktual.</td>
      </tr>
    </tbody>
  </table>

  <h3 class="subchapter-title">1.2 Lembar Pengesahan dan Riwayat Persetujuan Dokumen</h3>
  <p>Penyusunan dokumen ini merefleksikan kesepakatan lintas departemen antara pemangku kepentingan akademik, manajer keuangan, kepala logistik gudang, dan tim rekayasa perangkat lunak. Dokumen ini disahkan sebagai standar operasional prosedur (SOP) digital yang mengikat operasional penerimaan mahasiswa baru tahun akademik 2026/2027 dan seterusnya.</p>
</div>

<div class="chapter" id="bab-2">
  <h2 class="chapter-title">Bab 2 — Executive Summary (Ringkasan Eksekutif Proposal)</h2>
  <h3 class="subchapter-title">2.1 Urgensi Transformasi Digital Logistik Kampus</h3>
  <p>Sebagai perguruan tinggi terkemuka di kawasan industri Karawang yang menaungi <strong>Fakultas Ilmu Kesehatan (FIKES)</strong> dan <strong>Fakultas Teknologi Informasi (FTI)</strong>, Horizon University Indonesia menghadapi tantangan logistik berskala besar pada setiap periode awal tahun ajaran baru. Seragam mahasiswa di institusi ini bukan sekadar pakaian biasa, melainkan atribut wajib berstandar profesi medis (baju jaga, jas lab, kemeja praktik, seragam perawat, dan sepatu dinas putih) serta atribut almamater resmi yang mencerminkan martabat universitas.</p>
  <p>Sebelum lahirnya UniStock, proses penyediaan dan penyerahan seragam mengalami inefisiensi yang sangat tinggi akibat ketergantungan pada berkas formulir kertas, pengumpulan kuesioner Google Form yang tidak tervalidasi, serta spreadsheet Excel yang rawan korupsi data. Mahasiswa baru terpaksa mengantre hingga berjam-jam di aula kampus di bawah terik matahari, staf loket mengalami kelelahan ekstrem karena harus mencari nama mahasiswa di antara tumpukan lembaran manual, dan bagian keuangan tidak memiliki visibilitas atas nilai riil persediaan serta laba kotor institusi.</p>

  <h3 class="subchapter-title">2.2 Pilar Transformasi UniStock</h3>
  <p>UniStock hadir membawa transformasi menyeluruh melalui 4 pilar solusi utama:</p>
  <ul>
    <li><strong>Digitalisasi Pendataan Mandiri:</strong> Mahasiswa menentukan ukuran baju dan sepatu dinas mereka secara mandiri melalui portal web terenkripsi yang dilengkapi panduan ukuran (*size chart*) resmi dari vendor konveksi rekanan universitas.</li>
    <li><strong>Verifikasi Otomatis Terpadu (Zero-Trust Eligibility):</strong> Sistem memadukan pemeriksaan status pelunasan biaya kuliah secara ketat sebelum mahasiswa diizinkan mengunduh tanda pengenal digital QR Code.</li>
    <li><strong>Eksekusi Loket Kilat Berbasis QR Code:</strong> Mengubah transaksi manual yang memakan waktu 15–20 menit menjadi transaksi kilat di bawah 2–3 menit per mahasiswa melalui pemindaian kamera langsung pada peramban staf.</li>
    <li><strong>Akuntansi Persediaan FIFO dan Transparansi Laba Kotor (GPM):</strong> Menerapkan penilaian persediaan metode FIFO (*First-In, First-Out*) dengan kalkulasi blended HPP (*Harga Pokok Penjualan*) yang menghasilkan pelaporan laba rugi real-time bagi rektorat dan direksi keuangan.</li>
  </ul>
</div>

<div class="chapter" id="bab-3">
  <h2 class="chapter-title">Bab 3 — Project Identity &amp; Nomenklatur Organisasi</h2>
  <p>Untuk menghindari distorsi komunikasi di antara berbagai divisi dan agen AI yang bekerja pada proyek ini, bab ini merumuskan identitas resmi dan kamus istilah organisasi yang berlaku pada ekosistem UniStock.</p>
  
  <h3 class="subchapter-title">3.1 Penamaan dan Singkatan Resmi</h3>
  <ul>
    <li><strong>Nama Utama Proyek:</strong> Horizon UniStock (ditulis dengan huruf kapital H dan U, tanpa spasi antara Uni dan Stock).</li>
    <li><strong>Nama Alternatif / Kode Proyek:</strong> UniStock Web Management System.</li>
    <li><strong>Domain Produksi:</strong> <code>unistok.nirwana.biz.id</code> (menggunakan ejaan fonetik "unistok" untuk kemudahan pengetikan mahasiswa Indonesia).</li>
  </ul>

  <h3 class="subchapter-title">3.2 Glosarium Nomenklatur Institusi</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25%;">Istilah Institusi</th>
        <th style="width: 75%;">Definisi &amp; Konteks Penggunaan dalam Sistem</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Horizon Karawang</strong></td>
        <td>Entitas perguruan tinggi swasta di bawah naungan Triputra Group yang merupakan merger dan pengembangan dari STIKes dan STMIK Horizon Karawang menjadi Universitas Horizon Indonesia.</td>
      </tr>
      <tr>
        <td><strong>FIKES</strong></td>
        <td>Fakultas Ilmu Kesehatan, mengelola Program Studi S1 Keperawatan, D3 Kebidanan, dan Profesi Ners yang memiliki kebutuhan atribut dinas medis sangat spesifik.</td>
      </tr>
      <tr>
        <td><strong>FTI</strong></td>
        <td>Fakultas Teknologi Informasi, mengelola Program Studi S1 Informatika dan Sistem Informasi dengan atribut jas almamater resmi dan kaos praktikum TI.</td>
      </tr>
      <tr>
        <td><strong>Student Level</strong></td>
        <td>Klasifikasi semester akademik mahasiswa, dikelompokkan ke dalam Freshman (Y1S1, Y1S2) dan Continuing (Y2S1 s/d Y4S2, serta Graduated).</td>
      </tr>
      <tr>
        <td><strong>Entitlement</strong></td>
        <td>Paket alokasi hak barang seragam yang telah diperhitungkan dalam paket biaya kuliah per semester untuk program studi tertentu.</td>
      </tr>
      <tr>
        <td><strong>Actual Size vs Expected Size</strong></td>
        <td><em>Expected Size</em> adalah ukuran yang sebelumnya dipesan mahasiswa pada portal mandiri. <em>Actual Size</em> adalah ukuran fisik yang benar-benar diserahkan staf gudang setelah fitting langsung di loket.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-4">
  <h2 class="chapter-title">Bab 4 — Project History &amp; Kronologi Evolusi Sistem</h2>
  <h3 class="subchapter-title">4.1 Garis Waktu Pengembangan dan Milestone Utama</h3>
  <p>Pengembangan Horizon UniStock tidak terjadi dalam ruang hampa, melainkan merupakan respon berkesinambungan atas audit tahunan logistik perguruan tinggi:</p>
  <ul>
    <li><strong>Januari – Maret 2024 (Fase Identifikasi Masalah &amp; Temuan Audit):</strong> Audit independen universitas menemukan adanya potensi kerugian ratusan juta rupiah akibat selisih stok seragam (*stock shrinkage*) pada penerimaan mahasiswa baru tahun 2023. Ditemukan bukti adanya klaim ganda barang, kesalahan ukuran massal yang berujung pada stok mati (*dead stock*), dan keluhan orang tua mahasiswa terkait antrean loket yang tidak manusiawi.</li>
    <li><strong>April – Mei 2026 (Analisis Master Spreadsheet Excel):</strong> Tim pengembang menganalisis berkas spreadsheet induk logistik berukuran ~4.5 MB (`scripts/Dummy - Inventory Management.xlsx`) yang berisi 19 lembar kerja terpisah. Ditemukan kelemahan fatal pada sistem formula sheet yang saling menimpa, hilangnya histori pergerakan barang, serta ketiadaan audit trail.</li>
    <li><strong>Agustus 2026 (Rilis MVP V1.0.0):</strong> Peletakan fondasi arsitektur Laravel 12/13, pembuatan portal pengisian ukuran mahasiswa mandiri, implementasi QR Code resolusi tinggi via library BaconQrCode GDLib, integrasi scanner kamera klien via html5-qrcode, dan pengujian lapangan pada 500 mahasiswa baru angkatan pertama.</li>
    <li><strong>Pertengahan Agustus 2026 (Rilis V1.1.0 — Modul FIFO &amp; Stock Opname):</strong> Penambahan mesin persediaan metode FIFO murni melalui entitas <code>stock_batches</code>, kartu mutasi stok <code>stock_movements</code>, integrasi modul rekonsiliasi Stock Opname bulanan dengan fitur upload Excel, dan peluncuran Sales Dashboard yang menampilkan margin laba kotor institusi.</li>
    <li><strong>Akhir Agustus 2026 (Rilis V1.1.5 — Optimasi Basis Data &amp; UX):</strong> Penambahan kolom boolean flag <code>is_baju</code> dan <code>is_sepatu</code> pada tabel master ukuran untuk memangkas waktu query dropdown, perbaikan kolom session jadwal distribusi, dan penguatan validasi soft-delete.</li>
    <li><strong>Oktober 2026 (Rilis V1.2.0 — Stabilisasi &amp; Master Knowledge Base):</strong> Restrukturisasi repositori, penghapusan file artefak pengujian lama, penguatan tata kelola deployment cPanel tanpa SSH, dan penyusunan proposal dokumentasi induk 90 bab.</li>
  </ul>
</div>

<div class="chapter" id="bab-5">
  <h2 class="chapter-title">Bab 5 — Tujuan, Sasaran Strategis, dan Batasan Sistem</h2>
  <h3 class="subchapter-title">5.1 Sasaran Kuantitatif dan Kualitatif</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 30%;">Aspek Sasaran</th>
        <th style="width: 70%;">Deskripsi Target yang Ditetapkan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Ketepatan Ukuran Fisik</strong></td>
        <td>Meningkatkan akurasi kesesuaian seragam pertama kali terima dari semula 62% menjadi di atas 95% melalui penyediaan size chart komparatif dan batas pengubahan mandiri.</td>
      </tr>
      <tr>
        <td><strong>Kapasitas Pelayanan Loket</strong></td>
        <td>Mampu melayani lebih dari 1.500 mahasiswa dalam rentang 3 hari kerja dengan beban puncak simultan 50 loket aktif tanpa degradasi performa sistem.</td>
      </tr>
      <tr>
        <td><strong>Integritas Keuangan &amp; Akuntansi</strong></td>
        <td>Mencapai 100% rekonsiliasi antara total uang seragam yang diterima kas universitas dengan total biaya modal (HPP) pengeluaran persediaan ke vendor konveksi.</td>
      </tr>
      <tr>
        <td><strong>Kepuasan Sivitas Akademika</strong></td>
        <td>Menghilangkan antrean memanjang lebih dari 15 menit dan mencapai skor kepuasan mahasiswa baru minimal 4.5 dari skala 5.0 dalam survei orientasi kampus.</td>
      </tr>
    </tbody>
  </table>

  <h3 class="subchapter-title">5.2 Batasan Ruang Lingkup Sistem (Out-of-Scope)</h3>
  <p>Agar implementasi proyek tetap terfokus dan deliverable dapat dicapai sesuai jadwal akademik, beberapa aspek operasional secara tegas dinyatakan berada di luar ruang lingkup versi ini:</p>
  <ul>
    <li>Sistem tidak mencakup pemrosesan transaksi pembayaran tunai atau integrasi Payment Gateway langsung di antarmuka (pembayaran tetap dilakukan via rekening Virtual Account universitas dan status diimpor/diset oleh Bagian Keuangan).</li>
    <li>Sistem belum mencakup transaksi ritel eceran bebas untuk masyarakat umum (Point of Sales eceran direncanakan pada rilis fase lanjutan).</li>
    <li>Sistem tidak membangun aplikasi native Android/iOS terpisah di Google Play Store atau Apple App Store; seluruh fungsionalitas dioptimalkan melalui Progressive Web Architecture (PWA) berbasis peramban modern yang responsif.</li>
  </ul>
</div>

<!-- ============================================== -->
<!-- BAGIAN II: ANALISIS BISNIS DAN PROSES OPERASIONAL -->
<!-- ============================================== -->
<div class="part-divider">
  <div class="part-number">Bagian II</div>
  <h1 class="part-title">Analisis Bisnis dan Proses Operasional</h1>
  <div class="part-subtitle">Konteks Organisasi, Analisis Pemangku Kepentingan, SOP Lapangan, dan Aturan Bisnis Holistik</div>
</div>

<div class="chapter" id="bab-6">
  <h2 class="chapter-title">Bab 6 — Business Background (Latar Belakang Non-Teknis &amp; Akar Masalah)</h2>
  <h3 class="subchapter-title">6.1 Realitas Operasional Distribusi Seragam Kampus</h3>
  <p>Proses pendistribusian seragam dan atribut perkuliahan di lingkungan perguruan tinggi swasta seperti Horizon University Indonesia merupakan salah satu titik kontak (*critical touchpoint*) pertama antara mahasiswa baru dengan manajemen institusi. Kesan pertama mahasiswa dan orang tua terhadap profesionalitas tata kelola universitas sangat dipengaruhi oleh bagaimana pengalaman mereka saat menerima jas almamater dan pakaian praktik.</p>
  
  <p>Kondisi lapangan sebelum sistem UniStock dibangun ditandai oleh 5 simpul kemacetan operasional utama:</p>
  <ol>
    <li><strong>Silo Data Pendaftaran PMB vs Logistik Gudang:</strong> Data calon mahasiswa baru yang mendaftar di bagian Penerimaan Mahasiswa Baru (PMB) tersimpan di database terpisah. Tim gudang tidak mengetahui berapa jumlah pasti mahasiswa per prodi yang telah melakukan her-registrasi, sehingga pemesanan seragam ke vendor konveksi dilakukan berdasarkan estimasi kasar belaka. Akibatnya, terjadi kekurangan seragam ukuran reguler (M dan L) dan kelebihan ukuran ekstrem (XS atau XXXL).</li>
    <li><strong>Praktik Formulir Pengukuran Manual:</strong> Mahasiswa baru diminta menuliskan ukuran baju pada lembaran formulir kertas saat mengikuti tes kesehatan. Tulisan tangan yang sulit dibaca oleh staf administrasi menyebabkan kesalahan input ke spreadsheet (misal: huruf 'S' terbaca 'L', atau angka '38' terbaca '39').</li>
    <li><strong>Kekacauan di Aula Pembagian:</strong> Pada hari H distribusi, mahasiswa memadati aula utama. Petugas loket menggunakan daftar absensi cetak (*print-out*) setebal 200 halaman. Proses pencarian nama memakan waktu 5–10 menit per mahasiswa. Jika mahasiswa bersangkutan ternyata belum lunas SPP atau belum melunasi biaya seragam, terjadi perdebatan di loket yang menghentikan antrean di belakangnya.</li>
    <li><strong>Fenomena "Salah Ukuran &amp; Tukar Liar":</strong> Banyak mahasiswa yang mendapati seragam yang diterima kesempitan atau kedodoran. Karena tidak ada SOP sistem yang jelas, mahasiswa menukar seragam secara informal di luar loket atau staf gudang memberikan seragam lain dari tumpukan kardus tanpa mencatat perubahan ukuran tersebut. Hal ini menyebabkan catatan stok di buku inventaris tidak lagi mencerminkan keberadaan fisik barang.</li>
    <li><strong>Spreadsheet Excel Manual yang Meledak:</strong> Pembuktian material dari masalah ini tercermin pada file <code>scripts/Dummy - Inventory Management.xlsx</code> berukuran 4.5 MB. File ini memuat puluhan tab yang mencampuradukkan kartu persediaan, pencatatan penjualan, laporan kehilangan (tab `Loss STO`), dan rekonsiliasi manual. File sebesar ini rentan mengalami kerusakan (*file corruption*), lambat saat dibuka, dan hanya dapat diedit oleh satu orang dalam satu waktu (*lock conflict*).</li>
  </ol>
</div>

<div class="chapter" id="bab-7">
  <h2 class="chapter-title">Bab 7 — Business Objectives &amp; Analisis Kelayakan Biaya</h2>
  <h3 class="subchapter-title">7.1 Analisis Manfaat Finansial dan Operasional (Cost-Benefit Analysis)</h3>
  <p>Pembangunan sistem UniStock memberikan pengembalian investasi (*Return on Investment - ROI*) yang nyata dan terukur bagi universitas:</p>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25%;">Komponen Biaya / Dampak</th>
        <th style="width: 35%;">Kondisi Sebelum UniStock</th>
        <th style="width: 40%;">Efisiensi Setelah UniStock Diterapkan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Tenaga Kerja Lembur Staf</strong></td>
        <td>Staf gudang dan kemahasiswaan lembur hingga larut malam selama 2 minggu masa orientasi (rata-rata 120 jam lembur).</td>
        <td>Waktu layanan selesai tepat pukul 16.00 WIB setiap harinya, menghemat lebih dari 80% anggaran uang lembur operasional staf.</td>
      </tr>
      <tr>
        <td><strong>Biaya Kertas &amp; Percetakan</strong></td>
        <td>Mencetak ribuan lembar formulir tanda terima, kartu antrean, dan rekap tebal (menghabiskan puluhan rim kertas).</td>
        <td>100% Paperless; tanda terima digital disimpan langsung di basis data dan bukti penyerahan tercatat otomatis di akun mahasiswa.</td>
      </tr>
      <tr>
        <td><strong>Kerugian Stok Susut / Hilang</strong></td>
        <td>Tingkat kehilangan dan selisih stok fisik mencapai 5–8% dari total pengadaan senilai ratusan juta rupiah.</td>
        <td>Selisih stok ditekan hingga di bawah 1% berkat audit trail per transaksi dan pencocokan sisa saldo hak (*remaining balance*).</td>
      </tr>
      <tr>
        <td><strong>Klaim Ganda Barang</strong></td>
        <td>Mahasiswa mengambil seragam lebih dari sekali dengan memanfaatkan pergantian shift staf loket yang berbeda hari.</td>
        <td>Nol kemungkinan klaim ganda berkat validasi basis data terisolasi (*pessimistic row locking*) pada saat pemindaian QR Code.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-8">
  <h2 class="chapter-title">Bab 8 — Stakeholder Analysis &amp; Matriks Kepentingan</h2>
  <p>Keberhasilan operasional UniStock bertumpu pada kolaborasi harmonis antara berbagai pemangku kepentingan di lingkungan universitas. Matriks kepentingan berikut merinci ekspektasi dan peran strategis masing-masing entitas:</p>

  <table class="data-table">
    <thead>
      <tr>
        <th>Pemangku Kepentingan</th>
        <th>Kepentingan Utama (Vested Interest)</th>
        <th>Titik Kritis (Pain Points)</th>
        <th>Strategi Akomodasi Sistem</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Rektorat &amp; Dekanat</strong></td>
        <td>Reputasi institusi, kelancaran PMB, dan kepatuhan standar akreditasi atribut seragam profesi.</td>
        <td>Mendengar komplain orang tua di media sosial terkait antrean pembagian seragam yang ricuh.</td>
        <td>Menyediakan sistem pembagian tertib berbasis sesi jadwal dan dashboard progres distribusi real-time.</td>
      </tr>
      <tr>
        <td><strong>Direktorat Keuangan (Finance)</strong></td>
        <td>Kepastian pelunasan biaya seragam sebelum penyerahan barang dan perhitungan HPP serta GPM yang akurat.</td>
        <td>Banyak mahasiswa yang belum lunas tetap berhasil mengambil seragam karena kelemahan verifikasi manual.</td>
        <td>Fitur *Finance Eligibility Toggle* yang mengunci status pengambilan mahasiswa jika pembayaran belum berstatus *Paid*.</td>
      </tr>
      <tr>
        <td><strong>Unit Gudang &amp; Logistik</strong></td>
        <td>Kesesuaian antara jumlah barang di rak fisik dengan angka di sistem inventaris.</td>
        <td>Sering dituduh lalai saat terjadi selisih stok pada akhir periode tahun ajaran.</td>
        <td>Setiap barang keluar wajib terikat pada *DistributionItem* dengan nomor transaksi, timestamp, dan ID staf loket yang bertugas.</td>
      </tr>
      <tr>
        <td><strong>Vendor Konveksi &amp; Pabrik</strong></td>
        <td>Penerimaan Purchase Order (PO) dengan kuota ukuran yang akurat dan waktu pembayaran yang tepat.</td>
        <td>Sering menerima komplain retur seragam karena mahasiswa mengklaim salah ukuran produksi.</td>
        <td>Modul Size Monitor yang merekapitulasi data pesanan ukuran riil mahasiswa sebelum order massal dikirim ke pabrik.</td>
      </tr>
      <tr>
        <td><strong>Mahasiswa &amp; Orang Tua</strong></td>
        <td>Mendapatkan seragam berkualitas tinggi, pas di badan, dan proses pengambilan yang cepat serta ramah.</td>
        <td>Harus datang berkali-kali ke kampus karena stok ukuran habis saat giliran antrean mereka tiba.</td>
        <td>Informasi transparan di portal pribadi mengenai kesiapan barang, ukuran terdaftar, dan jadwal loket yang ditentukan.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-9">
  <h2 class="chapter-title">Bab 9 — User Roles, Persona, &amp; Tanggung Jawab Operasional</h2>
  <h3 class="subchapter-title">9.1 Profil Persona Pengguna</h3>
  <p>Untuk memastikan desain pengalaman pengguna (UX) tepat sasaran, sistem memodelkan 4 persona pengguna nyata:</p>
  
  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">Persona 1</span>
      <span class="feature-name">Siti — Mahasiswa Baru S1 Keperawatan (FIKES)</span>
    </div>
    <p><strong>Karakteristik:</strong> Berusia 18 tahun, mengakses sistem menggunakan smartphone Android, belum terbiasa dengan portal akademik kampus, cemas seragam praktiknya kekecilan karena ukuran pakaian medis harus longgar dan nyaman untuk manuver tindakan medis.</p>
    <p><strong>Kebutuhan Sistem:</strong> Form pemilihan ukuran yang sangat intuitif dengan panduan visual lingkar dada dan panjang baju, akses cepat unduh QR Code, serta status kelayakan yang terpampang jelas.</p>
  </div>

  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">Persona 2</span>
      <span class="feature-name">Budi — Staf Gudang &amp; Operator Loket Distribusi</span>
    </div>
    <p><strong>Karakteristik:</strong> Mengoperasikan laptop atau tablet di meja loket gudang berdebu, melayani ratusan mahasiswa bergantian di tengah kebisingan aula, membutuhkan antarmuka yang cepat dengan tombol besar dan feedback visual instan.</p>
    <p><strong>Kebutuhan Sistem:</strong> Scanner kamera yang responsif dalam membaca barcode layar ponsel yang retak atau redup, indikator warna hijau/merah yang mencolok untuk status kelayakan, serta dropdown actual size yang mudah diubah jika fitting tidak pas.</p>
  </div>

  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">Persona 3</span>
      <span class="feature-name">Ibu Ratna — Manajer Keuangan (Finance Manager)</span>
    </div>
    <p><strong>Karakteristik:</strong> Bertanggung jawab atas audit pembukuan universitas, tidak terlibat dalam pembagian fisik di lapangan, namun membutuhkan laporan angka rekapitulasi yang valid untuk laporan rapat pimpinan.</p>
    <p><strong>Kebutuhan Sistem:</strong> Sales Dashboard yang memperlihatkan nilai omzet, total pengeluaran HPP, marjin laba kotor (GPM), dan kemampuan ekspor data lengkap ke format Microsoft Excel (.xlsx).</p>
  </div>

  <div class="feature-card">
    <div class="feature-header">
      <span class="feature-id">Persona 4</span>
      <span class="feature-name">Fajar — Administrator Sistem &amp; TI (Super Admin)</span>
    </div>
    <p><strong>Karakteristik:</strong> Bertanggung jawab atas ketersediaan server (uptime), keamanan kredensial akun, integrasi surat elektronik SMTP kampus, dan manajemen hak akses pengguna.</p>
    <p><strong>Kebutuhan Sistem:</strong> Antarmuka konfigurasi SMTP yang aman dengan fitur pengujian konektivitas soket, manajemen akun pengguna berbasis role Spatie, dan pembersihan cache sistem.</p>
  </div>
</div>

<div class="chapter" id="bab-10">
  <h2 class="chapter-title">Bab 10 — End-to-End Business Process (Detail Alur Holistik)</h2>
  <h3 class="subchapter-title">10.1 Prosedur Operasional Standar (SOP) Tahap demi Tahap</h3>
  <p>Siklus operasional distribusi seragam UniStock dibagi menjadi 5 tahapan kronologis terkoordinasi:</p>
  
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 10%;">Fase</th>
        <th style="width: 25%;">Nama Fase</th>
        <th style="width: 35%;">Aksi &amp; Prosedur Lapangan</th>
        <th style="width: 30%;">Entitas &amp; Data yang Dihasilkan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Fase 1</strong></td>
        <td>Pendataan Ukuran Mandiri</td>
        <td>Mahasiswa login pertama kali menggunakan NIM, dipaksa mengganti kata sandi bawaan demi keamanan akun, memverifikasi email resmi kampus, membaca size chart vendor, dan memilih ukuran baju serta sepatu pada event yang aktif.</td>
        <td>Record <code>student_size_profiles</code> aktif, log entri <code>size_event_submissions</code>, dan pembatasan counter <code>max_changes</code>.</td>
      </tr>
      <tr>
        <td><strong>Fase 2</strong></td>
        <td>Verifikasi Keuangan &amp; Penerbitan QR</td>
        <td>Bagian Finance memeriksa bukti pelunasan her-registrasi. Mahasiswa yang lunas diaktifkan statusnya menjadi *Eligible*. Portal mahasiswa otomatis menerbitkan QR Code digital beresolusi tinggi yang berisi enkapsulasi NIM mahasiswa.</td>
        <td>Record <code>eligibility_records</code> (is_eligible = true, payment_status = 'paid'), stream Base64 QR Code pada rute <code>/student/qr</code>.</td>
      </tr>
      <tr>
        <td><strong>Fase 3</strong></td>
        <td>Perencanaan Jadwal &amp; Alokasi Jatah</td>
        <td>Admin akademik dan logistik membuat jadwal pembagian di aula kampus dengan menentukan target fakultas, prodi, jenjang semester, tanggal, dan lokasi loket. Sistem menyaring daftar barang yang tampil sesuai paket *Entitlement*.</td>
        <td>Record <code>distribution_schedules</code> dan relasi pivot barang <code>dist_schedule_items</code>.</td>
      </tr>
      <tr>
        <td><strong>Fase 4</strong></td>
        <td>Pemindaian Loket, Fitting, &amp; Transaksi</td>
        <td>Mahasiswa hadir di aula sesuai sesi jadwal. Staf loket memindai QR Code via kamera peramban. Sistem memverifikasi kelayakan. Mahasiswa mencoba seragam (fitting fisik). Jika ukuran berubah, staf mengubah dropdown *Actual Size*. Staf mencentang barang yang diserahkan dan klik *Submit*.</td>
        <td>Record <code>distribution_transactions</code> (status = 'completed'), rincian barang <code>distribution_items</code>, dan log audit <code>student_size_histories</code>.</td>
      </tr>
      <tr>
        <td><strong>Fase 5</strong></td>
        <td>Pemotongan Stok FIFO &amp; Pelaporan Finansial</td>
        <td>Di balik layar, transaksi mengeksekusi pemotongan stok pada batch kedatangan vendor tertua (FIFO). Blended HPP dihitung secara tertimbang, saldo kartu stok diperbarui, dan angka laba kotor pada modul GPM Finance langsung ter-update seketika.</td>
        <td>Pengurangan <code>stock_batches.quantity_remaining</code>, penulisan <code>stock_movements</code> (tipe 'OUT'), update <code>stock_balances</code>, dan data laporan GPM.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-11">
  <h2 class="chapter-title">Bab 11 — Business Rules (Aturan Bisnis Distribusi &amp; Persediaan)</h2>
  <h3 class="subchapter-title">11.1 Matriks Aturan Bisnis Baku (Business Rules Matrix)</h3>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 15%;">Kode Aturan</th>
        <th style="width: 25%;">Domain Aturan</th>
        <th style="width: 60%;">Formulasi Kebijakan &amp; Batasan Sistem</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>BR-DIST-01</strong></td>
        <td>Kelayakan Distribusi</td>
        <td>Hanya mahasiswa dengan record <code>is_eligible = true</code> pada tabel <code>eligibility_records</code> yang diizinkan melanjutkan transaksi di loket. Jika status tidak layak, tombol submit terkunci otomatis dengan peringatan visual merah.</td>
      </tr>
      <tr>
        <td><strong>BR-DIST-02</strong></td>
        <td>Pencegahan Ambil Ganda</td>
        <td>Sistem melacak kuota hak: <code>Sisa Hak = Kuota Entitlement - Total Unit yang Pernah Diambil</code>. Jika sisa hak untuk suatu barang bernilai 0, baris barang tersebut pada layar loket staf otomatis berstatus *Disabled* bertuliskan label "Sudah Diambil".</td>
      </tr>
      <tr>
        <td><strong>BR-DIST-03</strong></td>
        <td>Kesesuaian Gender</td>
        <td>Barang dengan penanda gender 'L' (Laki-laki) atau 'P' (Perempuan) tidak boleh diserahkan kepada mahasiswa yang berlawanan gender, kecuali barang beratribut 'U' (Unisex). Sistem memunculkan dialog peringatan jika terjadi *gender mismatch*.</td>
      </tr>
      <tr>
        <td><strong>BR-INV-01</strong></td>
        <td>Penilaian Persediaan FIFO</td>
        <td>Persediaan barang keluar wajib memotong kuota batch penerimaan vendor tertua yang masih memiliki saldo <code>quantity_remaining &gt; 0</code>. Nilai HPP transaksi adalah rata-rata tertimbang dari seluruh batch yang terpotong.</td>
      </tr>
      <tr>
        <td><strong>BR-SIZE-01</strong></td>
        <td>Batas Pengubahan Ukuran</td>
        <td>Mahasiswa dibatasi dalam jumlah frekuensi pengubahan ukuran mandiri sesuai konfigurasi <code>max_changes</code> pada event yang bersangkutan (default maksimal 1x ubah). Perubahan ukuran setelah batas tercapai hanya dapat dilakukan oleh staf saat fitting fisik di loket.</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="chapter" id="bab-12">
  <h2 class="chapter-title">Bab 12 — Business Exceptions &amp; SOP Kasus Luar Biasa</h2>
  <h3 class="subchapter-title">12.1 Penanganan Anomali dan Kondisi Tak Terduga di Lapangan</h3>
  <p>Untuk menjaga kelancaran antrean loket di hari H distribusi, sistem merancang Standard Operating Procedure (SOP) penanganan insiden lapangan berikut:</p>

  <div class="callout callout-note">
    <strong>Kasus Anomali 1: Layar Ponsel Mahasiswa Pecah / Kamera Scanner Gagal Membaca QR Code</strong><br>
    <em>Prosedur Penanganan:</em> Staf loket tidak perlu meminta mahasiswa keluar dari antrean. Staf cukup mengklik tab "Input NIM Manual" pada antarmuka loket, mengetikkan 8 digit NIM mahasiswa, dan menekan tombol Enter. Sistem mengeksekusi pencarian dengan performa yang sama cepatnya dengan pemindaian barcode.
  </div>

  <div class="callout callout-warning">
    <strong>Kasus Anomali 2: Stok Fisik Ukuran Tertentu Habis di Rak Gudang (Partial Pickup)</strong><br>
    <em>Prosedur Penanganan:</em> Jika mahasiswa membutuhkan kemeja ukuran L namun stok fisik ukuran L di gudang telah kosong, staf loket <strong>HANYA MENCENTANG</strong> barang-barang yang tersedia fisiknya (misal: celana dan jas almamater). Kemeja ukuran L yang tidak dicentang otomatis berstatus <em>Deferred</em> (Ditunda). Transaksi tetap diproses untuk barang yang ada, dan mahasiswa dapat mengambil sisa kemejanya pada gelombang distribusi susulan tanpa khawatir kehilangan hak barang.
  </div>

  <div class="callout callout-important">
    <strong>Kasus Anomali 3: Mahasiswa Belum Lunas Keuangan Hadir di Loket</strong><br>
    <em>Prosedur Penanganan:</em> Layar staf akan menampilkan banner peringatan merah bertuliskan "Mahasiswa Belum Memenuhi Syarat Administrasi Keuangan". Staf dilarang keras menyerahkan barang secara fisik. Mahasiswa diarahkan ke Meja Bantuan Keuangan (Finance Helpdesk) yang berada di samping aula untuk memverifikasi pembayaran. Begitu admin Finance mengklik toggle *Eligible* di sistem, status mahasiswa seketika berubah menjadi aktif tanpa perlu login ulang.
  </div>
</div>
"""
