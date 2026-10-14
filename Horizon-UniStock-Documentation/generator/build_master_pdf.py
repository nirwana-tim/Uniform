# -*- coding: utf-8 -*-
"""
Master PDF Generator for Horizon UniStock
Assembles all 90 chapters and generates the publication-grade PDF.
"""

import os
import sys
import base64
import subprocess
import pymupdf

BASE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
DOCS_DIR = os.path.join(BASE_DIR, "Horizon-UniStock-Documentation")
GENERATOR_DIR = os.path.join(DOCS_DIR, "generator")

sys.path.insert(0, GENERATOR_DIR)

from part_1_to_2 import get_parts_1_and_2_html
from part_3_to_4 import get_parts_3_and_4_html
from part_5_to_7 import get_parts_5_to_7_html
from part_8_to_11 import get_parts_8_to_11_html

def get_logo_base64():
    logo_path = os.path.join(BASE_DIR, "public", "images", "logo.png")
    if os.path.exists(logo_path):
        with open(logo_path, "rb") as f:
            return "data:image/png;base64," + base64.b64encode(f.read()).decode("utf-8")
    return ""

def build_full_html():
    logo_data = get_logo_base64()
    part1_2 = get_parts_1_and_2_html()
    part3_4 = get_parts_3_and_4_html()
    part5_7 = get_parts_5_to_7_html()
    part8_11 = get_parts_8_to_11_html()
    
    html = f"""<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Horizon UniStock — Project Knowledge Base & System Documentation</title>
<style>
  @page {{
    size: A4 portrait;
    margin: 18mm 16mm 20mm 16mm;
    @bottom-right {{
      content: counter(page);
      font-size: 8.5pt;
      font-family: 'Inter', sans-serif;
      color: #64748B;
    }}
    @bottom-left {{
      content: "Horizon UniStock • System Documentation v1.2.0";
      font-size: 8.5pt;
      font-family: 'Inter', sans-serif;
      color: #64748B;
    }}
  }}

  body {{
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    font-size: 9.6pt;
    line-height: 1.58;
    color: #1E293B;
    background-color: #FFFFFF;
    margin: 0;
    padding: 0;
  }}

  .cover-page {{
    page-break-after: always;
    min-height: 250mm;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 10mm 5mm 5mm 5mm;
    box-sizing: border-box;
    border-left: 6px solid #005A36;
    padding-left: 12mm;
  }}

  .cover-header {{ margin-bottom: 15mm; }}
  .cover-logo {{ max-height: 60px; margin-bottom: 12mm; }}
  .cover-badge {{
    display: inline-block;
    background-color: #E8F5E9;
    color: #005A36;
    font-size: 9pt;
    font-weight: 700;
    padding: 4px 14px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10mm;
    border: 1px solid #A5D6A7;
  }}

  .cover-title {{
    font-size: 32pt;
    font-weight: 800;
    color: #0F172A;
    line-height: 1.15;
    margin: 0 0 4mm 0;
    letter-spacing: -0.5px;
  }}

  .cover-subtitle {{
    font-size: 14pt;
    font-weight: 600;
    color: #005A36;
    margin: 0 0 8mm 0;
  }}

  .cover-desc {{
    font-size: 10.2pt;
    color: #475569;
    max-width: 580px;
    line-height: 1.6;
    margin-bottom: 12mm;
  }}

  .cover-meta-table {{
    width: 100%;
    border-collapse: collapse;
    margin-top: auto;
  }}

  .cover-meta-table td {{
    padding: 5.5px 0;
    font-size: 9.2pt;
    vertical-align: top;
  }}

  .cover-meta-table .meta-label {{
    width: 160px;
    color: #64748B;
    font-weight: 600;
  }}

  .cover-meta-table .meta-value {{
    color: #0F172A;
    font-weight: 600;
  }}

  .part-divider {{
    page-break-before: always;
    padding-top: 10mm;
    margin-bottom: 8mm;
    border-bottom: 3px solid #005A36;
    padding-bottom: 4mm;
  }}

  .part-number {{
    font-size: 10.5pt;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #005A36;
    margin-bottom: 1.5mm;
  }}

  .part-title {{
    font-size: 18pt;
    font-weight: 800;
    color: #0F172A;
    margin: 0 0 2mm 0;
  }}

  .part-subtitle {{
    font-size: 9.8pt;
    color: #64748B;
    font-weight: 500;
  }}

  .chapter {{
    margin-bottom: 8mm;
    page-break-inside: auto;
  }}

  h2.chapter-title {{
    font-size: 12.8pt;
    font-weight: 700;
    color: #005A36;
    border-bottom: 1px solid #E2E8F0;
    padding-bottom: 2mm;
    margin-top: 6mm;
    margin-bottom: 3mm;
    page-break-after: avoid;
  }}

  h3.subchapter-title {{
    font-size: 10.4pt;
    font-weight: 700;
    color: #1E293B;
    margin-top: 4.5mm;
    margin-bottom: 2mm;
    page-break-after: avoid;
  }}

  p {{
    margin: 0 0 3mm 0;
    text-align: justify;
  }}

  ul, ol {{
    margin: 0 0 3mm 0;
    padding-left: 5.5mm;
  }}

  li {{
    margin-bottom: 1.2mm;
  }}

  table.data-table {{
    width: 100%;
    border-collapse: collapse;
    margin: 3mm 0 4.5mm 0;
    font-size: 8.6pt;
  }}

  table.data-table tr {{
    page-break-inside: avoid;
  }}

  table.data-table th {{
    background-color: #004D2C;
    color: #FFFFFF;
    font-weight: 700;
    text-align: left;
    padding: 5.5px 8.5px;
    border: 1px solid #004D2C;
  }}

  table.data-table td {{
    padding: 4.5px 8.5px;
    border: 1px solid #E2E8F0;
    vertical-align: top;
  }}

  table.data-table tr:nth-child(even) td {{
    background-color: #F8FAFC;
  }}

  .callout {{
    padding: 3mm 4mm;
    margin: 3mm 0 4mm 0;
    border-radius: 4px;
    font-size: 9pt;
    page-break-inside: avoid;
  }}

  .callout-note {{
    background-color: #F0FDF4;
    border-left: 4px solid #16A34A;
    color: #166534;
  }}

  .callout-warning {{
    background-color: #FFFBEB;
    border-left: 4px solid #D97706;
    color: #92400E;
  }}

  .callout-important {{
    background-color: #EFF6FF;
    border-left: 4px solid #2563EB;
    color: #1E40AF;
  }}

  pre, code {{
    font-family: 'Consolas', 'Courier New', monospace;
  }}

  pre {{
    background-color: #0F172A;
    color: #F8FAFC;
    padding: 3mm 4mm;
    border-radius: 5px;
    font-size: 8.2pt;
    overflow-x: hidden;
    white-space: pre-wrap;
    word-break: break-all;
    margin: 2.5mm 0 4mm 0;
    page-break-inside: avoid;
  }}

  p code, td code, li code {{
    background-color: #F1F5F9;
    color: #0F172A;
    padding: 1px 4px;
    border-radius: 3px;
    font-size: 8.5pt;
    border: 1px solid #E2E8F0;
  }}

  .badge {{
    display: inline-block;
    padding: 2px 7px;
    font-size: 7.6pt;
    font-weight: 700;
    border-radius: 12px;
    text-transform: uppercase;
  }}
  .badge-success {{ background-color: #DCFCE7; color: #166534; }}
  .badge-warning {{ background-color: #FEF3C7; color: #92400E; }}
  .badge-danger  {{ background-color: #FEE2E2; color: #991B1B; }}
  .badge-info    {{ background-color: #E0E7FF; color: #3730A3; }}

  .feature-card {{
    border: 1px solid #E2E8F0;
    background-color: #FFFFFF;
    border-radius: 5px;
    padding: 3.5mm 4.5mm;
    margin-bottom: 4mm;
    page-break-inside: avoid;
  }}

  .feature-header {{
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #F1F5F9;
    padding-bottom: 1.5mm;
    margin-bottom: 2.5mm;
  }}

  .feature-id {{
    font-size: 9.8pt;
    font-weight: 800;
    color: #005A36;
  }}

  .feature-name {{
    font-size: 9.8pt;
    font-weight: 700;
    color: #0F172A;
    margin-left: 3mm;
  }}
</style>
</head>
<body>

<!-- COVER PAGE -->
<div class="cover-page">
  <div class="cover-header">
    {f'<img src="{logo_data}" class="cover-logo" alt="Horizon UniStock Logo">' if logo_data else ''}
    <div class="cover-badge">Master Documentation & Knowledge Base</div>
    <h1 class="cover-title">Horizon UniStock</h1>
    <div class="cover-subtitle">Project Knowledge Base & System Documentation</div>
    <div class="cover-desc">
      Dokumentasi induk berbasis bukti yang memuat identitas proyek, latar belakang bisnis pra-digitalisasi, analisis operasional, spesifikasi 49 kebutuhan fungsional, arsitektur teknis Service Layer, kamus 58 tabel basis data relasional, pengujian komprehensif, audit keamanan, panduan deployment cPanel tanpa SSH, serta instruksi permanen pengembangan bagi agen AI.
    </div>
  </div>

  <table class="cover-meta-table">
    <tr>
      <td class="meta-label">Institusi:</td>
      <td class="meta-value">Horizon University Indonesia (Universitas Horizon Indonesia / STMIK & STIKes Horizon Karawang)</td>
    </tr>
    <tr>
      <td class="meta-label">Alamat Produksi:</td>
      <td class="meta-value">https://unistok.nirwana.biz.id</td>
    </tr>
    <tr>
      <td class="meta-label">Repositori Git:</td>
      <td class="meta-value">nirwana-tim/Horizon-UniStock (Branch: main)</td>
    </tr>
    <tr>
      <td class="meta-label">Teknologi Inti:</td>
      <td class="meta-value">Laravel 12.63.0 / 13.x • PHP 8.3+ • MySQL • Tailwind CSS v3 • Alpine.js • Vite</td>
    </tr>
    <tr>
      <td class="meta-label">Versi Dokumen:</td>
      <td class="meta-value">1.2.0 (Proposal-Grade Master Documentation — Oktober 2026)</td>
    </tr>
    <tr>
      <td class="meta-label">Status Dokumen:</td>
      <td class="meta-value">Canonical Truth (Terverifikasi Penuh Berbasis Bukti Sumber Daya Aktual)</td>
    </tr>
  </table>
</div>

<!-- RINGKASAN STRUKTUR 90 BAB -->
<div class="part-divider" style="page-break-before: always;">
  <div class="part-number">Struktur Dokumen Induk</div>
  <h1 class="part-title">Sistematika 11 Bagian &amp; 90 Bab Lengkap</h1>
</div>
<p>Dokumentasi ini disajikan secara lengkap tanpa peringkasan bab. Setiap bab memuat uraian naratif mendalam, analisis kebutuhan non-teknis, matriks aturan bisnis, spesifikasi kode sumber, serta prosedur operasional standar:</p>

<table class="data-table">
  <thead>
    <tr>
      <th style="width: 15%;">Bagian</th>
      <th style="width: 35%;">Judul Bagian</th>
      <th style="width: 25%;">Cakupan Nomor Bab</th>
      <th style="width: 25%;">Fokus Pembahasan</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>Bagian I</strong></td>
      <td>Identitas dan Konteks Dasar Proyek</td>
      <td>Bab 1 s/d Bab 5</td>
      <td>Metadata, Executive Summary, Identitas, Sejarah, Batasan</td>
    </tr>
    <tr>
      <td><strong>Bagian II</strong></td>
      <td>Analisis Bisnis dan Proses Operasional</td>
      <td>Bab 6 s/d Bab 12</td>
      <td>Latar Belakang Non-Teknis, Cost-Benefit, Stakeholders, Persona, SOP 5 Fase, Rules &amp; Exceptions</td>
    </tr>
    <tr>
      <td><strong>Bagian III</strong></td>
      <td>Spesifikasi Produk dan Seluruh Fitur</td>
      <td>Bab 13 s/d Bab 24</td>
      <td>Product Overview, FR-01 s/d FR-49, Role Matrix, Student, Staff, Admin, QR, Distribution, Inventory, GPM, Auth</td>
    </tr>
    <tr>
      <td><strong>Bagian IV</strong></td>
      <td>Dokumentasi Teknis dan Arsitektur</td>
      <td>Bab 25 s/d Bab 36</td>
      <td>Tech Stack, 4-Tier Architecture, Repo Structure, Blade/Alpine, Service Layer, Database, API, Assets, SMTP, Config</td>
    </tr>
    <tr>
      <td><strong>Bagian V</strong></td>
      <td>Model Data dan Aturan Integritas</td>
      <td>Bab 37 s/d Bab 44</td>
      <td>Data Dictionary 42 Model, ERD Relasional, Lifecycle SoftDeletes, Matematika FIFO, Validasi, Backup</td>
    </tr>
    <tr>
      <td><strong>Bagian VI</strong></td>
      <td>UI/UX dan Dokumentasi Antarmuka</td>
      <td>Bab 45 s/d Bab 52</td>
      <td>Design Principles, Token Horizon Green, Page Inventory, Page-by-Page Specs, Journeys, States, Accessibility</td>
    </tr>
    <tr>
      <td><strong>Bagian VII</strong></td>
      <td>Keamanan, Kualitas, dan Performa</td>
      <td>Bab 53 s/d Bab 60</td>
      <td>Security Model, Access Control, QR Security Rationale, Privacy PDP, Error Handling, Benchmarks, Audit Trail</td>
    </tr>
    <tr>
      <td><strong>Bagian VIII</strong></td>
      <td>Instalasi, Deployment, dan Pemeliharaan</td>
      <td>Bab 61 s/d Bab 69</td>
      <td>Local Setup, .env Reference, Vite Build, Database Seed, cPanel Git-Pull No-SSH, Autoloader, Troubleshooting</td>
    </tr>
    <tr>
      <td><strong>Bagian IX</strong></td>
      <td>Testing, Debugging, dan Rekam Masalah</td>
      <td>Bab 70 s/d Bab 76</td>
      <td>Testing Strategy, 16 Test Cases PRD, Regression Matrix, Bug BUG-001 s/d 006, Kasus Operasional 1–13, Debt</td>
    </tr>
    <tr>
      <td><strong>Bagian X</strong></td>
      <td>Roadmap, Perubahan, dan Keputusan Teknis</td>
      <td>Bab 77 s/d Bab 83</td>
      <td>Status Kesiapan Modul, Feature Backlog (POS Eceran), Limitations, Opportunities, ADR 001–008, Change Log</td>
    </tr>
    <tr>
      <td><strong>Bagian XI</strong></td>
      <td>Glosarium, Sumber, dan Instruksi untuk AI</td>
      <td>Bab 84 s/d Bab 90</td>
      <td>Glosarium Istilah A-Z, 15 Fakta Baku Kanonis, File Registry Kunci, Evidence, Pedoman AI Pair Programming</td>
    </tr>
  </tbody>
</table>

{part1_2}
{part3_4}
{part5_7}
{part8_11}

</body>
</html>"""
    return html

