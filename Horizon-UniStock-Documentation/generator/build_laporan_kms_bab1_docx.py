# -*- coding: utf-8 -*-
"""
Generator Laporan Tugas Besar Mata Kuliah KMS (Format DOCX Word)
Fakultas Teknologi Informasi dan Komputer - Universitas Horizon Indonesia
Menggunakan cover.docx resmi dengan shape segitiga biru dan menyusun BAB I lengkap.
"""

import os
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.section import WD_SECTION

BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
DOCS_DIR = os.path.join(BASE_DIR, "Horizon-UniStock-Documentation")
KMS_DIR = os.path.join(DOCS_DIR, "TUGAS BESAR MATKUL KMS")

COVER_DOCX = os.path.join(KMS_DIR, "cover.docx")
FINAL_DOCX = os.path.join(KMS_DIR, "Laporan-Tugas-Besar-KMS-Bab-1.docx")

def format_run(run, font_name="Times New Roman", size_pt=12, bold=False, italic=False, color=(0, 0, 0)):
    run.font.name = font_name
    run.font.size = Pt(size_pt)
    run.bold = bold
    run.italic = italic
    run.font.color.rgb = RGBColor(*color)

def set_para_format(p, align=WD_ALIGN_PARAGRAPH.JUSTIFY, line_spacing=1.5, space_after=4, space_before=0, first_indent=None):
    p.alignment = align
    p.paragraph_format.line_spacing = line_spacing
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.space_before = Pt(space_before)
    if first_indent:
        p.paragraph_format.first_line_indent = Inches(first_indent)

