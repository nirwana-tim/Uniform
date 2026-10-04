<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ubah Alamat Email - HorizonU Uniform</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  @vite(['resources/css/app.css', 'resources/css/student.css', 'resources/js/app.js'])
</head>
<body class="modal-page-body">
  <div id="screen" class="modal-dialog-card">
    <div onclick="window.location.href='{{ route('profile.edit') }}'" class="sheet-handle" title="Kembali ke Profil" style="cursor: pointer;"></div>

    <a href="{{ route('profile.email.change') }}" class="btn-back" style="width: 38px; height: 38px; margin-bottom: 16px; text-decoration: none;" title="Kembali">
      <i class="fa-solid fa-arrow-left"></i>
    </a>

    <h1 style="font-size: 22px; font-weight: 600; margin-bottom: 8px;">Ubah Alamat Email</h1>
    <p style="color: var(--muted); font-size: 14px; line-height: 1.4; margin-bottom: 24px;">
      Masukkan alamat email baru untuk mengirimkan Kode OTP.
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

    <form method="POST" action="{{ route('profile.email.send-otp') }}">
      @csrf
      <div class="form-input-pill" style="margin-bottom: 24px;">
        <i class="fa-regular fa-envelope"></i>
        <input name="email_pribadi" type="email" required placeholder="example@gmail.com" value="{{ old('email_pribadi') }}">
      </div>

      <button type="submit" class="btn-cta" style="width: 100%; height: 52px; font-size: 15px; border: none; cursor: pointer;">
        Kirim OTP
      </button>
    </form>
  </div>
</body>
</html>
