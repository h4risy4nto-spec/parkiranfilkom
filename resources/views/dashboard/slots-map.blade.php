@extends('layouts.app')

@section('title', 'Peta Slot Parkir Visual - FILKOM UB')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="stat-icon-wrapper stat-icon-blue" style="width: 42px; height: 42px; border-radius: 10px;">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </div>
        <div>
            <h2 class="page-title mb-1" style="font-size: 1.55rem;">Peta Slot Parkir Visual</h2>
            <p class="page-subtitle">Lihat lokasi fisik dan status keterisian setiap nomor slot parkir di area FILKOM.</p>
        </div>
    </div>
    <div>
        <a href="{{ route('dashboard') }}" class="btn btn-ps-outline btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Area Selector Tabs -->
<div class="ps-card p-3 mb-4">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted small fw-semibold me-2"><i class="bi bi-funnel text-primary"></i> Pilih Area:</span>
        @foreach($areas as $area)
            <a href="{{ route('dashboard.slots', ['area_id' => $area->id]) }}" 
               class="btn {{ ($selectedArea && $selectedArea->id === $area->id) ? 'btn-ps-primary' : 'btn-ps-outline' }} btn-sm px-3">
                <i class="bi {{ $area->vehicle_type === 'Motor' ? 'bi-bicycle' : 'bi-car-front-fill' }} me-1"></i>
                {{ $area->name }} ({{ $area->available_count }} kosong)
            </a>
        @endforeach
    </div>
</div>

@if($selectedArea)
<div class="ps-card p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-4 border-bottom border-secondary">
        <div>
            <h4 class="fw-bold text-white mb-1">{{ $selectedArea->name }} ({{ $selectedArea->code }})</h4>
            <p class="text-muted small mb-0">
                <i class="bi bi-geo-alt text-warning me-1"></i> {{ $selectedArea->location_description ?? 'Area Parkir Fakultas Ilmu Komputer' }} &middot;
                Khusus <strong>{{ $selectedArea->vehicle_type }}</strong> &middot; Total <strong>{{ $selectedArea->capacity }} Slot</strong>
            </p>
        </div>
        <div class="d-flex gap-3 align-items-center mt-3 mt-sm-0">
            <div class="d-flex align-items-center gap-2 small">
                <span class="d-inline-block rounded" style="width: 14px; height: 14px; background: rgba(0, 255, 136, 0.2); border: 1px solid var(--neon-green);"></span>
                <span class="text-light">Tersedia ({{ $selectedArea->available_count }})</span>
            </div>
            <div class="d-flex align-items-center gap-2 small">
                <span class="d-inline-block rounded" style="width: 14px; height: 14px; background: rgba(255, 51, 102, 0.2); border: 1px solid var(--neon-red);"></span>
                <span class="text-light">Terisi ({{ $selectedArea->occupied_count }})</span>
            </div>
        </div>
    </div>

    <!-- Parking Grid Representation -->
    <div class="p-3 rounded" style="background: #090d13; border: 1px dashed var(--border-color);">
        <div class="slot-grid">
            @foreach($selectedArea->slots as $slot)
                <div class="slot-item {{ $slot->is_occupied ? 'occupied' : 'available' }}" 
                     data-bs-toggle="tooltip" 
                     data-bs-placement="top" 
                     title="{{ $slot->is_occupied ? 'Terisi: ' . ($slot->activeTicket->plate_number ?? 'Kendaraan') : 'Slot Tersedia' }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold">{{ $slot->slot_code }}</span>
                        @if($slot->is_occupied)
                            <i class="bi bi-lock-fill" style="color: var(--neon-red); font-size: 0.75rem;"></i>
                        @else
                            <i class="bi bi-check-circle-fill" style="color: var(--neon-green); font-size: 0.75rem;"></i>
                        @endif
                    </div>
                    
                    @if($slot->is_occupied && $slot->activeTicket)
                        <div class="font-monospace text-truncate text-light small px-1 rounded bg-black border border-secondary mt-1" style="font-size: 0.72rem;">
                            {{ $slot->activeTicket->plate_number }}
                        </div>
                    @else
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">KOSONG</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
</script>
@endpush
@endsection
