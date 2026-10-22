@extends('layouts.app')

@section('title', 'Kendaraan Keluar & Scan QR - ParkSafe')

@section('content')
<!-- Page Header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <div class="stat-icon-wrapper stat-icon-blue" style="width: 42px; height: 42px; border-radius: 10px;">
        <i class="bi bi-qr-code-scan"></i>
    </div>
    <div>
        <h2 class="page-title mb-1" style="font-size: 1.55rem;">Pintu Keluar & Scanner QR</h2>
        <p class="page-subtitle">Pindai QR Code tiket menggunakan kamera, drag & drop foto, atau cari berdasarkan plat nomor / kode tiket.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Scanner & Search Column -->
    <div class="col-12 col-lg-6">
        <!-- Search Input Card -->
        <div class="ps-card mb-4">
            <h3 class="ps-card-title mb-3" style="font-size: 1rem;">
                <i class="bi bi-search text-primary"></i> Cari Plat Nomor / Kode Tiket
            </h3>
            
            <form action="{{ route('checkout.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" 
                       name="q" 
                       id="checkoutSearchInput" 
                       class="form-control-ps font-monospace text-uppercase fw-bold" 
                       placeholder="Contoh: B 1234 KZ atau TKT-..." 
                       value="{{ $query ?? '' }}" 
                       required 
                       autofocus>
                <button type="submit" class="btn btn-ps-primary text-nowrap">
                    <i class="bi bi-arrow-right"></i> Periksa
                </button>
            </form>
            <small class="text-muted mt-2 d-block">Ketikkan nomor plat atau kode tiket kendaraan yang akan keluar.</small>
        </div>

        <!-- Camera & Drag-and-Drop QR Code Scanner -->
        <div class="ps-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="ps-card-title mb-0" style="font-size: 1rem;">
                    <i class="bi bi-camera text-info"></i> Scanner QR Code
                </h3>
                <button type="button" id="btnLocalCamera" class="btn btn-ps-primary btn-sm" onclick="toggleCheckoutCamera()">
                    <i class="bi bi-camera me-1"></i> <span id="localCameraLabel">Nyalakan Kamera</span>
                </button>
            </div>
            
            <!-- Live Camera Viewport -->
            <div id="checkoutCameraWrapper" style="display: none;" class="mb-3">
                <div id="checkout-qr-reader" class="rounded overflow-hidden" style="background: #000;"></div>
                <div id="checkoutCameraStatus" class="alert alert-dark small py-2 text-center text-muted mb-0 mt-2" style="background: rgba(255,255,255,0.05); border: none;">
                    Arahkan kamera ke QR Code tiket fisik atau layar ponsel...
                </div>
            </div>

            <!-- Drag & Drop Zone for QR Photo -->
            <div class="qr-dropzone mb-2" id="checkoutDropzone" onclick="document.getElementById('checkoutQrFileInput').click()">
                <i class="bi bi-cloud-arrow-up fs-1 d-block text-primary mb-2"></i>
                <strong class="d-block text-white mb-1">Tarik & Lepas Foto QR di Sini</strong>
                <span class="text-muted small">atau <span class="text-primary text-decoration-underline">Klik untuk Unggah Gambar Tiket</span> (JPG, PNG, WebP)</span>
                <input type="file" id="checkoutQrFileInput" accept="image/*" style="display: none;" onchange="handleCheckoutFileSelect(this)">
            </div>
            <div id="checkoutFileStatus" class="small text-center text-muted"></div>
        </div>
    </div>

    <!-- Ticket Detail & Exit Confirmation Column (NO FEES / NO PRICES) -->
    <div class="col-12 col-lg-6">
        @if($foundTicket)
            <div class="ps-card shadow-lg" style="border-color: var(--primary-blue);">
                <div class="d-flex justify-content-between align-items-start border-bottom border-secondary border-opacity-25 pb-3 mb-3">
                    <div>
                        <span class="status-pill status-pill-green mb-1">Tiket Ditemukan &bull; Aktif</span>
                        <h4 class="fw-bold text-white font-monospace mb-0" style="letter-spacing: 0.5px;">{{ $foundTicket->ticket_code }}</h4>
                    </div>
                    <span class="badge" style="background: rgba(29, 112, 243, 0.2); color: var(--primary-blue); border: 1px solid var(--primary-blue); font-size: 0.85rem;">
                        {{ $foundTicket->vehicle_type }}
                    </span>
                </div>

                <!-- License Plate Card -->
                <div class="text-center my-3 p-3 rounded-3" style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-color);">
                    <span class="font-monospace fw-bold text-white fs-3 letter-spacing-1">
                        {{ $foundTicket->plate_number }}
                    </span>
                </div>

                <!-- Timing & Location Info (Campus Free Model) -->
                <div class="d-flex flex-column gap-2 small py-2 mb-4">
                    <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                        <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-box-arrow-in-right text-success"></i> Waktu Masuk:</span>
                        <strong class="text-white">{{ $foundTicket->entry_time ? $foundTicket->entry_time->translatedFormat('d M Y - H:i') : '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                        <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-box-arrow-right text-danger"></i> Waktu Sekarang (Keluar):</span>
                        <strong class="text-white">{{ now()->translatedFormat('d M Y - H:i') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                        <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-hourglass-split text-info"></i> Durasi Parkir:</span>
                        <strong class="text-info fs-6">{{ $foundTicket->formatted_duration }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                        <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-geo-alt text-primary"></i> Area & Slot:</span>
                        <strong class="text-white">{{ $foundTicket->area->name ?? '-' }} (Slot {{ $foundTicket->slot->slot_code ?? '-' }})</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-shield-check text-success"></i> Biaya Parkir:</span>
                        <span class="status-pill status-pill-green">Gratis (Parkir Kampus)</span>
                    </div>
                </div>

                <!-- Exit Process Form -->
                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ticket_code" value="{{ $foundTicket->ticket_code }}">

                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Catatan Gerbang Keluar (Opsional)</label>
                        <input type="text" name="notes" class="form-control-ps form-control-sm" placeholder="Contoh: Mahasiswa / Dosen / Tamu Fakultas">
                    </div>

                    <button type="submit" class="btn btn-ps-primary btn-lg w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check2-circle fs-5"></i> KONFIRMASI KENDARAAN KELUAR
                    </button>
                </form>
            </div>
        @else
            <!-- Placeholder Card -->
            <div class="ps-card p-5 text-center h-100 d-flex flex-column justify-content-center align-items-center">
                <div class="stat-icon-wrapper stat-icon-blue mb-3" style="width: 60px; height: 60px; border-radius: 16px; font-size: 1.8rem;">
                    <i class="bi bi-qr-code"></i>
                </div>
                <h4 class="text-white fw-bold mb-1">Menunggu Pemindaian</h4>
                <p class="text-muted small mb-4" style="max-width: 320px;">
                    Scan QR Code tiket dengan kamera atau tarik & lepas foto tiket untuk memverifikasi dan mengeluarkan kendaraan.
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-ps-primary btn-sm" onclick="toggleCheckoutCamera()">
                        <i class="bi bi-camera me-1"></i> Nyalakan Kamera
                    </button>
                    <button type="button" class="btn btn-ps-outline btn-sm" onclick="document.getElementById('checkoutQrFileInput').click()">
                        <i class="bi bi-image me-1"></i> Drag / Pilih Foto
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    let checkoutHtml5Qr = null;
    let isCheckoutCameraRunning = false;

    function toggleCheckoutCamera() {
        if (isCheckoutCameraRunning) {
            stopCheckoutCamera();
        } else {
            startCheckoutCamera();
        }
    }

    function startCheckoutCamera() {
        const wrapper = document.getElementById('checkoutCameraWrapper');
        wrapper.style.display = 'block';

        if (!checkoutHtml5Qr) {
            checkoutHtml5Qr = new Html5Qrcode("checkout-qr-reader");
        }
        
        checkoutHtml5Qr.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 220, height: 220 } },
            (decodedText) => {
                stopCheckoutCamera();
                window.location.href = `{{ route('checkout.index') }}?q=${encodeURIComponent(decodedText)}`;
            },
            (err) => {}
        ).then(() => {
            isCheckoutCameraRunning = true;
            document.getElementById('localCameraLabel').textContent = "Matikan Kamera";
            document.getElementById('checkoutCameraStatus').textContent = "Kamera aktif. Arahkan QR code ke kotak pemindai...";
        }).catch(err => {
            console.warn(err);
            document.getElementById('checkoutCameraStatus').textContent = "Tidak dapat mengakses kamera. Silakan drag/drop foto tiket di bawah.";
        });
    }

    function stopCheckoutCamera() {
        if (checkoutHtml5Qr && isCheckoutCameraRunning) {
            checkoutHtml5Qr.stop().then(() => {
                isCheckoutCameraRunning = false;
                document.getElementById('localCameraLabel').textContent = "Nyalakan Kamera";
                document.getElementById('checkoutCameraWrapper').style.display = 'none';
            }).catch(e => console.error(e));
        }
    }

    // Drag and Drop file handling
    const checkoutDropzone = document.getElementById('checkoutDropzone');
    if (checkoutDropzone) {
        ['dragenter', 'dragover'].forEach(evt => {
            checkoutDropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                checkoutDropzone.classList.add('drag-active');
            });
        });
        ['dragleave', 'drop'].forEach(evt => {
            checkoutDropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                checkoutDropzone.classList.remove('drag-active');
            });
        });
        checkoutDropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt.files && dt.files[0]) {
                processCheckoutFile(dt.files[0]);
            }
        });
    }

    function handleCheckoutFileSelect(input) {
        if (input.files && input.files[0]) {
            processCheckoutFile(input.files[0]);
        }
    }

    function processCheckoutFile(file) {
        const statusEl = document.getElementById('checkoutFileStatus');
        statusEl.innerHTML = `<span class="text-info"><i class="bi bi-hourglass-split"></i> Memindai file ${file.name}...</span>`;

        if (!checkoutHtml5Qr) {
            checkoutHtml5Qr = new Html5Qrcode("checkout-qr-reader");
        }

        checkoutHtml5Qr.scanFile(file, true)
            .then(code => {
                statusEl.innerHTML = `<span class="text-success"><i class="bi bi-check-circle"></i> Berhasil scan: ${code}! Mengalihkan...</span>`;
                window.location.href = `{{ route('checkout.index') }}?q=${encodeURIComponent(code)}`;
            })
            .catch(err => {
                statusEl.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> QR Code tidak terdeteksi pada gambar ini. Silakan coba gambar tiket yang lebih tajam.</span>`;
            });
    }
</script>
@endpush
@endsection
