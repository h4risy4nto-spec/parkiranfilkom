@extends('layouts.app')

@section('title', 'Struk Pembayaran Parkir - ' . $ticket->ticket_code)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="ticket-paper text-center mb-4">
            <div class="border-bottom pb-3 mb-3">
                <span class="badge bg-success text-dark font-monospace mb-1 px-3 py-1">TRANSAKSI SELESAI / LUNAS</span>
                <h4 class="fw-bold text-dark mb-0">🅿️ PARKIRAN FILKOM UB</h4>
                <p class="text-secondary small mb-0">Bukti Pembayaran & Keluar Parkir</p>
            </div>

            <!-- Plate Number -->
            <div class="my-2">
                <span class="ticket-plate">
                    {{ $ticket->plate_number }}
                </span>
            </div>

            <div class="font-monospace fw-bold text-secondary small mb-3">
                {{ $ticket->ticket_code }}
            </div>

            <!-- Receipt Breakdown -->
            <div class="meta text-start small border-top border-bottom py-2 my-3">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Jenis Kendaraan:</span>
                    <strong class="text-dark">{{ $ticket->vehicle_type }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Area Parkir:</span>
                    <strong class="text-dark">{{ $ticket->area->name ?? '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Waktu Masuk:</span>
                    <span class="text-dark">{{ $ticket->entry_time->translatedFormat('d M Y - H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Waktu Keluar:</span>
                    <span class="text-dark">{{ $ticket->exit_time ? $ticket->exit_time->translatedFormat('d M Y - H:i') : '-' }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Durasi Parkir:</span>
                    <strong class="text-primary">{{ $ticket->formatted_duration }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Tarif:</span>
                    <span class="text-dark">Rp {{ number_format($ticket->hourly_rate, 0, ',', '.') }} / jam</span>
                </div>
                @if($ticket->status === 'lost')
                <div class="d-flex justify-content-between py-1 text-danger">
                    <span>Keterangan:</span>
                    <strong>Denda Tiket Hilang</strong>
                </div>
                @endif
                <div class="d-flex justify-content-between pt-2 border-top mt-1 align-items-center">
                    <strong class="text-dark fs-6">TOTAL DIBAYAR:</strong>
                    <strong class="text-dark fs-4 font-monospace">Rp {{ number_format($ticket->total_amount, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="small text-muted mb-2">
                Petugas Keluar: {{ $ticket->operatorOut->name ?? 'Petugas Gerbang' }}
            </div>

            <p class="text-secondary small mb-0 fst-italic">
                Terima kasih atas kunjungan Anda di Fakultas Ilmu Komputer Universitas Brawijaya. Hati-hati di jalan!
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex flex-column gap-2 no-print">
            <button onclick="window.print()" class="btn btn-neon-green btn-lg fw-bold d-flex justify-content-center align-items-center gap-2">
                <i class="bi bi-printer-fill fs-5"></i> Cetak Struk Bukti Bayar
            </button>
            <div class="d-flex gap-2">
                <a href="{{ route('checkout.index') }}" class="btn btn-outline-custom flex-grow-1">
                    <i class="bi bi-qr-code-scan me-1"></i> Scan Tiket Lain
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-custom flex-grow-1">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
