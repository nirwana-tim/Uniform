# -*- coding: utf-8 -*-
"""
Builder Laporan Tugas Besar Mata Kuliah Knowledge Management System (KMS) SI-3-A
Fakultas Teknologi Informasi dan Komputer - Universitas Horizon Indonesia
Sesuai Format Resmi: Format-Laporan-Proyek-Akhir-KMS-SI (26-27).pdf:
- Kertas A4, Margin: 4 cm (kiri), 3 cm (kanan), 3 cm (atas), 3 cm (bawah)
- Font: Times New Roman 12pt (14pt untuk Judul Bab), Spasi 1.5, Paragraf Justify, Indent 1cm (10mm)
- Halaman 1: Cover Resmi dengan Background Poligon Segitiga Biru Resmi (#558ED5)
- Halaman 2: Kata Pengantar (Tepat 1 Halaman, Nomor Halaman Romawi 'i' di Bawah Tengah)
- Halaman 3: Daftar Isi (Tepat 1 Halaman dengan Titik-Titik Dotted Leader Presisi, Nomor 'ii' di Bawah Tengah)
- Halaman 4: BAB I Pendahuluan (Halaman Pertama Bab: Nomor Arab '1' di Bawah Tengah)
- Halaman 5 dst: Lanjutan BAB I (Nomor Arab di Sudut Kanan Bawah Sesuai Margin)
"""

import os
import sys
import base64
import subprocess
import pymupdf

BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
DOCS_DIR = os.path.join(BASE_DIR, "Horizon-UniStock-Documentation")
KMS_DIR = os.path.join(DOCS_DIR, "TUGAS BESAR MATKUL KMS")

os.makedirs(KMS_DIR, exist_ok=True)

def get_logo_base64():
    logo_path = os.path.join(BASE_DIR, "public", "images", "logo.png")
    if os.path.exists(logo_path):
        with open(logo_path, "rb") as f:
            return "data:image/png;base64," + base64.b64encode(f.read()).decode("utf-8")
    return ""

