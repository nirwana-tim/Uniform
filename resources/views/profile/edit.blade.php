@if($student)
  <x-student-layout title="Profil Akun" :showNav="false">
    <!-- HERO COVER & HEADER -->
    <div class="profile-hero">
      <img src="{{ asset('assets/img/profile-header.webp') }}" alt="Profile Cover" class="profile-hero-img">
      <div class="profile-hero-top">
        <a href="{{ route('dashboard') }}" class="btn-back" title="Kembali ke Beranda">
          <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="header-back-title">Profil Akun</h1>
      </div>
    </div>

    <!-- AVATAR -->
    <div class="profile-avatar-wrap">
      <i class="fa-solid fa-user"></i>
    </div>

    <!-- NAME & NIM -->
    <div class="profile-name-sec">
      <h2 class="profile-name">{{ $student->name }}</h2>
      <p class="profile-nim">{{ $student->nim }}</p>
    </div>

    <!-- MAIN CONTENT -->
    <main class="app-content">
      <div class="profile-dashboard-grid">
        <!-- SECTION 1: INFORMASI MAHASISWA -->
        <div class="profile-col">
          <div class="section-header">
            <h3 class="section-title">Informasi Mahasiswa</h3>
          </div>

          <div class="info-card">
            <!-- Faculty -->
            <div class="info-row">
              <i class="fa-solid fa-building info-row-icon"></i>
              <div class="info-row-content">
                <p class="info-row-label">Faculty</p>
                <p class="info-row-val">{{ $student->studyProgram?->faculty?->name ?? 'Faculty of Information and Computer Technology' }}</p>
              </div>
            </div>

            <div class="info-row-divider"></div>

            <!-- Study Program -->
            <div class="info-row">
              <i class="fa-solid fa-book-open info-row-icon"></i>
              <div class="info-row-content">
                <p class="info-row-label">Studi Program</p>
                <p class="info-row-val">{{ $student->studyProgram?->name ?? '-' }}</p>
              </div>
            </div>

            <div class="info-row-divider"></div>

            <!-- Batch -->
            <div class="info-row">
              <i class="fa-solid fa-users info-row-icon"></i>
              <div class="info-row-content">
                <p class="info-row-label">Batch</p>
                <p class="info-row-val">{{ $student->generation?->name ?? $student->generation?->code ?? 'SY 24-25' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTION 2: PENGATURAN AKUN -->
        <div class="profile-col">
          <div class="section-header">
            <h3 class="section-title">Pengaturan Akun</h3>
          </div>

          <div class="info-card">
            <!-- Email Universitas -->
            <div class="info-row">
              <i class="fa-regular fa-envelope info-row-icon"></i>
              <div class="info-row-content">
                <p class="info-row-label">Email Universitas</p>
                <p class="info-row-val">{{ $student->email_kampus ?: $user->email }}</p>
              </div>
            </div>

            <div class="info-row-divider"></div>

            <!-- Email Pribadi -->
            <a href="{{ route('profile.email.change') }}" class="info-row" style="cursor: pointer; text-decoration: none;" title="Klik untuk ubah email pribadi">
              <i class="fa-regular fa-envelope info-row-icon"></i>
              <div class="info-row-content">
                <p class="info-row-label">Email Pribadi</p>
                <p class="info-row-val">{{ $student->email_pribadi ?: 'Belum diatur' }}</p>
              </div>
              <i class="fa-solid fa-chevron-right info-row-chevron"></i>
            </a>

            <div class="info-row-divider"></div>

            <!-- Password -->
            <a href="{{ route('password.change') }}" class="info-row" style="cursor: pointer; text-decoration: none;" title="Klik untuk ubah password">
              <i class="fa-solid fa-lock info-row-icon"></i>
              <div class="info-row-content">
                <p class="info-row-label">Password</p>
                <p class="info-row-val">Ubah password Anda</p>
              </div>
              <i class="fa-solid fa-chevron-right info-row-chevron"></i>
            </a>

            <div class="info-row-divider"></div>

            <!-- Logout -->
            <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
            <div onclick="confirmStudentLogout()" class="info-row cursor-pointer" style="cursor: pointer;" title="Keluar dari akun">
              <i class="fa-solid fa-arrow-right-from-bracket info-row-icon" style="color: var(--danger);"></i>
              <div class="info-row-content">
                <p class="info-row-val" style="color: var(--danger);">Logout</p>
                <p class="info-row-label" style="color: var(--danger);">Keluar dari akun</p>
              </div>
              <i class="fa-solid fa-chevron-right info-row-chevron" style="color: var(--danger);"></i>
            </div>
          </div>
        </div>
      </div>
    </main>

    @push('scripts')
    <script>
      function confirmStudentLogout() {
        showAppModal({
          title: 'Keluar dari Akun?',
          message: 'Apakah Anda yakin ingin keluar dari akun HorizonU Uniform? Anda harus login kembali untuk mengakses data seragam Anda.',
          type: 'danger',
          cancelText: 'Batal',
          confirmText: 'Ya, Logout',
          onConfirm: () => {
            document.getElementById('logoutForm').submit();
          }
        });
      }
    </script>
    @endpush
  </x-student-layout>
@else
  <x-app-layout>
    <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Profile') }}
      </h2>
    </x-slot>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
          </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="max-w-xl">
            @include('profile.partials.update-password-form')
          </div>
        </div>
      </div>
    </div>
  </x-app-layout>
@endif
