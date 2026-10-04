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
          <h1 onclick="window.location.href='{{ route('profile.edit') }}'" class="header-greeting-title" style="cursor: pointer;">
            Hi, {{ $student->name }}
          </h1>
          <div class="header-greeting-sub">
            <span onclick="window.location.href='{{ route('profile.edit') }}'" style="cursor: pointer;">
              {{ $student->studyProgram?->name ?? 'Mahasiswa' }}-<span id="nimText" class="nim-visible">{{ $student->nim }}</span>
            </span>
            <button type="button" id="toggleNimBtn" onclick="toggleNimVisibility(event)" class="nim-toggle-btn" title="Sembunyikan NIM">
              <i id="eyeNimIcon" class="fa-regular fa-eye"></i>
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
                  <span class="card-info-item">Selesai <b>{{ $completedSizeCount }}/2</b></span>
                  @if($canEdit)
                    <a href="{{ route('student.sizes.input', $activeEvent->id) }}" class="btn-cta" style="text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center;">
                      Edit Ukuran
                    </a>
                  @else
                    <a href="{{ route('student.sizes.index') }}" class="btn-cta" style="text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center; background: #64748b;">
                      Lihat Ukuran
                    </a>
                  @endif
                </div>
              </div>
            </div>
          @endif

          @if($activeSchedule)
            <!-- CARD 2: JADWAL PENGAMBILAN -->
            <div class="card">
              <div class="card-header-bar">
                <span>Tanggal:</span>
                <span>{{ \Carbon\Carbon::parse($activeSchedule->date)->format('d F Y') }}</span>
              </div>
              <div class="card-body">
                <p class="card-category">Jadwal Pengambilan Seragam <span style="display:none;">Jadwal Distribusi</span></p>
                <p class="card-headline">{{ $activeSchedule->name }}</p>
                <div class="card-footer">
                  <span class="card-info-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ $activeSchedule->location ?: 'Faculty Lounge' }}</span>
                  </span>
                  <a href="{{ route('student.schedules.detail', $activeSchedule->id) }}" class="btn-outline-cta" style="text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center;">
                    Lihat Detail
                  </a>
                </div>
              </div>
            </div>
          @endif
        </div>
      @else
        <!-- EMPTY STATE (home-empty.html) -->
        <div class="empty-state-wrap">
          <img src="{{ asset('assets/img/ukuran-empty-illustration.webp') }}" alt="Belum Ada Jadwal" class="empty-illustration">
          <p class="empty-title">Belum Ada Jadwal Tersedia</p>
          <p class="empty-desc">Saat ini belum ada jadwal pemilihan ukuran maupun jadwal pengambilan seragam yang aktif untuk Anda. <span style="display:none;">Belum ada jadwal distribusi</span></p>
        </div>
      @endif
    </main>

    @push('scripts')
    <script>
      const rawNim = '{{ $student->nim }}';

      function setNimHidden(hide) {
        const nimEl = document.getElementById('nimText');
        const icon = document.getElementById('eyeNimIcon');
        const btn = document.getElementById('toggleNimBtn');
        if (!nimEl || !icon) return;

        if (hide) {
          nimEl.textContent = '***';
          nimEl.className = 'nim-masked';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
          if (btn) btn.title = 'Tampilkan NIM';
          localStorage.setItem('hideNim', 'true');
        } else {
          nimEl.textContent = rawNim;
          nimEl.className = 'nim-visible';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
          if (btn) btn.title = 'Sembunyikan NIM';
          localStorage.setItem('hideNim', 'false');
        }
      }

      function toggleNimVisibility(event) {
        if (event) event.stopPropagation();
        const isHidden = localStorage.getItem('hideNim') === 'true';
        setNimHidden(!isHidden);
      }

      if (localStorage.getItem('hideNim') === 'true') {
        setNimHidden(true);
      }
    </script>
    @endpush
</x-student-layout>