def generate_report_html(toc_pages=None):
    if toc_pages is None:
        toc_pages = {
            "1.1": 1,
            "1.2": 3,
            "1.3": 3,
            "1.4": 4,
            "1.5": 5,
            "1.6": 6,
            "bab2": 7,
            "bab3": 10,
            "bab4": 18,
            "dp": 20
        }
        
    logo_b64 = get_logo_base64()
    
    html = f"""<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Tugas Besar KMS — BAB I Pendahuluan</title>
<style>
  @page {{
    size: A4 portrait;
    margin-top: 30mm;
    margin-bottom: 30mm;
    margin-left: 40mm;
    margin-right: 30mm;
  }}

  * {{
    box-sizing: border-box;
  }}

  body {{
    font-family: 'Times New Roman', Times, serif;
    font-size: 12pt;
    line-height: 1.5;
    color: #000000;
    background-color: #FFFFFF;
    margin: 0;
    padding: 0;
  }}

  /* Halaman Cover presisi 1 halaman */
  .cover-container {{
    page-break-after: always;
    break-after: page;
    text-align: center;
    margin: 0;
    padding: 0;
    position: relative;
    height: 100%;
  }}

  .cover-header-top {{
    font-size: 13.5pt;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.25;
    margin-bottom: 5mm;
  }}

  .cover-title-main {{
    font-size: 13.5pt;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.35;
    margin-bottom: 4mm;
    color: #000000;
  }}

  .cover-logo-img {{
    max-width: 120px;
    height: auto;
    margin: 3mm 0 4mm 0;
  }}

  .cover-disusun-wrapper {{
    font-size: 12pt;
    line-height: 1.35;
    text-align: center;
    margin-bottom: 3.5mm;
  }}

  .cover-author-table {{
    margin: 2mm auto;
    border-collapse: collapse;
    font-size: 11.5pt;
    text-align: left;
    display: inline-block;
  }}

  .cover-author-table td {{
    padding: 1.5px 6px;
    vertical-align: top;
  }}

  .cover-dosen {{
    font-size: 12pt;
    font-weight: bold;
    margin: 3mm 0 5mm 0;
  }}

  .cover-footer {{
    font-size: 13pt;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.25;
    margin-top: 4mm;
  }}

  /* Halaman Kata Pengantar (Tepat 1 Halaman - Halaman Pertama Dokumen HTML) */
  .page-kp {{
    page-break-after: always;
    break-after: page;
    font-size: 11pt;
    line-height: 1.36;
  }}

  .page-kp p.par-indent {{
    text-align: justify;
    text-indent: 10mm;
    margin-top: 0;
    margin-bottom: 2mm;
    line-height: 1.36;
  }}

  .page-kp ol.list-numbered {{
    margin-top: 0;
    margin-bottom: 2mm;
    padding-left: 7mm;
    text-align: justify;
    line-height: 1.36;
  }}

  .page-kp ol.list-numbered li {{
    margin-bottom: 1.2mm;
  }}

  .signature-box {{
    margin-top: 3.5mm;
    text-align: right;
    font-size: 11pt;
    line-height: 1.3;
  }}

  /* Halaman Daftar Isi (Tepat 1 Halaman Rapi) */
  .page-di {{
    page-break-before: always;
    break-before: page;
    page-break-after: always;
    break-after: page;
    font-size: 10.8pt;
    line-height: 1.28;
  }}

  .toc-container {{
    margin-top: 3mm;
  }}

  .toc-row {{
    display: flex;
    align-items: baseline;
    margin-bottom: 1.8mm;
    font-size: 10.8pt;
    line-height: 1.28;
  }}

  .toc-title {{
    flex-shrink: 0;
    padding-right: 2.5mm;
  }}

  .toc-title-sub {{
    flex-shrink: 0;
    padding-left: 6mm;
    padding-right: 2.5mm;
  }}

  .toc-leader {{
    flex-grow: 1;
    border-bottom: 1.2px dotted #000000;
    height: 0;
    margin-bottom: 3.5px;
  }}

  .toc-num {{
    flex-shrink: 0;
    padding-left: 2.5mm;
    text-align: right;
    min-width: 22px;
  }}

  /* Bab I Alur Alami Berkelanjutan */
  .page-bab1-root {{
    page-break-before: always;
    break-before: page;
  }}

  .heading-center-1 {{
    text-align: center;
    font-size: 14pt;
    font-weight: bold;
    text-transform: uppercase;
    margin-top: 0;
    margin-bottom: 2mm;
  }}

  .heading-center-2 {{
    text-align: center;
    font-size: 14pt;
    font-weight: bold;
    text-transform: uppercase;
    margin-top: 0;
    margin-bottom: 6mm;
  }}

  .heading-sub {{
    font-size: 12pt;
    font-weight: bold;
    margin-top: 5mm;
    margin-bottom: 2mm;
    text-align: left;
    break-after: avoid;
    page-break-after: avoid;
  }}

  .section-block {{
    margin-top: 4mm;
  }}

  .section-page-start {{
    page-break-before: always;
    break-before: page;
  }}

  p.par-indent {{
    text-align: justify;
    text-indent: 10mm;
    margin-top: 0;
    margin-bottom: 2.5mm;
    line-height: 1.5;
    orphans: 3;
    widows: 3;
  }}

  p.par-regular {{
    text-align: justify;
    margin-top: 0;
    margin-bottom: 2.5mm;
    line-height: 1.5;
    orphans: 3;
    widows: 3;
  }}

  ol.list-numbered {{
    margin-top: 0;
    margin-bottom: 2.5mm;
    padding-left: 6mm;
    text-align: justify;
    line-height: 1.5;
  }}

  ol.list-numbered li {{
    margin-bottom: 2mm;
    padding-left: 1.5mm;
    text-align: justify;
    line-height: 1.5;
  }}

  ul.list-bullet {{
    margin-top: 1.5mm;
    margin-bottom: 2mm;
    padding-left: 5mm;
    text-align: justify;
    line-height: 1.5;
  }}

  ul.list-bullet li {{
    margin-bottom: 1.5mm;
    padding-left: 1.5mm;
    text-align: justify;
    line-height: 1.5;
  }}
</style>
</head>
<body>

<!-- ========================================== -->
<!-- 1. KATA PENGANTAR (TEPAT 1 HALAMAN)        -->
<!-- (Cover resmi dibuat via PyMuPDF native)     -->
<!-- ========================================== -->
<div class="page-kp">
  <div class="heading-center-1" style="margin-bottom: 4mm;">KATA PENGANTAR</div>

  <p class="par-indent">
    Puji dan syukur penulis panjatkan ke hadirat Tuhan Yang Maha Esa atas limpahan rahmat, karunia, serta taufik-Nya, sehingga Laporan Proyek Tugas Besar Mata Kuliah <em>Knowledge Management System</em> (KMS) ini dapat diselesaikan dengan baik dan tepat pada waktu yang telah ditetapkan.
  </p>

  <p class="par-indent">
    Laporan proyek dengan judul <strong>"Perancangan Knowledge Management System untuk Meningkatkan Efektifitas Berbagi Pengetahuan pada Pengelolaan dan Distribusi Logistik Seragam Mahasiswa di Universitas Horizon Indonesia"</strong> ini disusun guna memenuhi salah satu capaian pembelajaran mata kuliah Knowledge Management System pada Semester Ganjil Tahun Akademik 2026/2027 di Program Studi Sistem Informasi, Fakultas Teknologi Informasi dan Komputer, Universitas Horizon Indonesia.
  </p>

  <p class="par-indent">
    Dalam proses perancangan sistem dan penyusunan laporan ini, penulis mendapatkan banyak bimbingan, arahan, saran, serta dukungan moril yang sangat berharga. Oleh karena itu, pada kesempatan ini penulis menyampaikan rasa hormat dan terima kasih yang setinggi-tingginya kepada:
  </p>

  <ol class="list-numbered">
    <li>Bapak <strong>Arif Budimansyah Purba, M.Kom.</strong>, selaku Dosen Pengajar Mata Kuliah Knowledge Management System, yang telah dengan sabar memberikan bimbingan teoretis, arahan teknis, koreksi, serta motivasi berharga sepanjang perkuliahan;</li>
    <li>Bapak Dekan beserta segenap jajaran pimpinan Fakultas Teknologi Informasi dan Komputer (FTIK) Universitas Horizon Indonesia;</li>
    <li>Bapak/Ibu Ketua Program Studi Sistem Informasi Universitas Horizon Indonesia atas dukungan kurikulum dan kompetensi aplikatif yang diberikan;</li>
    <li>Pimpinan Direktorat Keuangan, Biro Administrasi Akademik dan Kemahasiswaan (BAAK), serta staf Bagian Logistik Universitas Horizon Indonesia atas keterbukaan akses data operasional lapangan bagi studi kasus ini;</li>
    <li>Rekan-rekan mahasiswa Kelas SI-3-A, khususnya anggota Kelompok I, atas kerja sama tim, dedikasi, dan soliditas yang luar biasa selama menyelesaikan proyek ini.</li>
  </ol>

  <p class="par-indent">
    Penulis menyadari bahwa laporan ini masih memiliki ruang penyempurnaan. Kritik dan saran konstruktif dari pembaca sangat diharapkan demi penyempurnaan karya di masa mendatang. Semoga laporan proyek ini dapat memberikan kontribusi nyata bagi pengembangan ilmu pengetahuan dan kemajuan institusi.
  </p>

  <div class="signature-box">
    Karawang, Oktober 2026<br><br><br>
    <strong>Tim Penulis (Kelompok I)</strong>
  </div>
</div>

<!-- ========================================== -->
<!-- 3. DAFTAR ISI (TEPAT 1 HALAMAN RAPI)       -->
<!-- ========================================== -->
<div class="page-di">
  <div class="heading-center-1" style="margin-bottom: 4mm;">DAFTAR ISI</div>

  <div class="toc-container">
    <div class="toc-row">
      <span class="toc-title"><strong>KATA PENGANTAR</strong></span>
      <span class="toc-leader"></span>
      <span class="toc-num"><strong>i</strong></span>
    </div>
    <div class="toc-row">
      <span class="toc-title"><strong>DAFTAR ISI</strong></span>
      <span class="toc-leader"></span>
      <span class="toc-num"><strong>ii</strong></span>
    </div>
    <div class="toc-row" style="margin-top: 2.2mm;">
      <span class="toc-title"><strong>BAB I PENDAHULUAN</strong></span>
      <span class="toc-leader"></span>
      <span class="toc-num"><strong>1</strong></span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">1.1 Latar Belakang</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['1.1']}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">1.2 Rumusan Masalah</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['1.2']}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">1.3 Batasan Masalah</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['1.3']}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">1.4 Tujuan</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['1.4']}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">1.5 Manfaat</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['1.5']}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">1.6 Sistematika Penulisan</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['1.6']}</span>
    </div>
    <div class="toc-row" style="margin-top: 2.2mm;">
      <span class="toc-title"><strong>BAB II TINJAUAN PUSTAKA</strong></span>
      <span class="toc-leader"></span>
      <span class="toc-num"><strong>{toc_pages['bab2']}</strong></span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">2.1 Konsep Knowledge Management</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab2']}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">2.2 Bentuk dan Jenis Pengetahuan (Tacit dan Explicit)</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab2']}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">2.3 Karakteristik Knowledge Management System (KMS)</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab2'] + 1}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">2.4 Siklus Konversi Pengetahuan (Model SECI Nonaka-Takeuchi)</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab2'] + 1}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">2.5 Arsitektur Repositori Pengetahuan dan Kerangka Kerja Logistik</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab2'] + 2}</span>
    </div>
    <div class="toc-row" style="margin-top: 2.2mm;">
      <span class="toc-title"><strong>BAB III PEMBAHASAN</strong></span>
      <span class="toc-leader"></span>
      <span class="toc-num"><strong>{toc_pages['bab3']}</strong></span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">3.1 Gambaran Umum Objek dan Kondisi Eksisting</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab3']}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">3.2 Analisis Kebutuhan Pengetahuan dan Spesifikasi Sistem</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab3'] + 1}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">3.3 Perancangan Arsitektur Basis Pengetahuan dan Sistem Web</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab3'] + 2}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">3.4 Implementasi Prototype Sistem Horizon UniStock</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab3'] + 4}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">3.5 Pengujian Fungsional dan Evaluasi Sistem</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab3'] + 6}</span>
    </div>
    <div class="toc-row" style="margin-top: 2.2mm;">
      <span class="toc-title"><strong>BAB IV PENUTUP</strong></span>
      <span class="toc-leader"></span>
      <span class="toc-num"><strong>{toc_pages['bab4']}</strong></span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">4.1 Simpulan</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab4']}</span>
    </div>
    <div class="toc-row">
      <span class="toc-title-sub">4.2 Saran</span>
      <span class="toc-leader"></span>
      <span class="toc-num">{toc_pages['bab4']}</span>
    </div>
    <div class="toc-row" style="margin-top: 2.2mm;">
      <span class="toc-title"><strong>DAFTAR PUSTAKA</strong></span>
      <span class="toc-leader"></span>
      <span class="toc-num"><strong>{toc_pages['dp']}</strong></span>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- 4. BAB I PENDAHULUAN                      -->
<!-- ========================================== -->
<div class="page-bab1-root">
  <div class="heading-center-1">BAB I</div>
  <div class="heading-center-2">PENDAHULUAN</div>

  <div class="section-block">
    <div class="heading-sub">1.1 Latar Belakang</div>
    <p class="par-indent">
      Perkembangan teknologi informasi era Revolusi Industri 4.0 dan Society 5.0 mendorong institusi perguruan tinggi menempatkan pengetahuan (<em>knowledge</em>) sebagai aset intelektual strategis. Berdasarkan teori spiral penciptaan pengetahuan Nonaka dan Takeuchi (1995) serta kerangka <em>Knowledge Management System</em> (KMS) oleh Tiwana (2002), transformasi pengetahuan implisit (<em>tacit</em>) ke eksplisit (<em>explicit</em>) melalui model SECI (<em>Socialization, Externalization, Combination, Internalization</em>) merupakan fondasi mutlak guna mencegah risiko kehilangan modal intelektual (<em>knowledge loss</em>) dan sekat informasi operasional (<em>knowledge silo</em>).
    </p>

    <p class="par-indent">
      Pada tata kelola operasional distribusi seragam mahasiswa baru di Universitas Horizon Indonesia, pengelolaan data hak barang (<em>entitlements</em>), kuota angkatan, dan penanganan kasus khusus selama ini bertumpu pada ingatan lisan staf atau lembar kerja (<em>spreadsheet</em>) lokal yang tidak terintegrasi. Verifikasi manual bukti pembayaran kasir kerap menimbulkan antrean panjang ratusan mahasiswa, kekeliruan spesifikasi barang, serta kerentanan pembagian ganda (<em>double-claim</em>). Selain itu, belum diterapkannya metode persediaan <em>First-In, First-Out</em> (FIFO) menyulitkan perhitungan Harga Pokok Penjualan (HPP) dan akurasi rekonsiliasi audit fisik (<em>stock opname</em>) gudang di akhir periode.
    </p>

    <p class="par-indent">
      Menjawab kebutuhan tersebut, dirancang platform web <em>Knowledge Management System</em> "Horizon UniStock" sebagai repositori pengetahuan terpusat sekaligus instrumen kendali distribusi logistik terpadu. Sistem ini mengintegrasikan identifikasi digital QR Code mahasiswa, mekanisme <em>pessimistic row locking</em> basis data anti-klaim ganda, otomasi mutasi persediaan berbasis FIFO, portal pemilihan ukuran mandiri, serta dasbor analitis pelaporan manajerial. Berdasarkan urgensi pemecahan masalah tersebut, disusun laporan proyek tugas besar berjudul <strong>"PERANCANGAN KNOWLEDGE MANAGEMENT SYSTEM UNTUK MENINGKATKAN EFEKTIFITAS BERBAGI PENGETAHUAN PADA PENGELOLAAN DAN DISTRIBUSI LOGISTIK SERAGAM MAHASISWA DI UNIVERSITAS HORIZON INDONESIA"</strong>.
    </p>
  </div>

  <div class="section-block section-page-start">
    <div class="heading-sub">1.2 Rumusan Masalah</div>
    <p class="par-indent">
      Berdasarkan latar belakang yang telah diuraikan, identifikasi permasalahan dalam proyek ini dirumuskan ke dalam pertanyaan-pertanyaan penelitian dan perancangan terukur yang relevan dengan ranah keilmuan Knowledge Management System sebagai berikut:
    </p>
    <ol class="list-numbered">
      <li>Bagaimana struktur arsitektur dasar <em>Knowledge Management System</em> (KMS) berbasis web yang tepat dalam mengintegrasikan alur data, informasi hak alokasi, dan pengetahuan logistik distribusi seragam di lingkungan Universitas Horizon Indonesia?</li>
      <li>Bagaimana penerapan siklus konversi pengetahuan model SECI (<em>Socialization, Externalization, Combination, Internalization</em>) untuk mentransformasikan <em>tacit knowledge</em> staf operasional loket dan bagian keuangan menjadi <em>explicit knowledge</em> yang terpusat dan mudah diakses?</li>
      <li>Bagaimana merancang repositori pengetahuan persediaan dengan algoritma penilaian <em>First-In, First-Out</em> (FIFO) serta instrumen kendali distribusi berbasis QR Code guna mencegah kehilangan informasi (<em>knowledge loss</em>) dan penyerahan ganda (<em>double-claim</em>)?</li>
      <li>Bagaimana efektivitas implementasi platform KMS berbasis web dalam meningkatkan transparansi pelaporan manajerial (<em>Gross Profit Margin</em>) bagi pimpinan perguruan tinggi?</li>
    </ol>
  </div>

  <div class="section-block">
    <div class="heading-sub">1.3 Batasan Masalah</div>
    <p class="par-indent">
      Agar pembahasan laporan proyek ini terarah, terfokus, dan mendalam sesuai dengan ruang lingkup yang ditetapkan, maka batasan masalah dalam proyek ini diatur sebagai berikut:
    </p>
    <ol class="list-numbered">
      <li><strong>Objek dan Lokasi Studi Kasus:</strong> Penelitian dan perancangan KMS difokuskan secara spesifik pada pengelolaan logistik dan operasional pembagian seragam mahasiswa baru di lingkungan Universitas Horizon Indonesia Kampus Karawang.</li>
      <li><strong>Ruang Lingkup Dimensi KMS:</strong> Aspek manajemen pengetahuan yang dibangun mencakup empat pilar utama: penangkapan pengetahuan operasional (<em>knowledge capture</em>), penyimpanan dan pengorganisasian pada repositori terpusat (<em>knowledge storage</em>), pembagian informasi antar-divisi (<em>knowledge sharing</em>), dan penerapan pengetahuan pada proses serah terima barang fisik di meja loket (<em>knowledge application</em>).</li>
      <li><strong>Batasan Modul Fungsional Sistem:</strong> Sistem yang dikembangkan (Horizon UniStock) mencakup modul autentikasi berbasis peran (<em>Role-Based Access Control</em>), portal mandiri mahasiswa (input ukuran baju/sepatu dan penerbitan QR Code), loket staf (pemindaian kamera dan verifikasi kelayakan), manajemen persediaan multi-batch FIFO, audit <em>stock opname</em> fisik, serta dasbor pemantauan finansial institusi.</li>
      <li><strong>Batasan Teknologi dan Lingkungan Deployment:</strong> Perangkat lunak dibangun menggunakan bahasa pemrograman PHP 8.3/8.4 dengan kerangka kerja Laravel 11, basis data MySQL 8.0, antarmuka Tailwind CSS dan Alpine.js, serta diuji coba untuk lingkungan server hosting berbasis cPanel.</li>
    </ol>
  </div>

  <div class="section-block">
    <div class="heading-sub">1.4 Tujuan</div>
    <p class="par-indent">
      Tujuan dari penyusunan laporan proyek ini adalah pernyataan terukur yang mencerminkan capaian yang hendak dihasilkan, meliputi:
    </p>
    <ol class="list-numbered">
      <li><strong>Tujuan Terkait Judul Proyek:</strong>
        Merancang dan mengimplementasikan prototipe <em>Knowledge Management System</em> berbasis web yang mampu mengotomasi dan mengintegrasikan seluruh alur pendataan hak, pemindaian digital, verifikasi kelayakan, dan pelaporan logistik seragam di Universitas Horizon Indonesia.
      </li>
      <li><strong>Tujuan Terkait Knowledge Management System (KMS):</strong>
        <ul class="list-bullet">
          <li>Mengeliminasi fenomena sekat informasi (<em>knowledge silo</em>) antar-unit kerja dengan mewujudkan satu sumber kebenaran data terpusat (<em>Single Source of Truth</em> / SSOT).</li>
          <li>Mengkodifikasi kebiasaan operasional staf menjadi prosedur operasional standar (SOP) digital 5 fase yang terdokumentasi dan mudah dipelajari oleh panitia baru.</li>
          <li>Menyediakan instrumen pendukung keputusan manajerial bagi rektorat melalui visualisasi performa distribusi dan analisis margin laba kotor secara akurat.</li>
        </ul>
      </li>
      <li><strong>Tujuan Khusus bagi Mahasiswa:</strong>
        <ul class="list-bullet">
          <li>Melatih dan memperdalam pemahaman mahasiswa Program Studi Sistem Informasi dalam membedah permasalahan nyata organisasi dan merancang arsitektur KMS berbasis teori ilmiah.</li>
          <li>Mengembangkan keterampilan rekayasa perangkat lunak enterprise modern serta melatih kemampuan komunikasi teknis melalui penyusunan karya ilmiah akademik yang terstruktur.</li>
        </ul>
      </li>
    </ol>
  </div>

  <div class="section-block">
    <div class="heading-sub">1.5 Manfaat</div>
    <p class="par-indent">
      Pelaksanaan proyek dan penyusunan laporan ini diharapkan mampu memberikan kontribusi positif yang ditinjau dari empat elemen pemangku kepentingan:
    </p>
    <ol class="list-numbered">
      <li><strong>Bagi Bidang Disiplin Ilmu (Program Studi Sistem Informasi):</strong>
        Memberikan kontribusi kajian empiris dan studi kasus nyata mengenai integrasi teori Knowledge Management System dengan rekayasa web modern (MVC, algoritma persediaan FIFO, pengamanan basis data <em>pessimistic locking</em>, dan identifikasi QR Code) sebagai referensi pustaka bagi penelitian mahasiswa selanjutnya di Program Studi Sistem Informasi.
      </li>
      <li><strong>Bagi Masyarakat Luas dan Kelompok Mahasiswa:</strong>
        Meningkatkan kepuasan layanan bagi mahasiswa baru melalui pemangkasan waktu antrean loket dari 10–15 menit menjadi di bawah 45 detik, menjamin ketepatan ukuran seragam saat fitting, serta memberikan transparansi alokasi hak barang secara mandiri via smartphone.
      </li>
      <li><strong>Bagi Institusi (Universitas Horizon Indonesia):</strong>
        Mencegah kerugian finansial akibat penyerahan ganda atau penyusutan stok fisik, menjamin kesinambungan modal intelektual organisasi tanpa ketergantungan pada personil tertentu, menyediakan laporan keuangan akurat, serta memperkuat citra kampus bertata kelola TI modern.
      </li>
      <li><strong>Bagi Penulis atau Penyusun Laporan:</strong>
        Memperdalam kompetensi dalam menganalisis siklus pengetahuan organisasi, mengasah keterampilan rekayasa sistem berskala enterprise, serta melatih daya pikir analitis, kritis, dan metodologis dalam merumuskan solusi berbasis data ilmiah.
      </li>
    </ol>
  </div>

  <div class="section-block">
    <div class="heading-sub">1.6 Sistematika Penulisan</div>
    <p class="par-indent">
      Penyusunan laporan proyek tugas besar ini disusun secara sistematis ke dalam empat bab utama sesuai ketentuan panduan akademik sebagai berikut:
    </p>
    <ul class="list-bullet">
      <li>
        <strong>BAB I PENDAHULUAN:</strong>
        Memuat latar belakang permasalahan, rumusan masalah dalam bentuk pertanyaan terukur, batasan masalah penelitian, tujuan proyek (terkait judul, KMS, dan mahasiswa), manfaat bagi empat elemen pemangku kepentingan, serta sistematika penulisan laporan.
      </li>
      <li>
        <strong>BAB II TINJAUAN PUSTAKA:</strong>
        Menyajikan landasan teori ilmiah mencakup konsep Knowledge Management, klasifikasi tacit-explicit, karakteristik KMS, siklus konversi SECI Nonaka-Takeuchi, arsitektur repositori pengetahuan logistik, penelitian terdahulu, dan kerangka pemikiran.
      </li>
      <li>
        <strong>BAB III PEMBAHASAN:</strong>
        Merupakan bab inti yang memuat profil objek kampus, analisis kebutuhan pengetahuan, perancangan arsitektur basis pengetahuan dan pemodelan data (ERD 31 tabel), implementasi prototipe Horizon UniStock, serta dokumentasi pengujian dan evaluasi fungsional.
      </li>
      <li>
        <strong>BAB IV PENUTUP:</strong>
        Memuat simpulan yang merangkum hasil implementasi prototipe KMS dalam menjawab rumusan masalah proyek, serta saran-saran strategis bagi pengembangan lanjutan sistem di masa yang akan datang.
      </li>
    </ul>
  </div>
</div>

</body>
</html>"""
    return html

