<x-student-layout title="Akses Kode QR">
    <!-- HEADER -->
    <header class="app-header">
      <div class="header-top">
        <div class="header-brand">
          <img src="{{ asset('assets/img/logo.webp') }}" alt="HorizonU" class="header-logo">
          <span class="header-brand-title">HorizonU Uniform</span>
        </div>
        <a href="{{ route('profile.edit') }}" class="header-profile-btn" title="Profil Akun">
          <i class="fa-solid fa-user"></i>
        </a>
      </div>

      <div class="header-divider"></div>

      <h1 class="header-page-title">Akses Kode QR</h1>
    </header>

    <!-- CONTENT -->
    <main class="app-content">
      @if(!$isEligible)
        <!-- NOT ELIGIBLE STATE (kode-qr-not-eligible.html) -->
        <div class="qr-card" style="position: relative; overflow: hidden;">
          <!-- Strike Line (Not Eligible) -->
          <div class="qr-strike-line"></div>

          <h2 class="qr-user-name">{{ $student->name }}</h2>
          <p class="qr-user-nim">{{ $student->nim }}</p>

          <div class="qr-box-wrap" style="opacity: 0.5;">
            <div class="qr-corner tl" style="border-color: var(--border-pink);"></div>
            <div class="qr-corner tr" style="border-color: var(--border-pink);"></div>
            <div class="qr-corner bl" style="border-color: var(--border-pink);"></div>
            <div class="qr-corner br" style="border-color: var(--border-pink);"></div>

            <div class="qr-inner-white">
              <div style="width: 140px; height: 140px; background-color: #E5E7EB; border-radius: 8px;"></div>
            </div>
          </div>

          <button disabled class="btn-download-qr disabled" style="opacity: 0.5; cursor: not-allowed;">
            <i class="fa-solid fa-download"></i>
            <span>Download Kode QR</span>
          </button>

          <p class="qr-instruction" style="color: var(--danger); font-weight: 500;">
            Anda belum eligible untuk mengambil seragam saat ini. Silakan hubungi bagian keuangan/administrasi kampus.
          </p>
        </div>
      @elseif($activeSchedules->isEmpty() && !$activeEvent)
        <!-- EMPTY STATE (kode-qr-empty.html) -->
        <div class="empty-state-wrap">
          <img src="{{ asset('assets/img/ukuran-empty-illustration.webp') }}" alt="Belum Ada Jadwal" class="empty-illustration">
          <p class="empty-title">Belum Ada Jadwal Pengambilan</p>
          <p class="empty-desc">Saat ini belum ada jadwal pengambilan seragam yang aktif untuk Anda.</p>
        </div>
      @else
        <!-- ACTIVE QR (kode-qr.html) -->
        <div class="desktop-split-grid">
          <!-- QR CARD -->
          <div class="qr-card">
            <h2 class="qr-user-name">{{ $student->name }}</h2>
            <p class="qr-user-nim">{{ $student->nim }}</p>

            <div class="qr-box-wrap">
              <div class="qr-corner tl"></div>
              <div class="qr-corner tr"></div>
              <div class="qr-corner bl"></div>
              <div class="qr-corner br"></div>

              <div class="qr-inner-white">
                <img src="{{ $qrDataUrl }}" class="qr-image" alt="QR Code {{ $student->nim }}">
              </div>
            </div>

            <a href="{{ $qrDataUrl }}" download="qr-{{ $student->nim }}.png" class="btn-download-qr" style="text-decoration: none;">
              <i class="fa-solid fa-download"></i>
              <span>Download Kode QR</span>
            </a>

            <p class="qr-instruction">
              Tunjukkan QR ini pada petugas di tempat pada saat pengambilan seragam, untuk verifikasi identitas Anda.
            </p>
          </div>

          <!-- RIGHT SIDE: JADWAL & STATUS -->
          <div class="desktop-split-side">
            <!-- SECTION TITLE -->
            <div class="section-header">
              <h2 class="section-title">Jadwal Ukuran &amp; Pembagian</h2>
              <span class="section-badge">Terbaru</span>
            </div>

            <!-- CARDS GRID -->
            <div class="cards-grid">
              @if($activeEvent)
                <!-- CARD 1 -->
                <div class="card">
                  <div class="card-header-bar">
                    <span>Periode:</span>
                    <span>{{ $activeEvent->start_date->format('d M') }} - {{ $activeEvent->end_date->format('d M Y') }}</span>
                  </div>
                  <div class="card-body">
                    <p class="card-category">Edit Ukuran Seragam</p>
                    <p class="card-headline">{{ $activeEvent->title ?: 'Segera edit ukuran sebelum masa periode habis!' }}</p>
                    <div class="card-footer">
                      <span class="card-info-item">Selesai <b>{{ $completedSizeCount }}/2</b></span>
                      <a href="{{ route('student.sizes.index') }}" class="btn-cta" style="text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center;">
                        Lihat Ukuran
                      </a>
                    </div>
                  </div>
                </div>
              @endif

              @foreach($activeSchedules as $schedule)
                <!-- SCHEDULE CARD -->
                <div class="card">
                  <div class="card-header-bar">
                    <span>Tanggal:</span>
                    <span>{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</span>
                  </div>
                  <div class="card-body">
                    <p class="card-category">Jadwal Pengambilan Seragam</p>
                    <p class="card-headline">{{ $schedule->name }}</p>
                    <div class="card-footer">
                      <span class="card-info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>{{ $schedule->location ?: 'Faculty Lounge' }}</span>
                      </span>
                      <a href="{{ route('student.schedules.detail', $schedule->id) }}" class="btn-outline-cta" style="text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center;">
                        Lihat Detail
                      </a>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif
    </main>
</x-student-layout>
