import os
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.dml.color import RGBColor
from pptx.enum.shapes import MSO_SHAPE

# 1. Initialize 16:9 Presentation
prs = Presentation()
prs.slide_width = Inches(13.333)
prs.slide_height = Inches(7.5)
blank_layout = prs.slide_layouts[6]

# Brand Colors
COLOR_PRIMARY = RGBColor(152, 4, 22)      # Maroon Horizon (#980416)
COLOR_PRIMARY_DARK = RGBColor(90, 2, 13) # Deep Maroon (#5A020D)
COLOR_SECONDARY = RGBColor(15, 23, 42)    # Dark Slate (#0F172A)
COLOR_TEXT_MAIN = RGBColor(30, 41, 59)    # Slate 800 (#1E293B)
COLOR_TEXT_MUTED = RGBColor(100, 116, 139) # Slate 500 (#64748B)
COLOR_WHITE = RGBColor(255, 255, 255)
COLOR_BG_CARD = RGBColor(248, 250, 252)   # Slate 50 (#F8FAFC)
COLOR_CARD_BORDER = RGBColor(226, 232, 240) # Slate 200 (#E2E8F0)
COLOR_LIGHT_RED = RGBColor(254, 242, 242)
COLOR_LIGHT_GREEN = RGBColor(240, 253, 244)
COLOR_GREEN_TEXT = RGBColor(21, 128, 61)
COLOR_LIGHT_AMBER = RGBColor(254, 243, 199)
COLOR_AMBER_TEXT = RGBColor(146, 64, 14)
COLOR_LIGHT_BLUE = RGBColor(224, 242, 254)
COLOR_BLUE_TEXT = RGBColor(7, 89, 133)

def set_shape_flat(shape, fill_color, border_color=None, border_width=1):
    shape.fill.solid()
    shape.fill.fore_color.rgb = fill_color
    if border_color:
        shape.line.color.rgb = border_color
        shape.line.width = Pt(border_width)
    else:
        shape.line.fill.background()

def add_header(slide, title_text, category_badge="UNISTOCK CAMPUS SUPPLY CHAIN BLUEPRINT"):
    # Category badge
    badge_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.4), Inches(8), Inches(0.4))
    tf_badge = badge_box.text_frame
    tf_badge.word_wrap = True
    p_b = tf_badge.paragraphs[0]
    p_b.text = category_badge.upper()
    p_b.font.size = Pt(9.5)
    p_b.font.bold = True
    p_b.font.color.rgb = COLOR_PRIMARY

    # Title
    title_box = slide.shapes.add_textbox(Inches(0.8), Inches(0.7), Inches(11.5), Inches(0.7))
    tf_title = title_box.text_frame
    tf_title.word_wrap = True
    p_t = tf_title.paragraphs[0]
    p_t.text = title_text
    p_t.font.size = Pt(22)
    p_t.font.bold = True
    p_t.font.color.rgb = COLOR_SECONDARY

    # Red accent bar
    line = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.8), Inches(1.42), Inches(1.2), Inches(0.04))
    set_shape_flat(line, COLOR_PRIMARY)

def add_footer(slide, current_page, total_pages=10):
    # Footer text
    foot_box = slide.shapes.add_textbox(Inches(0.8), Inches(6.9), Inches(10), Inches(0.35))
    tf = foot_box.text_frame
    p = tf.paragraphs[0]
    p.text = "Universitas Horizon Indonesia  |  UniStock Closed-Loop SCM Architecture"
    p.font.size = Pt(9)
    p.font.color.rgb = COLOR_TEXT_MUTED

    # Page number
    page_box = slide.shapes.add_textbox(Inches(11.5), Inches(6.9), Inches(1), Inches(0.35))
    tf_p = page_box.text_frame
    p_p = tf_p.paragraphs[0]
    p_p.alignment = PP_ALIGN.RIGHT
    p_p.text = f"{current_page} / {total_pages}"
    p_p.font.size = Pt(9)
    p_p.font.bold = True
    p_p.font.color.rgb = COLOR_PRIMARY

# ==============================================================================
# SLIDE 1: Title Slide (Cover)
# ==============================================================================
s1 = prs.slides.add_slide(blank_layout)

# Background gradient dark maroon
bg_s1 = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(13.333), Inches(7.5))
set_shape_flat(bg_s1, COLOR_PRIMARY_DARK)

# Inner elegant card
card_s1 = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.8), Inches(11.733), Inches(5.9))
set_shape_flat(card_s1, COLOR_WHITE, COLOR_CARD_BORDER)

# Title Text inside card
title_box = s1.shapes.add_textbox(Inches(1.2), Inches(1.2), Inches(11), Inches(2.2))
tf = title_box.text_frame
tf.word_wrap = True

p_badge = tf.paragraphs[0]
p_badge.text = "BLUEPRINT ARSITEKTUR SCM & LOGISTIK KAMPUS"
p_badge.font.size = Pt(11)
p_badge.font.bold = True
p_badge.font.color.rgb = COLOR_PRIMARY

p_main = tf.add_paragraph()
p_main.text = "Transformasi Distribusi Logistik Kampus\nPendekatan Closed-Loop SCM"
p_main.font.size = Pt(28)
p_main.font.bold = True
p_main.font.color.rgb = COLOR_SECONDARY
p_main.space_before = Pt(8)

p_sub = tf.add_paragraph()
p_sub.text = "Studi Kasus: Implementasi Sistem Distribusi Terintegrasi UniStock"
p_sub.font.size = Pt(14)
p_sub.font.color.rgb = COLOR_PRIMARY
p_sub.space_before = Pt(6)