def render_html_to_pdf(html_content, output_pdf):
    temp_html = os.path.join(DOCS_DIR, "temp_render.html")
    with open(temp_html, "w", encoding="utf-8") as f:
        f.write(html_content)
        
    chrome_path = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
    if not os.path.exists(chrome_path):
        chrome_path = r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
        
    cmd = [
        chrome_path,
        "--headless=new",
        "--disable-gpu",
        "--no-pdf-header-footer",
        f"--print-to-pdf={output_pdf}",
        temp_html
    ]
    res = subprocess.run(cmd, capture_output=True, text=True)
    if os.path.exists(temp_html):
        os.remove(temp_html)
        
    if res.returncode != 0 or not os.path.exists(output_pdf):
        print(f"Error printing PDF: {res.stderr}")
        return False
    return True

def create_cover_doc():
    template_pdf = os.path.join(KMS_DIR, "Format-Laporan-Proyek-Akhir-KMS-SI (26-27).pdf")
    logo_path = os.path.join(BASE_DIR, "public", "images", "logo.png")
    
    img_bytes = None
    if os.path.exists(template_pdf):
        try:
            doc_src = pymupdf.open(template_pdf)
            page_template = doc_src[1]
            images = page_template.get_images()
            if images:
                xref = images[0][0]
                img_bytes = doc_src.extract_image(xref)['image']
            doc_src.close()
        except Exception as e:
            print("Could not extract logo from template:", e)
            
    if img_bytes is None and os.path.exists(logo_path):
        with open(logo_path, "rb") as f:
            img_bytes = f.read()

    doc = pymupdf.open()
    page = doc.new_page(width=595.4, height=841.9)

    # 1. Official Blue Triangle Polyline (#558ED5)
    shape = page.new_shape()
    shape.draw_polyline([
        pymupdf.Point(4.45, 837.20),
        pymupdf.Point(4.45, 3.80),
        pymupdf.Point(464.60, 837.20),
        pymupdf.Point(4.45, 837.20)
    ])
    shape.finish(fill=(0.333, 0.557, 0.835), color=None)
    shape.commit()

    # 2. Official Logo (120 x 120 pt, Jarak Lega 35 pt dari Judul)
    if img_bytes:
        logo_w = 120.0
        logo_h = 120.0
        logo_x0 = 314.2 - (logo_w / 2)
        logo_y0 = 255.0
        page.insert_image(pymupdf.Rect(logo_x0, logo_y0, logo_x0 + logo_w, logo_y0 + logo_h), stream=img_bytes)

    font_bold = "times-bold"
    font_roman = "times-roman"
    CX = 314.2

    def draw_center(text, y, fontsize=14, fontname=font_bold):
        w = pymupdf.get_text_length(text, fontname=fontname, fontsize=fontsize)
        x = CX - (w / 2)
        page.insert_text(pymupdf.Point(x, y), text, fontsize=fontsize, fontname=fontname)

    # 1. Header Top (Konsisten 14 pt Bold)
    draw_center("PROYEK TUGAS BESAR", 78, 14)
    draw_center("MATA KULIAH KNOWLEDGE MANAGEMENT SYSTEM", 96, 14)
    draw_center("SEMESTER GANJIL 2026-2027", 114, 14)

    # 2. Judul Laporan (Konsisten 14 pt Bold, 5 Baris Rapi, Tidak Menumpuk)
    draw_center("PERANCANGAN KNOWLEDGE MANAGEMENT SYSTEM", 152, 14)
    draw_center("UNTUK MENINGKATKAN EFEKTIFITAS BERBAGI", 170, 14)
    draw_center("PENGETAHUAN PADA PENGELOLAAN DAN DISTRIBUSI", 188, 14)
    draw_center("LOGISTIK SERAGAM MAHASISWA", 206, 14)
    draw_center("DI UNIVERSITAS HORIZON INDONESIA", 224, 14)

    # 3. Disusun oleh (Konsisten 12 pt Bold, Jarak Lega 35 pt dari Logo)
    draw_center("Disusun oleh :", 405, 12)
    draw_center("Kelompok I Kelas SI-3-A", 423, 12)

    # 4. Tabel Anggota (Konsisten 12 pt Regular, 2 Kolom Rapi & Sejajar)
    authors = [
        ("1. Fhazar Raffiful Aqyla", "(4112757201240021)"),
        ("2. Ferry Noer Hendriawan", "(4112757201240055)"),
        ("3. Daffa Nazriel Zaildan", "(4112757201240051)"),
        ("4. Fadel Rahmadhan", "(4112757201240054)"),
        ("5. Hildan Anugrah tamadi Putra", "(4112757201240008)"),
        ("6. Acep Ugan Saputra", "(4112757201240034)"),
    ]
    y_author = 445
    for name, nim in authors:
        page.insert_text(pymupdf.Point(160.0, y_author), name, fontsize=12, fontname=font_roman)
        page.insert_text(pymupdf.Point(335.0, y_author), nim, fontsize=12, fontname=font_roman)
        y_author += 17.5

    # 5. Dosen Pengajar (Konsisten 12 pt Bold, Jarak Lega 30 pt)
    draw_center("Dosen Pengajar : Arif Budimansyah Purba, M.Kom.", 562, 12, fontname=font_bold)

    # 6. Institusi Footer (Konsisten 14 pt Bold, Spasi Berimbang)
    draw_center("PROGRAM STUDI SISTEM INFORMASI", 635, 14)
    draw_center("FAKULTAS TEKNOLOGI INFORMASI DAN KOMPUTER", 655, 14)
    draw_center("UNIVERSITAS HORIZON INDONESIA", 675, 14)
    draw_center("KARAWANG", 695, 14)
    draw_center("2026", 715, 14)

    return doc