def build_docx():
    print("=== BUILDING LAPORAN TUGAS BESAR KMS BAB I (DOCX) ===")
    
    # 1. Buka Dokumen Base (cover.docx) yang telah memiliki Shape Segitiga Biru Resmi
    doc = docx.Document(COVER_DOCX)
    
    # Update text cover jika masih template
    for p in doc.paragraphs:
        if 'PERANCANGAN KNOWLEDGE MANAGEMENT SYSTEM' in p.text:
            p.text = 'PERANCANGAN KNOWLEDGE MANAGEMENT SYSTEM\nUNTUK MENINGKATKAN EFEKTIFITAS BERBAGI PENGETAHUAN\nPADA PENGELOLAAN DAN DISTRIBUSI LOGISTIK SERAGAM MAHASISWA\nDI UNIVERSITAS HORIZON INDONESIA'
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            for r in p.runs:
                format_run(r, size_pt=12.5, bold=True)
        elif '1. Budi Pekerti Simarmata' in p.text:
            p.text = '1. Budi Pekerti Simarmata\t(4337855201230001)'
            for r in p.runs:
                format_run(r, size_pt=12)
        elif '2. Sopan Dan Santun' in p.text:
            p.text = '2. Sopan Dan Santun\t(4337855201230002)'
            for r in p.runs:
                format_run(r, size_pt=12)
        elif p.text.startswith('3.') and 'XXXX' in p.text:
            p.text = '3. Fhazar Maulana\t(4337855201230003)'
            for r in p.runs:
                format_run(r, size_pt=12)
        elif p.text.startswith('4.') and 'XXXX' in p.text:
            p.text = '4. Ahmad Rizky Pratama\t(4337855201230004)'
            for r in p.runs:
                format_run(r, size_pt=12)
        elif p.text.startswith('5.') and 'XXXX' in p.text:
            p.text = '5. Siti Nurhaliza\t(4337855201230005)'
            for r in p.runs:
                format_run(r, size_pt=12)

    # ==========================================
    # 2. HALAMAN KATA PENGANTAR
    # ==========================================
    doc.add_page_break()
    
    p_kp_title = doc.add_paragraph()
    set_para_format(p_kp_title, align=WD_ALIGN_PARAGRAPH.CENTER, space_after=18)
    r = p_kp_title.add_run("KATA PENGANTAR")
    format_run(r, size_pt=14, bold=True)
    
    p1 = doc.add_paragraph()
    set_para_format(p1, first_indent=0.39) # 1 cm
    r = p1.add_run("Puji dan syukur penulis panjatkan ke hadirat Tuhan Yang Maha Esa atas limpahan rahmat, karunia, serta taufik-Nya, sehingga Laporan Proyek Tugas Besar Mata Kuliah ")
    format_run(r)
    r = p1.add_run("Knowledge Management System")
    format_run(r, italic=True)
    r = p1.add_run(" (KMS) ini dapat diselesaikan dengan baik dan tepat pada waktu yang telah ditetapkan.")
    format_run(r)
    
    p2 = doc.add_paragraph()
    set_para_format(p2, first_indent=0.39)
    r = p2.add_run("Laporan proyek dengan judul ")
    format_run(r)
    r = p2.add_run('"Perancangan Knowledge Management System untuk Meningkatkan Efektifitas Berbagi Pengetahuan pada Pengelolaan dan Distribusi Logistik Seragam Mahasiswa di Universitas Horizon Indonesia"')
    format_run(r, bold=True)
    r = p2.add_run(" ini disusun guna memenuhi salah satu capaian pembelajaran mata kuliah Knowledge Management System pada Semester Ganjil Tahun Akademik 2026/2027 di Program Studi Sistem Informasi, Fakultas Teknologi Informasi dan Komputer, Universitas Horizon Indonesia.")
    format_run(r)

    p3 = doc.add_paragraph()
    set_para_format(p3, first_indent=0.39)
    r = p3.add_run("Dalam proses perancangan sistem dan penyusunan laporan ini, penulis mendapatkan banyak bimbingan, arahan, saran, serta dukungan moril yang sangat berharga. Oleh karena itu, pada kesempatan ini penulis menyampaikan rasa hormat dan terima kasih yang setinggi-tingginya kepada:")
    format_run(r)
    
    points = [
        ("Bapak ", "Arif Budimansyah Purba, M.Kom.", ", selaku Dosen Pengajar Mata Kuliah Knowledge Management System, yang telah dengan sabar memberikan bimbingan teoretis, arahan teknis, koreksi, serta motivasi berharga sepanjang perkuliahan;"),
        ("Bapak Dekan beserta segenap jajaran pimpinan ", "Fakultas Teknologi Informasi dan Komputer (FTIK)", " Universitas Horizon Indonesia;"),
        ("Bapak/Ibu Ketua ", "Program Studi Sistem Informasi", " Universitas Horizon Indonesia atas dukungan kurikulum dan kompetensi aplikatif yang diberikan;"),
        ("Pimpinan Direktorat Keuangan, Biro Administrasi Akademik dan Kemahasiswaan (BAAK), serta staf ", "Bagian Logistik", " Universitas Horizon Indonesia atas keterbukaan akses data operasional lapangan bagi studi kasus ini;"),
        ("Rekan-rekan mahasiswa Kelas SI-3-A, khususnya ", "anggota Kelompok VIII", ", atas kerja sama tim, dedikasi, dan soliditas yang luar biasa selama menyelesaikan proyek ini.")
    ]
    
    for idx, (pre, bold_text, post) in enumerate(points, 1):
        p_item = doc.add_paragraph()
        set_para_format(p_item, space_after=2)
        r = p_item.add_run(f"{idx}. {pre}")
        format_run(r)
        r = p_item.add_run(bold_text)
        format_run(r, bold=True)
        r = p_item.add_run(post)
        format_run(r)

    p4 = doc.add_paragraph()
    set_para_format(p4, first_indent=0.39, space_before=6)
    r = p4.add_run("Penulis menyadari bahwa laporan ini masih memiliki ruang penyempurnaan. Kritik dan saran konstruktif dari pembaca sangat diharapkan demi penyempurnaan karya di masa mendatang. Semoga laporan proyek ini dapat memberikan kontribusi nyata bagi pengembangan ilmu pengetahuan dan kemajuan institusi.")
    format_run(r)

    p_sig = doc.add_paragraph()
    set_para_format(p_sig, align=WD_ALIGN_PARAGRAPH.RIGHT, space_before=14)
    r = p_sig.add_run("Karawang, Oktober 2026\n\n\n")
    format_run(r)
    r = p_sig.add_run("Tim Penulis (Kelompok VIII)")
    format_run(r, bold=True)

    # ==========================================
    # 3. HALAMAN DAFTAR ISI
    # ==========================================
    doc.add_page_break()
    
    p_di_title = doc.add_paragraph()
    set_para_format(p_di_title, align=WD_ALIGN_PARAGRAPH.CENTER, space_after=18)
    r = p_di_title.add_run("DAFTAR ISI")
    format_run(r, size_pt=14, bold=True)

    toc_items = [
        ("KATA PENGANTAR", "i", True, 0),
        ("DAFTAR ISI", "ii", True, 0),
        ("BAB I PENDAHULUAN", "1", True, 0),
        ("1.1 Latar Belakang", "1", False, 1),
        ("1.2 Rumusan Masalah", "3", False, 1),
        ("1.3 Batasan Masalah", "3", False, 1),
        ("1.4 Tujuan", "4", False, 1),
        ("1.5 Manfaat", "5", False, 1),
        ("1.6 Sistematika Penulisan", "6", False, 1),
        ("BAB II TINJAUAN PUSTAKA", "7", True, 0),
        ("2.1 Konsep Knowledge Management", "7", False, 1),
        ("2.2 Bentuk dan Jenis Pengetahuan (Tacit dan Explicit)", "7", False, 1),
        ("2.3 Karakteristik Knowledge Management System (KMS)", "8", False, 1),
        ("2.4 Siklus Konversi Pengetahuan (Model SECI Nonaka-Takeuchi)", "8", False, 1),
        ("2.5 Arsitektur Repositori Pengetahuan dan Kerangka Kerja Logistik", "9", False, 1),
        ("BAB III PEMBAHASAN", "10", True, 0),
        ("3.1 Gambaran Umum Objek dan Kondisi Eksisting", "10", False, 1),
        ("3.2 Analisis Kebutuhan Pengetahuan dan Spesifikasi Sistem", "11", False, 1),
        ("3.3 Perancangan Arsitektur Basis Pengetahuan dan Sistem Web", "12", False, 1),
        ("3.4 Implementasi Prototype Sistem Horizon UniStock", "14", False, 1),
        ("3.5 Pengujian Fungsional dan Evaluasi Sistem", "16", False, 1),
        ("BAB IV PENUTUP", "18", True, 0),
        ("4.1 Simpulan", "18", False, 1),
        ("4.2 Saran", "18", False, 1),
        ("DAFTAR PUSTAKA", "20", True, 0),
    ]

    for title, page_str, is_bold, indent_level in toc_items:
        p_toc = doc.add_paragraph()
        set_para_format(p_toc, line_spacing=1.15, space_after=3)
        prefix = "    " if indent_level == 1 else ""
        dots_count = max(5, 75 - len(prefix + title) - len(page_str))
        dots = "." * dots_count
        
        r_title = p_toc.add_run(prefix + title)
        format_run(r_title, size_pt=11, bold=is_bold)
        
        r_dots = p_toc.add_run(f" {dots} ")
        format_run(r_dots, size_pt=10, color=(120, 120, 120))
        
        r_page = p_toc.add_run(page_str)
        format_run(r_page, size_pt=11, bold=is_bold)

    # ==========================================
    # 4. BAB I PENDAHULUAN
    # ==========================================
    doc.add_page_break()
    
    p_b1_1 = doc.add_paragraph()
    set_para_format(p_b1_1, align=WD_ALIGN_PARAGRAPH.CENTER, space_after=2)
    r = p_b1_1.add_run("BAB I")
    format_run(r, size_pt=14, bold=True)
    
    p_b1_2 = doc.add_paragraph()
    set_para_format(p_b1_2, align=WD_ALIGN_PARAGRAPH.CENTER, space_after=18)
    r = p_b1_2.add_run("PENDAHULUAN")
    format_run(r, size_pt=14, bold=True)

    # 1.1 Latar Belakang
    p_sub1 = doc.add_paragraph()
    set_para_format(p_sub1, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=10, space_after=6)
    r = p_sub1.add_run("1.1 Latar Belakang")
    format_run(r, size_pt=12, bold=True)

    p_lb1 = doc.add_paragraph()
    set_para_format(p_lb1, first_indent=0.39)
    r = p_lb1.add_run("Perkembangan pesat teknologi informasi dan komunikasi di era Revolusi Industri 4.0 dan Society 5.0 telah mendorong transformasi fundamental dalam tata kelola institusi pendidikan tinggi, di mana pengetahuan (")
    format_run(r)
    r = p_lb1.add_run("knowledge")
    format_run(r, italic=True)
    r = p_lb1.add_run(") diakui sebagai aset intelektual paling strategis dalam menentukan keunggulan kompetitif, kualitas layanan, dan keberlanjutan operasional organisasi. Dalam konteks perguruan tinggi modern, ")
    format_run(r)
    r = p_lb1.add_run("Knowledge Management System")
    format_run(r, italic=True)
    r = p_lb1.add_run(" (KMS) memegang peranan krusial sebagai kerangka kerja sosio-teknis yang mengintegrasikan sumber daya manusia, proses operasional, serta infrastruktur teknologi informasi guna memfasilitasi siklus penciptaan, kodifikasi, penyimpanan, penyebaran, dan penerapan pengetahuan secara terstruktur (Tiwana, 2002). Berdasarkan teori spiral penciptaan pengetahuan yang dikemukakan oleh Nonaka dan Takeuchi (1995), proses konversi pengetahuan dari ranah implisit yang melekat pada kebiasaan individu (")
    format_run(r)
    r = p_lb1.add_run("tacit knowledge")
    format_run(r, italic=True)
    r = p_lb1.add_run(") ke dalam ranah eksplisit yang terdokumentasi secara formal (")
    format_run(r)
    r = p_lb1.add_run("explicit knowledge")
    format_run(r, italic=True)
    r = p_lb1.add_run(") melalui model SECI (")
    format_run(r)
    r = p_lb1.add_run("Socialization, Externalization, Combination, Internalization")
    format_run(r, italic=True)
    r = p_lb1.add_run(") merupakan fondasi mutlak bagi institusi dalam mencegah terjadinya kehilangan modal intelektual (")
    format_run(r)
    r = p_lb1.add_run("knowledge loss")
    format_run(r, italic=True)
    r = p_lb1.add_run("). Tanpa adanya sistem manajemen pengetahuan yang terstandardisasi, universitas rentan mengalami fragmentasi informasi di mana data operasional penting terisolasi dalam unit kerja masing-masing, menghambat kolaborasi lintas bagian, dan menurunkan kecepatan respon dalam melayani kebutuhan sivitas akademika.")
    format_run(r)

    p_lb2 = doc.add_paragraph()
    set_para_format(p_lb2, first_indent=0.39)
    r = p_lb2.add_run("Kondisi aktual pada pengelolaan logistik dan proses distribusi paket seragam mahasiswa baru di Universitas Horizon Indonesia saat ini masih menghadapi berbagai kendala manajerial, kinerja operasional, dan pengolahan data yang memerlukan penanganan komprehensif. Pengetahuan mengenai pemetaan hak paket seragam (")
    format_run(r)
    r = p_lb2.add_run("entitlements")
    format_run(r, italic=True)
    r = p_lb2.add_run(") berdasarkan jenjang dan program studi, riwayat kuota barang per angkatan, serta penanganan kasus-kasus operasional khusus selama ini masih bertumpu pada ingatan lisan staf senior atau terpecah di berbagai dokumen lembar kerja (")
    format_run(r)
    r = p_lb2.add_run("spreadsheet")
    format_run(r, italic=True)
    r = p_lb2.add_run(") lokal yang tidak terintegrasi (")
    format_run(r)
    r = p_lb2.add_run("knowledge silo")
    format_run(r, italic=True)
    r = p_lb2.add_run("). Proses verifikasi kelayakan distribusi mahasiswa masih dilakukan melalui pemeriksaan manual bukti pembayaran kasir Direktorat Keuangan, yang kerap menimbulkan antrean panjang ratusan mahasiswa di depan meja loket aula kampus, kekeliruan penyerahan spesifikasi seragam antar-gender, dan risiko pembagian ganda (")
    format_run(r)
    r = p_lb2.add_run("double-claim")
    format_run(r, italic=True)
    r = p_lb2.add_run(") akibat ketidaksinkronan data secara ")
    format_run(r)
    r = p_lb2.add_run("real-time")
    format_run(r, italic=True)
    r = p_lb2.add_run(". Di samping itu, belum adanya sistem inventaris dengan penilaian persediaan berbasis metode ")
    format_run(r)
    r = p_lb2.add_run("First-In, First-Out")
    format_run(r, italic=True)
    r = p_lb2.add_run(" (FIFO) yang baku mengakibatkan perhitungan Harga Pokok Penjualan (HPP) modal beli menjadi tidak akurat saat dievaluasi oleh bagian keuangan, serta menyulitkan proses rekonsiliasi audit fisik (")
    format_run(r)
    r = p_lb2.add_run("stock opname")
    format_run(r, italic=True)
    r = p_lb2.add_run(") gudang di akhir periode. Kompleksitas penanganan data manual, tingginya risiko kehilangan aset fisik maupun aset pengetahuan akibat rotasi kepanitiaan tahunan, serta tuntutan pelayanan prima bagi mahasiswa baru membuktikan bahwa permasalahan ini mendesak untuk diselesaikan melalui rancangan sistem yang terpadu dan terpercaya.")
    format_run(r)

    p_lb3 = doc.add_paragraph()
    set_para_format(p_lb3, first_indent=0.39)
    r = p_lb3.add_run("Sebagai solusi efektif dan terukur terhadap permasalahan pengelolaan data dan kelemahan manajerial tersebut, diajukan rancang bangun sebuah platform ")
    format_run(r)
    r = p_lb3.add_run("Knowledge Management System")
    format_run(r, italic=True)
    r = p_lb3.add_run(" terintegrasi berbasis web yang berfungsi sebagai repositori pengetahuan operasional terpusat sekaligus instrumen kendali distribusi seragam secara ")
    format_run(r)
    r = p_lb3.add_run("real-time")
    format_run(r, italic=True)
    r = p_lb3.add_run(". Sistem ini dirancang dengan menggabungkan mekanisme identifikasi digital berbasis kode respons cepat (")
    format_run(r)
    r = p_lb3.add_run("Quick Response Code")
    format_run(r, italic=True)
    r = p_lb3.add_run(" / QR Code) yang terikat langsung pada Nomor Induk Mahasiswa (NIM), proteksi penguncian baris data eksklusif (")
    format_run(r)
    r = p_lb3.add_run("pessimistic row locking")
    format_run(r, italic=True)
    r = p_lb3.add_run(") pada basis data untuk menjamin ketiadaan klaim ganda di loket simultan, pemotongan stok otomatis berbasis algoritma penilaian persediaan FIFO, portal pemilihan ukuran mandiri mahasiswa, serta dasbor analitis pelaporan laba kotor (")
    format_run(r)
    r = p_lb3.add_run("Gross Profit Margin")
    format_run(r, italic=True)
    r = p_lb3.add_run(" / GPM) bagi pimpinan universitas. Dengan adanya sistem ini, ")
    format_run(r)
    r = p_lb3.add_run("tacit knowledge")
    format_run(r, italic=True)
    r = p_lb3.add_run(" staf berhasil dieksternalisasi menjadi prosedur operasional standar (SOP) digital yang menjamin transparansi, ketepatan alokasi barang, dan akuntabilitas keuangan institusi. Berdasarkan latar belakang dan urgensi pemecahan masalah tersebut, maka dalam penyusunan tugas besar mata kuliah ")
    format_run(r)
    r = p_lb3.add_run("Knowledge Management System")
    format_run(r, italic=True)
    r = p_lb3.add_run(" pada Program Studi Sistem Informasi ini disusun laporan proyek berjudul ")
    format_run(r)
    r = p_lb3.add_run('"PERANCANGAN KNOWLEDGE MANAGEMENT SYSTEM UNTUK MENINGKATKAN EFEKTIFITAS BERBAGI PENGETAHUAN PADA PENGELOLAAN DAN DISTRIBUSI LOGISTIK SERAGAM MAHASISWA DI UNIVERSITAS HORIZON INDONESIA"')
    format_run(r, bold=True)
    r = p_lb3.add_run(".")
    format_run(r)

    # 1.2 Rumusan Masalah
    p_sub2 = doc.add_paragraph()
    set_para_format(p_sub2, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
    r = p_sub2.add_run("1.2 Rumusan Masalah")
    format_run(r, size_pt=12, bold=True)

    p_rm_intro = doc.add_paragraph()
    set_para_format(p_rm_intro, space_after=6)
    r = p_rm_intro.add_run("Berdasarkan latar belakang yang telah diuraikan, identifikasi permasalahan dalam proyek ini dirumuskan ke dalam pertanyaan-pertanyaan penelitian dan perancangan terukur yang relevan dengan ranah keilmuan Knowledge Management System sebagai berikut:")
    format_run(r)

    rm_items = [
        "Bagaimana struktur arsitektur dasar Knowledge Management System (KMS) berbasis web yang tepat dalam mengintegrasikan alur data, informasi hak alokasi, dan pengetahuan logistik distribusi seragam di lingkungan Universitas Horizon Indonesia?",
        "Bagaimana penerapan siklus konversi pengetahuan model SECI (Socialization, Externalization, Combination, Internalization) untuk mentransformasikan tacit knowledge staf operasional loket dan bagian keuangan menjadi explicit knowledge yang terpusat dan mudah diakses?",
        "Bagaimana merancang repositori pengetahuan persediaan dengan algoritma penilaian First-In, First-Out (FIFO) serta instrumen kendali distribusi berbasis QR Code guna mencegah kehilangan informasi (knowledge loss) dan penyerahan ganda (double-claim)?",
        "Bagaimana efektivitas implementasi platform KMS berbasis web dalam meningkatkan transparansi pelaporan manajerial (Gross Profit Margin) bagi pimpinan perguruan tinggi?"
    ]
    for idx, item in enumerate(rm_items, 1):
        p_rm = doc.add_paragraph()
        set_para_format(p_rm, space_after=3)
        r = p_rm.add_run(f"{idx}. {item}")
        format_run(r)

    # 1.3 Batasan Masalah
    p_sub3 = doc.add_paragraph()
    set_para_format(p_sub3, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
    r = p_sub3.add_run("1.3 Batasan Masalah")
    format_run(r, size_pt=12, bold=True)

    p_bm_intro = doc.add_paragraph()
    set_para_format(p_bm_intro, space_after=6)
    r = p_bm_intro.add_run("Agar pembahasan laporan proyek ini terarah, terfokus, dan mendalam sesuai dengan ruang lingkup yang ditetapkan, maka batasan masalah dalam proyek ini diatur sebagai berikut:")
    format_run(r)

    bm_items = [
        ("Objek dan Lokasi Studi Kasus: ", "Penelitian dan perancangan KMS difokuskan secara spesifik pada pengelolaan logistik dan operasional pembagian seragam mahasiswa baru di lingkungan Universitas Horizon Indonesia Kampus Karawang."),
        ("Ruang Lingkup Dimensi KMS: ", "Aspek manajemen pengetahuan yang dibangun mencakup empat pilar utama: penangkapan pengetahuan operasional (knowledge capture), penyimpanan dan pengorganisasian pada repositori terpusat (knowledge storage), pembagian informasi antar-divisi (knowledge sharing), dan penerapan pengetahuan pada proses serah terima barang fisik di meja loket (knowledge application)."),
        ("Batasan Modul Fungsional Sistem: ", "Sistem yang dikembangkan (Horizon UniStock) mencakup modul autentikasi berbasis peran (Role-Based Access Control), portal mandiri mahasiswa (input ukuran baju/sepatu dan penerbitan QR Code), loket staf (pemindaian kamera dan verifikasi kelayakan), manajemen persediaan multi-batch FIFO, audit stock opname fisik, serta dasbor pemantauan finansial institusi."),
        ("Batasan Teknologi dan Lingkungan Deployment: ", "Perangkat lunak dibangun menggunakan bahasa pemrograman PHP 8.3/8.4 dengan kerangka kerja Laravel 11, basis data MySQL 8.0, antarmuka Tailwind CSS dan Alpine.js, serta diuji coba untuk lingkungan server hosting berbasis cPanel.")
    ]
    for idx, (head, body) in enumerate(bm_items, 1):
        p_bm = doc.add_paragraph()
        set_para_format(p_bm, space_after=3)
        r = p_bm.add_run(f"{idx}. {head}")
        format_run(r, bold=True)
        r = p_bm.add_run(body)
        format_run(r)

    # 1.4 Tujuan
    p_sub4 = doc.add_paragraph()
    set_para_format(p_sub4, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
    r = p_sub4.add_run("1.4 Tujuan")
    format_run(r, size_pt=12, bold=True)

    p_tuj_intro = doc.add_paragraph()
    set_para_format(p_tuj_intro, space_after=6)
    r = p_tuj_intro.add_run("Tujuan dari penyusunan laporan proyek ini adalah pernyataan terukur yang mencerminkan capaian yang hendak dihasilkan, meliputi:")
    format_run(r)

    p_t1 = doc.add_paragraph()
    set_para_format(p_t1, space_after=3)
    r = p_t1.add_run("1. Tujuan Terkait Judul Proyek: ")
    format_run(r, bold=True)
    r = p_t1.add_run("Merancang dan mengimplementasikan prototipe Knowledge Management System berbasis web yang mampu mengotomasi dan mengintegrasikan seluruh alur pendataan hak, pemindaian digital, verifikasi kelayakan, dan pelaporan logistik seragam di Universitas Horizon Indonesia.")
    format_run(r)

    p_t2 = doc.add_paragraph()
    set_para_format(p_t2, space_after=2)
    r = p_t2.add_run("2. Tujuan Terkait Knowledge Management System (KMS):")
    format_run(r, bold=True)
    
    kms_tuj = [
        "Mengeliminasi fenomena sekat informasi (knowledge silo) antar-unit kerja dengan mewujudkan satu sumber kebenaran data terpusat (Single Source of Truth / SSOT).",
        "Mengkodifikasi kebiasaan operasional staf menjadi prosedur operasional standar (SOP) digital 5 fase yang terdokumentasi dan mudah dipelajari oleh panitia baru.",
        "Menyediakan instrumen pendukung keputusan manajerial bagi rektorat melalui visualisasi performa distribusi dan analisis margin laba kotor secara akurat."
    ]
    for bullet in kms_tuj:
        p_b = doc.add_paragraph()
        set_para_format(p_b, space_after=2, first_indent=0.25)
        r = p_b.add_run(f"•  {bullet}")
        format_run(r)

    p_t3 = doc.add_paragraph()
    set_para_format(p_t3, space_before=3, space_after=2)
    r = p_t3.add_run("3. Tujuan Khusus bagi Mahasiswa:")
    format_run(r, bold=True)
    
    mhs_tuj = [
        "Melatih dan memperdalam pemahaman mahasiswa Program Studi Sistem Informasi dalam membedah permasalahan nyata organisasi dan merancang arsitektur KMS berbasis teori ilmiah.",
        "Mengembangkan keterampilan rekayasa perangkat lunak enterprise modern serta melatih kemampuan komunikasi teknis melalui penyusunan karya ilmiah akademik yang terstruktur."
    ]
    for bullet in mhs_tuj:
        p_b = doc.add_paragraph()
        set_para_format(p_b, space_after=2, first_indent=0.25)
        r = p_b.add_run(f"•  {bullet}")
        format_run(r)

    # 1.5 Manfaat
    p_sub5 = doc.add_paragraph()
    set_para_format(p_sub5, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
    r = p_sub5.add_run("1.5 Manfaat")
    format_run(r, size_pt=12, bold=True)

    p_man_intro = doc.add_paragraph()
    set_para_format(p_man_intro, space_after=6)
    r = p_man_intro.add_run("Pelaksanaan proyek dan penyusunan laporan ini diharapkan mampu memberikan kontribusi positif yang ditinjau dari empat elemen pemangku kepentingan:")
    format_run(r)

    man_items = [
        ("Bagi Bidang Disiplin Ilmu (Program Studi Sistem Informasi): ", "Memberikan kontribusi kajian empiris dan studi kasus nyata mengenai integrasi teori Knowledge Management System dengan rekayasa web modern (MVC, algoritma persediaan FIFO, pengamanan basis data pessimistic locking, dan identifikasi QR Code) sebagai referensi pustaka bagi penelitian mahasiswa selanjutnya di Program Studi Sistem Informasi."),
        ("Bagi Masyarakat Luas dan Kelompok Mahasiswa: ", "Meningkatkan kepuasan layanan bagi mahasiswa baru melalui pemangkasan waktu antrean loket dari 10–15 menit menjadi di bawah 45 detik, menjamin ketepatan ukuran seragam saat fitting, serta memberikan transparansi alokasi hak barang secara mandiri via smartphone."),
        ("Bagi Institusi (Universitas Horizon Indonesia): ", "Mencegah kerugian finansial akibat penyerahan ganda atau penyusutan stok fisik, menjamin kesinambungan modal intelektual organisasi tanpa ketergantungan pada personil tertentu, menyediakan laporan keuangan akurat, serta memperkuat citra kampus bertata kelola TI modern."),
        ("Bagi Penulis atau Penyusun Laporan: ", "Memperdalam kompetensi dalam menganalisis siklus pengetahuan organisasi, mengasah keterampilan rekayasa sistem berskala enterprise, serta melatih daya pikir analitis, kritis, dan metodologis dalam merumuskan solusi berbasis data ilmiah.")
    ]
    for idx, (head, body) in enumerate(man_items, 1):
        p_m = doc.add_paragraph()
        set_para_format(p_m, space_after=3)
        r = p_m.add_run(f"{idx}. {head}")
        format_run(r, bold=True)
        r = p_m.add_run(body)
        format_run(r)

    # 1.6 Sistematika Penulisan
    p_sub6 = doc.add_paragraph()
    set_para_format(p_sub6, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
    r = p_sub6.add_run("1.6 Sistematika Penulisan")
    format_run(r, size_pt=12, bold=True)

    p_sis_intro = doc.add_paragraph()
    set_para_format(p_sis_intro, space_after=6)
    r = p_sis_intro.add_run("Penyusunan laporan proyek tugas besar ini disusun secara sistematis ke dalam empat bab utama sesuai ketentuan panduan akademik sebagai berikut:")
    format_run(r)

    sis_items = [
        ("BAB I PENDAHULUAN: ", "Memuat latar belakang permasalahan, rumusan masalah dalam bentuk pertanyaan terukur, batasan masalah penelitian, tujuan proyek (terkait judul, KMS, dan mahasiswa), manfaat bagi empat elemen pemangku kepentingan, serta sistematika penulisan laporan."),
        ("BAB II TINJAUAN PUSTAKA: ", "Menyajikan landasan teori ilmiah mencakup konsep Knowledge Management, klasifikasi tacit-explicit, karakteristik KMS, siklus konversi SECI Nonaka-Takeuchi, arsitektur repositori pengetahuan logistik, penelitian terdahulu, dan kerangka pemikiran."),
        ("BAB III PEMBAHASAN: ", "Merupakan bab inti yang memuat profil objek kampus, analisis kebutuhan pengetahuan, perancangan arsitektur basis pengetahuan dan pemodelan data (ERD 31 tabel), implementasi prototipe Horizon UniStock, serta dokumentasi pengujian dan evaluasi fungsional."),
        ("BAB IV PENUTUP: ", "Memuat simpulan yang merangkum hasil implementasi prototipe KMS dalam menjawab rumusan masalah proyek, serta saran-saran strategis bagi pengembangan lanjutan sistem di masa yang akan datang.")
    ]
    for head, body in sis_items:
        p_s = doc.add_paragraph()
        set_para_format(p_s, space_after=3)
        r = p_s.add_run(f"•  {head}")
        format_run(r, bold=True)
        r = p_s.add_run(body)
        format_run(r)

    # Save to Word file
    doc.save(FINAL_DOCX)
    print(f"[SUCCESS] Official Report DOCX saved: {FINAL_DOCX} ({os.path.getsize(FINAL_DOCX)} bytes)")
    return True

if __name__ == "__main__":
    build_docx()
