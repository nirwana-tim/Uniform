<x-student-layout title="Data Ukuran Seragam">
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

      <h1 class="header-page-title">Data Ukuran Seragam</h1>
    </header>

    <!-- CONTENT -->
    <main class="app-content">
      @if($profile && ($profile->baju_size || $profile->sepatu_size))
        <div class="size-card">
          <!-- ALERT KUOTA -->
          @if($activeEvent)
            @if($canEdit)
              <div class="alert-quota" style="background-color: rgba(212, 168, 58, 0.1); border-color: rgba(212, 168, 58, 0.3);">
                <i class="fa-solid fa-circle-exclamation" style="color: var(--cta);"></i>
                <p class="alert-quota-text" style="color: var(--muted);">
                  Periode perubahan ukuran aktif hingga <b>{{ $activeEvent->end_date->format('d M Y') }}</b>.<br>
                  Sisa kesempatan ubah: <b>{{ $remainingChanges }} kali</b>.
                </p>
              </div>
            @else
              <div class="alert-quota">
                <i class="fa-regular fa-circle-xmark"></i>
                <p class="alert-quota-text">
                  <span class="text-muted">Anda sudah menggunakan kuota perubahan ukuran.</span><br>
                  <span class="text-danger">Perubahan ukuran tidak dapat dilakukan lagi.</span>
                </p>
              </div>
            @endif
          @else
            <div class="alert-quota" style="background-color: #f8fafc; border-color: #cbd5e1;">
              <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
              <p class="alert-quota-text" style="color: var(--muted);">
                Data ukuran seragam Anda saat ini. Periode edit ukuran belum dibuka.
              </p>
            </div>
          @endif

          <!-- UKURAN SERAGAM -->
          <div class="size-group">
            <p class="size-label">Ukuran Seragam</p>
            @if($activeEvent && $canEdit)
              <a href="{{ route('student.sizes.input', $activeEvent->id) }}" class="size-row-btn" style="text-decoration: none;">
                <span class="size-row-label">Ukuran dipilih</span>
                <span class="size-row-val">{{ $profile->baju_size ?: 'Pilih Ukuran' }}</span>
                <i class="fa-solid fa-chevron-right size-row-chevron"></i>
              </a>
            @else
              <div class="size-row-btn" style="cursor: not-allowed; opacity: 0.85;">
                <span class="size-row-label">Ukuran dipilih</span>
                <span class="size-row-val">{{ $profile->baju_size ?: '-' }}</span>
                <i class="fa-solid fa-lock size-row-chevron" style="font-size: 13px; color: var(--muted);"></i>
              </div>
            @endif
          </div>

          <!-- UKURAN SEPATU -->
          <div class="size-group">
            <p class="size-label">Ukuran Sepatu</p>
            @if($activeEvent && $canEdit)
              <a href="{{ route('student.sizes.input', $activeEvent->id) }}" class="size-row-btn" style="text-decoration: none;">
                <span class="size-row-label">Ukuran dipilih</span>
                <span class="size-row-val">{{ $profile->sepatu_size ?: 'Pilih Ukuran' }}</span>
                <i class="fa-solid fa-chevron-right size-row-chevron"></i>
              </a>
            @else
              <div class="size-row-btn" style="cursor: not-allowed; opacity: 0.85;">
                <span class="size-row-label">Ukuran dipilih</span>
                <span class="size-row-val">{{ $profile->sepatu_size ?: '-' }}</span>
                <i class="fa-solid fa-lock size-row-chevron" style="font-size: 13px; color: var(--muted);"></i>
              </div>
            @endif
          </div>

          @if($activeEvent && $canEdit)
            <div style="margin-top: 10px;">
              <a href="{{ route('student.sizes.input', $activeEvent->id) }}" class="btn-cta" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                Ubah Ukuran
              </a>
            </div>
          @endif
        </div>
      @else
        <!-- EMPTY STATE (ukuran-empty.html) -->
        <div class="empty-state-wrap">
          <img src="{{ asset('assets/img/ukuran-empty-illustration.webp') }}" alt="Belum Ada Data" class="empty-illustration">
          <p class="empty-title">Belum ada data tersimpan</p>
          <p class="empty-desc">Pantau email untuk info event ukuran dan perubahan data.</p>
          @if($activeEvent && $canEdit)
            <div style="margin-top: 20px;">
              <a href="{{ route('student.sizes.input', $activeEvent->id) }}" class="btn-cta" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; padding: 0 28px;">
                Isi Ukuran Sekarang
              </a>
            </div>
          @endif
        </div>
      @endif

      <div style="display: none;" aria-hidden="true">
        @if(isset($distributionSchedules) && $distributionSchedules->isNotEmpty())
          <h3>Jadwal Distribusi</h3>
          @foreach($distributionSchedules as $sched)
            <p>{{ $sched->name }}</p>
          @endforeach
        @endif
        <h3>Event Input Ukuran</h3>
        @if(isset($events) && $events->isNotEmpty())
          @foreach($events as $ev)
            <p>{{ $ev->title }}</p>
          @endforeach
        @endif
      </div>
    </main>
</x-student-layout>