# 4 Bottom Highlight Cards
highlights = [
    ("KONTEKS INDUSTRI", "Logistik Pendidikan", "Distribusi seragam, sepatu, & medical kits tanpa perantara.", COLOR_LIGHT_BLUE, COLOR_BLUE_TEXT),
    ("MODEL BISNIS", "Closed-Loop SCM", "Direct Inbound-to-Outbound: dari pabrik vendor langsung ke mahasiswa.", COLOR_LIGHT_GREEN, COLOR_GREEN_TEXT),
    ("MASALAH UTAMA", "Chaos Hari-H", "Mismatch ukuran pakaian, antrean membeludak, & kerugian dead-stock.", COLOR_LIGHT_RED, COLOR_PRIMARY),
    ("EKOSISTEM SOLUSI", "UniStock System", "Menghubungkan Hulu (Vendor), Gudang, Keuangan, hingga Mahasiswa.", COLOR_LIGHT_AMBER, COLOR_AMBER_TEXT),
]

card_w = Inches(2.55)
card_h = Inches(2.0)
card_y = Inches(3.8)

for i, (kicker, title, desc, bg_c, txt_c) in enumerate(highlights):
    cx = Inches(1.2) + i * Inches(2.75)
    c_shape = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, cx, card_y, card_w, card_h)
    set_shape_flat(c_shape, bg_c, COLOR_CARD_BORDER)
    
    tb = s1.shapes.add_textbox(cx + Inches(0.12), card_y + Inches(0.12), card_w - Inches(0.24), card_h - Inches(0.24))
    tf_c = tb.text_frame
    tf_c.word_wrap = True
    
    p1 = tf_c.paragraphs[0]
    p1.text = kicker
    p1.font.size = Pt(8.5)
    p1.font.bold = True
    p1.font.color.rgb = txt_c
    
    p2 = tf_c.add_paragraph()
    p2.text = title
    p2.font.size = Pt(12)
    p2.font.bold = True
    p2.font.color.rgb = COLOR_SECONDARY
    p2.space_before = Pt(4)
    
    p3 = tf_c.add_paragraph()
    p3.text = desc
    p3.font.size = Pt(8.5)
    p3.font.color.rgb = COLOR_TEXT_MUTED
    p3.space_before = Pt(4)

# Notes
s1.notes_slide.notes_text_frame.text = (
    "Selamat pagi/siang rekan-rekan dan jajaran pimpinan.\n"
    "Presentasi ini membedah arsitektur dan ekosistem rantai pasok logistik di lingkungan pendidikan tinggi: UniStock.\n"
    "Distribusi seragam dan perlengkapan perkuliahan mahasiswa baru adalah tantangan operasional yang masif setiap tahunnya. "
    "Tanpa sistem tertutup (closed-loop), universitas menanggung risiko dead stock dan kerugian finansial yang signifikan."
)

# ==============================================================================
# SLIDE 2: Struktur Supply Chain: Pemetaan Aktor & Aliran
# ==============================================================================
s2 = prs.slides.add_slide(blank_layout)
add_header(s2, "Struktur Supply Chain: Pemetaan Aktor & Aliran")
add_footer(s2, 2)

# Flow Top Card (Aliran Fisik)
flow_box = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.7), Inches(11.733), Inches(2.2))
set_shape_flat(flow_box, COLOR_BG_CARD, COLOR_CARD_BORDER)

tb_fl = s2.shapes.add_textbox(Inches(1.0), Inches(1.8), Inches(11.3), Inches(0.4))
p_fl = tb_fl.text_frame.paragraphs[0]
p_fl.text = "ALIRAN BARANG FISIK (LINEAR SUPPLY CHAIN)"
p_fl.font.size = Pt(11)
p_fl.font.bold = True
p_fl.font.color.rgb = COLOR_PRIMARY

actors = [
    ("1. VENDOR / HULU", "Pabrik Garmen & Sepatu\nProduksi paket seragam berkode.", COLOR_LIGHT_RED, COLOR_PRIMARY),
    ("2. GUDANG (INBOUND)", "Logistik Kampus\nPenerimaan, Batch FIFO & Saldo.", COLOR_LIGHT_BLUE, COLOR_BLUE_TEXT),
    ("3. KAMPUS (OUTBOUND)", "Staff Posko Hari-H\nMeja layanan scan QR & serah terima.", COLOR_LIGHT_AMBER, COLOR_AMBER_TEXT),
    ("4. MAHASISWA (HILIR)", "Penerima Akhir\nPengguna pakaian & sepatu.", COLOR_LIGHT_GREEN, COLOR_GREEN_TEXT),
]

for i, (act_title, act_desc, bg_act, txt_act) in enumerate(actors):
    ax = Inches(1.0) + i * Inches(2.85)
    ay = Inches(2.25)
    aw = Inches(2.55)
    ah = Inches(1.35)
    ashape = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, ax, ay, aw, ah)
    set_shape_flat(ashape, bg_act, COLOR_CARD_BORDER)
    
    atb = s2.shapes.add_textbox(ax + Inches(0.1), ay + Inches(0.1), aw - Inches(0.2), ah - Inches(0.2))
    atf = atb.text_frame
    atf.word_wrap = True
    ap1 = atf.paragraphs[0]
    ap1.text = act_title
    ap1.font.size = Pt(10)
    ap1.font.bold = True
    ap1.font.color.rgb = txt_act
    
    ap2 = atf.add_paragraph()
    ap2.text = act_desc
    ap2.font.size = Pt(8.5)
    ap2.font.color.rgb = COLOR_TEXT_MAIN
    ap2.space_before = Pt(3)

# Bottom Integration Details (Aliran Data)
bot_box = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(4.1), Inches(11.733), Inches(2.5))
set_shape_flat(bot_box, COLOR_WHITE, COLOR_PRIMARY, border_width=2)

tb_bot = s2.shapes.add_textbox(Inches(1.1), Inches(4.25), Inches(11.1), Inches(2.1))
tf_b = tb_bot.text_frame
tf_b.word_wrap = True

pb_title = tf_b.paragraphs[0]
pb_title.text = "ALIRAN DATA & INFORMASI TERSINKRONISASI (CLOSED-LOOP ENGINE)"
pb_title.font.size = Pt(12)
pb_title.font.bold = True
pb_title.font.color.rgb = COLOR_PRIMARY

