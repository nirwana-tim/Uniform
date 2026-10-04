/**
 * UNISTOK - GLOBAL APP MODAL MANAGER
 * Menangani modal notifikasi/alert responsif:
 * - Layar HP (< 768px): Muncul meluncur dari bawah (Bottom Sheet)
 * - Layar Tablet & Desktop (>= 768px): Modal dialog mengambang di tengah layar (Centered Dialog)
 */

export function showAppModal(options) {
  const opts = typeof options === 'string' ? { message: options } : (options || {});
  const message = opts.message || 'Operasi berhasil diproses.';
  const type = opts.type || 'success';
  const title = opts.title || (type === 'info' ? 'Informasi' : (type === 'warning' ? 'Perhatian' : 'Berhasil!'));
  const confirmText = opts.confirmText || 'Oke, Mengerti';
  const cancelText = opts.cancelText || null;
  const onCancel = opts.onCancel || null;
  const onConfirm = opts.onConfirm || null;

  let backdrop = document.getElementById('globalAlertBackdrop');
  let card = document.getElementById('globalAlertCard');

  if (!backdrop) {
    backdrop = document.createElement('div');
    backdrop.id = 'globalAlertBackdrop';
    backdrop.className = 'alert-modal-backdrop';
    document.body.appendChild(backdrop);
  }

  if (!card) {
    card = document.createElement('div');
    card.id = 'globalAlertCard';
    card.className = 'alert-modal-card';
    document.body.appendChild(card);
  }

  const iconHtml = type === 'info' 
    ? '<i class="fa-solid fa-circle-info"></i>' 
    : (type === 'warning' 
        ? '<i class="fa-solid fa-triangle-exclamation"></i>' 
        : (type === 'question' 
            ? '<i class="fa-solid fa-shirt"></i>' 
            : (type === 'danger' 
                ? '<i class="fa-solid fa-arrow-right-from-bracket"></i>' 
                : '<i class="fa-solid fa-circle-check"></i>')));

  const confirmBtnClass = type === 'danger' ? 'alert-modal-btn-danger' : 'alert-modal-btn-confirm';

  const actionsHtml = cancelText ? `
    <div class="alert-modal-actions">
      <button type="button" id="alertModalCancelBtn" class="alert-modal-btn-cancel">
        ${cancelText}
      </button>
      <button type="button" id="alertModalConfirmBtn" class="${confirmBtnClass}">
        ${confirmText}
      </button>
    </div>
  ` : `
    <button type="button" id="alertModalConfirmBtn" class="${type === 'danger' ? 'alert-modal-btn-danger' : 'alert-modal-btn'}">
      ${confirmText}
    </button>
  `;

  card.innerHTML = `
    <div class="alert-modal-handle"></div>
    <div class="alert-modal-icon ${type}">
      ${iconHtml}
    </div>
    <h3 class="alert-modal-title">${title}</h3>
    <p class="alert-modal-message">${message}</p>
    ${actionsHtml}
  `;

  let isClosing = false;
  function closeModal(callback) {
    if (isClosing) return;
    isClosing = true;
    backdrop.classList.remove('show');
    card.classList.remove('show');
    document.removeEventListener('keydown', handleKey);
    setTimeout(() => {
      if (typeof callback === 'function') {
        callback();
      }
      isClosing = false;
    }, 280);
  }

  function handleKey(e) {
    if (e.key === 'Escape') {
      closeModal(onCancel);
    } else if (e.key === 'Enter') {
      closeModal(onConfirm);
    }
  }

  const confirmBtn = document.getElementById('alertModalConfirmBtn');
  const cancelBtn = document.getElementById('alertModalCancelBtn');
  
  if (confirmBtn) confirmBtn.onclick = () => closeModal(onConfirm);
  if (cancelBtn) cancelBtn.onclick = () => closeModal(onCancel);
  backdrop.onclick = () => closeModal(onCancel);
  document.addEventListener('keydown', handleKey);

  // Trigger animasi muncul
  requestAnimationFrame(() => {
    backdrop.classList.add('show');
    card.classList.add('show');
    if (confirmBtn) confirmBtn.focus();
  });
}

// Global Logout Confirmation Modal
export function confirmLogout(redirectUrl = '/login') {
  showAppModal({
    title: 'Keluar dari Akun?',
    message: 'Apakah Anda yakin ingin keluar dari akun HorizonU Uniform? Anda harus login kembali untuk mengakses data seragam Anda.',
    type: 'danger',
    cancelText: 'Batal',
    confirmText: 'Ya, Logout',
    onConfirm: () => {
      const form = document.getElementById('logout-form');
      if (form) {
        form.submit();
      } else {
        window.location.href = redirectUrl;
      }
    }
  });
}

if (typeof window !== 'undefined') {
  window.showAppModal = showAppModal;
  window.confirmLogout = confirmLogout;
  window.alert = function(message) {
    showAppModal({ message: String(message) });
  };
}