def run_build():
    print("=== STARTING MASTER PDF GENERATION (90 CHAPTERS) ===")
    html_content = build_full_html()
    temp_html = os.path.join(DOCS_DIR, "temp_master_full.html")
    raw_pdf = os.path.join(DOCS_DIR, "temp_raw_master.pdf")
    final_pdf = os.path.join(DOCS_DIR, "01-UniStock-Master-Documentation.pdf")
    
    with open(temp_html, "w", encoding="utf-8") as f:
        f.write(html_content)
    print(f"Compiled Master HTML: {len(html_content)} bytes")
    
    chrome_path = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
    if not os.path.exists(chrome_path):
        chrome_path = r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
        
    print(f"Launching Chrome Headless: {chrome_path}")
    cmd = [
        chrome_path,
        "--headless=new",
        "--disable-gpu",
        "--no-pdf-header-footer",
        f"--print-to-pdf={raw_pdf}",
        temp_html
    ]
    
    res = subprocess.run(cmd, capture_output=True, text=True)
    if res.returncode != 0 or not os.path.exists(raw_pdf):
        print(f"Error rendering PDF: {res.stderr}")
        return False
        
    print(f"Rendered Raw PDF: {os.path.getsize(raw_pdf)} bytes")
    
    # Process with PyMuPDF
    doc = pymupdf.open(raw_pdf)
    total_pages = len(doc)
    print(f"Total Pages Generated: {total_pages}")
    
    doc.set_metadata({
        "title": "Horizon UniStock — Project Knowledge Base & System Documentation",
        "author": "Horizon University Indonesia (Nirwana Team)",
        "subject": "Complete 90-Chapter Master System Proposal, Business Analysis, Technical Reference, and AI Context",
        "keywords": "Horizon UniStock, Laravel, Inventory, Uniform Distribution, FIFO, QR Code, System Documentation, 90 Chapters",
        "creator": "UniStock System Documentation Builder",
        "producer": "PyMuPDF & Chrome Headless Engine"
    })
    
    # Generate full 90 chapters outline TOC
    toc = []
    
    parts_def = [
        ("Bagian I", "Bagian I: Identitas dan Konteks Dasar Proyek", 1, 5),
        ("Bagian II", "Bagian II: Analisis Bisnis dan Proses Operasional", 6, 12),
        ("Bagian III", "Bagian III: Spesifikasi Produk dan Seluruh Fitur", 13, 24),
        ("Bagian IV", "Bagian IV: Dokumentasi Teknis dan Arsitektur", 25, 36),
        ("Bagian V", "Bagian V: Model Data dan Aturan Integritas", 37, 44),
        ("Bagian VI", "Bagian VI: UI/UX dan Dokumentasi Antarmuka", 45, 52),
        ("Bagian VII", "Bagian VII: Keamanan, Kualitas, dan Performa", 53, 60),
        ("Bagian VIII", "Bagian VIII: Instalasi, Deployment, dan Pemeliharaan", 61, 69),
        ("Bagian IX", "Bagian IX: Testing, Debugging, dan Rekam Masalah", 70, 76),
        ("Bagian X", "Bagian X: Roadmap, Perubahan, dan Keputusan Teknis", 77, 83),
        ("Bagian XI", "Bagian XI: Glosarium, Sumber, dan Instruksi untuk AI", 84, 90)
    ]
    
    # Search each part and chapter in pages to find exact page number
    # First add Cover and Summary
    toc.append([1, "Cover & Metadata", 1])
    toc.append([1, "Sistematika 11 Bagian & 90 Bab Lengkap", 2])
    
    for part_num, part_title, start_ch, end_ch in parts_def:
        # Find page for Part (skipping summary table on pages 1-3)
        part_page = 4
        for pno in range(3, total_pages):
            text = doc[pno].get_text()
            if part_num in text and "Bagian" in text:
                part_page = pno + 1
                break
        toc.append([1, part_title, part_page])
        
        # Add chapters
        for ch_idx in range(start_ch, end_ch + 1):
            ch_page = part_page
            ch_full_title = f"Bab {ch_idx}"
            
            for pno in range(max(3, part_page - 1), total_pages):
                text = doc[pno].get_text()
                found = False
                for line in text.splitlines():
                    line_s = line.strip()
                    if (line_s.startswith(f"Bab {ch_idx} ") or line_s.startswith(f"Bab {ch_idx}—") or line_s.startswith(f"Bab {ch_idx}–")) and " s/d " not in line_s:
                        ch_page = pno + 1
                        ch_full_title = line_s
                        found = True
                        break
                if found:
                    break
                    
            toc.append([2, ch_full_title, ch_page])
            
    print(f"Constructed TOC Entries: {len(toc)}")
    doc.set_toc(toc)
    doc.save(final_pdf)
    doc.close()
    
    # Cleanup temporary files
    if os.path.exists(temp_html):
        os.remove(temp_html)
    if os.path.exists(raw_pdf):
        os.remove(raw_pdf)
        
    print(f"[SUCCESS] Final Master PDF saved to: {final_pdf} ({os.path.getsize(final_pdf)} bytes)")
    return True

if __name__ == "__main__":
    run_build()