points = [
    ("Verifikasi Kelayakan Finansial (Eligibility)", "Bagian Keuangan men-sinkronisasi data pelunasan mahasiswa langsung ke sistem. Mahasiswa yang belum lunas tidak dapat melakukan klaim barang."),
    ("Manajemen Hak Kuota (Entitlement Matrix)", "Setiap mahasiswa memiliki kuota barang terprogram berdasarkan Student Level (Y1S1), Fakultas, dan Program Studi (contoh: Y1S1FKIP01)."),
    ("Prinsip 'Data Mengunci Fisik'", "Barang fisik di posko TIDAK DAPAT diserahkan sebelum staf memindai QR code mahasiswa dan sistem mengeksekusi validasi secara atomik."),
]

for title, desc in points:
    p_item = tf_b.add_paragraph()
    p_item.text = f"• {title}: {desc}"
    p_item.font.size = Pt(9.5)
    p_item.font.color.rgb = COLOR_TEXT_MAIN
    p_item.space_before = Pt(6)

s2.notes_slide.notes_text_frame.text = (
    "Fokus utama slide ini: Pemisahan tegas antara Aliran Fisik dan Aliran Data.\n"
    "Pada sistem konvensional, barang fisik bergerak mendahului data, sehingga catatan di posko selalu terlambat.\n"
    "UniStock membalik logika tersebut: Data harus valid dan memverifikasi terlebih dahulu sebelum barang fisik dilepaskan dari meja staf."
)

# ==============================================================================
# SLIDE 3: Analisis Akar Masalah (Diagnostic Matrix)
# ==============================================================================
s3 = prs.slides.add_slide(blank_layout)
add_header(s3, "Analisis Akar Masalah: Mengapa Sistem Manual Gagal?")
add_footer(s3, 3)

# Table for Diagnostic Matrix
rows = 4
cols = 3
left = Inches(0.8)
top = Inches(1.7)
width = Inches(11.733)
height = Inches(4.8)

table_shape = s3.shapes.add_table(rows, cols, left, top, width, height)
table = table_shape.table
table.columns[0].width = Inches(3.2)
table.columns[1].width = Inches(4.5)
table.columns[2].width = Inches(4.033)

headers = ["Masalah (Gejala Lapangan)", "Akar Masalah Sistemik", "Dampak Bisnis Terukur"]
for idx, text in enumerate(headers):
    cell = table.cell(0, idx)
    cell.fill.solid()
    cell.fill.fore_color.rgb = COLOR_SECONDARY
    cell.vertical_anchor = MSO_ANCHOR.MIDDLE
    p = cell.text_frame.paragraphs[0]
    p.text = text
    p.font.size = Pt(11)
    p.font.bold = True
    p.font.color.rgb = COLOR_WHITE

matrix_data = [
    (
        "Ketidaksesuaian Stok vs Kebutuhan\n(Mismatch & Dead-Stock)",
        "Tidak ada profiling ukuran di awal. Pengadaan ke vendor pabrik hanya menggunakan tebakan atau rasio historis angkatan lalu.",
        "• Penumpukan dead stock pada ukuran ekstrem.\n• Kekurangan stok ukuran populer saat hari-H.\n• Biaya pergudangan dan modal mati membengkak."
    ),
    (
        "Bottleneck & Antrean Membeludak\n(Chaotic Distribution)",
        "Validasi bukti transfer bank dan cek kuota hak seragam dicek manual melalui tumpukan lembar kertas atau rekapan spreadsheet terpisah.",
        "• Waktu layan lambat (3-5 menit/mahasiswa).\n• Mahasiswa berdesakan di loket pembagian.\n• Rentan salah serah barang & tertukar ukuran."
    ),
    (
        "Susut Stok Tanpa Jejak Audit\n(Financial Leakage)",
        "Nihil sistem First-In-First-Out (FIFO). Opname fisik tahunan tidak terhubung ke jurnal mutasi otomatis pembukuan akuntansi.",
        "• Selisih barang hilang tidak terlacak penanggungjawabnya.\n• Valuasi HPP/COGS tidak akurat.\n• Laporan laba/rugi per periode terdistorsi."
    )
]

for row_idx, row_data in enumerate(matrix_data, start=1):
    for col_idx, cell_value in enumerate(row_data):
        cell = table.cell(row_idx, col_idx)
        cell.fill.solid()
        if row_idx % 2 == 1:
            cell.fill.fore_color.rgb = COLOR_WHITE
        else:
            cell.fill.fore_color.rgb = COLOR_BG_CARD
        cell.vertical_anchor = MSO_ANCHOR.MIDDLE
        p = cell.text_frame.paragraphs[0]
        p.text = cell_value
        p.font.size = Pt(9.5)
        p.font.color.rgb = COLOR_TEXT_MAIN
        if col_idx == 0:
            p.font.bold = True
            p.font.color.rgb = COLOR_PRIMARY

s3.notes_slide.notes_text_frame.text = (
    "Soroti 3 pilar kegagalan sistem manual: Mismatch, Bottleneck, dan Financial Leakage.\n"
    "Masalah ini bukan sekadar ketidaksiapan staf posko, melainkan kelemahan struktural sistem informasi yang terfragmentasi.\n"
    "Ketika pengadaan dilakukan dengan 'tebakan', institusi hampir pasti merugi karena modal tertahan pada ukuran yang tidak terpakai."
)

# ==============================================================================
# SLIDE 4: Pemetaan Aliran Informasi (As-Is State)
# ==============================================================================
s4 = prs.slides.add_slide(blank_layout)
add_header(s4, "Pemetaan Aliran Informasi: As-Is State (Kondisi Lama)")
add_footer(s4, 4)

