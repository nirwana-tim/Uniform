<x-student-layout title="Edit Ukuran Seragam" :showNav="false">
    <!-- HEADER -->
    <header class="app-header-back">
      <a href="{{ route('student.sizes.index') }}" class="btn-back" title="Kembali">
        <i class="fa-solid fa-arrow-left"></i>
      </a>
      <h1 class="header-back-title">Edit Ukuran Seragam</h1>
    </header>

    <!-- CONTENT -->
    <main class="app-content">
      @if($canEdit)
        <form id="sizeForm" action="{{ route('student.sizes.store') }}" method="POST">
          @csrf
          <input type="hidden" name="event_id" value="{{ $event->id }}">
          <input type="hidden" name="sizes[baju]" id="inputUniformVal" value="{{ old('sizes.baju', $currentSizes['baju'] ?? '') }}">
          <input type="hidden" name="sizes[sepatu]" id="inputShoeVal" value="{{ old('sizes.sepatu', $currentSizes['sepatu'] ?? '') }}">

          <div class="size-card">
            <!-- ALERT INFO -->
            @if(empty($sizeOptions['baju']) && empty($sizeOptions['sepatu']))
              <div class="alert-quota" style="background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3);">
                <i class="fa-solid fa-circle-exclamation" style="color: #ef4444;"></i>
                <p class="alert-quota-text" style="color: #ef4444;">Ukuran belum diatur oleh admin.</p>
              </div>
            @else
              <div class="alert-quota" style="background-color: rgba(212, 168, 58, 0.1); border-color: rgba(212, 168, 58, 0.3);">
                <i class="fa-solid fa-circle-exclamation" style="color: var(--cta);"></i>
                <p class="alert-quota-text" style="color: var(--muted);">
                  Anda hanya dapat mengubah ukuran saat periode dan batas max tersedia. Pastikan data ukuran yang dimasukkan sudah sesuai.<br>
                  Sisa kesempatan perubahan: <b>{{ $remainingChanges }} kali</b>.
                </p>
              </div>
            @endif

            <!-- UKURAN SERAGAM -->
            <div class="size-group">
              <p class="size-label">Ukuran Seragam <span style="display:none;">Ukuran Baju</span></p>
              <div onclick="openSizePicker('uniform')" class="size-row-btn" title="Klik untuk memilih ukuran seragam" style="cursor: pointer;">
                <span class="size-row-label">Ukuran dipilih</span>
                <span id="selectedUniformVal" class="size-row-val">{{ old('sizes.baju', $currentSizes['baju'] ?: 'Pilih Ukuran') }}</span>
                <i class="fa-solid fa-chevron-right size-row-chevron"></i>
              </div>
            </div>

            <!-- UKURAN SEPATU -->
            <div class="size-group">
              <p class="size-label">Ukuran Sepatu</p>
              <div onclick="openSizePicker('shoes')" class="size-row-btn" title="Klik untuk memilih ukuran sepatu" style="cursor: pointer;">
                <span class="size-row-label">Ukuran dipilih</span>
                <span id="selectedShoeVal" class="size-row-val">{{ old('sizes.sepatu', $currentSizes['sepatu'] ?: 'Pilih Ukuran') }}</span>
                <i class="fa-solid fa-chevron-right size-row-chevron"></i>
              </div>
            </div>
          </div>

          <!-- ACTION BUTTON -->
          <button type="button" onclick="confirmSizeChange()" class="btn-submit-action" {{ (empty($sizeOptions['baju']) && empty($sizeOptions['sepatu'])) ? 'disabled' : '' }}>
            Konfirmasi Perubahan
          </button>
        </form>
      @else
        <!-- DISABLED STATE (edit-ukuran-disable.html) -->
        <div class="size-card">
          <!-- ALERT KUOTA -->
          <div class="alert-quota">
            <i class="fa-regular fa-circle-xmark"></i>
            <p class="alert-quota-text">
              <span class="text-muted">Anda sudah menggunakan kuota perubahan ukuran.</span><br>
              <span class="text-danger">Perubahan ukuran tidak dapat dilakukan lagi.</span>
            </p>
          </div>

          <!-- UKURAN SERAGAM -->
          <div class="size-group">
            <p class="size-label">Ukuran Seragam <span style="display:none;">Ukuran Baju</span></p>
            <div class="size-row-btn" style="cursor: not-allowed; opacity: 0.8;">
              <span class="size-row-label">Ukuran dipilih</span>
              <span class="size-row-val">{{ $currentSizes['baju'] ?: '-' }}</span>
              <i class="fa-solid fa-chevron-right size-row-chevron"></i>
            </div>
          </div>

          <!-- UKURAN SEPATU -->
          <div class="size-group">
            <p class="size-label">Ukuran Sepatu</p>
            <div class="size-row-btn" style="cursor: not-allowed; opacity: 0.8;">
              <span class="size-row-label">Ukuran dipilih</span>
              <span class="size-row-val">{{ $currentSizes['sepatu'] ?: '-' }}</span>
              <i class="fa-solid fa-chevron-right size-row-chevron"></i>
            </div>
          </div>
        </div>

        <!-- ACTION BUTTON (DISABLED) -->
        <button disabled class="btn-submit-action disabled" style="opacity: 0.5; cursor: not-allowed;">
          Konfirmasi Perubahan
        </button>
      @endif
    </main>

    <!-- MODAL BACKDROP -->
    <div id="sizePickerBackdrop" onclick="closeSizePicker()" class="modal-backdrop"></div>

    <!-- BOTTOM SHEET / MODAL PICKER -->
    <div id="sizePickerSheet" class="modal-bottom-sheet">
      <button type="button" onclick="closeSizePicker()" class="modal-close-btn" title="Tutup Modal">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <div onclick="closeSizePicker()" class="sheet-handle" title="Tutup Modal"></div>

      <div class="modal-step-body">
        <h2 id="sizePickerTitle" class="modal-step-title">Pilih Ukuran</h2>
        <p id="sizePickerDesc" class="modal-step-desc">Pilih salah satu ukuran yang tersedia di bawah ini.</p>

        <div id="sizeChipsContainer" class="size-chips-grid">
          <!-- Dynamic chips generated in JS -->
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
      let currentPickerType = 'uniform';
      const uniformSizes = {!! json_encode(array_values($sizeOptions['baju'] ?? [])) !!};
      const shoeSizes = {!! json_encode(array_values($sizeOptions['sepatu'] ?? [])) !!};

      function openSizePicker(type) {
        currentPickerType = type;
        const title = document.getElementById('sizePickerTitle');
        const desc = document.getElementById('sizePickerDesc');
        const currentVal = type === 'uniform' 
          ? document.getElementById('inputUniformVal').value.trim()
          : document.getElementById('inputShoeVal').value.trim();

        if (type === 'uniform') {
          title.textContent = 'Pilih Ukuran Seragam';
          desc.textContent = uniformSizes.length ? 'Pilih salah satu ukuran seragam yang tersedia dari master data.' : 'Ukuran seragam belum diatur oleh admin.';
          renderChips(uniformSizes, currentVal);
        } else {
          title.textContent = 'Pilih Ukuran Sepatu';
          desc.textContent = shoeSizes.length ? 'Pilih salah satu ukuran sepatu yang tersedia dari master data.' : 'Ukuran sepatu belum diatur oleh admin.';
          renderChips(shoeSizes, currentVal);
        }

        document.getElementById('sizePickerBackdrop').classList.add('show');
        document.getElementById('sizePickerSheet').classList.add('show');
      }

      function renderChips(sizes, currentVal) {
        const container = document.getElementById('sizeChipsContainer');
        if (!sizes || sizes.length === 0) {
          container.innerHTML = '<p style="color: var(--muted); font-size: 14px; text-align: center; padding: 16px 0; grid-column: 1 / -1;">Tidak ada ukuran tersedia pada event ini.</p>';
          return;
        }
        container.innerHTML = sizes.map(size => `
          <button type="button" onclick="selectSize('${size}')" class="size-chip-btn ${size === currentVal ? 'active' : ''}">
            ${size}
          </button>
        `).join('');
      }

      function selectSize(size) {
        if (currentPickerType === 'uniform') {
          document.getElementById('selectedUniformVal').textContent = size;
          document.getElementById('inputUniformVal').value = size;
        } else {
          document.getElementById('selectedShoeVal').textContent = size;
          document.getElementById('inputShoeVal').value = size;
        }
        closeSizePicker();
      }

      function closeSizePicker() {
        document.getElementById('sizePickerBackdrop').classList.remove('show');
        document.getElementById('sizePickerSheet').classList.remove('show');
      }

      function confirmSizeChange() {
        const uniformVal = document.getElementById('inputUniformVal').value.trim();
        const shoeVal = document.getElementById('inputShoeVal').value.trim();

        if (!uniformVal && !shoeVal) {
          if (typeof showAppModal === 'function') {
            showAppModal({
              title: 'Peringatan',
              message: 'Silakan pilih minimal satu ukuran (Baju atau Sepatu) sebelum menyimpan.',
              type: 'warning',
              confirmText: 'Mengerti'
            });
          } else {
            alert('Silakan pilih minimal satu ukuran (Baju atau Sepatu) sebelum menyimpan.');
          }
          return;
        }

        const uniformDisplay = uniformVal || 'Tidak dipilih';
        const shoeDisplay = shoeVal || 'Tidak dipilih';

        showAppModal({
          title: 'Simpan Perubahan Ukuran?',
          message: `Pastikan data ukuran sudah benar:\n• Seragam: ${uniformDisplay}\n• Sepatu: ${shoeDisplay}\n\nApakah Anda yakin ingin menyimpan perubahan?`,
          type: 'question',
          cancelText: 'Batal',
          confirmText: 'Ya, Simpan',
          onConfirm: () => {
            document.getElementById('sizeForm').submit();
          }
        });
      }
    </script>
    @endpush
</x-student-layout>
