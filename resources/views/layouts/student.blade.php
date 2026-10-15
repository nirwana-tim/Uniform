@props(['title' => 'HorizonU Uniform', 'showNav' => true])
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title }} - HorizonU Uniform</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  @vite(['resources/css/app.css', 'resources/css/student.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body>
  <div id="screen" class="app-container">
    {{ $slot }}

    @if($showNav)
      <!-- BOTTOM NAV -->
      <nav class="app-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          @if(request()->routeIs('dashboard'))
            <div class="nav-indicator"></div>
          @endif
          <i class="fa-solid fa-house"></i>
          <span>Home</span>
        </a>
        <a href="{{ route('student.sizes.index') }}" class="nav-item {{ request()->routeIs('student.sizes.*') ? 'active' : '' }}">
          @if(request()->routeIs('student.sizes.*'))
            <div class="nav-indicator"></div>
          @endif
          <i class="fa-solid fa-ruler-horizontal"></i>
          <span>Ukuran</span>
        </a>
        <a href="{{ route('student.qr') }}" class="nav-item {{ request()->routeIs('student.qr') ? 'active' : '' }}">
          @if(request()->routeIs('student.qr'))
            <div class="nav-indicator"></div>
          @endif
          <i class="fa-solid fa-qrcode"></i>
          <span>Kode QR</span>
        </a>
        <a href="{{ route('student.items.index') }}" class="nav-item {{ request()->routeIs('student.items.*') ? 'active' : '' }}">
          @if(request()->routeIs('student.items.*'))
            <div class="nav-indicator"></div>
          @endif
          <i class="fa-solid fa-circle-info"></i>
          <span>Status</span>
        </a>
      </nav>
    @endif
  </div>

  @if(session('success') || session('error') || session('info'))
    <div id="flash-message-data"
         data-type="{{ session('success') ? 'success' : (session('error') ? 'warning' : 'info') }}"
         data-title="{{ session('success') ? 'Berhasil' : (session('error') ? 'Perhatian' : 'Informasi') }}"
         data-message="{{ session('success') ?? session('error') ?? session('info') }}"
         style="display: none;"></div>
  @endif

  <script>
    // Handle session messages
    document.addEventListener('DOMContentLoaded', () => {
      const flash = document.getElementById('flash-message-data');
      if (flash && typeof window.showAppModal === 'function') {
        window.showAppModal({
          title: flash.dataset.title,
          message: flash.dataset.message,
          type: flash.dataset.type
        });
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