# 4 Fractured Cards
cards_data = [
    ("MAHASISWA", "Butuh jadwal pasti & kepastian seragam", "Data ukuran diinput via Google Form acak; tidak terstandarisasi. Sering terjadi double-submit atau lupa ukuran yang dipilih.", COLOR_LIGHT_RED, COLOR_PRIMARY),
    ("KEUANGAN", "Verifikasi bukti bayar manual", "Data pelunasan cicilan menumpuk di file Excel terpisah; tidak ter-sinkronisasi real-time ke posko pembagian hari-H.", COLOR_LIGHT_AMBER, COLOR_AMBER_TEXT),
    ("GUDANG & STAFF", "Pelayanan serba kertas / sheet", "Mencatat serah terima secara manual di checklist kertas; rawan kecurangan double-claim dan pencatatan sisa stok bias.", COLOR_LIGHT_BLUE, COLOR_BLUE_TEXT),
    ("PENGADAAN (VENDOR)", "Produksi berdasarkan perkiraan", "Order barang ke pabrik hanya estimasi kasar tanpa data riil profil mahasiswa; lead time lama dan sulit retur.", COLOR_LIGHT_GREEN, COLOR_GREEN_TEXT),
]

for i, (head, sub, desc, bg_c, txt_c) in enumerate(cards_data):
    cx = Inches(0.8) + i * Inches(2.95)
    cy = Inches(1.8)
    cw = Inches(2.78)
    ch = Inches(3.4)
    cshape = s4.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, cx, cy, cw, ch)
    set_shape_flat(cshape, bg_c, COLOR_CARD_BORDER)
    
    tb = s4.shapes.add_textbox(cx + Inches(0.15), cy + Inches(0.15), cw - Inches(0.3), ch - Inches(0.3))
    tf = tb.text_frame
    tf.word_wrap = True
    
    p1 = tf.paragraphs[0]
    p1.text = head
    p1.font.size = Pt(13)
    p1.font.bold = True
    p1.font.color.rgb = txt_c
    
    p2 = tf.add_paragraph()
    p2.text = sub
    p2.font.size = Pt(9.5)
    p2.font.bold = True
    p2.font.color.rgb = COLOR_SECONDARY
    p2.space_before = Pt(6)
    
    p3 = tf.add_paragraph()
    p3.text = desc
    p3.font.size = Pt(9)
    p3.font.color.rgb = COLOR_TEXT_MAIN
    p3.space_before = Pt(8)

# Warning Banner at bottom
warn_box = s4.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(5.4), Inches(11.733), Inches(1.1))
set_shape_flat(warn_box, COLOR_LIGHT_RED, COLOR_PRIMARY, border_width=1.5)

tb_w = s4.shapes.add_textbox(Inches(1.0), Inches(5.45), Inches(11.3), Inches(1.0))
tf_w = tb_w.text_frame
tf_w.word_wrap = True
pw1 = tf_w.paragraphs[0]
pw1.text = "KESIMPULAN AUDIT AS-IS:"
pw1.font.size = Pt(10)
pw1.font.bold = True
pw1.font.color.rgb = COLOR_PRIMARY

pw2 = tf_w.add_paragraph()
pw2.text = "Informasi bersifat silo, terlambat, dan terputus di setiap titik serah-terima (handoff points). Mahasiswa menjadi korban antrean berulang, sementara institusi mengalami kebocoran biaya persediaan."
pw2.font.size = Pt(9.5)
pw2.font.color.rgb = COLOR_SECONDARY

s4.notes_slide.notes_text_frame.text = (
    "Visualisasikan silo antar departemen: Keuangan mengunci data di komputernya, Gudang mencatat di sheet sendiri, dan Mahasiswa menunggu tanpa kepastian.\n"
    "Ketiadaan jembatan data terpadu adalah akar dari semua inefisiensi ini."
)

# ==============================================================================
# SLIDE 5: Rekomendasi Solusi: Ekosistem Terintegrasi UniStock
# ==============================================================================
s5 = prs.slides.add_slide(blank_layout)
add_header(s5, "Rekomendasi Solusi: Ekosistem Terintegrasi UniStock")
add_footer(s5, 5)

# Table for Solution Mapping
rows = 4
cols = 4
left = Inches(0.8)
top = Inches(1.7)
width = Inches(11.733)
height = Inches(4.8)

table_shape = s5.shapes.add_table(rows, cols, left, top, width, height)
table = table_shape.table
table.columns[0].width = Inches(2.6)
table.columns[1].width = Inches(2.8)
table.columns[2].width = Inches(3.8)
table.columns[3].width = Inches(2.533)

headers = ["Masalah Utama", "Modul UniStock", "Logika & Komponen Teknis", "Manfaat Terukur"]
for idx, text in enumerate(headers):
    cell = table.cell(0, idx)
    cell.fill.solid()
    cell.fill.fore_color.rgb = COLOR_PRIMARY
    cell.vertical_anchor = MSO_ANCHOR.MIDDLE
    p = cell.text_frame.paragraphs[0]
    p.text = text
    p.font.size = Pt(10.5)
    p.font.bold = True
    p.font.color.rgb = COLOR_WHITE

sol_data = [
    (
        "Mismatch Stok &\nDead-Stock",
        "Demand Management\n(SCM Engine)",
        "Portal Pengisian Ukuran mandiri (student_size_profiles) dikunci periode event. Normalisasi otomatis label ke kode SKU via StudentSizeService.",
        "Presisi Pengadaan 100%\nBerdasarkan data ukuran riil mahasiswa terverifikasi."
    ),
    (
        "Bottleneck Distribusi &\nDouble-Claim",
        "Outbound Fulfillment\n(POS & QR Engine)",
        "Identitas QR Code permanen (NIM) dibaca via web-camera html5-qrcode. Eksekusi 6-Lapis Validasi atomik dalam DB::transaction.",
        "Waktu Layan < 30 Detik\nBebas antrean panjang & zero potensi double-claim."
    ),
    (
        "Susut Stok &\nValuasi HPP Buram",
        "Finance & ERP Control\n(Costing Engine)",
        "Pencatatan batch FIFO terikat (StockBatch). Pemotongan FIFO otomatis (deductStockFifo), Stock Opname adjustment, & GpmService.",
        "Valuasi HPP Presisi\nAudit mutasi per batch transparan dan akuntabel."
    )
]

