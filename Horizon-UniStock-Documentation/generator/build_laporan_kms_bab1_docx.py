# -*- coding: utf-8 -*-
"""
Generator Laporan Tugas Besar Mata Kuliah KMS (Format DOCX Word)
Fakultas Teknologi Informasi dan Komputer - Universitas Horizon Indonesia
Sesuai Ketentuan Format Resmi:
- Ukuran Kertas: A4
- Margin: 4 cm (kiri), 3 cm (kanan, atas, bawah)
- Font: Times New Roman 12pt (Judul Bab 14pt Bold)
- Spasi: 1.5 (Kata Pengantar 1.3 agar presisi 1 halaman)
- Paragraf: Rata kiri-kanan (justify), indent 1 cm (10 mm) di awal paragraf
- Halaman 1: Cover Resmi (Kelompok I Kelas SI-3-A, 6 Mahasiswa, Shape Segitiga Biru Resmi)
- Halaman 2: Kata Pengantar (Nomor Halaman Romawi 'i' di Bawah Tengah)
- Halaman 3: Daftar Isi (Nomor Halaman Romawi 'ii' di Bawah Tengah, Tab Leader Presisi)
- Halaman 4 dst: BAB I Pendahuluan (Semua Nomor Halaman Arab '1', '2', dst. di Bawah Tengah)
"""

import os
import docx
from docx.shared import Inches, Pt, RGBColor, Cm
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_TAB_ALIGNMENT, WD_TAB_LEADER
from docx.enum.section import WD_SECTION

BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
DOCS_DIR = os.path.join(BASE_DIR, "Horizon-UniStock-Documentation")
KMS_DIR = os.path.join(DOCS_DIR, "TUGAS BESAR MATKUL KMS")

COVER_DOCX = os.path.join(KMS_DIR, "cover.docx")
FINAL_DOCX = os.path.join(KMS_DIR, "LAPORAN TUGAS BESAR KNOWLEDGE MANAGEMENT SYSTEM KELOMPOK 1.docx")

def format_run(run, font_name="Times New Roman", size_pt=12, bold=False, italic=False, color=(0, 0, 0)):
    run.font.name = font_name
    run.font.size = Pt(size_pt)
    run.bold = bold
    run.italic = italic
    run.font.color.rgb = RGBColor(*color)

def set_para_format(p, align=WD_ALIGN_PARAGRAPH.JUSTIFY, line_spacing=1.5, space_after=4, space_before=0, first_indent=None, left_indent=None):
    p.alignment = align
    p.paragraph_format.line_spacing = line_spacing
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.space_before = Pt(space_before)
    if first_indent is not None:
        p.paragraph_format.first_line_indent = Inches(first_indent)
    if left_indent is not None:
        p.paragraph_format.left_indent = Inches(left_indent)

def add_footer_page_number(section, num_format='lowerRoman', start_num=1):
    section.header.is_linked_to_previous = False
    section.footer.is_linked_to_previous = False
    
    sectPr = section._sectPr
    pgNumType = sectPr.find(docx.oxml.ns.qn('w:pgNumType'))
    if pgNumType is None:
        pgNumType = docx.oxml.OxmlElement('w:pgNumType')
        sectPr.append(pgNumType)
    pgNumType.set(docx.oxml.ns.qn('w:fmt'), num_format)
    pgNumType.set(docx.oxml.ns.qn('w:start'), str(start_num))
    
    footer = section.footer
    footer.is_linked_to_previous = False
    p = footer.paragraphs[0]
    p.text = ""
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.left_indent = 0
    p.paragraph_format.right_indent = 0
    p.paragraph_format.first_line_indent = 0
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(0)
    p.paragraph_format.tab_stops.clear_all()
    
    r = p.add_run()
    format_run(r, font_name="Times New Roman", size_pt=11)
    
    fldChar1 = docx.oxml.OxmlElement('w:fldChar')
    fldChar1.set(docx.oxml.ns.qn('w:fldCharType'), 'begin')
    instrText = docx.oxml.OxmlElement('w:instrText')
    instrText.set(docx.oxml.ns.qn('xml:space'), 'preserve')
    instrText.text = "PAGE"
    fldChar2 = docx.oxml.OxmlElement('w:fldChar')
    fldChar2.set(docx.oxml.ns.qn('w:fldCharType'), 'separate')
    fldChar3 = docx.oxml.OxmlElement('w:fldChar')
    fldChar3.set(docx.oxml.ns.qn('w:fldCharType'), 'end')
    r._r.append(fldChar1)
    r._r.append(instrText)
    r._r.append(fldChar2)
    r._r.append(fldChar3)

