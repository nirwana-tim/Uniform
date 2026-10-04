<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masukkan Kode OTP - HorizonU Uniform</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  @vite(['resources/css/app.css', 'resources/css/student.css', 'resources/js/app.js'])
</head>
<body class="modal-page-body">
  <div id="screen" class="modal-dialog-card">
    <div onclick="window.location.href='{{ route('profile.edit') }}'" class="sheet-handle" title="Kembali ke Profil" style="cursor: pointer;"></div>

    <a href="{{ route('profile.email.input-email') }}" class="btn-back" style="width: 38px; height: 38px; margin-bottom: 16px; text-decoration: none;" title="Kembali">
      <i class="fa-solid fa-arrow-left"></i>
    </a>

    <h1 style="font-size: 22px; font-weight: 600; margin-bottom: 8px;">Masukkan Kode OTP</h1>
    <p style="color: var(--muted); font-size: 14px; line-height: 1.4; margin-bottom: 24px;">
      Kami mengirimkan Kode OTP ke email <span style="font-weight: 600; color: var(--dark-text);">{{ $email }}</span> untuk konfirmasi email.
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

    <form id="otpForm" method="POST" action="{{ route('profile.email.verify-otp.post') }}">
      @csrf
      <input type="hidden" name="code" id="fullOtpCode" value="">

      <div style="display: flex; justify-content: center; gap: 8px; margin-bottom: 20px;">
        <input class="otp-digit" maxlength="1" autofocus style="width: 46px; height: 56px; border-radius: 12px; background-color: var(--card-pink); text-align: center; font-size: 26px; font-weight: 700; border: 1px solid var(--border-pink); outline: none;">
        <input class="otp-digit" maxlength="1" style="width: 46px; height: 56px; border-radius: 12px; background-color: var(--card-pink); text-align: center; font-size: 26px; font-weight: 700; border: 1px solid var(--border-pink); outline: none;">
        <input class="otp-digit" maxlength="1" style="width: 46px; height: 56px; border-radius: 12px; background-color: var(--card-pink); text-align: center; font-size: 26px; font-weight: 700; border: 1px solid var(--border-pink); outline: none;">
        <input class="otp-digit" maxlength="1" style="width: 46px; height: 56px; border-radius: 12px; background-color: var(--card-pink); text-align: center; font-size: 26px; font-weight: 700; border: 1px solid var(--border-pink); outline: none;">
        <input class="otp-digit" maxlength="1" style="width: 46px; height: 56px; border-radius: 12px; background-color: var(--card-pink); text-align: center; font-size: 26px; font-weight: 700; border: 1px solid var(--border-pink); outline: none;">
        <input class="otp-digit" maxlength="1" style="width: 46px; height: 56px; border-radius: 12px; background-color: var(--card-pink); text-align: center; font-size: 26px; font-weight: 700; border: 1px solid var(--border-pink); outline: none;">
      </div>

      <div style="text-align: center; font-size: 14px; margin-bottom: 24px;">
        <span style="color: var(--muted);">Kode kedaluwarsa dalam: </span>
        <span id="countdownTimer" style="font-weight: 600; color: var(--dark-text);">10:00</span>
      </div>

      <button type="submit" class="btn-cta" style="width: 100%; height: 52px; font-size: 15px; border: none; cursor: pointer;">
        Verifikasi OTP
      </button>
    </form>
  </div>

  <script>
    const inputs = document.querySelectorAll('.otp-digit');
    const form = document.getElementById('otpForm');
    const fullCodeInput = document.getElementById('fullOtpCode');

    inputs.forEach((input, index) => {
      input.addEventListener('input', (e) => {
        if (e.target.value.length === 1 && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
        updateFullCode();
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
          inputs[index - 1].focus();
        }
      });
    });

    function updateFullCode() {
      let code = '';
      inputs.forEach(inp => code += inp.value);
      fullCodeInput.value = code;
    }

    form.addEventListener('submit', (e) => {
      updateFullCode();
      if (fullCodeInput.value.length < 6) {
        e.preventDefault();
        alert('Silakan masukkan 6 digit kode OTP secara lengkap.');
      }
    });

    // Countdown Timer (10 minutes)
    let timeLeft = 600;
    const timerEl = document.getElementById('countdownTimer');
    const interval = setInterval(() => {
      timeLeft--;
      if (timeLeft <= 0) {
        clearInterval(interval);
        timerEl.textContent = 'Kedaluwarsa';
        return;
      }
      const m = String(Math.floor(timeLeft / 60)).padStart(2, '0');
      const s = String(timeLeft % 60).padStart(2, '0');
      timerEl.textContent = `${m}:${s}`;
    }, 1000);
  </script>
</body>
</html>
