@extends('layouts.app')

@section('title', 'Dashboard Ketersediaan Slot Parkir - FILKOM UB')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span>Dashboard Monitoring Parkir</span>
            <span class="badge badge-green pulse-active small fs-6">Live Status</span>
        </h2>
        <p class="text-muted mb-0">Pantau ketersediaan slot kendaraan motor & mobil Fakultas Ilmu Komputer secara real-time.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('dashboard.slots') }}" class="btn btn-outline-custom d-flex align-items-center gap-2">
            <i class="bi bi-grid-3x3-gap-fill text-info"></i> Denah Slot Visual
        </a>
        <a href="{{ route('checkin.index') }}" class="btn btn-neon-green d-flex align-items-center gap-2">
            <i class="bi bi-plus-circle-fill"></i> Kendaraan Masuk
        </a>
    </div>
</div>

<!-- Quick Statistics Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-box green h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted small fw-semibold text-uppercase">Slot Kosong</span>
                <i class="bi bi-check2-circle fs-4" style="color: var(--neon-green);"></i>
            </div>
            <div class="number my-1" id="stat-available">{{ $totalAvailable }}</div>
            <span class="badge badge-green">Siap ditempati</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-box red h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted small fw-semibold text-uppercase">Slot Terisi</span>
                <i class="bi bi-car-front-fill fs-4" style="color: var(--neon-red);"></i>
            </div>
            <div class="number my-1" id="stat-occupied">{{ $totalOccupied }}</div>
            <span class="badge badge-red">Sedang parkir</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-box blue h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted small fw-semibold text-uppercase">Total Kapasitas</span>
                <i class="bi bi-pie-chart-fill fs-4" style="color: var(--neon-blue);"></i>
            </div>
            <div class="number my-1" id="stat-capacity">{{ $totalCapacity }}</div>
            <span class="badge badge-blue">Seluruh area</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-box orange h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted small fw-semibold text-uppercase">Pendapatan Hari Ini</span>
                <i class="bi bi-cash-stack fs-4" style="color: var(--neon-orange);"></i>
            </div>
            <div class="number my-1 fs-3">Rp {{ number_format($todayIncome, 0, ',', '.') }}</div>
            <span class="badge badge-orange">{{ $todayOutCount }} Transaksi Selesai</span>
        </div>
    </div>
</div>

<!-- Parking Areas Overview Cards -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0 text-white"><i class="bi bi-buildings me-2 text-warning"></i>Ketersediaan Berdasarkan Area</h4>
            <span class="text-muted small">Diperbarui otomatis dari database MySQL</span>
        </div>
    </div>
    
    @foreach($areas as $area)
    <div class="col-md-4 mb-3">
        <div class="card-dark p-3 h-100 position-relative">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h5 class="fw-bold text-white mb-0">{{ $area->name }}</h5>
                    <span class="text-muted small">
                        @if($area->vehicle_type === 'Motor')
                            <i class="bi bi-bicycle text-warning me-1"></i> Khusus Motor
                        @else
                            <i class="bi bi-car-front-fill text-info me-1"></i> Khusus Mobil
                        @endif
                        &middot; {{ $area->code }}
                    </span>
                </div>
                <span class="badge-custom {{ $area->status_color === 'danger' ? 'badge-red' : ($area->status_color === 'warning' ? 'badge-orange' : 'badge-green') }}">
                    {{ $area->status_label }}
                </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="my-3">
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Okupansi</span>
                    <strong class="text-light">{{ $area->occupancy_percentage }}%</strong>
                </div>
                <div class="progress-dark">
                    <div class="h-100 {{ $area->occupancy_percentage >= 100 ? 'progress-bar-red' : ($area->occupancy_percentage >= 80 ? 'progress-bar-orange' : 'progress-bar-neon') }}" 
                         style="width: {{ $area->occupancy_percentage }}%"></div>
                </div>
            </div>
            
            <!-- Slot Breakdown -->
            <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary">
                <div>
                    <span class="text-muted small d-block">Slot Kosong</span>
                    <strong class="fs-5" style="color: var(--neon-green);">{{ $area->available_count }}</strong>
                </div>
                <div>
                    <span class="text-muted small d-block text-center">Terisi</span>
                    <strong class="fs-5 text-center d-block" style="color: var(--neon-red);">{{ $area->occupied_count }}</strong>
                </div>
                <div>
                    <span class="text-muted small d-block text-end">Total Kapasitas</span>
                    <strong class="fs-5 text-end d-block text-light">{{ $area->capacity }}</strong>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('dashboard.slots', ['area_id' => $area->id]) }}" class="btn btn-outline-custom btn-sm w-100 text-center">
                    <i class="bi bi-eye me-1"></i> Lihat Slot Detail
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Recent Vehicle Activity Table -->
<div class="card-dark overflow-hidden">
    <div class="card-header-dark d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-info"></i> Aktivitas Kendaraan Terbaru
        </h5>
        <a href="{{ route('checkin.index') }}" class="btn btn-neon-green btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Masuk Baru
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-dark-custom">
            <thead>
                <tr>
                    <th>Kode Tiket</th>
                    <th>Plat Nomor</th>
                    <th>Jenis</th>
                    <th>Area & Slot</th>
                    <th>Waktu Masuk</th>
                    <th>Durasi Saat Ini</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTickets as $ticket)
                <tr>
                    <td>
                        <a href="{{ route('checkin.show', $ticket->ticket_code) }}" class="fw-bold font-monospace text-decoration-none" style="color: var(--neon-green);">
                            {{ $ticket->ticket_code }}
                        </a>
                    </td>
                    <td>
                        <span class="font-monospace fw-bold text-light px-2 py-1 rounded bg-black border border-secondary">
                            {{ $ticket->plate_number }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $ticket->vehicle_type === 'Motor' ? 'bg-secondary' : 'bg-primary' }}">
                            {{ $ticket->vehicle_type }}
                        </span>
                    </td>
                    <td>
                        {{ $ticket->area->name ?? '-' }}
                        <span class="badge bg-dark border border-secondary text-warning ms-1">
                            Slot {{ $ticket->slot->slot_code ?? '-' }}
                        </span>
                    </td>
                    <td class="text-muted small">
                        {{ $ticket->entry_time->translatedFormat('d M H:i') }}
                    </td>
                    <td>
                        <span class="text-info fw-semibold">{{ $ticket->formatted_duration }}</span>
                    </td>
                    <td>
                        @if($ticket->status === 'active')
                            <span class="badge badge-green">Sedang Parkir</span>
                        @elseif($ticket->status === 'paid')
                            <span class="badge bg-secondary">Selesai Keluar</span>
                        @else
                            <span class="badge badge-orange">Tiket Hilang</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($ticket->status === 'active')
                            <a href="{{ route('checkout.index', ['q' => $ticket->ticket_code]) }}" class="btn btn-neon-orange btn-sm py-1 px-2" title="Proses Keluar">
                                <i class="bi bi-box-arrow-right me-1"></i> Keluar
                            </a>
                        @else
                            <a href="{{ route('checkout.receipt', $ticket->ticket_code) }}" class="btn btn-outline-custom btn-sm py-1 px-2" title="Lihat Struk">
                                <i class="bi bi-receipt"></i> Struk
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada aktivitas kendaraan tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