def build_docx():
    print("=== BUILDING LAPORAN TUGAS BESAR KMS (DOCX) ===")
    
    # 1. Buka Dokumen Base (cover.docx) yang telah memiliki Shape Segitiga Biru Resmi
    doc = docx.Document(COVER_DOCX)
    
    # Pastikan Section 1 (Cover) tidak memiliki footer dan tidak menggeser halaman
    s1 = doc.sections[0]
    s1.page_width = Cm(21.0)
    s1.page_height = Cm(29.7)
    s1.top_margin = Cm(0.1)
    s1.bottom_margin = Cm(1.0)
    s1.left_margin = Cm(3.0)
    s1.right_margin = Cm(3.0)
    s1.header.is_linked_to_previous = False
    s1.footer.is_linked_to_previous = False
    for p in s1.footer.paragraphs:
        p.text = ""

    # Hapus paragraf kosong sisa template pada cover yang tidak mengandung drawing
    for p in list(doc.paragraphs):
        if len(p._p.xpath('.//w:drawing | .//w:pict')) == 0 and not p.text.strip():
            p._element.getparent().remove(p._element)

    # Format paragraf pada Cover agar tepat di tengah dan proporsional presisi 1 halaman
    for p in doc.paragraphs:
        txt = p.text.strip()
        if not txt:
            continue

        # Bersihkan automatic numbering template agar tidak terjadi duplikasi (1. 1. Nama)
        pPr = p._p.get_or_add_pPr()
        numPr = pPr.find(docx.oxml.ns.qn('w:numPr'))
        if numPr is not None:
            pPr.remove(numPr)

        if 'PERANCANGAN KNOWLEDGE MANAGEMENT SYSTEM' in p.text:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.left_indent = 0
            p.paragraph_format.right_indent = 0
            p.paragraph_format.first_line_indent = 0
            p.paragraph_format.space_before = Pt(28)
            p.paragraph_format.space_after = Pt(20)
            p.paragraph_format.line_spacing = 1.25
            for r in p.runs:
                format_run(r, size_pt=13, bold=True)
        elif 'Disusun oleh :' in p.text:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.left_indent = 0
            p.paragraph_format.right_indent = 0
            p.paragraph_format.first_line_indent = 0
            p.paragraph_format.space_before = Pt(45)
            p.paragraph_format.space_after = Pt(3)
            for r in p.runs:
                format_run(r, size_pt=12, bold=True)
        elif 'Kelompok I Kelas SI-3-A' in p.text:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.left_indent = 0
            p.paragraph_format.right_indent = 0
            p.paragraph_format.first_line_indent = 0
            p.paragraph_format.space_before = Pt(0)
            p.paragraph_format.space_after = Pt(10)
            for r in p.runs:
                format_run(r, size_pt=12, bold=True)
        elif any(txt.startswith(str(i) + '.') for i in range(1, 7)):
            p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            p.paragraph_format.left_indent = Cm(4.0)
            p.paragraph_format.right_indent = 0
            p.paragraph_format.first_line_indent = 0
            p.paragraph_format.space_before = Pt(0)
            p.paragraph_format.space_after = Pt(2)
            p.paragraph_format.line_spacing = 1.15
            p.paragraph_format.tab_stops.clear_all()
            p.paragraph_format.tab_stops.add_tab_stop(Cm(9.2), WD_TAB_ALIGNMENT.LEFT)
            for r in p.runs:
                format_run(r, font_name="Times New Roman", size_pt=11.5)
        elif 'Dosen Pengajar' in p.text:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.left_indent = 0
            p.paragraph_format.right_indent = 0
            p.paragraph_format.first_line_indent = 0
            p.paragraph_format.space_before = Pt(14)
            p.paragraph_format.space_after = Pt(0)
            for r in p.runs:
                format_run(r, size_pt=12, bold=True)
        elif 'PROGRAM STUDI SISTEM INFORMASI' in txt:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.left_indent = 0
            p.paragraph_format.right_indent = 0
            p.paragraph_format.first_line_indent = 0
            p.paragraph_format.space_before = Pt(150)
            p.paragraph_format.space_after = Pt(2)
            p.paragraph_format.line_spacing = 1.15
            for r in p.runs:
                format_run(r, size_pt=12, bold=True)
        elif any(k in txt for k in ['UNIVERSITAS HORIZON INDONESIA', 'KARAWANG']):
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.left_indent = 0
            p.paragraph_format.right_indent = 0
            p.paragraph_format.first_line_indent = 0
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            p.paragraph_format.line_spacing = 1.15
            for r in p.runs:
                format_run(r, size_pt=12, bold=True)
        else:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p.paragraph_format.left_indent = 0
            p.paragraph_format.right_indent = 0
            p.paragraph_format.first_line_indent = 0
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after = Pt(2)
            p.paragraph_format.line_spacing = 1.15
            for r in p.runs:
                format_run(r, font_name="Times New Roman", size_pt=r.font.size.pt if r.font.size else 12, bold=True)

    # ==========================================
    # 2. SECTION 2: KATA PENGANTAR & DAFTAR ISI
    # Margin Resmi: Kiri 4cm, Kanan 3cm, Atas 3cm, Bawah 3cm
    # Nomor Halaman Romawi: 'i', 'ii' di BAWAH TENGAH
    # ==========================================
    s2 = doc.add_section(WD_SECTION.NEW_PAGE)
    s2.page_width = Cm(21.0)
    s2.page_height = Cm(29.7)
    s2.top_margin = Cm(3.0)
    s2.bottom_margin = Cm(3.0)
    s2.left_margin = Cm(4.0)
    s2.right_margin = Cm(3.0)
    add_footer_page_number(s2, num_format='lowerRoman', start_num=1)
    
    # Judul KATA PENGANTAR
    p_kp_title = doc.add_paragraph()
    set_para_format(p_kp_title, align=WD_ALIGN_PARAGRAPH.CENTER, space_after=14)
    r = p_kp_title.add_run("KATA PENGANTAR")
    format_run(r, size_pt=14, bold=True)
    
    p1 = doc.add_paragraph()
    set_para_format(p1, line_spacing=1.3, space_after=3, first_indent=0.39) # 1 cm indent
    r = p1.add_run("Puji dan syukur penulis panjatkan ke hadirat Tuhan Yang Maha Esa atas limpahan rahmat, karunia, serta taufik-Nya, sehingga Laporan Proyek Tugas Besar Mata Kuliah ")
    format_run(r, size_pt=11.5)
    r = p1.add_run("Knowledge Management System")
    format_run(r, size_pt=11.5, italic=True)
    r = p1.add_run(" (KMS) ini dapat diselesaikan dengan baik dan tepat pada waktu yang telah ditetapkan.")
    format_run(r, size_pt=11.5)
    
    p2 = doc.add_paragraph()
    set_para_format(p2, line_spacing=1.3, space_after=3, first_indent=0.39)
    r = p2.add_run("Laporan proyek dengan judul ")
    format_run(r, size_pt=11.5)
    r = p2.add_run('"Perancangan Knowledge Management System untuk Meningkatkan Efektifitas Berbagi Pengetahuan pada Pengelolaan dan Distribusi Logistik Seragam Mahasiswa di Universitas Horizon Indonesia"')
    format_run(r, size_pt=11.5, bold=True)
    r = p2.add_run(" ini disusun guna memenuhi salah satu capaian pembelajaran mata kuliah Knowledge Management System pada Semester Ganjil Tahun Akademik 2026/2027 di Program Studi Sistem Informasi, Fakultas Teknologi Informasi dan Komputer, Universitas Horizon Indonesia.")
    format_run(r, size_pt=11.5)

    p3 = doc.add_paragraph()
    set_para_format(p3, line_spacing=1.3, space_after=3, first_indent=0.39)
    r = p3.add_run("Dalam proses perancangan sistem dan penyusunan laporan ini, penulis mendapatkan banyak bimbingan, arahan, saran, serta dukungan moril yang sangat berharga. Oleh karena itu, pada kesempatan ini penulis menyampaikan rasa hormat dan terima kasih yang setinggi-tingginya kepada:")
    format_run(r, size_pt=11.5)
    
    points = [
        ("Bapak ", "Arif Budimansyah Purba, M.Kom.", ", selaku Dosen Pengajar Mata Kuliah Knowledge Management System, yang telah dengan sabar memberikan bimbingan teoretis, arahan teknis, koreksi, serta motivasi berharga sepanjang perkuliahan;"),
        ("Bapak Dekan beserta segenap jajaran pimpinan ", "Fakultas Teknologi Informasi dan Komputer (FTIK)", " Universitas Horizon Indonesia;"),
        ("Bapak/Ibu Ketua ", "Program Studi Sistem Informasi", " Universitas Horizon Indonesia atas dukungan kurikulum dan kompetensi aplikatif yang diberikan;"),
        ("Pimpinan Direktorat Keuangan, Biro Administrasi Akademik dan Kemahasiswaan (BAAK), serta staf ", "Bagian Logistik", " Universitas Horizon Indonesia atas keterbukaan akses data operasional lapangan bagi studi kasus ini;"),
        ("Rekan-rekan mahasiswa Kelas SI-3-A, khususnya ", "anggota Kelompok I", ", atas kerja sama tim, dedikasi, dan soliditas yang luar biasa selama menyelesaikan proyek ini.")
    ]
    
    for idx, (pre, bold_text, post) in enumerate(points, 1):
        p_item = doc.add_paragraph()
        set_para_format(p_item, line_spacing=1.22, space_after=2, left_indent=0.3, first_indent=-0.3)
        r = p_item.add_run(f"{idx}.\t{pre}")
        format_run(r, size_pt=11)
        r = p_item.add_run(bold_text)
        format_run(r, size_pt=11, bold=True)
        r = p_item.add_run(post)
        format_run(r, size_pt=11)

    p4 = doc.add_paragraph()
    set_para_format(p4, line_spacing=1.3, first_indent=0.39, space_before=4, space_after=3)
    r = p4.add_run("Penulis menyadari bahwa laporan ini masih memiliki ruang penyempurnaan. Kritik dan saran konstruktif dari pembaca sangat diharapkan demi penyempurnaan karya di masa mendatang. Semoga laporan proyek ini dapat memberikan kontribusi nyata bagi pengembangan ilmu pengetahuan dan kemajuan institusi.")
    format_run(r, size_pt=11.5)

    p_sig = doc.add_paragraph()
    set_para_format(p_sig, align=WD_ALIGN_PARAGRAPH.RIGHT, space_before=10, space_after=0)
    r = p_sig.add_run("Karawang, Oktober 2026\n\n\n")
    format_run(r, size_pt=11)
    r = p_sig.add_run("Tim Penulis (Kelompok I)")
    format_run(r, size_pt=11, bold=True)

    # ==========================================
    # HALAMAN DAFTAR ISI (Masih dalam Section 2, Footer Lanjutan 'ii' di BAWAH TENGAH)
    # Menggunakan Native Right Tab Stop + Dot Leader Presisi
    # ==========================================
    doc.add_page_break()
    
    p_di_title = doc.add_paragraph()
    set_para_format(p_di_title, align=WD_ALIGN_PARAGRAPH.CENTER, space_after=16)
    r = p_di_title.add_run("DAFTAR ISI")
    format_run(r, size_pt=14, bold=True)

    toc_items = [
        ("KATA PENGANTAR", "i", True, 0),
        ("DAFTAR ISI", "ii", True, 0),
        ("BAB I PENDAHULUAN", "1", True, 0),
        ("1.1 Latar Belakang", "1", False, 1),
        ("1.2 Rumusan Masalah", "2", False, 1),
        ("1.3 Batasan Masalah", "2", False, 1),
        ("1.4 Tujuan", "3", False, 1),
        ("1.5 Manfaat", "4", False, 1),
        ("1.6 Sistematika Penulisan", "4", False, 1),
        ("BAB II TINJAUAN PUSTAKA", "5", True, 0),
        ("2.1 Konsep Knowledge Management", "5", False, 1),
        ("2.2 Bentuk dan Jenis Pengetahuan (Tacit dan Explicit)", "5", False, 1),
        ("2.3 Karakteristik Knowledge Management System (KMS)", "6", False, 1),
        ("2.4 Siklus Konversi Pengetahuan (Model SECI Nonaka-Takeuchi)", "6", False, 1),
        ("2.5 Arsitektur Repositori Pengetahuan dan Kerangka Kerja Logistik", "7", False, 1),
        ("BAB III PEMBAHASAN", "8", True, 0),
        ("3.1 Gambaran Umum Objek dan Kondisi Eksisting", "8", False, 1),
        ("3.2 Analisis Kebutuhan Pengetahuan dan Spesifikasi Sistem", "9", False, 1),
        ("3.3 Perancangan Arsitektur Basis Pengetahuan dan Sistem Web", "10", False, 1),
        ("3.4 Implementasi Prototype Sistem Horizon UniStock", "12", False, 1),
        ("3.5 Pengujian Fungsional dan Evaluasi Sistem", "14", False, 1),
        ("BAB IV PENUTUP", "16", True, 0),
        ("4.1 Simpulan", "16", False, 1),
        ("4.2 Saran", "16", False, 1),
        ("DAFTAR PUSTAKA", "18", True, 0),
    ]

    for title, page_str, is_bold, indent_level in toc_items:
        p_toc = doc.add_paragraph()
        p_toc.alignment = WD_ALIGN_PARAGRAPH.LEFT
        p_toc.paragraph_format.line_spacing = 1.2
        p_toc.paragraph_format.space_after = Pt(2.5)
        p_toc.paragraph_format.space_before = Pt(0)
        p_toc.paragraph_format.left_indent = Cm(0.6) if indent_level == 1 else 0
        p_toc.paragraph_format.tab_stops.clear_all()
        # Batas margin kanan: 21cm - 4cm (kiri) - 3cm (kanan) = 14cm
        p_toc.paragraph_format.tab_stops.add_tab_stop(Cm(14.0), WD_TAB_ALIGNMENT.RIGHT, WD_TAB_LEADER.DOTS)
        
        r_title = p_toc.add_run(title)
        format_run(r_title, size_pt=11, bold=is_bold)
        
        p_toc.add_run('\t')
        
        r_page = p_toc.add_run(page_str)
        format_run(r_page, size_pt=11, bold=is_bold)

    # ==========================================
    # 3. SECTION 3: BAB I PENDAHULUAN
    # Margin Resmi: Kiri 4cm, Kanan 3cm, Atas 3cm, Bawah 3cm
    # Nomor Halaman Arab: SEMUA di BAWAH TENGAH ('1', '2', '3', dst.)
    # ==========================================
    s3 = doc.add_section(WD_SECTION.NEW_PAGE)
    s3.page_width = Cm(21.0)
    s3.page_height = Cm(29.7)
    s3.top_margin = Cm(3.0)
    s3.bottom_margin = Cm(3.0)
    s3.left_margin = Cm(4.0)
    s3.right_margin = Cm(3.0)
    add_footer_page_number(s3, num_format='decimal', start_num=1)
    
    # Judul BAB I PENDAHULUAN
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
    r = p_lb1.add_run("Perkembangan teknologi informasi era Revolusi Industri 4.0 dan Society 5.0 mendorong institusi perguruan tinggi menempatkan pengetahuan (")
    format_run(r)
    r = p_lb1.add_run("knowledge")
    format_run(r, italic=True)
    r = p_lb1.add_run(") sebagai aset intelektual strategis. Berdasarkan teori spiral penciptaan pengetahuan Nonaka dan Takeuchi (1995) serta kerangka ")
    format_run(r)
    r = p_lb1.add_run("Knowledge Management System")
    format_run(r, italic=True)
    r = p_lb1.add_run(" (KMS) oleh Tiwana (2002), transformasi pengetahuan implisit (")
    format_run(r)
    r = p_lb1.add_run("tacit")
    format_run(r, italic=True)
    r = p_lb1.add_run(") ke eksplisit (")
    format_run(r)
    r = p_lb1.add_run("explicit")
    format_run(r, italic=True)
    r = p_lb1.add_run(") melalui model SECI (")
    format_run(r)
    r = p_lb1.add_run("Socialization, Externalization, Combination, Internalization")
    format_run(r, italic=True)
    r = p_lb1.add_run(") merupakan fondasi mutlak guna mencegah risiko kehilangan modal intelektual (")
    format_run(r)
    r = p_lb1.add_run("knowledge loss")
    format_run(r, italic=True)
    r = p_lb1.add_run(") dan sekat informasi operasional (")
    format_run(r)
    r = p_lb1.add_run("knowledge silo")
    format_run(r, italic=True)
    r = p_lb1.add_run(").")
    format_run(r)

    p_lb2 = doc.add_paragraph()
    set_para_format(p_lb2, first_indent=0.39)
    r = p_lb2.add_run("Pada tata kelola operasional distribusi seragam mahasiswa baru di Universitas Horizon Indonesia, pengelolaan data hak barang (")
    format_run(r)
    r = p_lb2.add_run("entitlements")
    format_run(r, italic=True)
    r = p_lb2.add_run("), kuota angkatan, dan penanganan kasus khusus selama ini bertumpu pada ingatan lisan staf atau lembar kerja (")
    format_run(r)
    r = p_lb2.add_run("spreadsheet")
    format_run(r, italic=True)
    r = p_lb2.add_run(") lokal yang tidak terintegrasi. Verifikasi manual bukti pembayaran kasir kerap menimbulkan antrean panjang ratusan mahasiswa, kekeliruan spesifikasi barang, serta kerentanan pembagian ganda (")
    format_run(r)
    r = p_lb2.add_run("double-claim")
    format_run(r, italic=True)
    r = p_lb2.add_run("). Selain itu, belum diterapkannya metode persediaan ")
    format_run(r)
    r = p_lb2.add_run("First-In, First-Out")
    format_run(r, italic=True)
    r = p_lb2.add_run(" (FIFO) menyulitkan perhitungan Harga Pokok Penjualan (HPP) dan akurasi rekonsiliasi audit fisik (")
    format_run(r)
    r = p_lb2.add_run("stock opname")
    format_run(r, italic=True)
    r = p_lb2.add_run(") gudang di akhir periode.")
    format_run(r)

    p_lb3 = doc.add_paragraph()
    set_para_format(p_lb3, first_indent=0.39)
    r = p_lb3.add_run('Menjawab kebutuhan tersebut, dirancang platform web ')
    format_run(r)
    r = p_lb3.add_run("Knowledge Management System")
    format_run(r, italic=True)
    r = p_lb3.add_run(' "Horizon UniStock" sebagai repositori pengetahuan terpusat sekaligus instrumen kendali distribusi logistik terpadu. Sistem ini mengintegrasikan identifikasi digital QR Code mahasiswa, mekanisme ')
    format_run(r)
    r = p_lb3.add_run("pessimistic row locking")
    format_run(r, italic=True)
    r = p_lb3.add_run(" basis data anti-klaim ganda, otomasi mutasi persediaan berbasis FIFO, portal pemilihan ukuran mandiri, serta dasbor analitis pelaporan manajerial. Berdasarkan urgensi pemecahan masalah tersebut, disusun laporan proyek tugas besar berjudul ")
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
    set_para_format(p_rm_intro, first_indent=0.39, space_after=6)
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
        set_para_format(p_rm, space_after=3, left_indent=0.3, first_indent=-0.3)
        r = p_rm.add_run(f"{idx}.\t{item}")
        format_run(r)

    # 1.3 Batasan Masalah
    p_sub3 = doc.add_paragraph()
    set_para_format(p_sub3, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
    r = p_sub3.add_run("1.3 Batasan Masalah")
    format_run(r, size_pt=12, bold=True)

    p_bm_intro = doc.add_paragraph()
    set_para_format(p_bm_intro, first_indent=0.39, space_after=6)
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
        set_para_format(p_bm, space_after=3, left_indent=0.3, first_indent=-0.3)
        r = p_bm.add_run(f"{idx}.\t{head}")
        format_run(r, bold=True)
        r = p_bm.add_run(body)
        format_run(r)

    # 1.4 Tujuan
    p_sub4 = doc.add_paragraph()
    set_para_format(p_sub4, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
    r = p_sub4.add_run("1.4 Tujuan")
    format_run(r, size_pt=12, bold=True)

    p_tuj_intro = doc.add_paragraph()
    set_para_format(p_tuj_intro, first_indent=0.39, space_after=6)
    r = p_tuj_intro.add_run("Tujuan dari penyusunan laporan proyek ini adalah pernyataan terukur yang mencerminkan capaian yang hendak dihasilkan, meliputi:")
    format_run(r)

    p_t1 = doc.add_paragraph()
    set_para_format(p_t1, space_after=3, left_indent=0.3, first_indent=-0.3)
    r = p_t1.add_run("1.\tTujuan Terkait Judul Proyek: ")
    format_run(r, bold=True)
    r = p_t1.add_run("Merancang dan mengimplementasikan prototipe Knowledge Management System berbasis web yang mampu mengotomasi dan mengintegrasikan seluruh alur pendataan hak, pemindaian digital, verifikasi kelayakan, dan pelaporan logistik seragam di Universitas Horizon Indonesia.")
    format_run(r)

    p_t2 = doc.add_paragraph()
    set_para_format(p_t2, space_after=2, left_indent=0.3, first_indent=-0.3)
    r = p_t2.add_run("2.\tTujuan Terkait Knowledge Management System (KMS):")
    format_run(r, bold=True)
    
    kms_tuj = [
        "Mengeliminasi fenomena sekat informasi (knowledge silo) antar-unit kerja dengan mewujudkan satu sumber kebenaran data terpusat (Single Source of Truth / SSOT).",
        "Mengkodifikasi kebiasaan operasional staf menjadi prosedur operasional standar (SOP) digital 5 fase yang terdokumentasi dan mudah dipelajari oleh panitia baru.",
        "Menyediakan instrumen pendukung keputusan manajerial bagi rektorat melalui visualisasi performa distribusi dan analisis margin laba kotor secara akurat."
    ]
    for bullet in kms_tuj:
        p_b = doc.add_paragraph()
        set_para_format(p_b, space_after=2, left_indent=0.6, first_indent=-0.2)
        r = p_b.add_run("•\t" + bullet)
        format_run(r)

    p_t3 = doc.add_paragraph()
    set_para_format(p_t3, space_before=3, space_after=2, left_indent=0.3, first_indent=-0.3)
    r = p_t3.add_run("3.\tTujuan Khusus bagi Mahasiswa:")
    format_run(r, bold=True)
    
    mhs_tuj = [
        "Melatih dan memperdalam pemahaman mahasiswa Program Studi Sistem Informasi dalam membedah permasalahan nyata organisasi dan merancang arsitektur KMS berbasis teori ilmiah.",
        "Mengembangkan keterampilan rekayasa perangkat lunak enterprise modern serta melatih kemampuan komunikasi teknis melalui penyusunan karya ilmiah akademik yang terstruktur."
    ]
    for bullet in mhs_tuj:
        p_b = doc.add_paragraph()
        set_para_format(p_b, space_after=2, left_indent=0.6, first_indent=-0.2)
        r = p_b.add_run("•\t" + bullet)
        format_run(r)

    # 1.5 Manfaat
    p_sub5 = doc.add_paragraph()
    set_para_format(p_sub5, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
    r = p_sub5.add_run("1.5 Manfaat")
    format_run(r, size_pt=12, bold=True)

    p_man_intro = doc.add_paragraph()
    set_para_format(p_man_intro, first_indent=0.39, space_after=6)
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
        set_para_format(p_m, space_after=3, left_indent=0.3, first_indent=-0.3)
        r = p_m.add_run(f"{idx}.\t{head}")
        format_run(r, bold=True)
        r = p_m.add_run(body)
        format_run(r)

    # 1.6 Sistematika Penulisan
    p_sub6 = doc.add_paragraph()
    set_para_format(p_sub6, align=WD_ALIGN_PARAGRAPH.LEFT, space_before=14, space_after=6)
    r = p_sub6.add_run("1.6 Sistematika Penulisan")
    format_run(r, size_pt=12, bold=True)

    p_sis_intro = doc.add_paragraph()
    set_para_format(p_sis_intro, first_indent=0.39, space_after=6)
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
        set_para_format(p_s, space_after=3, left_indent=0.4, first_indent=-0.2)
        r = p_s.add_run("•\t" + head)
        format_run(r, bold=True)
        r = p_s.add_run(body)
        format_run(r)

    # Save to Word file
    doc.save(FINAL_DOCX)
    print(f"[SUCCESS] Official Report DOCX saved: {FINAL_DOCX} ({os.path.getsize(FINAL_DOCX)} bytes)")

    # Bersihkan file PDF lama dan nama lama jika ada sesuai instruksi (jadikan DOCS saja jangan PDF)
    old_files = [
        os.path.join(KMS_DIR, "Laporan-Tugas-Besar-KMS-Bab-1.pdf"),
        os.path.join(KMS_DIR, "Laporan-Tugas-Besar-KMS-Bab-1.docx"),
        os.path.join(KMS_DIR, "Laporan-Tugas-Besar-KMS-kelompok-1pdf")
    ]
    for old_file in old_files:
        if os.path.exists(old_file):
            try:
                os.remove(old_file)
                print(f"[CLEANUP] Removed old file: {old_file}")
            except Exception as e:
                print(f"[WARNING] Could not remove {old_file}: {e}")

    return True

if __name__ == "__main__":
    build_docx()