for row_idx, row_data in enumerate(sol_data, start=1):
    for col_idx, cell_value in enumerate(row_data):
        cell = table.cell(row_idx, col_idx)
        cell.fill.solid()
        cell.fill.fore_color.rgb = COLOR_WHITE if row_idx % 2 == 1 else COLOR_BG_CARD
        cell.vertical_anchor = MSO_ANCHOR.MIDDLE
        p = cell.text_frame.paragraphs[0]
        p.text = cell_value
        p.font.size = Pt(9)
        p.font.color.rgb = COLOR_TEXT_MAIN
        if col_idx == 1:
            p.font.bold = True
            p.font.color.rgb = COLOR_PRIMARY
        elif col_idx == 3:
            p.font.bold = True
            p.font.color.rgb = COLOR_SECONDARY

s5.notes_slide.notes_text_frame.text = (
    "Jelaskan bahwa UniStock bukan solusi teoritis, melainkan modul perangkat lunak fungsional yang dibangun dengan arsitektur Laravel 12/13.\n"
    "Tiap masalah dipetakan langsung ke Service Layer di dalam kode: StudentSizeService, StockService, dan GpmService."
)

# ==============================================================================
# SLIDE 6: Arsitektur Solusi (To-Be Blueprint: 4 Fase Loop-Tertutup)
# ==============================================================================
s6 = prs.slides.add_slide(blank_layout)
add_header(s6, "Arsitektur Solusi (To-Be Blueprint: 4 Fase Loop-Tertutup)")
add_footer(s6, 6)

fases = [
    ("FASE 1: HULU", "Inbound Procurement", [
        "Pencatatan StockReceive dari vendor mitra.",
        "Pembuatan record StockBatch (FIFO sequence).",
        "Pencatatan jurnal mutasi masuk StockMovement (IN).",
        "Kalkulasi Moving Average HPP di StockBalance."
    ], COLOR_LIGHT_RED, COLOR_PRIMARY),
    ("FASE 2: DEMAND", "Planning & Rights", [
        "Verifikasi pelunasan di EligibilityRecord.",
        "Event pengisian ukuran baju & sepatu mahasiswa.",
        "Penentuan paket hak barang via EntitlementService.",
        "Kalkulasi Demand Shortage vs Stok Tersedia."
    ], COLOR_LIGHT_AMBER, COLOR_AMBER_TEXT),
    ("FASE 3: HILIR", "Outbound Fulfillment", [
        "Scan QR Code NIM mahasiswa oleh staf posko.",
        "Eksekusi 6-Lapis Validasi sistem.",
        "Pemotongan batch tertua via deductStockFifo.",
        "Pencatatan DistributionTransaction & audit size."
    ], COLOR_LIGHT_BLUE, COLOR_BLUE_TEXT),
    ("FASE 4: AUDIT", "Control & Costing", [
        "Stock Opname fisik menghitung computed_variance.",
        "Approval jurnal penyesuaian surplus/shortage.",
        "Generate Gross Profit Margin (GPM) & COGS.",
        "Feedback loop data untuk pengadaan semester depan."
    ], COLOR_LIGHT_GREEN, COLOR_GREEN_TEXT),
]

for i, (kicker, title, bullets, bg_c, txt_c) in enumerate(fases):
    fx = Inches(0.8) + i * Inches(2.95)
    fy = Inches(1.8)
    fw = Inches(2.8)
    fh = Inches(3.7)
    
    fshape = s6.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, fx, fy, fw, fh)
    set_shape_flat(fshape, bg_c, COLOR_CARD_BORDER)
    
    tb = s6.shapes.add_textbox(fx + Inches(0.15), fy + Inches(0.15), fw - Inches(0.3), fh - Inches(0.3))
    tf = tb.text_frame
    tf.word_wrap = True
    
    p1 = tf.paragraphs[0]
    p1.text = kicker
    p1.font.size = Pt(9.5)
    p1.font.bold = True
    p1.font.color.rgb = txt_c
    
    p2 = tf.add_paragraph()
    p2.text = title
    p2.font.size = Pt(12)
    p2.font.bold = True
    p2.font.color.rgb = COLOR_SECONDARY
    p2.space_before = Pt(4)
    
    for b in bullets:
        pb = tf.add_paragraph()
        pb.text = f"• {b}"
        pb.font.size = Pt(8.5)
        pb.font.color.rgb = COLOR_TEXT_MAIN
        pb.space_before = Pt(4)

# Loop Closed Banner
lp_box = s6.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(5.65), Inches(11.733), Inches(0.85))
set_shape_flat(lp_box, COLOR_BG_CARD, COLOR_PRIMARY, border_width=1.5)

tb_lp = s6.shapes.add_textbox(Inches(1.0), Inches(5.7), Inches(11.3), Inches(0.75))
tf_lp = tb_lp.text_frame
p_lp = tf_lp.paragraphs[0]
p_lp.text = "CLOSED-LOOP FEEDBACK: Data sisa stok dan tren ukuran dari Fase 4 langsung menjadi input pengadaan Fase 1 pada periode berikutnya, menjamin siklus logistik yang terus berkembang dan makin presisi."
p_lp.font.size = Pt(9.5)
p_lp.font.bold = True
p_lp.font.color.rgb = COLOR_PRIMARY

s6.notes_slide.notes_text_frame.text = (
    "Tekankan istilah Closed-Loop: Sistem ini tidak berakhir saat barang diserahkan ke mahasiswa.\n"
    "Data transaksi dan audit opname dari Fase 4 menjadi umpan balik (feedback) yang langsung digunakan untuk menghitung proyeksi kebutuhan pengadaan di Fase 1 periode berikutnya."
)

