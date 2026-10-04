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
    /* Local Fonts: Optima (Branding & Display Headings) */
    @font-face {
      font-family: 'Optima';
      src: url('../fonts/optima/OPTIMA.TTF') format('truetype');
      font-weight: 400;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Optima';
      src: url('../fonts/optima/Optima Medium.ttf') format('truetype');
      font-weight: 500;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Optima';
      src: url('../fonts/optima/OPTIMA_B.TTF') format('truetype');
      font-weight: 700;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Optima';
      src: url('../fonts/optima/Optima_Italic.ttf') format('truetype');
      font-weight: 400;
      font-style: italic;
      font-display: swap;
    }

    /* Local Fonts: Myriad Pro (Body & UI Elements) */
    @font-face {
      font-family: 'Myriad Pro';
      src: url('../fonts/myriad-pro/MyriadPro-Light.otf') format('opentype');
      font-weight: 300;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Myriad Pro';
      src: url('../fonts/myriad-pro/MYRIADPRO-REGULAR.OTF') format('opentype');
      font-weight: 400;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Myriad Pro';
      src: url('../fonts/myriad-pro/MYRIADPRO-SEMIBOLD.OTF') format('opentype');
      font-weight: 600;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Myriad Pro';
      src: url('../fonts/myriad-pro/MYRIADPRO-BOLD.OTF') format('opentype');
      font-weight: 700;
      font-style: normal;
      font-display: swap;
    }
    @font-face {
      font-family: 'Myriad Pro';
      src: url('../fonts/myriad-pro/MYRIADPRO-SEMIBOLDIT.OTF') format('opentype');
      font-weight: 600;
      font-style: italic;
      font-display: swap;
    }
    @font-face {
      font-family: 'Myriad Pro';
      src: url('../fonts/myriad-pro/MYRIADPRO-BOLDIT.OTF') format('opentype');
      font-weight: 700;
      font-style: italic;
      font-display: swap;
    }

    /* Root canvas background: #F0DADC at top (header match), #720411 at bottom (footer match) */
    html {
      background-color: #F0DADC;
      background: linear-gradient(to bottom, #F0DADC 0%, #F0DADC 50%, #720411 50%, #720411 100%) no-repeat fixed;
      overscroll-behavior-y: none;
      overscroll-behavior: none;
    }
    body {
      font-family: 'Myriad Pro', system-ui, -apple-system, sans-serif;
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
      height: 52px;
      padding: 0 1.5rem;
      border-radius: 9999px;
      font-size: 0.9375rem;
      font-weight: 700;
      letter-spacing: 0.01em;
      border: 1.5px solid #D4A83A;
      color: #D4A83A;
      background: transparent;
    }
    @media (max-width: 640px) {
      .badge-capsule {
        height: 44px;
        padding: 0 1.15rem;
        font-size: 0.8125rem;
      }
    }
    /* Photo Card Stack Visual Effect matching Figma SVG */
    .photo-stack-base {
      position: relative;
    }
    .photo-stack-base::before {
      content: '';
      position: absolute;
      top: -16px;
      left: -28px;
      right: 18px;
      bottom: 18px;
      border-radius: 12px;
      background: #D4A83A;
      z-index: 0;
    }
    .photo-stack-img {
      position: relative;
      z-index: 1;
      border-radius: 12px;
      overflow: hidden;
      border: 1.5px solid #980517;
      background: white;
      box-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.12);
    }
    @media (max-width: 640px) {
      .photo-stack-base::before {
        top: -10px;
        left: -16px;
        right: 10px;
        bottom: 10px;
        border-radius: 8.7px;
      }
      .photo-stack-img {
        border-radius: 8.7px;
      }
    }
    /* Accordion transition */
    .faq-answer {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s cubic-bezier(0, 1, 0, 1), padding 0.3s ease;
    }
    .faq-item.active .faq-answer {
      max-height: 350px;
      transition: max-height 0.3s cubic-bezier(1, 0, 1, 0), padding 0.3s ease;
    }
    .faq-item.active .faq-icon {
      transform: rotate(45deg);
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
      transition: opacity 800ms cubic-bezier(0.4, 0, 0.2, 1), transform 800ms cubic-bezier(0.4, 0, 0.2, 1);
      will-change: opacity, transform;
      pointer-events: none;
    }
    .hero-slide.active {
      opacity: 1;
      transform: scale(1);
      pointer-events: auto;
    }
    #carouselUniformTitle {
      transition: opacity 300ms cubic-bezier(0.4, 0, 0.2, 1), transform 300ms cubic-bezier(0.4, 0, 0.2, 1);
      will-change: opacity, transform;
      display: inline-block;
    }
  </style>
