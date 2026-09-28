@extends('layouts.app')

@section('title', 'Tiket Parkir ' . $ticket->ticket_code . ' - FILKOM UB')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <!-- Ticket Container -->
        <div class="ticket-paper text-center mb-4">
            <div class="border-bottom pb-3 mb-3">
                <h4 class="fw-bold text-dark mb-0">🅿️ PARKIRAN FILKOM UB</h4>
                <p class="text-secondary small mb-0">Fakultas Ilmu Komputer Universitas Brawijaya</p>
                <span class="badge bg-dark text-warning font-monospace mt-1 px-3 py-1">TIKET PARKIR DIGITAL</span>
            </div>

            <!-- QR Code Container -->
            <div class="my-3 p-3 d-inline-block bg-white rounded shadow-sm border">
                {!! $ticket->qr_code_svg !!}
            </div>

            <!-- Ticket Code -->
            <div class="font-monospace fw-bold fs-4 text-dark letter-spacing-1 mb-2">
                {{ $ticket->ticket_code }}
            </div>

            <!-- Big License Plate Display -->
            <div class="my-2">
                <span class="ticket-plate">
                    {{ $ticket->plate_number }}
                </span>
            </div>

            <!-- Vehicle Type & Slot Information -->
            <div class="row g-2 my-3 text-start small">
                <div class="col-6">
                    <span class="text-muted d-block">Jenis Kendaraan:</span>
                    <strong class="text-dark fs-6">{{ $ticket->vehicle_type }}</strong>
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted d-block">Alokasi Slot:</span>
                    <strong class="text-success fs-6">{{ $ticket->slot->slot_code ?? '-' }} ({{ $ticket->area->name ?? '-' }})</strong>
                </div>
                <div class="col-12 mt-2 pt-2 border-top">
                    <span class="text-muted d-block">Waktu Masuk:</span>
                    <strong class="text-dark">{{ $ticket->entry_time->translatedFormat('l, d F Y - H:i:s') }} WIB</strong>
                </div>
                <div class="col-12">
                    <span class="text-muted d-block">Petugas Masuk:</span>
                    <span class="text-secondary">{{ $ticket->operatorIn->name ?? 'Sistem Otomatis' }}</span>
                </div>
            </div>

            <div class="alert alert-warning py-2 small mb-0 border-0 text-start">
                <i class="bi bi-info-circle me-1"></i> <strong>Perhatian:</strong> Simpan tiket ini baik-baik. Tunjukkan atau scan QR Code saat keluar gerbang. Denda tiket hilang berlaku sesuai ketentuan.
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex flex-column gap-2 no-print">
            <a href="{{ route('checkin.print', $ticket->ticket_code) }}" target="_blank" class="btn btn-neon-green btn-lg fw-bold d-flex justify-content-center align-items-center gap-2">
                <i class="bi bi-printer-fill fs-5"></i> Cetak Tiket Parkir
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('checkin.index') }}" class="btn btn-outline-custom flex-grow-1">
                    <i class="bi bi-plus-circle me-1"></i> Input Kendaraan Lain
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-custom flex-grow-1">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