def detect_section_pages(raw_pdf, bab1_start_page=3):
    doc = pymupdf.open(raw_pdf)
    detected = {
        "1.1": 1,
        "1.2": 3,
        "1.3": 3,
        "1.4": 4,
        "1.5": 5,
        "1.6": 6
    }
    
    subheadings = ["1.1 Latar Belakang", "1.2 Rumusan Masalah", "1.3 Batasan Masalah", "1.4 Tujuan", "1.5 Manfaat", "1.6 Sistematika Penulisan"]
    
    for sub in subheadings:
        key = sub.split()[0]
        for pno in range(bab1_start_page - 1, len(doc)):
            text = doc[pno].get_text()
            lines = [l.strip() for l in text.splitlines()]
            # Pastikan bukan baris Daftar Isi
            if any(l.startswith(sub) and "...." not in l for l in lines):
                arabic_page = (pno + 1) - bab1_start_page + 1
                detected[key] = arabic_page
                break
                
    doc.close()
    return detected

def build_pdf():
    print("=== STARTING TUGAS BESAR KMS REPORT BUILD (BAB I) ===")
    raw_pass1 = os.path.join(DOCS_DIR, "temp_pass1.pdf")
    raw_pass2 = os.path.join(DOCS_DIR, "temp_pass2.pdf")
    final_pdf = os.path.join(KMS_DIR, "Laporan-Tugas-Besar-KMS-Bab-1.pdf")
    
    # PASS 1: Generate dokumen awal untuk membaca penempatan halaman presisi (tanpa Cover)
    html_pass1 = generate_report_html()
    if not render_html_to_pdf(html_pass1, raw_pass1):
        return False
        
    detected_pages = detect_section_pages(raw_pass1, bab1_start_page=3)
    print("Detected Section Pages in Bab 1:", detected_pages)
    
    doc_raw = pymupdf.open(raw_pass1)
    total_pages_detected = len(doc_raw)
    doc_raw.close()
    
    last_bab1_page = (total_pages_detected - 3) + 1
    p_bab2 = last_bab1_page + 1
    p_bab3 = p_bab2 + 3
    p_bab4 = p_bab3 + 8
    p_dp = p_bab4 + 2
    
    toc_pages = {
        "1.1": detected_pages["1.1"],
        "1.2": detected_pages["1.2"],
        "1.3": detected_pages["1.3"],
        "1.4": detected_pages["1.4"],
        "1.5": detected_pages["1.5"],
        "1.6": detected_pages["1.6"],
        "bab2": p_bab2,
        "bab3": p_bab3,
        "bab4": p_bab4,
        "dp": p_dp
    }
    
    # PASS 2: Re-generate HTML dengan TOC yang 100% sinkron
    html_pass2 = generate_report_html(toc_pages=toc_pages)
    if not render_html_to_pdf(html_pass2, raw_pass2):
        return False
        
    doc = pymupdf.open(raw_pass2)
    
    # Insert Official Cover Page at index 0 (Native PyMuPDF, no white masking artifacts)
    cover_doc = create_cover_doc()
    doc.insert_pdf(cover_doc, start_at=0)
    cover_doc.close()
    
    total_pages = len(doc)
    print(f"Total Pages in Final Report (with Cover): {total_pages}")
    
    # Set Metadata Dokumen PDF
    doc.set_metadata({
        "title": "Laporan Proyek Tugas Besar KMS — BAB I Pendahuluan",
        "author": "Kelompok I Kelas SI-3-A (Universitas Horizon Indonesia)",
        "subject": "Perancangan Knowledge Management System untuk Meningkatkan Efektifitas Berbagi Pengetahuan pada Pengelolaan dan Distribusi Logistik Seragam Mahasiswa di Universitas Horizon Indonesia",
        "keywords": "KMS, Knowledge Management System, Tugas Besar, Universitas Horizon Indonesia, Bab I Pendahuluan, SECI Model, FIFO",
        "creator": "Horizon UniStock Documentation Engine",
        "producer": "PyMuPDF & Chrome Headless Engine"
    })
    
    font_name = "times-roman"
    bab1_final_start_page = 4
    
    # Penomoran Halaman Presisi Sesuai Standar Akademik Indonesia:
    # - Halaman 1 (Cover): Tanpa nomor halaman
    # - Halaman 2 (Kata Pengantar): Romawi 'i' di Bawah Tengah
    # - Halaman 3 (Daftar Isi): Romawi 'ii' di Bawah Tengah
    # - Halaman 4 (Halaman Judul Bab I): Angka Arab '1' di Bawah Tengah
    # - Halaman 5 dst (Halaman Lanjutan Bab I): Angka Arab di Kanan Bawah Sesuai Margin
    for pno in range(total_pages):
        page = doc[pno]
        rect = page.rect
        
        if pno == 0:
            continue
        elif pno + 1 < bab1_final_start_page:
            roman_nums = ["i", "ii", "iii", "iv", "v"]
            idx = pno - 1
            num_str = roman_nums[idx] if idx < len(roman_nums) else str(idx + 1)
            text_point = pymupdf.Point(rect.width / 2 - 4, rect.height - 40)
            page.insert_text(text_point, num_str, fontsize=11, fontname=font_name)
        elif pno + 1 == bab1_final_start_page:
            # Halaman Pertama Bab: Bawah Tengah
            arabic_num = "1"
            text_point = pymupdf.Point(rect.width / 2 - 4, rect.height - 40)
            page.insert_text(text_point, arabic_num, fontsize=11, fontname=font_name)
        else:
            # Halaman Lanjutan: Bawah Tengah
            arabic_num = str((pno + 1) - bab1_final_start_page + 1)
            text_point = pymupdf.Point(rect.width / 2 - 4, rect.height - 40)
            page.insert_text(text_point, arabic_num, fontsize=11, fontname=font_name)
            
    # Outlines / PDF Bookmarks
    toc = [
        [1, "Halaman Judul / Cover", 1],
        [1, "Kata Pengantar", 2],
        [1, "Daftar Isi", 3],
        [1, "BAB I PENDAHULUAN", bab1_final_start_page],
        [2, "1.1 Latar Belakang", bab1_final_start_page]
    ]
    
    subheadings = [
        ("1.2 Rumusan Masalah", "1.2"),
        ("1.3 Batasan Masalah", "1.3"),
        ("1.4 Tujuan", "1.4"),
        ("1.5 Manfaat", "1.5"),
        ("1.6 Sistematika Penulisan", "1.6")
    ]
    
    for title, key in subheadings:
        page_num = detected_pages.get(key, 1) + bab1_final_start_page - 1
        toc.append([2, title, page_num])
        
    doc.set_toc(toc)
    doc.save(final_pdf)
    doc.close()
    
    if os.path.exists(raw_pass1):
        os.remove(raw_pass1)
    if os.path.exists(raw_pass2):
        os.remove(raw_pass2)
        
    print(f"[SUCCESS] Official Report PDF saved: {final_pdf} ({os.path.getsize(final_pdf)} bytes)")
    return True

if __name__ == "__main__":
    build_pdf()