</head>
<body class="antialiased overscroll-none">

  <!-- ================= NAVBAR ================= -->
  <header class="sticky top-0 z-50 bg-white border-b border-[#E3BCC0] transition-all duration-200 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
    <div class="w-full px-4 sm:px-9 lg:px-16 h-[65px] lg:h-[73px] flex items-center justify-between max-w-[1440px] mx-auto">
      
      <!-- Brand Logo (UHI Emblem img_10.webp) -->
      <a href="#" class="flex items-center gap-3 group">
        <img src="{{ asset('assets/img') }}/img_10.webp" alt="Logo Universitas Horizon Indonesia" class="w-9 h-9 sm:w-10 sm:h-10 lg:w-[46px] lg:h-[46px] object-contain rounded-full shadow-sm group-hover:scale-105 transition-transform">
        <span class="text-base sm:text-lg lg:text-xl font-bold tracking-tight text-[#980517] font-display">
          HorizonU Uniform
        </span>
      </a>

      <!-- Desktop Navigation Menu (Active at lg: 1024px+ to match Desktop SVG) -->
      <nav class="hidden lg:flex items-center gap-8">
        <a href="#tentang-kami" class="text-sm font-semibold text-[#171717] hover:text-[#980517] transition-colors">Tentang Kami</a>
        <div class="relative group">
          <button class="flex items-center gap-1.5 text-sm font-semibold text-[#171717] group-hover:text-[#980517] transition-colors">
            <span>Akses Akun</span>
            <span class="text-sm font-semibold transition-transform group-hover:rotate-45 leading-none">+</span>
          </button>
          <!-- Dropdown menu -->
          <div class="absolute top-full left-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
            <a href="#akses-maba" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-brand-pinkLight hover:text-brand-red font-medium">Mahasiswa Baru</a>
            <a href="#akses-continuing" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-brand-pinkLight hover:text-brand-red font-medium">Mahasiswa Continuing</a>
          </div>
        </div>
        <a href="#alur-pengambilan" class="text-sm font-semibold text-[#171717] hover:text-[#980517] transition-colors">Cara Pengambilan</a>
        <a href="#faq" class="text-sm font-semibold text-[#171717] hover:text-[#980517] transition-colors">FAQ</a>
      </nav>

      <!-- Desktop Auth CTA -->
      <div class="hidden lg:flex items-center gap-3">
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="inline-flex items-center justify-center px-7 py-2.5 text-sm font-bold text-[#171717] bg-brand-gold hover:bg-brand-goldHover active:scale-95 rounded-full shadow-sm hover:shadow transition-all">
          Login Sekarang
        </a>
      </div>

      <!-- Mobile & Tablet Hamburger Button (Active up to lg: to match Tablet 834px & Mobile 402px SVGs) -->
      <button id="mobileMenuBtn" type="button" class="lg:hidden w-9 h-9 rounded-lg bg-brand-gold flex items-center justify-center text-brand-dark shadow-sm hover:opacity-90 active:scale-95 focus:outline-none" aria-label="Buka Menu">
        <i class="fa-solid fa-bars text-base"></i>
      </button>
    </div>

    <!-- Mobile & Tablet Drawer Menu -->
    <div id="mobileMenu" class="hidden lg:hidden bg-white border-b border-[#E3BCC0] px-6 py-5 space-y-4 shadow-xl">
      <div class="flex flex-col space-y-3 font-medium">
        <a href="#tentang-kami" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold">Tentang Kami</a>
        <a href="#akses-maba" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold">Akses Akun - Mahasiswa Baru</a>
        <a href="#akses-continuing" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold">Akses Akun - Continuing</a>
        <a href="#alur-pengambilan" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold">Cara Pengambilan Seragam</a>
        <a href="#faq" class="mobile-nav-link text-gray-800 hover:text-brand-red py-1.5 font-semibold">FAQ (Pertanyaan Umum)</a>
      </div>
      <div class="pt-3 border-t border-gray-100">
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="w-full flex items-center justify-center gap-2 py-3 text-center text-sm font-bold text-brand-dark bg-brand-gold hover:bg-brand-goldHover rounded-full shadow-sm">
          <span>Login Sekarang</span>
          <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
      </div>
    </div>
  </header>

  <!-- ================= MAIN CONTENT ================= -->
  <main class="w-full bg-white flex-1 relative">

    <!-- ================= HERO SECTION (FULL IMMERSIVE) ================= -->
    <section class="w-full px-3 sm:px-4 lg:px-6 pt-2 sm:pt-3 pb-4 sm:pb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-[8px] lg:gap-[14px] items-stretch min-h-[460px] lg:min-h-[calc(100dvh-95px)] lg:h-[calc(100dvh-95px)]">
        
        <!-- Hero Left Card (Deep Maroon #980517, rx=14px on desktop, rx=8px on mobile/tablet) -->
        <div class="bg-brand-red rounded-[8px] lg:rounded-[14px] p-6 sm:p-10 lg:p-14 text-white flex flex-col justify-between shadow-lg relative overflow-hidden min-h-[460px] lg:min-h-full">
          <!-- Background subtle decorative pattern -->
          <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
          <div class="absolute left-10 top-1/2 -translate-y-1/2 w-48 h-48 bg-brand-gold/10 rounded-full blur-3xl pointer-events-none"></div>
          
          <div class="relative z-10 space-y-4 sm:space-y-6 lg:space-y-8 my-auto">
            <h1 class="text-3xl sm:text-4xl lg:text-[54px] xl:text-[60px] font-bold tracking-tight leading-[1.18] text-white font-display">
              Kelola Pengambilan<br class="hidden sm:inline"> Seragam dengan<br class="hidden sm:inline"> Mudah.
            </h1>

            <p class="text-sm sm:text-base lg:text-[18px] text-white/90 font-normal leading-relaxed max-w-[540px] font-sans">
              Mulai proses pengambilan seragammu dengan mudah. Login untuk melihat informasi seragam, mengetahui status pengambilan, mengakses QR Code, dan mendapatkan panduan lengkap sebelum datang ke lokasi.
            </p>
          </div>

          <!-- CTA Buttons (Pill rounded-full touching circle arrow button) -->
          <div class="relative z-10 pt-6 sm:pt-10 flex items-center">
            <div class="inline-flex items-center gap-0">
              <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="inline-flex items-center justify-center px-7 sm:px-9 h-12 sm:h-[54px] bg-brand-gold hover:bg-brand-goldHover text-[#171717] rounded-full font-bold text-sm sm:text-base shadow-md transition-all active:scale-95">
                <span>{{ auth()->check() ? 'Buka Dashboard' : 'Masuk ke Dashboard' }}</span>
              </a>
              <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="w-12 h-12 sm:w-[54px] sm:h-[54px] rounded-full bg-brand-gold hover:bg-brand-goldHover text-[#171717] flex items-center justify-center shadow-md transition-all active:scale-95 shrink-0" aria-label="Buka Dashboard">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 14 14" fill="none" stroke="#171717" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 2L2 12M2 2h10v10"/>
                </svg>
              </a>
            </div>
          </div>
        </div>

        <!-- Hero Right Card (Soft Pink Card #F0DADC with Uniform Showcase Carousel) -->
        <div id="heroUniformCard" class="bg-[#F0DADC] rounded-[8px] lg:rounded-[14px] relative overflow-hidden min-h-[460px] lg:min-h-full shadow-sm select-none flex flex-col justify-between">
          
          <!-- Top bar: Badge & Carousel arrows (Floating above slides with z-20) -->
          <div class="relative z-20 p-5 sm:p-8 lg:p-10 flex items-center justify-between pointer-events-none">
            <div class="inline-flex items-center gap-2.5 text-[#980517] font-bold text-sm sm:text-base lg:text-lg font-display pointer-events-auto">
              <svg class="w-5 h-5 lg:w-[22px] lg:h-[22px] shrink-0" viewBox="776 153 24 24" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M780.848 165L776 162.149L779.573 155.851L784.424 158.698L784.427 153H791.573L791.576 158.698L796.427 155.851L800 162.149L795.152 165L800 167.851L796.427 174.149L791.576 171.302L791.573 177H784.427L784.424 171.302L779.573 174.149L776 167.851L780.848 165Z" fill="#4A4A4A"/>
              </svg>
              <span id="carouselUniformTitle" class="transition-opacity duration-300">Kemeja PDH HorizonU</span>
            </div>
            <!-- Prev / Next Carousel Buttons -->
            <div class="flex items-center gap-3 sm:gap-4 pointer-events-auto">
              <button id="carouselPrevBtn" type="button" class="w-9 h-9 sm:w-10 sm:h-10 lg:w-[58px] lg:h-[58px] rounded-full bg-white flex items-center justify-center text-[#171717] hover:bg-brand-red hover:text-white active:scale-95 shadow-sm transition-all focus:outline-none" title="Sebelumnya" aria-label="Slide Sebelumnya">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
              </button>
              <button id="carouselNextBtn" type="button" class="w-9 h-9 sm:w-10 sm:h-10 lg:w-[58px] lg:h-[58px] rounded-full bg-white flex items-center justify-center text-[#171717] hover:bg-brand-red hover:text-white active:scale-95 shadow-sm transition-all focus:outline-none" title="Selanjutnya" aria-label="Slide Selanjutnya">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Slides Track / Container (Holding Display 1 to Display 5) -->
          <div class="absolute inset-0 w-full h-full z-10 overflow-hidden pointer-events-none">
            <!-- Slide 1: Display 1.png (Almamater HorizonU) -->
            <div class="hero-slide" data-title="Almamater HorizonU" data-index="0">
              <img src="{{ asset('assets/img/Display 1.webp') }}" alt="Jas Almamater HorizonU" class="w-full h-full object-cover object-bottom pointer-events-none select-none">
            </div>

            <!-- Slide 2: Display 2.png (Kemeja PDH HorizonU) -->
            <div class="hero-slide active" data-title="Kemeja PDH HorizonU" data-index="1">
              <img src="{{ asset('assets/img/Display 2.webp') }}" alt="Kemeja PDH HorizonU" class="w-full h-full object-cover object-bottom pointer-events-none select-none">
            </div>

            <!-- Slide 3: Display 3.png (Kemeja Harian HorizonU) -->
            <div class="hero-slide" data-title="Kemeja Harian HorizonU" data-index="2">
              <img src="{{ asset('assets/img/Display 3.webp') }}" alt="Kemeja Harian HorizonU" class="w-full h-full object-cover object-bottom pointer-events-none select-none">
            </div>

            <!-- Slide 4: Display 4.png (Seragam Medis HorizonU) -->
            <div class="hero-slide" data-title="Seragam Medis HorizonU" data-index="3">
              <img src="{{ asset('assets/img/Display 4.webp') }}" alt="Seragam Medis HorizonU" class="w-full h-full object-cover object-bottom pointer-events-none select-none">
            </div>

            <!-- Slide 5: Display 5.png (Praktikum & Lab HorizonU) -->
            <div class="hero-slide" data-title="Praktikum &amp; Lab HorizonU" data-index="4">
              <img src="{{ asset('assets/img/Display 5.webp') }}" alt="Praktikum &amp; Lab HorizonU" class="w-full h-full object-cover object-bottom pointer-events-none select-none">
            </div>
          </div>

        </div>

      </div>
    </section>

  <!-- ================= TENTANG KAMI SECTION (Matching user uploaded layout exactly) ================= -->
  <section id="tentang-kami" class="w-full px-4 sm:px-[14px] lg:px-[14px] py-4 sm:py-8 max-w-[1440px] mx-auto">
    <div class="relative bg-brand-red rounded-[6.85px] lg:rounded-[12px] overflow-hidden text-white shadow-xl min-h-[460px] sm:min-h-[440px] md:min-h-[360px] lg:h-[400px] flex items-center">
      
      <!-- Full Banner Background Image (image 61.png provides the complete background, ribbon, and maroon canvas) -->
      <img src="{{ asset('assets/img') }}/image 61.webp" alt="Tentang HorizonU Uniform" class="absolute inset-0 w-full h-full object-cover object-left md:object-[35%_center] lg:object-center pointer-events-none select-none z-0">

      <!-- Dark gradient overlay for small mobile screens only to ensure text contrast over ribbon if screen is narrow -->
      <div class="md:hidden absolute inset-0 bg-gradient-to-t from-brand-red via-brand-red/70 to-transparent pointer-events-none z-0"></div>

      <!-- Right Content: Golden Badge, Title, and Description positioned cleanly over the right maroon backdrop -->
      <div class="relative z-10 w-full md:w-[50%] lg:w-[48%] md:ml-auto p-6 sm:p-10 lg:p-14 lg:pl-2 space-y-4 lg:space-y-6">
        <div class="badge-capsule self-start">
          <i class="fa-solid fa-bullseye text-[#D4A83A] text-base"></i>
          <span>Tentang Kami</span>
        </div>

        <h2 class="text-2xl sm:text-3xl lg:text-[40px] font-bold tracking-tight text-white leading-tight font-display">
          Apa Itu HorizonU Uniform?
        </h2>

        <p class="text-xs sm:text-sm lg:text-[16px] text-white/95 leading-relaxed font-normal max-w-[580px] font-sans">
          HorizonU Uniform hadir untuk mempermudah mahasiswa dalam mendapatkan informasi dan melakukan pengambilan seragam. Dengan seluruh kebutuhan tersedia dalam satu platform, mahasiswa dapat mengetahui status pengambilan, mengakses QR Code, dan mengikuti proses distribusi dengan lebih mudah dan teratur.
        </p>
      </div>

    </div>
  </section>

  <!-- ================= AKSES AKUN - MAHASISWA BARU ================= -->
  <section id="akses-maba" class="py-14 sm:py-20 bg-white">
    <div class="w-full px-4 sm:px-9 lg:px-16 max-w-[1440px] mx-auto">
      
      <!-- Section Header -->
      <div class="mb-10 sm:mb-14">
        <div class="badge-capsule mb-4">
          <i class="fa-solid fa-lock text-[#D4A83A] text-base"></i>
          <span>Akses Akun</span>
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-gray-900 font-display">
          Cara Akses Akun HorizonU Uniform
        </h2>
        <p class="text-base sm:text-lg text-gray-500 font-medium mt-1">Untuk Mahasiswa Baru</p>
      </div>

      <!-- 4 Langkah Mahasiswa Baru (1 Kolom di Tablet 834px & Mobile 402px, 2 Kolom di Desktop 1440px) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-[54px]">
        
        <!-- Step 1 -->
        <div class="bg-[#F5F5F5] rounded-[4px] p-5 sm:p-6 lg:p-7 flex items-start gap-4 sm:gap-6 border border-gray-100 hover:border-gray-200 transition-all">
          <div class="w-12 h-12 sm:w-[68px] sm:h-[68px] lg:w-20 lg:h-20 rounded-[2.8px] sm:rounded-[4px] bg-brand-darkerRed text-white flex items-center justify-center font-extrabold text-xl sm:text-2xl lg:text-3xl shrink-0 shadow-sm font-display">
            1
          </div>
          <div class="space-y-1 sm:space-y-2">
            <h3 class="text-lg sm:text-xl font-bold text-[#171717] font-display">Cek Credential Akun</h3>
            <p class="text-xs sm:text-sm text-[#8E8D8D] leading-relaxed">
              Periksa email pribadi Anda untuk menemukan credential akun HorizonU Uniform yang telah dikirimkan.
            </p>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="bg-[#F5F5F5] rounded-[4px] p-5 sm:p-6 lg:p-7 flex items-start gap-4 sm:gap-6 border border-gray-100 hover:border-gray-200 transition-all">
          <div class="w-12 h-12 sm:w-[68px] sm:h-[68px] lg:w-20 lg:h-20 rounded-[2.8px] sm:rounded-[4px] bg-brand-darkerRed text-white flex items-center justify-center font-extrabold text-xl sm:text-2xl lg:text-3xl shrink-0 shadow-sm font-display">
            2
          </div>
          <div class="space-y-1 sm:space-y-2">
            <h3 class="text-lg sm:text-xl font-bold text-[#171717] font-display">Login ke HorizonU Uniform</h3>
            <p class="text-xs sm:text-sm text-[#8E8D8D] leading-relaxed">
              Gunakan username dan password yang tercantum pada credential untuk masuk ke HorizonU Uniform melalui halaman login.
            </p>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="bg-[#F5F5F5] rounded-[4px] p-5 sm:p-6 lg:p-7 flex items-start gap-4 sm:gap-6 border border-gray-100 hover:border-gray-200 transition-all">
          <div class="w-12 h-12 sm:w-[68px] sm:h-[68px] lg:w-20 lg:h-20 rounded-[2.8px] sm:rounded-[4px] bg-brand-darkerRed text-white flex items-center justify-center font-extrabold text-xl sm:text-2xl lg:text-3xl shrink-0 shadow-sm font-display">
            3
          </div>
          <div class="space-y-1 sm:space-y-2">
            <h3 class="text-lg sm:text-xl font-bold text-[#171717] font-display">Lengkapi Data Akun</h3>
            <p class="text-xs sm:text-sm text-[#8E8D8D] leading-relaxed">
              Setelah berhasil login, Anda diwajibkan mengganti password. Jika sudah memiliki email kampus, tambahkan email tersebut ke data profil Anda.
            </p>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="bg-[#F5F5F5] rounded-[4px] p-5 sm:p-6 lg:p-7 flex items-start gap-4 sm:gap-6 border border-gray-100 hover:border-gray-200 transition-all">
          <div class="w-12 h-12 sm:w-[68px] sm:h-[68px] lg:w-20 lg:h-20 rounded-[2.8px] sm:rounded-[4px] bg-brand-darkerRed text-white flex items-center justify-center font-extrabold text-xl sm:text-2xl lg:text-3xl shrink-0 shadow-sm font-display">
            4
          </div>
          <div class="space-y-1 sm:space-y-2">
            <h3 class="text-lg sm:text-xl font-bold text-[#171717] font-display">Isi Ukuran & Tunggu Jadwal</h3>
            <p class="text-xs sm:text-sm text-[#8E8D8D] leading-relaxed">
              Isi ukuran seragam sesuai dengan ukuran yang Anda gunakan. Jadwal pembagian akan diumumkan melalui email pribadi atau email kampus.
            </p>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- ================= AKSES AKUN - MAHASISWA CONTINUING (#350208 Darker Red) ================= -->
  <section id="akses-continuing" class="py-14 sm:py-20 bg-brand-darkerRed text-white">
    <div class="w-full px-4 sm:px-9 lg:px-16 max-w-[1440px] mx-auto">
      
      <!-- Section Header -->
      <div class="mb-10 sm:mb-14">
        <div class="badge-capsule mb-4">
          <i class="fa-solid fa-lock text-[#D4A83A] text-base"></i>
          <span>Akses Akun</span>
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-white font-display">
          Cara Akses Akun HorizonU Uniform
        </h2>
        <p class="text-base sm:text-lg text-brand-pinkLight/80 font-medium mt-1">Untuk Mahasiswa Continuing</p>
      </div>

      <!-- 4 Langkah Mahasiswa Continuing (Cards border #4A4A4A, White Box number with maroon text) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-[54px]">
        
        <!-- Step 1 -->
        <div class="bg-transparent rounded-[3.5px] p-5 sm:p-6 lg:p-7 flex items-start gap-4 sm:gap-6 border border-[#4A4A4A] hover:border-gray-400 transition-all">
          <div class="w-12 h-12 sm:w-[68px] sm:h-[68px] lg:w-20 lg:h-20 rounded-[2.8px] sm:rounded-[4px] bg-white text-brand-darkerRed flex items-center justify-center font-extrabold text-xl sm:text-2xl lg:text-3xl shrink-0 shadow-md font-display">
            1
          </div>
          <div class="space-y-1 sm:space-y-2">
            <h3 class="text-lg sm:text-xl font-bold text-white font-display">Cek Credential Akun</h3>
            <p class="text-xs sm:text-sm text-[#F0DADC] leading-relaxed">
              Periksa email Kampus Anda untuk menemukan credential akun HorizonU Uniform yang telah dikirimkan.
            </p>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="bg-transparent rounded-[3.5px] p-5 sm:p-6 lg:p-7 flex items-start gap-4 sm:gap-6 border border-[#4A4A4A] hover:border-gray-400 transition-all">
          <div class="w-12 h-12 sm:w-[68px] sm:h-[68px] lg:w-20 lg:h-20 rounded-[2.8px] sm:rounded-[4px] bg-white text-brand-darkerRed flex items-center justify-center font-extrabold text-xl sm:text-2xl lg:text-3xl shrink-0 shadow-md font-display">
            2
          </div>
          <div class="space-y-1 sm:space-y-2">
            <h3 class="text-lg sm:text-xl font-bold text-white font-display">Login ke HorizonU Uniform</h3>
            <p class="text-xs sm:text-sm text-[#F0DADC] leading-relaxed">
              Gunakan username dan password yang tercantum pada credential untuk masuk ke HorizonU Uniform melalui halaman login.
            </p>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="bg-transparent rounded-[3.5px] p-5 sm:p-6 lg:p-7 flex items-start gap-4 sm:gap-6 border border-[#4A4A4A] hover:border-gray-400 transition-all">
          <div class="w-12 h-12 sm:w-[68px] sm:h-[68px] lg:w-20 lg:h-20 rounded-[2.8px] sm:rounded-[4px] bg-white text-brand-darkerRed flex items-center justify-center font-extrabold text-xl sm:text-2xl lg:text-3xl shrink-0 shadow-md font-display">
            3
          </div>
          <div class="space-y-1 sm:space-y-2">
            <h3 class="text-lg sm:text-xl font-bold text-white font-display">Lengkapi Data Akun</h3>
            <p class="text-xs sm:text-sm text-[#F0DADC] leading-relaxed">
              Setelah berhasil login, Anda diwajibkan mengganti password untuk menjaga keamanan akun.
            </p>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="bg-transparent rounded-[3.5px] p-5 sm:p-6 lg:p-7 flex items-start gap-4 sm:gap-6 border border-[#4A4A4A] hover:border-gray-400 transition-all">
          <div class="w-12 h-12 sm:w-[68px] sm:h-[68px] lg:w-20 lg:h-20 rounded-[2.8px] sm:rounded-[4px] bg-white text-brand-darkerRed flex items-center justify-center font-extrabold text-xl sm:text-2xl lg:text-3xl shrink-0 shadow-md font-display">
            4
          </div>
          <div class="space-y-1 sm:space-y-2">
            <h3 class="text-lg sm:text-xl font-bold text-white font-display">Isi Ukuran & Tunggu Jadwal</h3>
            <p class="text-xs sm:text-sm text-[#F0DADC] leading-relaxed">
              Isi ukuran seragam sesuai dengan ukuran yang Anda gunakan. Tunggu jadwal pembagian yang akan diumumkan melalui email kampus.
            </p>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!-- ================= CARA PENGAMBILAN SERAGAM (6 LANGKAH TIMELINE) ================= -->
  <section id="alur-pengambilan" class="py-16 sm:py-24 bg-white relative">
    <div class="w-full px-4 sm:px-9 lg:px-16 max-w-[1440px] mx-auto">
      
      <!-- Section Title Centered -->
      <div class="text-center max-w-2xl mx-auto mb-16 sm:mb-20">
        <div class="badge-capsule mb-4">
          <i class="fa-solid fa-circle-info"></i>
          <span>Cara Pengambilan</span>
        </div>
        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900 mb-3 font-display">
          Bagaimana Cara Mengambil Seragam?
        </h2>
        <p class="text-gray-600 text-sm sm:text-base">
          Ikuti langkah berikut untuk memastikan proses pengambilan seragam berjalan lancar.
        </p>
      </div>

      <!-- Timeline Container -->
      <div class="relative w-full">
        
        <!-- Central vertical timeline line for Desktop (center at 50%) -->
        <div class="hidden lg:block absolute left-1/2 top-4 bottom-10 w-[3px] bg-[#980517] -translate-x-1/2 z-0"></div>
        <!-- Left vertical timeline line for Mobile & Tablet (matching Tablet 834px & Mobile 402px SVGs) -->
        <div class="lg:hidden absolute left-5 sm:left-7 top-4 bottom-10 w-[3px] bg-[#980517] -translate-x-1/2 z-0"></div>

        <div class="space-y-12 sm:space-y-16 lg:space-y-20">

          <!-- ================= STEP 1: Desktop Right (x=783), Mobile/Tablet Left-Timeline ================= -->
          <div class="relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Desktop Left: Empty Placeholder -->
            <div class="hidden lg:block lg:col-span-5"></div>
            
            <!-- Timeline Leaf Node (Exact vector marker matching Figma SVG) -->
            <div class="absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Content Card: Right side on Desktop (Col 7-12), Placed after line on Mobile/Tablet -->
            <div class="pl-12 sm:pl-16 lg:pl-4 lg:col-span-6 lg:col-start-7 space-y-3.5 sm:space-y-4">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-gray-900 text-xs font-bold font-sans">
                Step 1
              </div>
              <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 leading-tight font-display">
                Cek Eligible & Jadwal Pengambilan
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                Pastikan Anda telah memenuhi syarat untuk menerima seragam (eligible), termasuk telah menyelesaikan pembayaran uang kuliah yang dipersyaratkan. Setelah itu, pastikan Anda telah menerima pemberitahuan mengenai jadwal dan lokasi pengambilan melalui email pribadi atau email kampus.
              </p>
              <!-- Photo Stack (img_4.webp) -->
              <div class="pt-2">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[400px] photo-stack-base">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_4.webp" alt="Cek Eligible dan Jadwal Pengambilan" class="w-full h-48 sm:h-60 lg:h-[267px] object-cover">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ================= STEP 2: Desktop Left (x=64), Mobile/Tablet Left-Timeline ================= -->
          <div class="relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Content Card: Left side on Desktop (Col 1-6), Placed after line on Mobile/Tablet -->
            <div class="pl-12 sm:pl-16 lg:pl-0 lg:pr-4 lg:col-span-6 space-y-3.5 sm:space-y-4 lg:text-right flex flex-col lg:items-end">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-gray-900 text-xs font-bold font-sans">
                Step 2
              </div>
              <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 leading-tight font-display">
                Dapatkan Kode QR Pengambilan
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                Login kembali ke HorizonU Uniform dan isi ukuran untuk event pembagian seragam yang tersedia. Setelah itu, pastikan Kode QR pengambilan telah Anda terima dan siap ditunjukkan kepada petugas.
              </p>
              <!-- Photo Stack (img_6.webp) -->
              <div class="pt-2 w-full flex lg:justify-end">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[400px] photo-stack-base text-left">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_6.webp" alt="Dapatkan Kode QR Pengambilan" class="w-full h-48 sm:h-60 lg:h-[268px] object-cover">
                  </div>
                </div>
              </div>
            </div>

            <!-- Timeline Leaf Node (Exact vector marker matching Figma SVG) -->
            <div class="absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Desktop Right: Empty Placeholder -->
            <div class="hidden lg:block lg:col-span-5"></div>
          </div>

          <!-- ================= STEP 3: Desktop Right (x=783), Mobile/Tablet Left-Timeline ================= -->
          <div class="relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Desktop Left: Empty Placeholder -->
            <div class="hidden lg:block lg:col-span-5"></div>
            
            <!-- Timeline Leaf Node -->
            <div class="absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Content Card: Right side on Desktop (img_5.webp) -->
            <div class="pl-12 sm:pl-16 lg:pl-4 lg:col-span-6 lg:col-start-7 space-y-3.5 sm:space-y-4">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-gray-900 text-xs font-bold font-sans">
                Step 3
              </div>
              <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 leading-tight font-display">
                Datang Sesuai Jadwal
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                Datang tepat waktu ke lokasi pembagian sesuai dengan jadwal dan tempat yang tercantum dalam pemberitahuan melalui email.
              </p>
              <!-- Photo Stack (img_5.webp) -->
              <div class="pt-2">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[400px] photo-stack-base">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_5.webp" alt="Datang Sesuai Jadwal" class="w-full h-48 sm:h-60 lg:h-[267px] object-cover">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ================= STEP 4: Desktop Left (x=64), Mobile/Tablet Left-Timeline ================= -->
          <div class="relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Content Card: Left side on Desktop (img_8.webp) -->
            <div class="pl-12 sm:pl-16 lg:pl-0 lg:pr-4 lg:col-span-6 space-y-3.5 sm:space-y-4 lg:text-right flex flex-col lg:items-end">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-gray-900 text-xs font-bold font-sans">
                Step 4
              </div>
              <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 leading-tight font-display">
                Isi Kehadiran & Tunjukkan Kode QR
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                Isi data kehadiran pada saat pengambilan, kemudian tunjukkan Kode QR kepada petugas pembagian untuk proses verifikasi.
              </p>
              <!-- Photo Stack (img_8.webp) -->
              <div class="pt-2 w-full flex lg:justify-end">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[400px] photo-stack-base text-left">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_8.webp" alt="Isi Kehadiran dan Tunjukkan QR" class="w-full h-48 sm:h-60 lg:h-[268px] object-cover">
                  </div>
                </div>
              </div>
            </div>

            <!-- Timeline Leaf Node -->
            <div class="absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Desktop Right: Empty Placeholder -->
            <div class="hidden lg:block lg:col-span-5"></div>
          </div>

          <!-- ================= STEP 5: Desktop Right (x=783), Mobile/Tablet Left-Timeline ================= -->
          <div class="relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Desktop Left: Empty Placeholder -->
            <div class="hidden lg:block lg:col-span-5"></div>
            
            <!-- Timeline Leaf Node -->
            <div class="absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Content Card: Right side on Desktop (img_7.webp) -->
            <div class="pl-12 sm:pl-16 lg:pl-4 lg:col-span-6 lg:col-start-7 space-y-3.5 sm:space-y-4">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-gray-900 text-xs font-bold font-sans">
                Step 5
              </div>
              <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 leading-tight font-display">
                Periksa Seragam yang Diterima
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                Pastikan ukuran dan jenis seragam yang Anda terima sudah sesuai dengan data yang tercatat di HorizonU Uniform sebelum meninggalkan lokasi.
              </p>
              <!-- Photo Stack (img_7.webp) -->
              <div class="pt-2">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[400px] photo-stack-base">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_7.webp" alt="Periksa Seragam yang Diterima" class="w-full h-48 sm:h-60 lg:h-[267px] object-cover">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ================= STEP 6: Desktop Left (x=64), Mobile/Tablet Left-Timeline ================= -->
          <div class="relative lg:grid lg:grid-cols-12 lg:gap-8 items-start">
            <!-- Content Card: Left side on Desktop (img_9.webp) -->
            <div class="pl-12 sm:pl-16 lg:pl-0 lg:pr-4 lg:col-span-6 space-y-3.5 sm:space-y-4 lg:text-right flex flex-col lg:items-end">
              <div class="inline-block px-3.5 py-1 rounded-full border border-[#171717] text-gray-900 text-xs font-bold font-sans">
                Step 6
              </div>
              <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 leading-tight font-display">
                Cek Riwayat Pengambilan
              </h3>
              <p class="text-gray-600 text-xs sm:text-sm lg:text-base leading-relaxed">
                Setelah proses selesai, Anda dapat melihat data dan riwayat seragam yang telah diambil melalui HorizonU Uniform.
              </p>
              <!-- Photo Stack (img_9.webp) -->
              <div class="pt-2 w-full flex lg:justify-end">
                <div class="relative w-full max-w-[340px] sm:max-w-sm lg:max-w-[400px] photo-stack-base text-left">
                  <div class="photo-stack-img bg-white shadow-xl">
                    <img src="{{ asset('assets/img') }}/img_9.webp" alt="Cek Riwayat Pengambilan" class="w-full h-48 sm:h-60 lg:h-[267px] object-cover">
                  </div>
                </div>
              </div>
            </div>

            <!-- Timeline Leaf Node -->
            <div class="absolute left-5 sm:left-7 lg:left-1/2 -translate-x-1/2 top-1 z-20 pointer-events-none flex items-center justify-center">
              <svg width="46" height="61" viewBox="0 0 46 61" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-8 sm:w-8 sm:h-10 lg:w-[46px] lg:h-[61px] drop-shadow-sm">
                <path d="M0.029 28.5C-0.963 44.13 23.5 61 23.5 61C23.5 61 46.938 43.91 45.971 28.5C45.083 14.35 23.5 0 23.5 0C23.5 0 0.944 14.1 0.029 28.5Z" fill="#980517"/>
              </svg>
            </div>

            <!-- Desktop Right: Empty Placeholder -->
            <div class="hidden lg:block lg:col-span-5"></div>
          </div>

        </div>

      </div>

    </div>
  </section>

  <!-- ================= FAQ SECTION (#F5E6E8 Border Matching SVG) ================= -->
  <section id="faq" class="py-16 sm:py-20 bg-white">
    <div class="w-full px-4 sm:px-9 lg:px-[100px] max-w-[1440px] mx-auto">
      
      <!-- Section Title Centered -->
      <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
        <div class="badge-capsule mb-4">
          <span class="w-5 h-5 rounded-full bg-[#D4A83A] text-white flex items-center justify-center text-xs font-bold shrink-0">?</span>
          <span>FAQ</span>
        </div>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-gray-900 mb-3 font-display">
          Pertanyaan yang Sering Diajukan
        </h2>
        <p class="text-gray-600 text-xs sm:text-sm lg:text-base">
          Temukan informasi yang Anda butuhkan mengenai akses akun, persyaratan, jadwal pembagian, Kode QR, dan proses pengambilan seragam.
        </p>
      </div>

      <!-- 6 Accordion Items (1 Kolom di Tablet 834px w=[753.5px] & Mobile 402px, 2 Kolom di Desktop 1440px) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-3.5 sm:gap-4 lg:gap-[41.5px]">
        
        <!-- Q1 -->
        <div class="faq-item bg-white rounded-[7.2px] border border-[#F5E6E8] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
          <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-bold text-gray-900 text-sm sm:text-base lg:text-lg focus:outline-none">
            <span>Bagaimana cara mendapatkan akun HorizonU Uniform?</span>
            <span class="faq-icon text-2xl font-light text-gray-900 transition-transform duration-200 shrink-0 select-none">+</span>
          </button>
          <div class="faq-answer text-xs sm:text-sm text-gray-600 leading-relaxed pt-3">
            Akun mahasiswa baru diterbitkan secara otomatis dan credential login akan dikirimkan ke email pribadi yang terdaftar saat pendaftaran kampus. Untuk mahasiswa continuing, credential dikirim ke email kampus aktif.
          </div>
        </div>

        <!-- Q2 -->
        <div class="faq-item bg-white rounded-[7.2px] border border-[#F5E6E8] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
          <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-bold text-gray-900 text-sm sm:text-base lg:text-lg focus:outline-none">
            <span>Kapan saya bisa mengisi ukuran seragam?</span>
            <span class="faq-icon text-2xl font-light text-gray-900 transition-transform duration-200 shrink-0 select-none">+</span>
          </button>
          <div class="faq-answer text-xs sm:text-sm text-gray-600 leading-relaxed pt-3">
            Pengisian ukuran seragam dapat dilakukan segera setelah Anda berhasil login ke portal selama periode pengisian ukuran seragam masih dibuka oleh pihak universitas.
          </div>
        </div>

        <!-- Q3 -->
        <div class="faq-item bg-white rounded-[7.2px] border border-[#F5E6E8] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
          <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-bold text-gray-900 text-sm sm:text-base lg:text-lg focus:outline-none">
            <span>Apa yang harus dilakukan jika mengalami kendala dengan akun atau proses pengambilan?</span>
            <span class="faq-icon text-2xl font-light text-gray-900 transition-transform duration-200 shrink-0 select-none">+</span>
          </button>
          <div class="faq-answer text-xs sm:text-sm text-gray-600 leading-relaxed pt-3">
            Jika terjadi kendala saat login atau proses pengambilan, silakan hubungi bagian Finance atau kunjungi loket One Stop Service di kampus pada jam operasional kerja.
          </div>
        </div>

        <!-- Q4 -->
        <div class="faq-item bg-white rounded-[7.2px] border border-[#F5E6E8] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
          <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-bold text-gray-900 text-sm sm:text-base lg:text-lg focus:outline-none">
            <span>Bagaimana saya mengetahui jadwal dan lokasi pengambilan seragam?</span>
            <span class="faq-icon text-2xl font-light text-gray-900 transition-transform duration-200 shrink-0 select-none">+</span>
          </button>
          <div class="faq-answer text-xs sm:text-sm text-gray-600 leading-relaxed pt-3">
            Informasi tanggal, jam sesi, dan lokasi fisik pembagian seragam akan tertera langsung di dashboard beranda akun Anda dan disiarkan melalui email resmi universitas.
          </div>
        </div>

        <!-- Q5 -->
        <div class="faq-item bg-white rounded-[7.2px] border border-[#F5E6E8] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
          <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-bold text-gray-900 text-sm sm:text-base lg:text-lg focus:outline-none">
            <span>Di mana saya mendapatkan Kode QR untuk mengambil seragam?</span>
            <span class="faq-icon text-2xl font-light text-gray-900 transition-transform duration-200 shrink-0 select-none">+</span>
          </button>
          <div class="faq-answer text-xs sm:text-sm text-gray-600 leading-relaxed pt-3">
            Kode QR dapat diakses melalui menu "Kode QR" di dalam dashboard portal HorizonU Uniform setelah Anda berstatus eligible dan telah mengonfirmasi data ukuran seragam.
          </div>
        </div>

        <!-- Q6 -->
        <div class="faq-item bg-white rounded-[7.2px] border border-[#F5E6E8] p-5 sm:p-6 transition-all hover:border-[#D4A83A]/60 shadow-sm">
          <button type="button" class="faq-toggle w-full flex items-center justify-between text-left gap-4 font-bold text-gray-900 text-sm sm:text-base lg:text-lg focus:outline-none">
            <span>Saya sudah memiliki email kampus, apakah harus menambahkannya ke profil?</span>
            <span class="faq-icon text-2xl font-light text-gray-900 transition-transform duration-200 shrink-0 select-none">+</span>
          </button>
          <div class="faq-answer text-xs sm:text-sm text-gray-600 leading-relaxed pt-3">
            Sangat disarankan! Menambahkan email kampus resmi memastikan seluruh bukti transaksi, tiket QR, dan notifikasi jadwal penting terarsip dengan aman di akun civitas Anda.
          </div>
        </div>

      </div>

    </div>
  </section>

  </main>

  <!-- ================= FOOTER ================= -->
  <footer class="relative bg-brand-red text-white pt-16 pb-12">
    <div class="w-full px-4 sm:px-9 lg:px-16 max-w-[1440px] mx-auto space-y-12">
      
      <!-- Brand & Navigation in Footer -->
      <div class="flex flex-col items-center justify-center text-center space-y-5">
        <!-- Logo Universitas Horizon Indonesia (img_10.webp 56px desktop, 48px tablet, 42px mobile) -->
        <img src="{{ asset('assets/img') }}/img_10.webp" alt="Universitas Horizon Indonesia" class="w-11 h-11 sm:w-12 sm:h-12 lg:w-14 lg:h-14 object-contain rounded-full shadow-lg">
        
        <div class="space-y-3">
          <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-white font-display">HorizonU Uniform</h3>
          <div class="w-28 sm:w-48 lg:w-[280px] h-[1.5px] bg-white mx-auto"></div>
        </div>

        <nav class="flex flex-wrap justify-center gap-6 sm:gap-10 text-xs sm:text-sm font-semibold text-white/90 pt-1">
          <a href="#tentang-kami" class="hover:text-brand-gold transition-colors">Tentang Kami</a>
          <a href="#akses-maba" class="hover:text-brand-gold transition-colors">Akses Akun</a>
          <a href="#alur-pengambilan" class="hover:text-brand-gold transition-colors">Cara Pengambilan</a>
          <a href="#faq" class="hover:text-brand-gold transition-colors">FAQ</a>
        </nav>
      </div>

      <!-- Bottom Bar: Support & Copyright -->
      <div class="pt-4 flex flex-col sm:flex-row items-center justify-between text-xs text-white/90 gap-4">
        <p class="text-center sm:text-left">
          Hubungi Finance atau kunjungi One Stop Service untuk bantuan.
        </p>
        <div class="text-center sm:text-right leading-tight">
          <p>Copyright &copy; 2026 All Rights Reserved</p>
          <p>HorizonU Uniform</p>
        </div>
      </div>

    </div>
  </footer>

  <!-- Interactive JavaScript -->
  <script>
    // 1. Mobile & Tablet Menu Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenuBtn && mobileMenu) {
      mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });
      // Close menu on click link
      document.querySelectorAll('.mobile-nav-link').forEach(link => {
        link.addEventListener('click', () => {
          mobileMenu.classList.add('hidden');
        });
      });
    }

    // 2. FAQ Accordion Toggle
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

    // 3. Hero Uniform Showcase Carousel (Display 1 to Display 5) - Smooth & Autoplay
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

        // Smooth Title Animation with subtle vertical float
        if (titleEl) {
          titleEl.style.opacity = '0';
          titleEl.style.transform = 'translateY(5px)';
          setTimeout(() => {
            const currentTitle = slides[currentIndex].getAttribute('data-title') || 'Almamater HorizonU';
            titleEl.textContent = currentTitle;
            titleEl.style.opacity = '1';
            titleEl.style.transform = 'translateY(0)';
          }, 200);
        }

        // Button hover states
        prevBtn.className = "w-9 h-9 sm:w-10 sm:h-10 lg:w-[58px] lg:h-[58px] rounded-full bg-white flex items-center justify-center text-[#171717] hover:bg-brand-red hover:text-white active:scale-95 shadow-sm transition-all focus:outline-none";
        nextBtn.className = "w-9 h-9 sm:w-10 sm:h-10 lg:w-[58px] lg:h-[58px] rounded-full bg-white flex items-center justify-center text-[#171717] hover:bg-brand-red hover:text-white active:scale-95 shadow-sm transition-all focus:outline-none";
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
  </script>
</body>
</html>

