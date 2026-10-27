<!DOCTYPE html>
<html lang="id" class="scroll-smooth overscroll-none">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#F0DADC">
  <title>HorizonU Uniform - Kelola Pengambilan Seragam Mahasiswa</title>
  
  <!-- Vite Tailwind CSS & JS -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    /* Self-hosted Optima Fonts (Branding & Display Headings) */
    @font-face {
      font-family: 'Optima';
      src: url('{{ asset('fonts/optima/OPTIMA.woff') }}') format('woff');
      font-weight: 400;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Optima';
      src: url('{{ asset('fonts/optima/Optima_Medium.woff') }}') format('woff');
      font-weight: 500;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Optima';
      src: url('{{ asset('fonts/optima/OPTIMA_B.woff') }}') format('woff');
      font-weight: 700;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Optima';
      src: url('{{ asset('fonts/optima/Optima_Italic.woff') }}') format('woff');
      font-weight: 400;
      font-style: italic;
      font-display: swap;
    }

    /* Root canvas background: #FFFFFF at top (header match), #800412 at bottom (footer match) */
    html {
      scroll-behavior: smooth;
      scroll-padding-top: 84px;
      background-color: #FFFFFF;
      background: linear-gradient(to bottom, #FFFFFF 0%, #FFFFFF 50%, #800412 50%, #800412 100%) no-repeat fixed;
      overscroll-behavior-y: none;
      overscroll-behavior: none;
    }
    @media (min-width: 1024px) {
      html {
        scroll-padding-top: 96px;
      }
    }
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      color: #171717;
      background-color: transparent;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: clip;
      overscroll-behavior-y: none;
      overscroll-behavior: none;
    }
    /* Header overscroll extension (prevents white gap when pulling down at the top) */
    header::before {
      content: '';
      position: absolute;
      bottom: 100%;
      left: 0;
      right: 0;
      height: 100vh;
      background-color: #FFFFFF;
      pointer-events: none;
    }

    .badge-capsule {
      display: inline-flex;
      align-items: center;
      gap: 0.625rem;
      height: 48px;
      padding: 0 1.35rem;
      border-radius: 9999px;
      font-size: 0.9375rem;
      font-weight: 500;
      letter-spacing: 0.01em;
      border: 1.5px solid #D4A83A;
      color: #D4A83A;
      background: transparent;
    }
    @media (max-width: 640px) {
      .badge-capsule {
        height: 42px;
        padding: 0 1.1rem;
        font-size: 0.8125rem;
      }
    }
    /* Photo Card Stack Visual Effect matching Figma SVG */
    .photo-stack-base {
      position: relative;
      padding-left: 20px;
      padding-top: 12px;
    }
    @media (min-width: 640px) {
      .photo-stack-base {
        padding-left: 24px;
        padding-top: 14px;
      }
    }
    .photo-stack-base::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 20px;
      bottom: 12px;
      border-radius: 12px;
      background: #D4A83A;
      z-index: 0;
    }
    @media (min-width: 640px) {
      .photo-stack-base::before {
        right: 24px;
        bottom: 14px;
        border-radius: 14px;
      }
    }
    .photo-stack-img {
      position: relative;
      z-index: 1;
      border-radius: 12px;
      overflow: hidden;
      border: 2px solid #980517;
      background: white;
      box-shadow: 0 10px 25px -6px rgba(0, 0, 0, 0.12);
    }
    @media (min-width: 640px) {
      .photo-stack-img {
        border-radius: 14px;
      }
    }
    /* Mobile Menu Drawer Animation with CSS Grid */
    #mobileMenu {
      display: grid;
      grid-template-rows: 0fr;
      transition: grid-template-rows 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease, border-color 0.25s ease;
      opacity: 0;
      border-bottom: 1px solid transparent;
    }
    #mobileMenu.open {
      grid-template-rows: 1fr;
      opacity: 1;
      border-bottom-color: #E3BCC0;
    }
    #mobileMenu > div {
      overflow: hidden;
      min-height: 0;
    }

    /* Accordion transition with CSS Grid for accurate height calculation */
    .faq-answer {
      display: grid;
      grid-template-rows: 0fr;
      transition: grid-template-rows 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .faq-item.active .faq-answer {
      grid-template-rows: 1fr;
    }
    .faq-answer-inner {
      overflow: hidden;
      min-height: 0;
    }
    .faq-icon {
      display: inline-block;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), color 0.25s ease;
      transform-origin: center;
    }
    .faq-item.active .faq-icon {
      transform: rotate(45deg);
      color: #980517;
    }

    /* Hero Carousel Ultra-Smooth Transition */
    .hero-slide {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      opacity: 0;
      transform: scale(1.02);
      transition: opacity 500ms cubic-bezier(0.4, 0, 0.2, 1), transform 500ms cubic-bezier(0.4, 0, 0.2, 1);
      will-change: opacity, transform;
      pointer-events: none;
    }
    .hero-slide.active {
      opacity: 1;
      transform: scale(1);
      pointer-events: auto;
    }
    #carouselUniformTitle {
      transition: opacity 250ms cubic-bezier(0.4, 0, 0.2, 1), transform 250ms cubic-bezier(0.4, 0, 0.2, 1);
      will-change: opacity, transform;
      display: inline-block;
    }

    /* Scroll-Driven Interactive Timeline Styles */
    #timelineProgressDesktop,
    #timelineProgressMobile {
      will-change: transform;
      transform-origin: top center;
    }

    html.timeline-ready .timeline-node {
      transform: translate(-50%, 0) scale(0.85);
      opacity: 0.35;
      transition: transform 0.6s cubic-bezier(0.34, 1.45, 0.64, 1), opacity 0.5s ease, filter 0.5s ease;
      will-change: transform, opacity, filter;
    }
    html.timeline-ready .timeline-node svg path {
      fill: #C9A2A6;
      transition: fill 0.45s ease;
    }
    html.timeline-ready .timeline-node.active {
      transform: translate(-50%, 0) scale(1.06);
      opacity: 1;
      filter: drop-shadow(0 6px 14px rgba(152, 5, 23, 0.35)) drop-shadow(0 0 12px rgba(212, 168, 58, 0.45));
    }
    html.timeline-ready .timeline-node.active svg path {
      fill: #980517;
    }
    html.timeline-ready .timeline-step {
      opacity: 0.5;
      transform: translateY(10px);
      transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity, transform;
    }
    html.timeline-ready .timeline-step.active {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body class="antialiased overscroll-none">

  <!-- ================= NAVBAR ================= -->
  <header class="sticky top-0 z-50 bg-white border-b border-[#F0DADC] transition-all duration-200">
    <div class="w-full px-4 sm:px-8 lg:px-12 h-[72px] lg:h-[78px] flex items-center justify-between mx-auto">
      
      <!-- Brand Logo (UHI Emblem img_10.webp) -->
      <a href="#" class="flex items-center gap-3 group">
        <img src="{{ asset('assets/img') }}/img_10.webp" alt="Logo Universitas Horizon Indonesia" class="w-9 h-9 sm:w-10 sm:h-10 lg:w-[44px] lg:h-[44px] object-contain rounded-full shadow-sm group-hover:scale-105 transition-transform">
        <span class="text-base sm:text-lg lg:text-xl font-medium sm:font-semibold tracking-tight text-[#980517] font-display">
          HorizonU Uniform
        </span>
      </a>

      <!-- Desktop Navigation Menu (Active at lg: 1024px+ to match Desktop SVG) -->
      <nav class="hidden lg:flex items-center gap-8 xl:gap-9">
        <a href="#tentang-kami" class="text-sm font-medium text-[#171717] hover:text-[#980517] transition-colors">Tentang Kami</a>
        <div class="relative group">
          <button class="flex items-center gap-1.5 text-sm font-medium text-[#171717] group-hover:text-[#980517] transition-colors">
            <span>Akses Akun</span>
            <span class="text-sm font-medium transition-transform duration-200 group-hover:rotate-45 leading-none">+</span>
          </button>
          <!-- Dropdown menu -->
          <div class="absolute top-full left-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 opacity-0 invisible translate-y-1.5 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
            <a href="#akses-maba" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-brand-pinkLight hover:text-brand-red font-medium transition-colors">Mahasiswa Baru</a>
            <a href="#akses-continuing" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-brand-pinkLight hover:text-brand-red font-medium transition-colors">Mahasiswa Continuing</a>
          </div>
        </div>
        <a href="#alur-pengambilan" class="text-sm font-medium text-[#171717] hover:text-[#980517] transition-colors">Cara Pengambilan</a>
        <a href="#faq" class="text-sm font-medium text-[#171717] hover:text-[#980517] transition-colors">FAQ</a>
      </nav>

      <!-- Desktop Auth CTA -->
      <div class="hidden lg:flex items-center gap-3">
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="inline-flex items-center justify-center px-7 py-2.5 text-sm font-bold text-[#171717] bg-[#D4A83A] hover:bg-[#B8902E] active:scale-95 rounded-full shadow-sm hover:shadow transition-all">
          {{ auth()->check() ? 'Dashboard' : 'Login' }}
        </a>
      </div>

      <!-- Mobile & Tablet Hamburger Button -->
      <button id="mobileMenuBtn" type="button" class="lg:hidden w-9 h-9 rounded-lg bg-[#D4A83A] flex items-center justify-center text-brand-dark shadow-sm hover:opacity-90 active:scale-95 focus:outline-none transition-transform" aria-label="Buka Menu">
        <i class="fa-solid fa-bars text-base transition-all duration-200"></i>
      </button>
    </div>

    <!-- Mobile & Tablet Drawer Menu -->
    <div id="mobileMenu" class="lg:hidden bg-white shadow-xl">
      <div class="px-6 py-5 space-y-4">
        <div class="flex flex-col space-y-3 font-medium">
          <a href="#tentang-kami" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold transition-colors">Tentang Kami</a>
          <a href="#akses-maba" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold transition-colors">Akses Akun - Mahasiswa Baru</a>
          <a href="#akses-continuing" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold transition-colors">Akses Akun - Continuing</a>
          <a href="#alur-pengambilan" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold transition-colors">Cara Pengambilan Seragam</a>
          <a href="#faq" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold transition-colors">FAQ (Pertanyaan Umum)</a>
        </div>
        <div class="pt-3 border-t border-gray-100">
          <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="w-full flex items-center justify-center gap-2 py-3 text-center text-sm font-bold text-[#171717] bg-[#D4A83A] hover:bg-[#B8902E] active:scale-95 rounded-full shadow-sm transition-all">
            <span>{{ auth()->check() ? 'Dashboard' : 'Login' }}</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- ================= MAIN CONTENT ================= -->
  <main class="w-full bg-white flex-1 relative">

    <!-- ================= HERO SECTION (FULL IMMERSIVE) ================= -->
    <section class="w-full px-4 sm:px-6 lg:px-8 pt-3 pb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-5 items-stretch min-h-[500px] md:min-h-[520px] lg:min-h-[calc(100dvh-115px)] lg:h-[calc(100dvh-115px)] max-h-[820px]">
        
        <!-- Hero Left Card (Deep Maroon #980517, rx=24px on desktop) -->
        <div class="bg-[#980517] rounded-[22px] lg:rounded-[26px] pt-8 sm:pt-11 lg:pt-[54px] pb-8 sm:pb-9 lg:pb-[42px] px-7 sm:px-10 lg:px-[50px] text-white flex flex-col justify-between shadow-md relative overflow-hidden min-h-[460px] md:min-h-full">
          
          <!-- Top Text Content (Pinned to top, aligned with right card header) -->
          <div class="relative z-10">
            <h1 class="text-3xl sm:text-[36px] lg:text-[54px] xl:text-[60px] font-normal sm:font-medium tracking-normal leading-[1.18] sm:leading-[1.18] text-white font-display">
              Kelola Pengambilan<br>
              Seragam dengan<br>
              Mudah.
            </h1>

            <p class="mt-7 sm:mt-9 lg:mt-11 text-sm sm:text-[15px] text-white/85 font-normal leading-[26px] max-w-[500px] font-sans">
              Mulai proses pengambilan seragammu dengan mudah. Login untuk melihat informasi seragam, mengetahui status pengambilan, mengakses QR Code, dan mendapatkan panduan lengkap sebelum datang ke lokasi.
            </p>
          </div>

          <!-- CTA Button (Unified Capsule matching Figma Mobile & Desktop) -->
          <div class="relative z-10 flex items-center pt-6">
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="inline-flex items-center justify-between gap-3 px-6 sm:px-7 h-[48px] sm:h-[52px] bg-[#D4A83A] hover:bg-[#B8902E] text-[#171717] rounded-full font-bold text-sm sm:text-base shadow-sm transition-all active:scale-95 group">
              <span>{{ auth()->check() ? 'Buka Dashboard' : 'Masuk ke Dashboard' }}</span>
              <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#171717]/10 flex items-center justify-center shrink-0 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="#171717" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="7" y1="17" x2="17" y2="7"></line>
                  <polyline points="7 7 17 7 17 17"></polyline>
                </svg>
              </span>
            </a>
          </div>
        </div>

        <!-- Hero Right Card (Soft Pink Card #F0DADC with Uniform Showcase Carousel) -->
        <div id="heroUniformCard" class="bg-[#F0DADC] rounded-[22px] lg:rounded-[26px] relative overflow-hidden min-h-[460px] md:min-h-full shadow-sm select-none flex flex-col justify-between">
          
          <!-- Top bar: Badge & Carousel arrows (Aligned horizontally at pt-[54px] px-[50px]) -->
          <div class="relative z-20 pt-8 sm:pt-11 lg:pt-[54px] px-7 sm:px-10 lg:px-[50px] flex items-center justify-between pointer-events-none">
            <div class="inline-flex items-center gap-2.5 text-[#980517] font-medium lg:font-semibold text-base sm:text-lg lg:text-xl font-display pointer-events-auto">
              <svg class="w-5 h-5 lg:w-[22px] lg:h-[22px] shrink-0" viewBox="776 153 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M780.848 165L776 162.149L779.573 155.851L784.424 158.698L784.427 153H791.573L791.576 158.698L796.427 155.851L800 162.149L795.152 165L800 167.851L796.427 174.149L791.576 171.302L791.573 177H784.427L784.424 171.302L779.573 174.149L776 167.851L780.848 165Z" fill="#4A4A4A"/>
              </svg>
              <span id="carouselUniformTitle" class="transition-opacity duration-300">Almamater HorizonU</span>
            </div>
            <!-- Prev / Next Carousel Buttons -->
            <div class="flex items-center gap-3 pointer-events-auto">
              <button id="carouselPrevBtn" type="button" class="w-10 h-10 lg:w-11 lg:h-11 rounded-full bg-[#F5E6E8] flex items-center justify-center text-[#980517] hover:bg-white active:scale-95 transition-all focus:outline-none" title="Sebelumnya" aria-label="Slide Sebelumnya">
                <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
              </button>
              <button id="carouselNextBtn" type="button" class="w-10 h-10 lg:w-11 lg:h-11 rounded-full bg-white flex items-center justify-center text-[#171717] hover:bg-[#980517] hover:text-white active:scale-95 shadow-sm transition-all focus:outline-none" title="Selanjutnya" aria-label="Slide Selanjutnya">
                <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Slides Track / Container (Holding Display 1 to Display 5) -->
          <div class="absolute inset-0 w-full h-full z-10 overflow-hidden pointer-events-none flex items-end justify-center">
            <!-- Slide 1: Display 1.webp (Almamater HorizonU) - Active by default -->
            <div class="hero-slide active" data-title="Almamater HorizonU" data-index="0">
              <img src="{{ asset('assets/img/Display 1.webp') }}" alt="Jas Almamater HorizonU" class="h-[84%] sm:h-[86%] lg:h-[88%] w-auto max-w-full object-contain object-bottom pointer-events-none select-none">
            </div>

            <!-- Slide 2: Display 2.webp (Kemeja PDH HorizonU) -->
            <div class="hero-slide" data-title="Kemeja PDH HorizonU" data-index="1">
              <img src="{{ asset('assets/img/Display 2.webp') }}" alt="Kemeja PDH HorizonU" class="h-[84%] sm:h-[86%] lg:h-[88%] w-auto max-w-full object-contain object-bottom pointer-events-none select-none">
            </div>

            <!-- Slide 3: Display 3.webp (Kemeja Harian HorizonU) -->
            <div class="hero-slide" data-title="Kemeja Harian HorizonU" data-index="2">
              <img src="{{ asset('assets/img/Display 3.webp') }}" alt="Kemeja Harian HorizonU" class="h-[84%] sm:h-[86%] lg:h-[88%] w-auto max-w-full object-contain object-bottom pointer-events-none select-none">
            </div>

            <!-- Slide 4: Display 4.webp (Seragam Medis HorizonU) -->
            <div class="hero-slide" data-title="Seragam Medis HorizonU" data-index="3">
              <img src="{{ asset('assets/img/Display 4.webp') }}" alt="Seragam Medis HorizonU" class="h-[84%] sm:h-[86%] lg:h-[88%] w-auto max-w-full object-contain object-bottom pointer-events-none select-none">
            </div>

            <!-- Slide 5: Display 5.webp (Praktikum & Lab HorizonU) -->
            <div class="hero-slide" data-title="Praktikum &amp; Lab HorizonU" data-index="4">
              <img src="{{ asset('assets/img/Display 5.webp') }}" alt="Praktikum &amp; Lab HorizonU" class="h-[84%] sm:h-[86%] lg:h-[88%] w-auto max-w-full object-contain object-bottom pointer-events-none select-none">
            </div>
          </div>

        </div>

      </div>
    </section>

  <!-- ================= TENTANG KAMI SECTION ================= -->
  <section id="tentang-kami" class="w-full px-4 sm:px-6 lg:px-8 py-4 sm:py-8 max-w-[1840px] mx-auto scroll-mt-20 lg:scroll-mt-24">
    <div class="relative bg-[#980517] rounded-[14px] sm:rounded-[20px] lg:rounded-[24px] overflow-hidden text-white shadow-xl flex flex-col md:flex-row md:items-center min-h-0 md:min-h-[380px] lg:min-h-[420px] xl:min-h-[460px]">
      
      <!-- Mobile Top Graphic (Vertical composition matching Figma Mobile Landing Page(5).png) -->
      <div class="md:hidden w-full relative shrink-0">
        <img src="{{ asset('assets/img/tentang_mobile_top.webp') }}" alt="Tentang HorizonU Uniform" class="w-full h-auto object-cover select-none pointer-events-none">
      </div>

      <!-- Desktop & Tablet Full Banner Background Image (Horizontal layout matching Figma Desktop & Tablet) -->
      <img src="{{ asset('assets/img') }}/image 61.webp" alt="Tentang HorizonU Uniform" class="hidden md:block absolute inset-0 w-full h-full object-cover md:object-[35%_center] lg:object-center pointer-events-none select-none z-0">

      <!-- Right Content: Golden Badge, Title, and Description positioned cleanly over the maroon backdrop -->
      <div class="relative z-10 w-full md:w-[50%] lg:w-[48%] md:ml-auto p-6 sm:p-10 lg:p-14 lg:pl-2 xl:p-16 space-y-4 lg:space-y-6">
        <div class="badge-capsule self-start">
          <svg class="w-5 h-5 text-[#D4A83A] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <circle cx="12" cy="12" r="6"/>
            <circle cx="12" cy="12" r="2" fill="currentColor"/>
          </svg>
          <span>Tentang Kami</span>
        </div>

        <h2 class="text-2xl sm:text-3xl lg:text-[40px] xl:text-[44px] font-normal sm:font-medium tracking-tight text-white leading-tight font-display">
          Apa Itu HorizonU Uniform?
        </h2>

        <p class="text-xs sm:text-sm lg:text-[15px] xl:text-base text-white/95 leading-relaxed font-normal max-w-[580px] xl:max-w-[640px] font-sans">
          HorizonU Uniform hadir untuk mempermudah mahasiswa dalam mendapatkan informasi dan melakukan pengambilan seragam. Dengan seluruh kebutuhan tersedia dalam satu platform, mahasiswa dapat mengetahui status pengambilan, mengakses QR Code, dan mengikuti proses distribusi dengan lebih mudah dan teratur.
        </p>
      </div>

    </div>
  </section>

  <!-- ================= AKSES AKUN - MAHASISWA BARU ================= -->
  <section id="akses-maba" class="py-14 sm:py-20 bg-white scroll-mt-20 lg:scroll-mt-24">
    <div class="w-full px-4 sm:px-9 lg:px-16 max-w-[1440px] mx-auto">
      
      <!-- Section Header -->
      <div class="mb-8 sm:mb-11">
        <div class="badge-capsule mb-4">
          <svg class="w-5 h-5 text-[#D4A83A] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 8V5a2 2 0 0 1 2-2h3"/>
            <path d="M16 3h3a2 2 0 0 1 2 2v3"/>
            <path d="M21 16v3a2 2 0 0 1-2 2h-3"/>
            <path d="M8 21H5a2 2 0 0 1-2-2v-3"/>
            <rect x="7" y="11" width="10" height="7" rx="1.5" fill="currentColor"/>
            <path d="M9 11V9a3 3 0 0 1 6 0v2"/>
          </svg>
          <span>Akses Akun</span>
        </div>
        <h2 class="text-3xl sm:text-4xl lg:text-[46px] font-normal sm:font-medium tracking-tight text-[#171717] font-display leading-[1.2]">
          Cara Akses Akun HorizonU Uniform
        </h2>
        <p class="text-base sm:text-lg text-[#333333] font-bold font-sans mt-2">Untuk Mahasiswa Baru</p>
      </div>

      <!-- 4 Langkah Mahasiswa Baru (1 Kolom di Tablet 834px & Mobile 402px, 2 Kolom di Desktop 1440px) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 lg:gap-8">
        
        <!-- Step 1 -->
        <div class="bg-[#F6F6F6] rounded-[8px] p-6 sm:p-7 lg:p-8 flex items-start gap-4 sm:gap-6">
          <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-[84px] lg:h-[84px] rounded-[6px] bg-[#2E0105] text-white flex items-center justify-center font-normal sm:font-medium text-2xl sm:text-3xl lg:text-[34px] shrink-0 font-display">
            1
          </div>
          <div class="space-y-1.5">
            <h3 class="text-xl sm:text-2xl font-normal sm:font-medium text-[#171717] font-display leading-snug">Cek Credential Akun</h3>
            <p class="text-xs sm:text-sm text-[#8E8D8D] font-sans leading-relaxed">
              Periksa email pribadi Anda untuk menemukan credential akun HorizonU Uniform yang telah dikirimkan.
            </p>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="bg-[#F6F6F6] rounded-[8px] p-6 sm:p-7 lg:p-8 flex items-start gap-4 sm:gap-6">
          <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-[84px] lg:h-[84px] rounded-[6px] bg-[#2E0105] text-white flex items-center justify-center font-normal sm:font-medium text-2xl sm:text-3xl lg:text-[34px] shrink-0 font-display">
            2
          </div>
          <div class="space-y-1.5">
            <h3 class="text-xl sm:text-2xl font-normal sm:font-medium text-[#171717] font-display leading-snug">Login ke HorizonU Uniform</h3>
            <p class="text-xs sm:text-sm text-[#8E8D8D] font-sans leading-relaxed">
              Gunakan username dan password yang tercantum pada credential untuk masuk ke HorizonU Uniform melalui <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="font-semibold text-[#980517] hover:underline inline-flex items-center gap-1">halaman login <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i></a>.
            </p>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="bg-[#F6F6F6] rounded-[8px] p-6 sm:p-7 lg:p-8 flex items-start gap-4 sm:gap-6">
          <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-[84px] lg:h-[84px] rounded-[6px] bg-[#2E0105] text-white flex items-center justify-center font-normal sm:font-medium text-2xl sm:text-3xl lg:text-[34px] shrink-0 font-display">
            3
          </div>
          <div class="space-y-1.5">
            <h3 class="text-xl sm:text-2xl font-normal sm:font-medium text-[#171717] font-display leading-snug">Lengkapi Data Akun</h3>
            <p class="text-xs sm:text-sm text-[#8E8D8D] font-sans leading-relaxed">
              Setelah berhasil login, Anda diwajibkan mengganti password. Jika sudah memiliki email kampus, tambahkan email tersebut ke data profil Anda.
            </p>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="bg-[#F6F6F6] rounded-[8px] p-6 sm:p-7 lg:p-8 flex items-start gap-4 sm:gap-6">
          <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-[84px] lg:h-[84px] rounded-[6px] bg-[#2E0105] text-white flex items-center justify-center font-normal sm:font-medium text-2xl sm:text-3xl lg:text-[34px] shrink-0 font-display">
            4
          </div>
          <div class="space-y-1.5">
            <h3 class="text-xl sm:text-2xl font-normal sm:font-medium text-[#171717] font-display leading-snug">Isi Ukuran & Tunggu Jadwal</h3>
            <p class="text-xs sm:text-sm text-[#8E8D8D] font-sans leading-relaxed">
              Isi ukuran seragam sesuai dengan ukuran yang Anda gunakan. Jadwal pembagian akan diumumkan melalui email pribadi atau email kampus.
            </p>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- ================= AKSES AKUN - MAHASISWA CONTINUING (#250005 Dark Wine) ================= -->
  <section id="akses-continuing" class="py-14 sm:py-20 bg-[#250005] text-white scroll-mt-20 lg:scroll-mt-24">
    <div class="w-full px-4 sm:px-9 lg:px-16 max-w-[1440px] mx-auto">
      
      <!-- Section Header -->
      <div class="mb-8 sm:mb-11">
        <div class="badge-capsule mb-4">
          <svg class="w-5 h-5 text-[#D4A83A] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 8V5a2 2 0 0 1 2-2h3"/>
            <path d="M16 3h3a2 2 0 0 1 2 2v3"/>
            <path d="M21 16v3a2 2 0 0 1-2 2h-3"/>
            <path d="M8 21H5a2 2 0 0 1-2-2v-3"/>
            <rect x="7" y="11" width="10" height="7" rx="1.5" fill="currentColor"/>
            <path d="M9 11V9a3 3 0 0 1 6 0v2"/>
          </svg>
          <span>Akses Akun</span>
        </div>
        <h2 class="text-3xl sm:text-4xl lg:text-[46px] font-normal sm:font-medium tracking-tight text-white font-display leading-[1.2]">
          Cara Akses Akun HorizonU Uniform
        </h2>
        <p class="text-base sm:text-lg text-white font-medium font-sans mt-2">Untuk Mahasiswa Continuing</p>
      </div>

      <!-- 4 Langkah Mahasiswa Continuing (Cards border border-white/15, White Box number with maroon text) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 lg:gap-8">
        
        <!-- Step 1 -->
        <div class="bg-transparent rounded-[8px] p-6 sm:p-7 lg:p-8 flex items-start gap-4 sm:gap-6 border border-white/15">
          <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-[84px] lg:h-[84px] rounded-[6px] bg-white text-[#250005] flex items-center justify-center font-normal sm:font-medium text-2xl sm:text-3xl lg:text-[34px] shrink-0 font-display shadow-sm">
            1
          </div>
          <div class="space-y-1.5">
            <h3 class="text-xl sm:text-2xl font-normal sm:font-medium text-white font-display leading-snug">Cek Credential Akun</h3>
            <p class="text-xs sm:text-sm text-[#D5C0C4] font-sans leading-relaxed">
              Periksa email Kampus Anda untuk menemukan credential akun HorizonU Uniform yang telah dikirimkan.
            </p>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="bg-transparent rounded-[8px] p-6 sm:p-7 lg:p-8 flex items-start gap-4 sm:gap-6 border border-white/15">
          <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-[84px] lg:h-[84px] rounded-[6px] bg-white text-[#250005] flex items-center justify-center font-normal sm:font-medium text-2xl sm:text-3xl lg:text-[34px] shrink-0 font-display shadow-sm">
            2
          </div>
          <div class="space-y-1.5">
            <h3 class="text-xl sm:text-2xl font-normal sm:font-medium text-white font-display leading-snug">Login ke HorizonU Uniform</h3>
            <p class="text-xs sm:text-sm text-[#D5C0C4] font-sans leading-relaxed">
              Gunakan username dan password yang tercantum pada credential untuk masuk ke HorizonU Uniform melalui <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="font-semibold text-[#D4A83A] hover:underline inline-flex items-center gap-1">halaman login <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i></a>.
            </p>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="bg-transparent rounded-[8px] p-6 sm:p-7 lg:p-8 flex items-start gap-4 sm:gap-6 border border-white/15">
          <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-[84px] lg:h-[84px] rounded-[6px] bg-white text-[#250005] flex items-center justify-center font-normal sm:font-medium text-2xl sm:text-3xl lg:text-[34px] shrink-0 font-display shadow-sm">
            3
          </div>
          <div class="space-y-1.5">
            <h3 class="text-xl sm:text-2xl font-normal sm:font-medium text-white font-display leading-snug">Lengkapi Data Akun</h3>
            <p class="text-xs sm:text-sm text-[#D5C0C4] font-sans leading-relaxed">
              Setelah berhasil login, Anda diwajibkan mengganti password untuk menjaga keamanan akun.
            </p>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="bg-transparent rounded-[8px] p-6 sm:p-7 lg:p-8 flex items-start gap-4 sm:gap-6 border border-white/15">
          <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-[84px] lg:h-[84px] rounded-[6px] bg-white text-[#250005] flex items-center justify-center font-normal sm:font-medium text-2xl sm:text-3xl lg:text-[34px] shrink-0 font-display shadow-sm">
            4
          </div>
          <div class="space-y-1.5">
            <h3 class="text-xl sm:text-2xl font-normal sm:font-medium text-white font-display leading-snug">Isi Ukuran & Tunggu Jadwal</h3>
            <p class="text-xs sm:text-sm text-[#D5C0C4] font-sans leading-relaxed">
              Isi ukuran seragam sesuai dengan ukuran yang Anda gunakan. Tunggu jadwal pembagian yang akan diumumkan melalui email kampus.
            </p>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- ================= CARA PENGAMBILAN SERAGAM (6 LANGKAH TIMELINE) ================= -->
  <section id="alur-pengambilan" class="py-16 sm:py-24 bg-white relative scroll-mt-20 lg:scroll-mt-24">
    <div class="w-full px-4 sm:px-9 lg:px-16 max-w-[1440px] mx-auto">
      
      <!-- Section Title Centered -->
      <div class="text-center max-w-2xl mx-auto mb-16 sm:mb-20">
        <div class="badge-capsule mb-4">
          <span class="w-6 h-6 rounded-full bg-[#D4A83A] text-white flex items-center justify-center font-bold text-xs sm:text-sm font-sans shrink-0">i</span>
          <span>Cara Pengambilan</span>
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-[44px] font-medium sm:font-semibold tracking-tight text-[#171717] mb-3 font-display leading-tight">
          Bagaimana Cara Mengambil Seragam?
        </h2>
        <p class="text-gray-600 text-xs sm:text-sm lg:text-base font-sans max-w-xl mx-auto leading-relaxed">
          Ikuti langkah berikut untuk memastikan proses pengambilan seragam berjalan lancar.
        </p>
      </div>

      <!-- Timeline Container -->
      <div id="timelineContainer" class="relative w-full">
        
        <!-- Central vertical timeline line for Desktop (center at 50%) -->
        <div id="timelineBaseDesktop" class="hidden lg:block absolute left-1/2 top-4 bottom-10 w-[3px] bg-[#F0DADC] -translate-x-1/2 z-0 rounded-full"></div>
        <div id="timelineProgressDesktop" class="hidden lg:block absolute left-1/2 top-4 bottom-10 w-[3px] bg-gradient-to-b from-[#980517] via-[#980517] to-[#D4A83A] -translate-x-1/2 z-0 rounded-full origin-top" style="transform: scaleY(0);"></div>

        <!-- Left vertical timeline line for Mobile & Tablet (matching Tablet 834px & Mobile 402px SVGs) -->
        <div id="timelineBaseMobile" class="lg:hidden absolute left-5 sm:left-7 top-4 bottom-10 w-[3px] bg-[#F0DADC] -translate-x-1/2 z-0 rounded-full"></div>
        <div id="timelineProgressMobile" class="lg:hidden absolute left-5 sm:left-7 top-4 bottom-10 w-[3px] bg-gradient-to-b from-[#980517] via-[#980517] to-[#D4A83A] -translate-x-1/2 z-0 rounded-full origin-top" style="transform: scaleY(0);"></div>

        <div class="space-y-12 sm:space-y-16 lg:space-y-20">

          <!-- ================= STEP 1: Desktop Right (x=783), Mobile/Tablet Left-Timeline ================= -->
          <div class="timeline-step relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Desktop Left: Empty Placeholder (Col 1-6) -->
            <div class="hidden lg:block lg:col-span-6"></div>
            
            <!-- Timeline Leaf Node (Exact vector marker matching Figma SVG) -->
            <div class="timeline-node absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-0.5 sm:top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Content Card: Right side on Desktop (Col 7-12), Placed after line on Mobile/Tablet -->
            <div class="pl-12 sm:pl-16 lg:pl-10 lg:col-span-6 space-y-3.5 sm:space-y-4 text-left">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-[#171717] text-xs font-bold font-sans">
                Step 1
              </div>
              <h3 class="text-xl sm:text-2xl lg:text-[26px] font-normal sm:font-medium text-[#171717] leading-snug font-display">
                Cek Eligible & Jadwal Pengambilan
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-[15px] leading-relaxed font-sans max-w-lg">
                Pastikan Anda telah memenuhi syarat untuk menerima seragam (eligible), termasuk telah menyelesaikan pembayaran uang kuliah yang dipersyaratkan. Setelah itu, pastikan Anda telah menerima pemberitahuan mengenai jadwal dan lokasi pengambilan melalui email pribadi atau email kampus.
              </p>
              <!-- Photo Stack (img_4.webp) -->
              <div class="pt-2">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[420px] photo-stack-base">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_4.webp" alt="Cek Eligible dan Jadwal Pengambilan" class="w-full h-48 sm:h-60 lg:h-[267px] object-cover">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ================= STEP 2: Desktop Left (x=64), Mobile/Tablet Left-Timeline ================= -->
          <div class="timeline-step relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Content Card: Left side on Desktop (Col 1-6), Placed after line on Mobile/Tablet -->
            <div class="pl-12 sm:pl-16 lg:pl-0 lg:pr-10 lg:col-span-6 space-y-3.5 sm:space-y-4 text-left flex flex-col items-start">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-[#171717] text-xs font-bold font-sans">
                Step 2
              </div>
              <h3 class="text-xl sm:text-2xl lg:text-[26px] font-normal sm:font-medium text-[#171717] leading-snug font-display">
                Dapatkan Kode QR Pengambilan
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-[15px] leading-relaxed font-sans max-w-lg">
                Login kembali ke HorizonU Uniform dan isi ukuran untuk event pembagian seragam yang tersedia. Setelah itu, pastikan Kode QR pengambilan telah Anda terima dan siap ditunjukkan kepada petugas.
              </p>
              <!-- Photo Stack (img_7.webp - QR Code Dashboard) -->
              <div class="pt-2 w-full flex justify-start">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[420px] photo-stack-base text-left">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_7.webp" alt="Dapatkan Kode QR Pengambilan" class="w-full h-48 sm:h-60 lg:h-[268px] object-cover">
                  </div>
                </div>
              </div>
            </div>

            <!-- Timeline Leaf Node (Exact vector marker matching Figma SVG) -->
            <div class="timeline-node absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-0.5 sm:top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Desktop Right: Empty Placeholder (Col 7-12) -->
            <div class="hidden lg:block lg:col-span-6"></div>
          </div>

          <!-- ================= STEP 3: Desktop Right (x=783), Mobile/Tablet Left-Timeline ================= -->
          <div class="timeline-step relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Desktop Left: Empty Placeholder (Col 1-6) -->
            <div class="hidden lg:block lg:col-span-6"></div>
            
            <!-- Timeline Leaf Node -->
            <div class="timeline-node absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-0.5 sm:top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Content Card: Right side on Desktop (Col 7-12) -->
            <div class="pl-12 sm:pl-16 lg:pl-10 lg:col-span-6 space-y-3.5 sm:space-y-4 text-left">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-[#171717] text-xs font-bold font-sans">
                Step 3
              </div>
              <h3 class="text-xl sm:text-2xl lg:text-[26px] font-normal sm:font-medium text-[#171717] leading-snug font-display">
                Datang Sesuai Jadwal
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-[15px] leading-relaxed font-sans max-w-lg">
                Datang tepat waktu ke lokasi pembagian sesuai dengan jadwal dan tempat yang tercantum dalam pemberitahuan melalui email.
              </p>
              <!-- Photo Stack (img_5.webp) -->
              <div class="pt-2">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[420px] photo-stack-base">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_5.webp" alt="Datang Sesuai Jadwal" class="w-full h-48 sm:h-60 lg:h-[267px] object-cover">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ================= STEP 4: Desktop Left (x=64), Mobile/Tablet Left-Timeline ================= -->
          <div class="timeline-step relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Content Card: Left side on Desktop (Col 1-6) -->
            <div class="pl-12 sm:pl-16 lg:pl-0 lg:pr-10 lg:col-span-6 space-y-3.5 sm:space-y-4 text-left flex flex-col items-start">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-[#171717] text-xs font-bold font-sans">
                Step 4
              </div>
              <h3 class="text-xl sm:text-2xl lg:text-[26px] font-normal sm:font-medium text-[#171717] leading-snug font-display">
                Isi Kehadiran & Tunjukkan Kode QR
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-[15px] leading-relaxed font-sans max-w-lg">
                Isi data kehadiran pada saat pengambilan, kemudian tunjukkan Kode QR kepada petugas pembagian untuk proses verifikasi.
              </p>
              <!-- Photo Stack (img_8.webp) -->
              <div class="pt-2 w-full flex justify-start">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[420px] photo-stack-base text-left">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_8.webp" alt="Isi Kehadiran dan Tunjukkan QR" class="w-full h-48 sm:h-60 lg:h-[268px] object-cover">
                  </div>
                </div>
              </div>
            </div>

            <!-- Timeline Leaf Node -->
            <div class="timeline-node absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-0.5 sm:top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Desktop Right: Empty Placeholder (Col 7-12) -->
            <div class="hidden lg:block lg:col-span-6"></div>
          </div>

          <!-- ================= STEP 5: Desktop Right (x=783), Mobile/Tablet Left-Timeline ================= -->
          <div class="timeline-step relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Desktop Left: Empty Placeholder (Col 1-6) -->
            <div class="hidden lg:block lg:col-span-6"></div>
            
            <!-- Timeline Leaf Node -->
            <div class="timeline-node absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-0.5 sm:top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Content Card: Right side on Desktop (Col 7-12) -->
            <div class="pl-12 sm:pl-16 lg:pl-10 lg:col-span-6 space-y-3.5 sm:space-y-4 text-left">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-[#171717] text-xs font-bold font-sans">
                Step 5
              </div>
              <h3 class="text-xl sm:text-2xl lg:text-[26px] font-normal sm:font-medium text-[#171717] leading-snug font-display">
                Periksa Seragam yang Diterima
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-[15px] leading-relaxed font-sans max-w-lg">
                Pastikan ukuran dan jenis seragam yang Anda terima sudah sesuai dengan data yang tercatat di HorizonU Uniform sebelum meninggalkan lokasi.
              </p>
              <!-- Photo Stack (img_6.webp - Periksa Seragam di Loket) -->
              <div class="pt-2">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[420px] photo-stack-base">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_6.webp" alt="Periksa Seragam yang Diterima" class="w-full h-48 sm:h-60 lg:h-[267px] object-cover">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ================= STEP 6: Desktop Left (x=64), Mobile/Tablet Left-Timeline ================= -->
          <div class="timeline-step relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Content Card: Left side on Desktop (Col 1-6) -->
            <div class="pl-12 sm:pl-16 lg:pl-0 lg:pr-10 lg:col-span-6 space-y-3.5 sm:space-y-4 text-left flex flex-col items-start">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-[#171717] text-xs font-bold font-sans">
                Step 6
              </div>
              <h3 class="text-xl sm:text-2xl lg:text-[26px] font-normal sm:font-medium text-[#171717] leading-snug font-display">
                Cek Riwayat Pengambilan
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-[15px] leading-relaxed font-sans max-w-lg">
                Setelah proses selesai, Anda dapat melihat data dan riwayat seragam yang telah diambil melalui HorizonU Uniform.
              </p>
              <!-- Photo Stack (img_9.webp) -->
              <div class="pt-2 w-full flex justify-start">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[420px] photo-stack-base text-left">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_9.webp" alt="Cek Riwayat Pengambilan" class="w-full h-48 sm:h-60 lg:h-[267px] object-cover">
                  </div>
                </div>
              </div>
            </div>

            <!-- Timeline Leaf Node -->
            <div class="timeline-node absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-0.5 sm:top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Desktop Right: Empty Placeholder (Col 7-12) -->
            <div class="hidden lg:block lg:col-span-6"></div>
          </div>

        </div>

      </div>

    </div>
  </section>

  <!-- ================= FAQ SECTION (#F5E6E8 Border Matching SVG) ================= -->
  <section id="faq" class="py-16 sm:py-20 bg-white scroll-mt-20 lg:scroll-mt-24">
    <div class="w-full px-4 sm:px-9 lg:px-[100px] max-w-[1440px] mx-auto">
      
      <!-- Section Title Centered -->
      <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
        <div class="badge-capsule mb-4">
          <span class="w-6 h-6 rounded-full bg-[#D4A83A] text-white flex items-center justify-center font-bold text-xs sm:text-sm font-sans shrink-0">?</span>
          <span>FAQ</span>
        </div>
        <h2 class="text-3xl sm:text-4xl lg:text-[44px] font-normal sm:font-medium tracking-tight text-[#171717] mb-3 font-display leading-tight">
          Pertanyaan yang Sering Diajukan
        </h2>
        <p class="text-gray-600 text-xs sm:text-sm lg:text-base font-sans max-w-2xl mx-auto leading-relaxed">
          Temukan informasi yang Anda butuhkan mengenai akses akun, persyaratan, jadwal pembagian, Kode QR, dan proses pengambilan seragam.
        </p>
      </div>

      <!-- 6 Accordion Items: 2 Columns on Desktop, Single Column on Tablet & Mobile with exact Figma sequence -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        
        <!-- Left Column on Desktop / First 3 on Mobile & Tablet -->
        <div class="space-y-4 lg:space-y-6">
          <!-- Q1: Akun -->
          <div class="faq-item bg-white rounded-[10px] border border-[#F0DADC] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
            <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-normal sm:font-medium text-gray-900 text-sm sm:text-base lg:text-[17px] focus:outline-none font-sans">
              <span>Bagaimana cara mendapatkan akun HorizonU Uniform?</span>
              <span class="faq-icon text-2xl font-light text-gray-900 shrink-0 select-none leading-none">+</span>
            </button>
            <div class="faq-answer">
              <div class="faq-answer-inner">
                <div class="pt-3 text-xs sm:text-sm text-gray-600 font-sans leading-relaxed">
                  Akun mahasiswa baru diterbitkan secara otomatis dan credential login akan dikirimkan ke email pribadi yang terdaftar saat pendaftaran kampus. Untuk mahasiswa continuing, credential dikirim ke email kampus aktif.
                </div>
              </div>
            </div>
          </div>

          <!-- Q3: Kendala Akun / Proses -->
          <div class="faq-item bg-white rounded-[10px] border border-[#F0DADC] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
            <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-normal sm:font-medium text-gray-900 text-sm sm:text-base lg:text-[17px] focus:outline-none font-sans">
              <span>Apa yang harus dilakukan jika mengalami kendala dengan akun atau proses pengambilan?</span>
              <span class="faq-icon text-2xl font-light text-gray-900 shrink-0 select-none leading-none">+</span>
            </button>
            <div class="faq-answer">
              <div class="faq-answer-inner">
                <div class="pt-3 text-xs sm:text-sm text-gray-600 font-sans leading-relaxed">
                  Jika terjadi kendala saat login atau proses pengambilan, silakan hubungi bagian Finance atau kunjungi loket One Stop Service di kampus pada jam operasional kerja.
                </div>
              </div>
            </div>
          </div>

          <!-- Q5: Dapatkan Kode QR -->
          <div class="faq-item bg-white rounded-[10px] border border-[#F0DADC] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
            <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-normal sm:font-medium text-gray-900 text-sm sm:text-base lg:text-[17px] focus:outline-none font-sans">
              <span>Di mana saya mendapatkan Kode QR untuk mengambil seragam?</span>
              <span class="faq-icon text-2xl font-light text-gray-900 shrink-0 select-none leading-none">+</span>
            </button>
            <div class="faq-answer">
              <div class="faq-answer-inner">
                <div class="pt-3 text-xs sm:text-sm text-gray-600 font-sans leading-relaxed">
                  Kode QR dapat diakses melalui menu "Kode QR" di dalam dashboard portal HorizonU Uniform setelah Anda berstatus eligible dan telah mengonfirmasi data ukuran seragam.
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column on Desktop / Next 3 on Mobile & Tablet -->
        <div class="space-y-4 lg:space-y-6">
          <!-- Q2: Kapan Mengisi Ukuran -->
          <div class="faq-item bg-white rounded-[10px] border border-[#F0DADC] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
            <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-normal sm:font-medium text-gray-900 text-sm sm:text-base lg:text-[17px] focus:outline-none font-sans">
              <span>Kapan saya bisa mengisi ukuran seragam?</span>
              <span class="faq-icon text-2xl font-light text-gray-900 shrink-0 select-none leading-none">+</span>
            </button>
            <div class="faq-answer">
              <div class="faq-answer-inner">
                <div class="pt-3 text-xs sm:text-sm text-gray-600 font-sans leading-relaxed">
                  Pengisian ukuran seragam dapat dilakukan segera setelah Anda berhasil login ke portal selama periode pengisian ukuran seragam masih dibuka oleh pihak universitas.
                </div>
              </div>
            </div>
          </div>

          <!-- Q4: Jadwal & Lokasi -->
          <div class="faq-item bg-white rounded-[10px] border border-[#F0DADC] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
            <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-normal sm:font-medium text-gray-900 text-sm sm:text-base lg:text-[17px] focus:outline-none font-sans">
              <span>Bagaimana saya mengetahui jadwal dan lokasi pengambilan seragam?</span>
              <span class="faq-icon text-2xl font-light text-gray-900 shrink-0 select-none leading-none">+</span>
            </button>
            <div class="faq-answer">
              <div class="faq-answer-inner">
                <div class="pt-3 text-xs sm:text-sm text-gray-600 font-sans leading-relaxed">
                  Informasi tanggal, jam sesi, dan lokasi fisik pembagian seragam akan tertera langsung di dashboard beranda akun Anda dan disiarkan melalui email resmi universitas.
                </div>
              </div>
            </div>
          </div>

          <!-- Q6: Email Kampus -->
          <div class="faq-item bg-white rounded-[10px] border border-[#F0DADC] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
            <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-normal sm:font-medium text-gray-900 text-sm sm:text-base lg:text-[17px] focus:outline-none font-sans">
              <span>Saya sudah memiliki email kampus, apakah harus menambahkannya ke profil?</span>
              <span class="faq-icon text-2xl font-light text-gray-900 shrink-0 select-none leading-none">+</span>
            </button>
            <div class="faq-answer">
              <div class="faq-answer-inner">
                <div class="pt-3 text-xs sm:text-sm text-gray-600 font-sans leading-relaxed">
                  Sangat disarankan! Menambahkan email kampus resmi memastikan seluruh bukti transaksi, tiket QR, dan notifikasi jadwal penting terarsip dengan aman di akun civitas Anda.
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>

  </main>

  <!-- ================= FOOTER ================= -->
  <footer class="relative bg-[#800412] text-white pt-16 pb-12">
    <div class="w-full px-4 sm:px-9 lg:px-16 max-w-[1440px] mx-auto space-y-10 sm:space-y-12">
      
      <!-- Brand & Navigation in Footer -->
      <div class="flex flex-col items-center justify-center text-center space-y-5">
        <!-- Logo Universitas Horizon Indonesia (img_10.webp 56px desktop, 48px tablet, 42px mobile) -->
        <img src="{{ asset('assets/img') }}/img_10.webp" alt="Universitas Horizon Indonesia" class="w-11 h-11 sm:w-12 sm:h-12 lg:w-14 lg:h-14 object-contain rounded-full shadow-lg">
        
        <div class="space-y-2.5">
          <h3 class="text-2xl sm:text-3xl lg:text-[32px] font-normal sm:font-medium tracking-tight text-white font-display">HorizonU Uniform</h3>
          <div class="w-48 sm:w-56 lg:w-[260px] h-[1.5px] bg-white mx-auto"></div>
        </div>

        <nav class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-10 text-xs sm:text-sm font-medium text-white/90 pt-1">
          <a href="#tentang-kami" class="hover:text-brand-gold transition-colors font-display tracking-wide">Tentang Kami</a>
          <a href="#akses-maba" class="hover:text-brand-gold transition-colors font-display tracking-wide">Akses Akun</a>
          <a href="#alur-pengambilan" class="hover:text-brand-gold transition-colors font-display tracking-wide">Cara Pengambilan</a>
          <a href="#faq" class="hover:text-brand-gold transition-colors font-display tracking-wide">FAQ</a>
        </nav>
      </div>

      <!-- Bottom Bar: Support & Copyright (Responsive order matching Mobile, Tablet, and Desktop Figma designs) -->
      <div class="pt-6 border-t-0 md:border-t border-white/10 flex flex-col md:flex-row lg:flex-row items-center justify-between text-xs sm:text-[13px] text-white/80 gap-4">
        <!-- Support Text: On Mobile (top), On Tablet (right), On Desktop (left) -->
        <p class="text-center md:text-right lg:text-left font-display order-1 md:order-2 lg:order-1">
          Hubungi Finance atau kunjungi One Stop Service untuk bantuan.
        </p>
        <!-- Copyright Text: On Mobile (bottom), On Tablet (left), On Desktop (right) -->
        <div class="text-center md:text-left lg:text-right leading-relaxed font-display order-2 md:order-1 lg:order-2">
          <p>Copyright &copy; 2026 All Rights Reserved</p>
          <p>HorizonU Uniform</p>
        </div>
      </div>

    </div>
  </footer>

  <!-- Interactive JavaScript -->
  <script>
    // 1. Mobile & Tablet Menu Toggle & Sticky Header Elevation
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuIcon = mobileMenuBtn ? mobileMenuBtn.querySelector('i') : null;
    const headerEl = document.querySelector('header');

    function toggleMobileMenu(forceState) {
      if (!mobileMenu) return;
      const isOpen = typeof forceState === 'boolean' ? forceState : !mobileMenu.classList.contains('open');
      if (isOpen) {
        mobileMenu.classList.add('open');
        if (mobileMenuIcon) {
          mobileMenuIcon.classList.remove('fa-bars');
          mobileMenuIcon.classList.add('fa-xmark');
        }
      } else {
        mobileMenu.classList.remove('open');
        if (mobileMenuIcon) {
          mobileMenuIcon.classList.remove('fa-xmark');
          mobileMenuIcon.classList.add('fa-bars');
        }
      }
    }

    if (mobileMenuBtn) {
      mobileMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMobileMenu();
      });
    }

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (mobileMenu && mobileMenu.classList.contains('open') && !mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
        toggleMobileMenu(false);
      }
    });

    // Sticky Header elevation on scroll
    if (headerEl) {
      const handleHeaderScroll = () => {
        if (window.scrollY > 15) {
          headerEl.classList.add('shadow-md', 'bg-white/95', 'backdrop-blur-md');
          headerEl.classList.remove('bg-white');
        } else {
          headerEl.classList.remove('shadow-md', 'bg-white/95', 'backdrop-blur-md');
          headerEl.classList.add('bg-white');
        }
      };
      window.addEventListener('scroll', handleHeaderScroll, { passive: true });
      handleHeaderScroll();
    }

    // 2. Smooth Anchor Navigation with Sticky Header Offset
    function scrollToTargetWithOffset(target) {
      if (!target) return;
      const headerHeight = headerEl ? headerEl.offsetHeight : 78;
      const extraOffset = window.innerWidth >= 1024 ? 20 : 16;
      const elementPosition = target.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - (headerHeight + extraOffset);

      window.scrollTo({
        top: Math.max(0, offsetPosition),
        behavior: 'smooth'
      });
    }

    // Intercept all hash anchor links on page
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const hash = this.getAttribute('href');
        if (!hash || hash === '#' || hash === '#!') return;

        const target = document.querySelector(hash);
        if (target) {
          e.preventDefault();

          // Close mobile menu first if open
          if (mobileMenu && mobileMenu.classList.contains('open')) {
            toggleMobileMenu(false);
          }

          // Allow DOM reflow to settle before scrolling
          requestAnimationFrame(() => {
            scrollToTargetWithOffset(target);
            if (history.pushState) {
              history.pushState(null, '', hash);
            }
          });
        }
      });
    });

    // Handle initial hash on page load (e.g. direct link welcome#faq)
    if (window.location.hash) {
      setTimeout(() => {
        const initialTarget = document.querySelector(window.location.hash);
        if (initialTarget) {
          scrollToTargetWithOffset(initialTarget);
        }
      }, 150);
    }

    // 3. FAQ Accordion Toggle
    document.querySelectorAll('.faq-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        const parent = btn.closest('.faq-item');
        const isActive = parent.classList.contains('active');
        
        // Close other accordions
        document.querySelectorAll('.faq-item').forEach(item => {
          if (item !== parent) item.classList.remove('active');
        });

        if (isActive) {
          parent.classList.remove('active');
        } else {
          parent.classList.add('active');
        }
      });
    });

    // 4. Hero Uniform Showcase Carousel (Display 1 to Display 5) - Smooth & Autoplay
    (function initUniformCarousel() {
      const slides = document.querySelectorAll('.hero-slide');
      const titleEl = document.getElementById('carouselUniformTitle');
      const prevBtn = document.getElementById('carouselPrevBtn');
      const nextBtn = document.getElementById('carouselNextBtn');
      const cardContainer = document.getElementById('heroUniformCard');

      if (!slides.length || !prevBtn || !nextBtn) return;

      let currentIndex = Array.from(slides).findIndex(s => s.classList.contains('active'));
      if (currentIndex === -1) currentIndex = 0;
      const totalSlides = slides.length;
      let autoplayTimer = null;
      let titleTimer = null;
      const AUTOPLAY_INTERVAL = 4000; // Berganti otomatis setiap 4 detik

      function updateSlide(newIndex) {
        if (newIndex < 0) newIndex = totalSlides - 1;
        if (newIndex >= totalSlides) newIndex = 0;

        currentIndex = newIndex;

        // Ultra-smooth slide transitions via .active class
        slides.forEach((slide, idx) => {
          if (idx === currentIndex) {
            slide.classList.add('active');
          } else {
            slide.classList.remove('active');
          }
        });

        // Smooth Title Animation with subtle vertical float & debounce
        if (titleEl) {
          if (titleTimer) clearTimeout(titleTimer);
          titleEl.style.opacity = '0';
          titleEl.style.transform = 'translateY(6px)';
          titleTimer = setTimeout(() => {
            const currentTitle = slides[currentIndex].getAttribute('data-title') || 'Almamater HorizonU';
            titleEl.textContent = currentTitle;
            titleEl.style.opacity = '1';
            titleEl.style.transform = 'translateY(0)';
          }, 180);
        }
      }

      function startAutoplay() {
        stopAutoplay();
        autoplayTimer = setInterval(() => {
          updateSlide(currentIndex + 1);
        }, AUTOPLAY_INTERVAL);
      }

      function stopAutoplay() {
        if (autoplayTimer) {
          clearInterval(autoplayTimer);
          autoplayTimer = null;
        }
      }

      // Prev & Next Buttons with auto-play timer reset
      prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        updateSlide(currentIndex - 1);
        startAutoplay();
      });

      nextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        updateSlide(currentIndex + 1);
        startAutoplay();
      });

      // Keyboard navigation
      window.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
          updateSlide(currentIndex - 1);
          startAutoplay();
        }
        if (e.key === 'ArrowRight') {
          updateSlide(currentIndex + 1);
          startAutoplay();
        }
      });

      // Pause autoplay on mouse hover, resume on leave
      if (cardContainer) {
        cardContainer.addEventListener('mouseenter', stopAutoplay);
        cardContainer.addEventListener('mouseleave', startAutoplay);

        // Touch swipe support for mobile & tablet
        let touchStartX = 0;
        let touchEndX = 0;
        cardContainer.addEventListener('touchstart', (e) => {
          stopAutoplay();
          touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        cardContainer.addEventListener('touchend', (e) => {
          touchEndX = e.changedTouches[0].screenX;
          if (touchStartX - touchEndX > 45) {
            updateSlide(currentIndex + 1); // swipe left -> next
          } else if (touchEndX - touchStartX > 45) {
            updateSlide(currentIndex - 1); // swipe right -> prev
          }
          startAutoplay();
        }, { passive: true });
      }

      // Pause when tab is hidden, resume when active
      document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
          stopAutoplay();
        } else {
          startAutoplay();
        }
      });

      // Initialize
      updateSlide(0);
      startAutoplay();
    })();

    // 5. Ultra-Smooth Scroll-Driven Timeline with LERP (Linear Interpolation)
    (function initTimelineScrollAnimation() {
      const container = document.getElementById('timelineContainer');
      const progressDesktop = document.getElementById('timelineProgressDesktop');
      const progressMobile = document.getElementById('timelineProgressMobile');
      const baseDesktop = document.getElementById('timelineBaseDesktop');
      const baseMobile = document.getElementById('timelineBaseMobile');
      const nodes = document.querySelectorAll('.timeline-node');
      const steps = document.querySelectorAll('.timeline-step');

      if (!container || (!progressDesktop && !progressMobile) || !nodes.length) return;

      document.documentElement.classList.add('timeline-ready');

      let targetProgress = 0;
      let currentProgress = 0;
      let isRunning = false;

      function setLineDimensions() {
        if (!nodes.length) return;
        const firstNode = nodes[0];
        const lastNode = nodes[nodes.length - 1];
        const containerRect = container.getBoundingClientRect();
        const firstRect = firstNode.getBoundingClientRect();
        const lastRect = lastNode.getBoundingClientRect();

        const topOffset = (firstRect.top - containerRect.top) + (firstRect.height / 2);
        const totalLineHeight = (lastRect.top + lastRect.height / 2) - (firstRect.top + firstRect.height / 2);

        [progressDesktop, progressMobile, baseDesktop, baseMobile].forEach(el => {
          if (el) {
            el.style.top = `${topOffset}px`;
            el.style.height = `${Math.max(0, totalLineHeight)}px`;
            el.style.bottom = 'auto';
          }
        });
      }

      function updateTarget() {
        if (!nodes.length) return;
        const firstNode = nodes[0];
        const lastNode = nodes[nodes.length - 1];
        const firstRect = firstNode.getBoundingClientRect();
        const lastRect = lastNode.getBoundingClientRect();
        const windowHeight = window.innerHeight;

        // Dynamic trigger point around 60% of viewport
        const triggerY = windowHeight * 0.60;
        const startY = firstRect.top + firstRect.height / 2;
        const endY = lastRect.top + lastRect.height / 2;
        const totalDistance = Math.max(1, endY - startY);

        if (triggerY > startY) {
          const scrolled = triggerY - startY;
          targetProgress = Math.min(1, Math.max(0, scrolled / totalDistance));
        } else {
          targetProgress = 0;
        }

        // Activate / Deactivate nodes based on trigger position
        nodes.forEach((node, idx) => {
          const nodeRect = node.getBoundingClientRect();
          const nodeCenterY = nodeRect.top + nodeRect.height / 2;

          if (nodeCenterY <= triggerY + 15) {
            node.classList.add('active');
            if (steps[idx]) steps[idx].classList.add('active');
          } else {
            node.classList.remove('active');
            if (steps[idx]) steps[idx].classList.remove('active');
          }
        });

        if (!isRunning) {
          isRunning = true;
          requestAnimationFrame(smoothRenderLoop);
        }
      }

      function smoothRenderLoop() {
        // Silky smooth dampening factor: 0.12 gives fluid momentum between scroll ticks
        currentProgress += (targetProgress - currentProgress) * 0.12;

        const scaleVal = currentProgress.toFixed(4);
        if (progressDesktop) progressDesktop.style.transform = `scaleY(${scaleVal})`;
        if (progressMobile) progressMobile.style.transform = `scaleY(${scaleVal})`;

        if (Math.abs(targetProgress - currentProgress) > 0.0006) {
          requestAnimationFrame(smoothRenderLoop);
        } else {
          currentProgress = targetProgress;
          if (progressDesktop) progressDesktop.style.transform = `scaleY(${targetProgress})`;
          if (progressMobile) progressMobile.style.transform = `scaleY(${targetProgress})`;
          isRunning = false;
        }
      }

      function onResize() {
        setLineDimensions();
        updateTarget();
      }

      window.addEventListener('scroll', updateTarget, { passive: true });
      window.addEventListener('resize', onResize, { passive: true });
      window.addEventListener('load', onResize, { passive: true });

      // Run on page load
      setLineDimensions();
      updateTarget();
    })();
  </script>
</body>
</html>

