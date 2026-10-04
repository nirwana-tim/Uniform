<x-student-layout title="Status Seragam">
    @php
      $receivedCount = $receivedTransactions->sum(fn($tx) => $tx->items->count());
      $pendingCount = $pendingItems->count();
      $activeTab = request()->input('tab', 'pending');
    @endphp

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

      <h1 class="header-page-title">Status Seragam</h1>
    </header>

    <!-- CONTENT -->
    <main class="app-content">
      <!-- TABS -->
      <div class="status-tabs">
        <button type="button" onclick="switchStatusTab('pending')" id="btnTabPending" class="status-tab-btn {{ $activeTab === 'pending' ? 'active' : '' }}">
          <span>Belum diterima ({{ $pendingCount }})</span>
          <div class="status-tab-indicator"></div>
        </button>
        <button type="button" onclick="switchStatusTab('received')" id="btnTabReceived" class="status-tab-btn {{ $activeTab === 'received' ? 'active' : '' }}">
          <span>Diterima ({{ $receivedCount }})</span>
          <div class="status-tab-indicator"></div>
        </button>
      </div>

      <!-- TAB PENDING CONTENT -->
      <div id="tabPendingContent" class="status-list" style="{{ $activeTab === 'pending' ? '' : 'display: none;' }}">
        @forelse($pendingItems as $item)
          @php
            $itemName = $item->name;
            $isShoes = stripos($itemName, 'sepatu') !== false || ($item->category?->code ?? '') === 'SHO';
          @endphp
          <div class="status-item">
            <div class="status-item-icon">
              @if($isShoes)
                <i class="fa-solid fa-shoe-prints"></i>
              @else
                <i class="fa-solid fa-shirt"></i>
              @endif
            </div>
            <div class="status-item-info">
              <h3 class="status-item-title">{{ $item->name }}</h3>
              <span class="status-item-sub-danger">Keterangan: Perlu diambil</span>
            </div>
          </div>
        @empty
          <!-- EMPTY STATE (status-belum-diterima-empty.html) -->
          <div class="empty-state-wrap">
            <img src="{{ asset('assets/img/ukuran-empty-illustration.webp') }}" alt="Semua Diambil" class="empty-illustration">
            <p class="empty-title">Tidak ada seragam pending</p>
            <p class="empty-desc">Semua paket seragam yang menjadi hak Anda sudah berhasil diambil.</p>
          </div>
        @endforelse
      </div>

      <!-- TAB RECEIVED CONTENT -->
      <div id="tabReceivedContent" class="status-list" style="{{ $activeTab === 'received' ? '' : 'display: none;' }}">
        @php $hasReceived = false; @endphp
        @foreach($receivedTransactions as $tx)
          @foreach($tx->items as $txItem)
            @php
              $hasReceived = true;
              $itemName = $txItem->item?->name ?? 'Item Seragam';
              $isShoes = stripos($itemName, 'sepatu') !== false || ($txItem->item?->category?->code ?? '') === 'SHO';
              $pickupDate = $tx->pickup_time ?: $tx->created_at;
            @endphp
            <div class="status-item">
              <div class="status-item-icon">
                @if($isShoes)
                  <i class="fa-solid fa-shoe-prints"></i>
                @else
                  <i class="fa-solid fa-shirt"></i>
                @endif
              </div>
              <div class="status-item-info">
                <h3 class="status-item-title">{{ $itemName }}</h3>
                <span class="status-item-sub-success">
                  Sudah diambil - {{ \Carbon\Carbon::parse($pickupDate)->translatedFormat('d F Y') }}
                </span>
              </div>
            </div>
          @endforeach
        @endforeach

        @if(!$hasReceived)
          <!-- EMPTY STATE (status-diterima-empty.html) -->
          <div class="empty-state-wrap">
            <img src="{{ asset('assets/img/ukuran-empty-illustration.webp') }}" alt="Belum Diambil" class="empty-illustration">
            <p class="empty-title">Belum ada seragam yang diambil</p>
            <p class="empty-desc">Silakan kunjungi lokasi pembagian sesuai jadwal yang ditentukan untuk mengambil seragam Anda.</p>
          </div>
        @endif
      </div>
    </main>

    @push('scripts')
    <script>
      function switchStatusTab(tab) {
        const btnPending = document.getElementById('btnTabPending');
        const btnReceived = document.getElementById('btnTabReceived');
        const contentPending = document.getElementById('tabPendingContent');
        const contentReceived = document.getElementById('tabReceivedContent');

        if (tab === 'pending') {
          btnPending.classList.add('active');
          btnReceived.classList.remove('active');
          contentPending.style.display = '';
          contentReceived.style.display = 'none';
        } else {
          btnReceived.classList.add('active');
          btnPending.classList.remove('active');
          contentReceived.style.display = '';
          contentPending.style.display = 'none';
        }
      }
    </script>
    @endpush
</x-student-layout>
