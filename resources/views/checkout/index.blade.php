@extends('layouts.app')

@section('title', 'Kendaraan Keluar & Scan QR Code - FILKOM UB')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span>Pemindaian QR & Kendaraan Keluar</span>
            <span class="badge badge-orange small fs-6">Gerbang Keluar</span>
        </h2>
        <p class="text-muted mb-0">Pindai QR Code tiket menggunakan kamera scanner atau cari berdasarkan plat nomor / kode tiket untuk kalkulasi tarif dan konfirmasi keluar.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Scanner & Search Column -->
    <div class="col-lg-6">
        <!-- Search Input -->
        <div class="card-dark p-4 mb-4">
            <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-search text-warning"></i> Cari Tiket Parkir
            </h5>
            
            <form action="{{ route('checkout.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" 
                       name="q" 
                       id="searchQuery" 
                       class="form-control form-control-dark font-monospace text-uppercase fw-bold" 
                       placeholder="Contoh: TKT-20260928-ABCD atau N 1234 AB" 
                       value="{{ $query ?? '' }}" 
                       required 
                       autofocus>
                <button type="submit" class="btn btn-neon-green d-flex align-items-center gap-1 text-nowrap">
                    <i class="bi bi-arrow-right"></i> Periksa
                </button>
            </form>
            <small class="text-muted mt-2 d-block">Masukkan kode tiket atau plat nomor kendaraan yang sedang aktif parkir.</small>
        </div>

        <!-- Camera QR Code Scanner -->
        <div class="card-dark p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-camera-fill text-info"></i> Scanner QR Kamera
                </h5>
                <button type="button" id="toggleScannerBtn" class="btn btn-outline-custom btn-sm">
                    <i class="bi bi-power me-1"></i> Nyalakan Kamera
                </button>
            </div>
            
            <div id="scannerWrapper" style="display: none;">
                <div id="qr-reader" class="mb-3"></div>
                <div id="scannerStatus" class="alert alert-dark small py-2 text-center text-muted mb-0">
                    Arahkan kamera ke QR Code pada tiket fisik atau layar ponsel...
                </div>
            </div>
            
            <div id="scannerPlaceholder" class="p-4 text-center border border-secondary rounded" style="background: rgba(0,0,0,0.3);">
                <i class="bi bi-qr-code-scan fs-1 d-block text-muted mb-2"></i>
                <p class="text-muted small mb-0">Kamera scanner dalam posisi nonaktif. Klik tombol <strong>Nyalakan Kamera</strong> di atas untuk mengaktifkan pemindai langsung dari webcam atau kamera smartphone.</p>
            </div>
        </div>
    </div>

    <!-- Ticket Detail & Payment Confirmation Column -->
    <div class="col-lg-6">
        @if($foundTicket && $feeDetails)
            <div class="card-dark p-4 border-warning shadow-lg">
                <div class="d-flex justify-content-between align-items-start border-bottom border-secondary pb-3 mb-3">
                    <div>
                        <span class="badge badge-green mb-1">Tiket Ditemukan</span>
                        <h4 class="fw-bold text-white font-monospace mb-0">{{ $foundTicket->ticket_code }}</h4>
                    </div>
                    <span class="badge {{ $foundTicket->vehicle_type === 'Motor' ? 'bg-secondary' : 'bg-primary' }} fs-6">
                        {{ $foundTicket->vehicle_type }}
                    </span>
                </div>

                <!-- Big Plate Display -->
                <div class="text-center my-3">
                    <span class="ticket-plate">
                        {{ $foundTicket->plate_number }}
                    </span>
                </div>

                <!-- Timing and Duration -->
                <div class="row g-2 text-muted small my-3 p-3 rounded" style="background: #090d13;">
                    <div class="col-6">
                        <span>Waktu Masuk:</span>
                        <strong class="d-block text-white">{{ $foundTicket->entry_time->translatedFormat('d M Y - H:i') }}</strong>
                    </div>
                    <div class="col-6 text-end">
                        <span>Waktu Sekarang (Keluar):</span>
                        <strong class="d-block text-white">{{ now()->translatedFormat('d M Y - H:i') }}</strong>
                    </div>
                    <div class="col-6 mt-2 pt-2 border-top border-secondary">
                        <span>Lokasi Parkir:</span>
                        <strong class="d-block text-info">{{ $foundTicket->area->name ?? '-' }} (Slot {{ $foundTicket->slot->slot_code ?? '-' }})</strong>
                    </div>
                    <div class="col-6 mt-2 pt-2 border-top border-secondary text-end">
                        <span>Durasi Parkir:</span>
                        <strong class="d-block text-warning fs-6">{{ $foundTicket->formatted_duration }} ({{ $feeDetails['hours'] }} jam terhitung)</strong>
                    </div>
                </div>

                <!-- Fee Breakdown -->
                <div class="p-3 rounded mb-3" style="background: rgba(255, 132, 0, 0.08); border: 1px solid rgba(255, 132, 0, 0.3);">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tarif Dasar ({{ $feeDetails['hours'] }} jam &times; Rp {{ number_format($feeDetails['hourly_rate'], 0, ',', '.') }}):</span>
                        <strong class="text-light">Rp {{ number_format($feeDetails['base_fee'], 0, ',', '.') }}</strong>
                    </div>
                    @if(request()->boolean('is_lost'))
                    <div class="d-flex justify-content-between mb-1 text-danger">
                        <span>Denda Tiket Hilang:</span>
                        <strong>+ Rp {{ number_format($feeDetails['penalty'], 0, ',', '.') }}</strong>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between pt-2 border-top border-secondary align-items-center">
                        <span class="fs-5 fw-bold text-white">TOTAL BIAYA:</span>
                        <strong class="fs-2 fw-bold font-monospace" style="color: var(--neon-orange);">
                            Rp {{ number_format($feeDetails['total_fee'], 0, ',', '.') }}
                        </strong>
                    </div>
                </div>

                <!-- Form Konfirmasi Keluar -->
                <form action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="ticket_code" value="{{ $foundTicket->ticket_code }}">
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_lost" id="is_lost" value="1" {{ request()->boolean('is_lost') ? 'checked' : '' }} onchange="toggleLost(this)">
                        <label class="form-check-label text-warning small fw-semibold" for="is_lost">
                            Tiket Fisik Hilang? (Terapkan Denda Tambahan)
                        </label>
                    </div>

                    <div class="mb-3">
                        <input type="text" name="notes" class="form-control form-control-dark form-control-sm" placeholder="Catatan opsional (misal: pembayaran tunai / QRIS)">
                    </div>

                    <button type="submit" class="btn btn-neon-orange btn-lg w-100 fw-bold d-flex justify-content-center align-items-center gap-2 py-3 shadow">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                        <span>KONFIRMASI PEMBAYARAN & BUKA GERBANG</span>
                    </button>
                </form>
            </div>
        @elseif($query)
            <div class="card-dark p-4 text-center border-danger">
                <i class="bi bi-exclamation-circle text-danger fs-1 mb-2 d-block"></i>
                <h5 class="fw-bold text-white">Tiket Aktif Tidak Ditemukan</h5>
                <p class="text-muted small">Tidak ada kendaraan aktif dengan kode tiket atau plat nomor <strong>"{{ $query }}"</strong>. Pastikan kode atau plat nomor sudah benar, atau kendaraan mungkin sudah keluar sebelumnya.</p>
                <a href="{{ route('checkout.index') }}" class="btn btn-outline-custom btn-sm">Reset Pencarian</a>
            </div>
        @else
            <!-- Waiting State -->
            <div class="card-dark p-4 text-center h-100 d-flex flex-column justify-content-center align-items-center">
                <i class="bi bi-qr-code text-muted fs-1 mb-3"></i>
                <h5 class="fw-bold text-light">Menunggu Pemindaian Tiket</h5>
                <p class="text-muted small mb-0" style="max-width: 320px;">
                    Gunakan scanner kamera di sebelah kiri atau ketikkan nomor plat/tiket untuk menampilkan rincian durasi dan tarif parkir.
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Riwayat Keluar Terakhir -->
<div class="card-dark mt-4 overflow-hidden">
    <div class="card-header-dark">
        <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-success"></i> Transaksi Keluar Terakhir
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-dark-custom">
            <thead>
                <tr>
                    <th>Plat Nomor</th>
                    <th>Jenis</th>
                    <th>Area</th>
                    <th>Waktu Masuk</th>
                    <th>Waktu Keluar</th>
                    <th>Durasi</th>
                    <th>Total Biaya</th>
                    <th class="text-end">Struk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentCheckouts as $item)
                <tr>
                    <td>
                        <span class="font-monospace fw-bold text-white px-2 py-1 rounded bg-black border border-secondary">
                            {{ $item->plate_number }}
                        </span>
                    </td>
                    <td>{{ $item->vehicle_type }}</td>
                    <td>{{ $item->area->name ?? '-' }}</td>
                    <td class="text-muted small">{{ $item->entry_time->format('d/m H:i') }}</td>
                    <td class="text-muted small">{{ $item->exit_time ? $item->exit_time->format('d/m H:i') : '-' }}</td>
                    <td><span class="text-info">{{ $item->formatted_duration }}</span></td>
                    <td><strong style="color: var(--neon-orange);">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</strong></td>
                    <td class="text-end">
                        <a href="{{ route('checkout.receipt', $item->ticket_code) }}" class="btn btn-outline-custom btn-sm py-1 px-2" title="Cetak Struk">
                            <i class="bi bi-receipt"></i> Struk
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-3 text-muted">Belum ada transaksi keluar hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<!-- HTML5-QRCode Scanner Library CDN -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode = null;
    let isScanning = false;

    const toggleBtn = document.getElementById('toggleScannerBtn');
    const wrapper = document.getElementById('scannerWrapper');
    const placeholder = document.getElementById('scannerPlaceholder');
    const statusText = document.getElementById('scannerStatus');

    toggleBtn.addEventListener('click', function() {
        if (isScanning) {
            stopScanner();
        } else {
            startScanner();
        }
    });

    function startScanner() {
        wrapper.style.display = 'block';
        placeholder.style.display = 'none';
        toggleBtn.innerHTML = '<i class="bi bi-stop-circle me-1"></i> Matikan Kamera';
        toggleBtn.classList.replace('btn-outline-custom', 'btn-danger');

        html5QrCode = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText, decodedResult) => {
                // Berhasil scan!
                statusText.className = "alert alert-success small py-2 text-center text-success mb-0";
                statusText.innerHTML = `<strong>QR Terdeteksi:</strong> ${decodedText}. Memeriksa tiket...`;
                
                stopScanner();
                // Redirect ke URL pencarian
                window.location.href = `{{ route('checkout.index') }}?q=${encodeURIComponent(decodedText)}`;
            },
            (errorMessage) => {
                // scan failure, ignore for continuous scanning
            }
        ).then(() => {
            isScanning = true;
        }).catch(err => {
            statusText.className = "alert alert-danger small py-2 text-center text-danger mb-0";
            statusText.innerHTML = `Gagal mengakses kamera: ${err}`;
            stopScanner();
        });
    }

    function stopScanner() {
        if (html5QrCode && isScanning) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                isScanning = false;
                wrapper.style.display = 'none';
                placeholder.style.display = 'block';
                toggleBtn.innerHTML = '<i class="bi bi-power me-1"></i> Nyalakan Kamera';
                toggleBtn.classList.replace('btn-danger', 'btn-outline-custom');
            }).catch(err => console.error(err));
        } else {
            wrapper.style.display = 'none';
            placeholder.style.display = 'block';
            toggleBtn.innerHTML = '<i class="bi bi-power me-1"></i> Nyalakan Kamera';
            toggleBtn.classList.replace('btn-danger', 'btn-outline-custom');
        }
    }

    function toggleLost(checkbox) {
        const url = new URL(window.location.href);
        if (checkbox.checked) {
            url.searchParams.set('is_lost', '1');
        } else {
            url.searchParams.delete('is_lost');
        }
        window.location.href = url.toString();
    }
</script>
@endpush
@endsection
