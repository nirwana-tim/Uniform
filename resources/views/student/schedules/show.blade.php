<x-student-layout title="Detail Pembagian Seragam" :showNav="false">
    <!-- HEADER -->
    <header class="app-header-back">
      <button onclick="window.location.href='{{ route('dashboard') }}'" class="btn-back" title="Kembali">
        <i class="fa-solid fa-arrow-left"></i>
      </button>
      <h1 class="header-back-title">Detail Pembagian Seragam</h1>
    </header>

    <!-- CONTENT -->
    <main class="app-content">
      <div class="detail-dashboard-grid">
        <!-- LEFT COLUMN: DETAIL & WAKTU -->
        <div class="detail-col-main">
          <!-- JUDUL -->
          <div class="detail-section">
            <p class="detail-label">Judul</p>
            <h2 class="detail-value-lg">{{ $schedule->name }}</h2>
          </div>

          <!-- SERAGAM DI BAGIKAN -->
          <div class="detail-section">
            <p class="detail-label">Lihat Seragam di Bagikan</p>
            <div class="detail-list">
              @forelse($schedule->items as $distItem)
                @php
                  $itemName = $distItem->item?->name ?? 'Item Seragam';
                  $isShoes = stripos($itemName, 'sepatu') !== false || ($distItem->item?->category?->code ?? '') === 'SHO';
                @endphp
                <div class="detail-card-row">
                  <div class="detail-card-icon">
                    @if($isShoes)
                      <i class="fa-solid fa-shoe-prints"></i>
                    @else
                      <i class="fa-solid fa-shirt"></i>
                    @endif
                  </div>
                  <span class="detail-card-title">{{ $itemName }}</span>
                </div>
              @empty
                <div class="detail-card-row">
                  <span class="detail-card-title" style="color: var(--muted);">Daftar paket seragam belum ditentukan.</span>
                </div>
              @endforelse
            </div>
          </div>

          <!-- WAKTU LENGKAP -->
          <div class="detail-section">
            <p class="detail-label">Waktu Lengkap</p>
            <div class="detail-list">
              <div class="detail-card-row">
                <div class="detail-card-icon">
                  <i class="fa-regular fa-clock text-dark-text"></i>
                </div>
                <span class="detail-card-title">{{ $schedule->timeWindowLabel() }} WIB</span>
              </div>

              <div class="detail-card-row">
                <div class="detail-card-icon">
                  <i class="fa-regular fa-calendar text-dark-text"></i>
                </div>
                <span class="detail-card-title">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: LOKASI & LAMPIRAN -->
        <div class="detail-col-side">
          <!-- TEMPAT DETAIL -->
          <div class="detail-section">
            <p class="detail-label">Tempat Detail</p>
            <h3 class="detail-value-lg">{{ $schedule->location ?: 'Faculty Lounge' }}</h3>

            @if(!empty($schedule->attachments))
              @foreach($schedule->attachments as $att)
                <a href="{{ asset($att['url'] ?? $att['path'] ?? '#') }}" target="_blank" class="attachment-card" style="text-decoration: none;">
                  <i class="fa-solid fa-file-pdf attachment-icon"></i>
                  <div class="attachment-info">
                    <span class="attachment-name">{{ $att['name'] ?? 'Lampiran Lokasi.pdf' }}</span>
                    <span class="attachment-meta">{{ $att['size'] ?? 'Dokumen PDF' }}</span>
                  </div>
                </a>
              @endforeach
            @else
              <!-- Default Mock Attachments from Prototype -->
              <div onclick="showAppModal({ title: 'Informasi Lokasi', message: 'Denah lokasi pengambilan: {{ $schedule->location ?: 'Faculty Lounge' }}. Silakan ikuti arahan petugas di tempat.', type: 'info' });" class="attachment-card" style="cursor: pointer;">
                <i class="fa-solid fa-location-dot attachment-icon" style="color: var(--primary);"></i>
                <div class="attachment-info">
                  <span class="attachment-name">Informasi Ruangan</span>
                  <span class="attachment-meta">{{ $schedule->location ?: 'Faculty Lounge' }}</span>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </main>
</x-student-layout>