# ==============================================================================
# SLIDE 7: 6-Lapis Validasi Distribusi & Eksekusi FIFO
# ==============================================================================
s7 = prs.slides.add_slide(blank_layout)
add_header(s7, "6-Lapis Validasi Distribusi & Engine FIFO")
add_footer(s7, 7)

# Left Box: 6 Layers List
left_box = s7.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.7), Inches(6.5), Inches(4.8))
set_shape_flat(left_box, COLOR_WHITE, COLOR_CARD_BORDER)

tb_l = s7.shapes.add_textbox(Inches(1.0), Inches(1.85), Inches(6.1), Inches(4.5))
tf_l = tb_l.text_frame
tf_l.word_wrap = True

p_lh = tf_l.paragraphs[0]
p_lh.text = "RANTAI VALIDASI ATOMIK (SCAN POS ENGINE)"
p_lh.font.size = Pt(11)
p_lh.font.bold = True
p_lh.font.color.rgb = COLOR_PRIMARY

validasi_steps = [
    ("1. Status Kelayakan Bayar", "EligibilityRecord::is_eligible == true. Jika belum lunas, transaksi ditolak otomatis."),
    ("2. Jadwal Distribusi Aktif", "DistributionSchedule aktif dan tanggal pelaksanaan tidak berada di masa depan."),
    ("3. Kesesuaian Scope Mahasiswa", "Memeriksa kecocokan filter Jenjang, Program Studi, Fakultas, & Generasi angkatan."),
    ("4. Kesesuaian Gender Barang", "Mencegah kesalahan serah seragam bertipe Laki-Laki atau Perempuan yang tidak cocok."),
    ("5. Sisa Kuota Hak Barang", "Menghitung hak entitlement dikurangi transaksi sebelumnya yang tidak berstatus cancelled."),
    ("6. Validasi Stok Batch FIFO", "Memeriksa ketersediaan stok fisik di gudang sebelum pemotongan saldo dieksekusi."),
]

for v_title, v_desc in validasi_steps:
    pv = tf_l.add_paragraph()
    pv.text = f"{v_title}"
    pv.font.size = Pt(9.5)
    pv.font.bold = True
    pv.font.color.rgb = COLOR_SECONDARY
    pv.space_before = Pt(5)
    
    pvd = tf_l.add_paragraph()
    pvd.text = f"{v_desc}"
    pvd.font.size = Pt(8.5)
    pvd.font.color.rgb = COLOR_TEXT_MUTED

# Right Box: FIFO Execution & Partial Pickup
right_box = s7.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(7.5), Inches(1.7), Inches(5.033), Inches(4.8))
set_shape_flat(right_box, COLOR_BG_CARD, COLOR_CARD_BORDER)

tb_r = s7.shapes.add_textbox(Inches(7.7), Inches(1.85), Inches(4.6), Inches(4.5))
tf_r = tb_r.text_frame
tf_r.word_wrap = True

p_rh = tf_r.paragraphs[0]
p_rh.text = "LOGIKA EKSEKUSI FIFO & PARTIAL PICKUP"
p_rh.font.size = Pt(11)
p_rh.font.bold = True
p_rh.font.color.rgb = COLOR_PRIMARY

r_items = [
    ("Algoritma deductStockFifo", "Mengunci baris batch dengan lockForUpdate(), mengambil batch dengan quantity_remaining > 0 berurutan (received_date ASC, id ASC), lalu memotong batch terlama lebih dulu."),
    ("Kalkulasi Blended HPP", "Menghitung rata-rata tertimbang harga pokok perolehan dari batch yang dikonsumsi untuk dicatat ke record transaksi (distribution_items.hpp)."),
    ("Mekanisme Partial Pickup", "Jika stok fisik di posko kurang/habis, transaksi tetap dapat disubmit dengan status 'partial'. Sisa hak kuota TIDAK HANGUS dan otomatis dapat diambil pada jadwal susulan."),
    ("Audit Log Perubahan Ukuran", "Jika ukuran fisik yang diserahkan berbeda dari profil awal, perubahan dicatat lengkap dengan timestamp dan ID staf di student_size_histories."),
]

for r_t, r_d in r_items:
    pr = tf_r.add_paragraph()
    pr.text = f"• {r_t}: {r_d}"
    pr.font.size = Pt(8.5)
    pr.font.color.rgb = COLOR_TEXT_MAIN
    pr.space_before = Pt(6)

s7.notes_slide.notes_text_frame.text = (
    "Soroti keunggulan arsitektural: Fitur Partial Pickup.\n"
    "Di banyak sistem lain, jika stok kurang 1 barang, transaksi harus dibatalkan seluruhnya atau dicatat manual di kertas.\n"
    "Di UniStock, sistem mencatat apa yang ada, menandai status transaksi 'partial', dan menyimpan sisa haknya agar mahasiswa bisa mengambil sisanya nanti tanpa birokrasi ulang."
)

# ==============================================================================
# SLIDE 8: Strategi Implementasi & Manajemen Risiko
# ==============================================================================
s8 = prs.slides.add_slide(blank_layout)
add_header(s8, "Strategi Implementasi & Manajemen Risiko Lapangan")
add_footer(s8, 8)

# Top: 3 Implementation Phases
phases = [
    ("FASE 1: MASTER DATA", "Standardisasi Nomenklatur", "Setup Base Code 4 Segmen (KATEGORI-GENDER-TIPE-DEPT) & SKU Varian 5 Segmen (KODE-UKURAN) secara konsisten dan immutable.", COLOR_LIGHT_RED),
    ("FASE 2: PROFILING", "Eligibility & Ukuran", "Integrasi data kelayakan bayar dari Keuangan & pembukaan event pengisian profil ukuran seragam/sepatu mahasiswa.", COLOR_LIGHT_AMBER),
    ("FASE 3: OPERASIONAL", "Eksekusi Hari-H", "Setup posko berbasis mobile web-cam, pelatihan staf lapangan, dan simulasi penanganan skenario darurat.", COLOR_LIGHT_GREEN),
]

