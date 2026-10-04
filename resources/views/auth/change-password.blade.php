<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ubah Password - HorizonU Uniform</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  @vite(['resources/css/app.css', 'resources/css/student.css', 'resources/js/app.js'])
</head>
<body class="modal-page-body">
  <div id="screen" class="modal-dialog-card">
    <div onclick="window.location.href='{{ route('profile.edit') }}'" class="sheet-handle" title="Kembali ke Profil" style="cursor: pointer;"></div>

    <h1 style="font-size: 22px; font-weight: 600; margin-bottom: 8px;">Ubah Password</h1>
    <p style="color: var(--muted); font-size: 14px; line-height: 1.4; margin-bottom: 20px;">
      Masukkan password saat ini untuk melanjutkan proses perubahan password akun Anda.
    </p>

    @if ($errors->any())
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px;">
        <ul style="margin: 0; padding-left: 18px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('password.change.store') }}">
      @csrf

      <!-- Password Saat Ini -->
      <div class="form-input-pill" style="margin: 12px 0;">
        <i class="fa-solid fa-lock"></i>
        <input id="currentPass" name="current_password" type="password" required placeholder="Masukkan password saat ini">
        <i onclick="togglePass('currentPass', this)" class="fa-regular fa-eye-slash" style="color: var(--border-pink); cursor: pointer;"></i>
      </div>

      <!-- Password Baru -->
      <div class="form-input-pill" style="margin: 12px 0;">
        <i class="fa-solid fa-lock"></i>
        <input id="newPass" name="password" type="password" required placeholder="Masukkan password baru">
        <i onclick="togglePass('newPass', this)" class="fa-regular fa-eye-slash" style="color: var(--border-pink); cursor: pointer;"></i>
      </div>

      <!-- Konfirmasi Password Baru -->
      <div class="form-input-pill" style="margin: 12px 0 24px 0;">
        <i class="fa-solid fa-lock"></i>
        <input id="confirmPass" name="password_confirmation" type="password" required placeholder="Ulangi password baru">
        <i onclick="togglePass('confirmPass', this)" class="fa-regular fa-eye-slash" style="color: var(--border-pink); cursor: pointer;"></i>
      </div>

      <button type="submit" class="btn-cta" style="width: 100%; height: 52px; font-size: 15px; border: none; cursor: pointer;">
        Konfirmasi Password Baru
      </button>
    </form>
  </div>

  <script>
    function togglePass(inputId, icon) {
      const input = document.getElementById(inputId);
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      }
    }
  </script>
</body>
</html>
