<x-student-layout title="Home">
    <!-- HEADER -->
    <header class="app-header app-header-home">
      <div class="header-brand">
        <img src="{{ asset('assets/img/logo.webp') }}" alt="HorizonU" class="header-logo">
        <span class="header-brand-title">HorizonU Uniform</span>
      </div>

      <div class="header-divider"></div>

      <div class="header-user-group">
        <div class="header-greeting-wrap">
          <h1 class="header-greeting-title">
            Hi, {{ $student->name }}
          </h1>
          <div class="header-greeting-sub" x-data="{
              hidden: localStorage.getItem('hideNim') === 'true',
              rawNim: '{{ $student->nim }}',
              toggle() {
                  this.hidden = !this.hidden;
                  localStorage.setItem('hideNim', this.hidden ? 'true' : 'false');
              }
          }">
            <span>{{ $student->studyProgram?->name ?? 'Mahasiswa' }}-<span x-text="hidden ? '***' : rawNim" :class="hidden ? 'nim-masked' : 'nim-visible'">{{ $student->nim }}</span></span>
            <button type="button" @click="toggle()" class="nim-toggle-btn" :title="hidden ? 'Tampilkan NIM' : 'Sembunyikan NIM'">
              <i :class="hidden ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
            </button>
          </div>
        </div>
        <a href="{{ route('profile.edit') }}" class="header-profile-btn" title="Profil Akun">
          <i class="fa-solid fa-user"></i>
        </a>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="app-content">
      @if($activeEvent || $activeSchedule)
        <!-- SECTION TITLE -->
        <div class="section-header">
          <h2 class="section-title">Jadwal Ukuran &amp; Pembagian</h2>
          <span class="section-badge">Terbaru</span>
        </div>

        <!-- CARDS GRID -->
        <div class="cards-grid">
          @if($activeEvent)
            <!-- CARD 1: EVENT UKURAN -->
            <div class="card">
              <div class="card-header-bar">
                <span>Periode:</span>
                <span>{{ $activeEvent->start_date->format('d M') }} - {{ $activeEvent->end_date->format('d M Y') }}</span>
              </div>
              <div class="card-body">
                <p class="card-category">Edit Ukuran Seragam</p>
                <p class="card-headline">{{ $activeEvent->title ?: 'Segera edit ukuran sebelum masa periode habis!' }}</p>
                <div class="card-footer">
                  <span class="card-info-item">Selesai <b>{{ $completedSizeCount }}/{{ $totalRequiredSizes ?? 2 }}</b></span>
                  @if($canEdit)
                    <a href="{{ route('student.sizes.input', $activeEvent->id) }}" class="btn-cta">
                      Edit Ukuran
                    </a>
                  @else
                    <a href="{{ route('student.sizes.index') }}" class="btn-cta btn-cta-secondary">
                      Lihat Ukuran
                    </a>
                  @endif
                </div>
              </div>
            </div>
          @endif

          @if($activeSchedule)
            <!-- CARD 2: JADWAL PENGAMBILAN (DISTRIBUSI) -->
            <div class="card">
              <div class="card-header-bar">
                <span>Tanggal:</span>
                <span>{{ \Carbon\Carbon::parse($activeSchedule->date)->format('d F Y') }}</span>
              </div>
              <div class="card-body">
                <p class="card-category">Jadwal Distribusi Seragam</p>
                <p class="card-headline">{{ $activeSchedule->name }}</p>
                <div class="card-footer">
                  <span class="card-info-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ $activeSchedule->location ?: 'Faculty Lounge' }}</span>
                  </span>
                  <a href="{{ route('student.schedules.detail', $activeSchedule->id) }}" class="btn-outline-cta">
                    Lihat Detail
                  </a>
                </div>
              </div>
            </div>
          @endif
        </div>
      @else
        <!-- EMPTY STATE -->
        <div class="empty-state-wrap">
          <img src="{{ asset('assets/img/ukuran-empty-illustration.webp') }}" alt="Belum Ada Jadwal" class="empty-illustration">
          <p class="empty-title">Belum Ada Jadwal Tersedia</p>
          <p class="empty-desc">Belum ada jadwal distribusi maupun pemilihan ukuran seragam yang aktif untuk Anda saat ini.</p>
        </div>
      @endif
    </main>
</x-student-layout>