for i, (kicker, title, desc, bg_c) in enumerate(phases):
    px = Inches(0.8) + i * Inches(3.95)
    py = Inches(1.7)
    pw = Inches(3.8)
    ph = Inches(1.5)
    
    pshape = s8.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, px, py, pw, ph)
    set_shape_flat(pshape, bg_c, COLOR_CARD_BORDER)
    
    tb = s8.shapes.add_textbox(px + Inches(0.1), py + Inches(0.1), pw - Inches(0.2), ph - Inches(0.2))
    tf = tb.text_frame
    tf.word_wrap = True
    
    p1 = tf.paragraphs[0]
    p1.text = kicker
    p1.font.size = Pt(8.5)
    p1.font.bold = True
    p1.font.color.rgb = COLOR_PRIMARY
    
    p2 = tf.add_paragraph()
    p2.text = title
    p2.font.size = Pt(11)
    p2.font.bold = True
    p2.font.color.rgb = COLOR_SECONDARY
    p2.space_before = Pt(2)
    
    p3 = tf.add_paragraph()
    p3.text = desc
    p3.font.size = Pt(8)
    p3.font.color.rgb = COLOR_TEXT_MAIN
    p3.space_before = Pt(3)

# Bottom: Risk Matrix Table
rows = 4
cols = 3
left = Inches(0.8)
top = Inches(3.35)
width = Inches(11.733)
height = Inches(3.15)

table_shape = s8.shapes.add_table(rows, cols, left, top, width, height)
table = table_shape.table
table.columns[0].width = Inches(3.3)
table.columns[1].width = Inches(3.8)
table.columns[2].width = Inches(4.633)

headers = ["Potensi Risiko Lapangan", "Dampak Terhadap Aliran", "Strategi Mitigasi Terintegrasi"]
for idx, text in enumerate(headers):
    cell = table.cell(0, idx)
    cell.fill.solid()
    cell.fill.fore_color.rgb = COLOR_SECONDARY
    cell.vertical_anchor = MSO_ANCHOR.MIDDLE
    p = cell.text_frame.paragraphs[0]
    p.text = text
    p.font.size = Pt(10)
    p.font.bold = True
    p.font.color.rgb = COLOR_WHITE

risk_data = [
    (
        "Mahasiswa Lupa Membawa QR / HP Mati",
        "Antrean tersendat jika staf posko kaku hanya menerima pemindaian kamera.",
        "Fitur Fallback Search: Staf dapat mencari data mahasiswa melalui input manual NIM atau Nama pada bilah pencarian scanner."
    ),
    (
        "Varian Ukuran Tertentu Habis di Posko",
        "Mahasiswa pulang tanpa kejelasan sisa hak barang yang belum diterima.",
        "Fitur Partial Pickup: Menyerahkan barang yang ada, menandai status transaksi 'partial', dan kuota sisa otomatis dijadwalkan ulang."
    ),
    (
        "Koneksi Internet Fluktuatif / Double Click",
        "Potensi duplikasi submit dan pengurangan stok ganda saat jaringan tidak stabil.",
        "Pessimistic Locking: Database transaction mengunci record saldo dengan lockForUpdate(), dilindungi idempotensi transaksi per jadwal."
    )
]

for row_idx, row_data in enumerate(risk_data, start=1):
    for col_idx, cell_value in enumerate(row_data):
        cell = table.cell(row_idx, col_idx)
        cell.fill.solid()
        cell.fill.fore_color.rgb = COLOR_WHITE if row_idx % 2 == 1 else COLOR_BG_CARD
        cell.vertical_anchor = MSO_ANCHOR.MIDDLE
        p = cell.text_frame.paragraphs[0]
        p.text = cell_value
        p.font.size = Pt(8.5)
        p.font.color.rgb = COLOR_TEXT_MAIN
        if col_idx == 0:
            p.font.bold = True
            p.font.color.rgb = COLOR_PRIMARY

s8.notes_slide.notes_text_frame.text = (
    "Tunjukkan kesiapan sistem menghadapi dinamika hari-H: HP mahasiswa mati, jaringan lambat, atau stok fisik habis.\n"
    "Semua skenario darurat ini telah memiliki mekanisme mitigasi langsung di dalam aplikasi UniStock."
)

# ==============================================================================
# SLIDE 9: Target Dampak Terukur & KPI
# ==============================================================================
s9 = prs.slides.add_slide(blank_layout)
add_header(s9, "Target Dampak Terukur & Key Performance Indicators (KPI)")
add_footer(s9, 9)

# 3 Big Stat Cards
kpis = [
    ("EFISIENSI WAKTU", "< 30 Detik", "Waktu Pelayanan per Mahasiswa", "Sebelumnya 3-5 menit/orang dengan tumpukan map kertas dan pencoretan checklist manual.", COLOR_PRIMARY),
    ("AKURASI STOK", "Zero (0)", "Dead-Stock Seragam & Sepatu", "Pengadaan barang hulu selaras 100% dengan profil ukuran riil mahasiswa aktif terdaftar.", COLOR_SECONDARY),
    ("AKUNTABILITAS BIAYA", "Real-Time", "Valuasi HPP & Jurnal FIFO", "Integrasi pergerakan stok keluar-masuk langsung terhubung ke COGS & Gross Profit Margin.", COLOR_PRIMARY_DARK),
]

for i, (kicker, big_num, label, desc, bg_c) in enumerate(kpis):
    kx = Inches(0.8) + i * Inches(3.95)
    ky = Inches(1.8)
    kw = Inches(3.8)
    kh = Inches(3.5)
    
    kshape = s9.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, kx, ky, kw, kh)
    set_shape_flat(kshape, bg_c)
    
    tb = s9.shapes.add_textbox(kx + Inches(0.2), ky + Inches(0.2), kw - Inches(0.4), kh - Inches(0.4))
    tf = tb.text_frame
    tf.word_wrap = True
    
    p1 = tf.paragraphs[0]
    p1.text = kicker
    p1.font.size = Pt(9.5)
    p1.font.bold = True
    p1.font.color.rgb = COLOR_LIGHT_AMBER
    
    p2 = tf.add_paragraph()
    p2.text = big_num
    p2.font.size = Pt(36)
    p2.font.bold = True
    p2.font.color.rgb = COLOR_WHITE
    p2.space_before = Pt(8)
    
    p3 = tf.add_paragraph()
    p3.text = label
    p3.font.size = Pt(12)
    p3.font.bold = True
    p3.font.color.rgb = COLOR_WHITE
    p3.space_before = Pt(4)
    
    p4 = tf.add_paragraph()
    p4.text = desc
    p4.font.size = Pt(8.5)
    p4.font.color.rgb = COLOR_CARD_BORDER
    p4.space_before = Pt(8)

# Quote Callout at bottom
quote_box = s9.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(5.5), Inches(11.733), Inches(1.0))
set_shape_flat(quote_box, COLOR_LIGHT_RED, COLOR_PRIMARY, border_width=1.5)

tb_q = s9.shapes.add_textbox(Inches(1.0), Inches(5.55), Inches(11.3), Inches(0.9))
tf_q = tb_q.text_frame
p_q = tf_q.paragraphs[0]
p_q.text = '"Sistem Informasi bukan sekadar alat pencatat administratif, melainkan saraf pusat rantai pasok yang mengubah intuisi menjadi keputusan operasional dan finansial berbasis data presisi."'
p_q.font.size = Pt(11)
p_q.font.bold = True
p_q.font.color.rgb = COLOR_PRIMARY

s9.notes_slide.notes_text_frame.text = (
    "Tiga angka ini adalah tolok ukur keberhasilan proyek UniStock.\n"
    "Kecepatan < 30 detik berarti antrean ribuan mahasiswa dapat diselesaikan tertib dalam hitungan jam.\n"
    "Zero dead stock mengamankan modal kerja kampus dari kerugian seragam yang tak terpakai."
)

# ==============================================================================
# SLIDE 10: Penutup & Sesi Diskusi
# ==============================================================================
s10 = prs.slides.add_slide(blank_layout)

# Background
bg_s10 = s10.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(13.333), Inches(7.5))
set_shape_flat(bg_s10, COLOR_BG_CARD)

# Center Card
card_s10 = s10.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.5), Inches(1.0), Inches(10.333), Inches(5.5))
set_shape_flat(card_s10, COLOR_WHITE, COLOR_CARD_BORDER)

tb_c = s10.shapes.add_textbox(Inches(2.0), Inches(1.3), Inches(9.333), Inches(4.8))
tf_c = tb_c.text_frame
tf_c.word_wrap = True

p1 = tf_c.paragraphs[0]
p1.alignment = PP_ALIGN.CENTER
p1.text = "SESI DISKUSI & TANYA JAWAB"
p1.font.size = Pt(11)
p1.font.bold = True
p1.font.color.rgb = COLOR_PRIMARY

p2 = tf_c.add_paragraph()
p2.alignment = PP_ALIGN.CENTER
p2.text = "Terima Kasih"
p2.font.size = Pt(32)
p2.font.bold = True
p2.font.color.rgb = COLOR_SECONDARY
p2.space_before = Pt(6)

p3 = tf_c.add_paragraph()
p3.alignment = PP_ALIGN.CENTER
p3.text = "UniStock Siap Mendukung Distribusi Logistik Kampus yang Presisi, Efisien, dan Akuntabel."
p3.font.size = Pt(13)
p3.font.color.rgb = COLOR_PRIMARY
p3.space_before = Pt(4)

p4 = tf_c.add_paragraph()
p4.alignment = PP_ALIGN.CENTER
p4.text = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
p4.font.size = Pt(10)
p4.font.color.rgb = COLOR_CARD_BORDER
p4.space_before = Pt(10)

p5 = tf_c.add_paragraph()
p5.alignment = PP_ALIGN.CENTER
p5.text = "Indeks Dokumentasi Teknis Sistem:\n" \
          "• Alur Teknis Lengkap SCM: docs/project/scm-flow.md\n" \
          "• Arsitektur Sistem Laravel: docs/project/architecture.md\n" \
          "• Skema ERD & Database: docs/project/erd.md\n" \
          "• Standarisasi Kode Barang: docs/project/item-code.md"
p5.font.size = Pt(10)
p5.font.color.rgb = COLOR_TEXT_MAIN
p5.space_before = Pt(8)

p6 = tf_c.add_paragraph()
p6.alignment = PP_ALIGN.CENTER
p6.text = "Universitas Horizon Indonesia  ·  Tim Analis Sistem & Pengembang UniStock"
p6.font.size = Pt(9.5)
p6.font.bold = True
p6.font.color.rgb = COLOR_TEXT_MUTED
p6.space_before = Pt(14)

s10.notes_slide.notes_text_frame.text = (
    "Tutup presentasi dengan menyampaikan kesiapan implementasi.\n"
    "Arahkan audiens atau evaluator ke dokumen teknis di folder docs/project/ jika memerlukan pendalaman teknis kode Laravel atau skema database."
)

# Output Paths
out_project = r"C:\laragon\www\Horizon-UniStock - Copy\docs\project\UniStock_Campus_Supply_Chain_Blueprint.pptx"
out_downloads = r"C:\Users\j\Downloads\UniStock_Campus_Supply_Chain_Blueprint.pptx"

prs.save(out_project)
prs.save(out_downloads)

print(f"Successfully generated PPTX presentations:\n1. {out_project}\n2. {out_downloads}")
