@extends('layouts.app')

@section('title', 'Kendaraan Masuk & Terbitkan Tiket QR - FILKOM UB')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span>Penerbitan Tiket Kendaraan Masuk</span>
            <span class="badge badge-green small fs-6">Gerbang Masuk</span>
        </h2>
        <p class="text-muted mb-0">Input plat nomor kendaraan untuk menerbitkan tiket parkir ber-QR Code dan mengalokasikan slot secara otomatis.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Form Kendaraan Masuk -->
    <div class="col-lg-7">
        <div class="card-dark p-4 h-100">
            <h4 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-ticket-perforated-fill text-warning"></i> Form Tiket Masuk
            </h4>
            
            <form action="{{ route('checkin.store') }}" method="POST">
                @csrf
                
                <!-- Plat Nomor -->
                <div class="mb-4">
                    <label class="form-label text-light fw-semibold small text-uppercase">
                        Plat Nomor Kendaraan <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-light fw-bold font-monospace">ID</span>
                        <input type="text" 
                               name="plate_number" 
                               id="plate_number"
                               class="form-control form-control-lg form-control-dark font-monospace text-uppercase fw-bold fs-4 @error('plate_number') is-invalid @enderror" 
                               placeholder="N 1234 AB" 
                               value="{{ old('plate_number') }}" 
                               required 
                               autocomplete="off"
                               autofocus>
                    </div>
                    <small class="text-muted">Contoh: N 1234 AB, B 5678 CD, L 9999 EF</small>
                    @error('plate_number')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Jenis Kendaraan -->
                <div class="mb-4">
                    <label class="form-label text-light fw-semibold small text-uppercase">
                        Jenis Kendaraan <span class="text-danger">*</span>
                    </label>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="card-dark p-3 d-block text-center cursor-pointer border-secondary h-100" style="cursor: pointer;">
                                <input type="radio" name="vehicle_type" value="Motor" class="form-check-input me-2" {{ old('vehicle_type', 'Motor') === 'Motor' ? 'checked' : '' }} onchange="filterAreas()">
                                <i class="bi bi-bicycle fs-2 d-block my-1 text-warning"></i>
                                <strong class="d-block text-white">Sepeda Motor</strong>
                                <span class="text-muted small">Rp 2.000 / jam</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="card-dark p-3 d-block text-center cursor-pointer border-secondary h-100" style="cursor: pointer;">
                                <input type="radio" name="vehicle_type" value="Mobil" class="form-check-input me-2" {{ old('vehicle_type') === 'Mobil' ? 'checked' : '' }} onchange="filterAreas()">
                                <i class="bi bi-car-front-fill fs-2 d-block my-1 text-info"></i>
                                <strong class="d-block text-white">Mobil</strong>
                                <span class="text-muted small">Rp 5.000 / jam</span>
                            </label>
                        </div>
                    </div>
                    @error('vehicle_type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Pilihan Area Parkir -->
                <div class="mb-4">
                    <label class="form-label text-light fw-semibold small text-uppercase">
                        Pilihan Area Parkir
                    </label>
                    <select name="parking_area_id" id="parking_area_id" class="form-select form-select-lg form-select-dark">
                        <option value="">-- Alokasikan Otomatis (Rekomendasi Slot Terdekat) --</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" 
                                    data-type="{{ $area->vehicle_type }}" 
                                    {{ old('parking_area_id') == $area->id ? 'selected' : '' }}
                                    {{ $area->available_count == 0 ? 'disabled' : '' }}>
                                {{ $area->name }} ({{ $area->vehicle_type }}) — Sisa {{ $area->available_count }} slot kosong
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Biarkan kosong agar sistem otomatis mencarikan slot terdekat yang masih kosong.</small>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="btn btn-neon-green btn-lg w-100 fw-bold d-flex justify-content-center align-items-center gap-2 py-3 shadow">
                        <i class="bi bi-qr-code fs-4"></i>
                        <span>TERBITKAN TIKET PARKIR & QR CODE</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar Info & Sisa Slot -->
    <div class="col-lg-5">
        <!-- Ketersediaan Area Saat Ini -->
        <div class="card-dark p-4 mb-4">
            <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-p-square-fill text-success"></i> Sisa Slot Parkir Aktif
            </h5>
            <div class="list-group list-group-flush bg-transparent">
                @foreach($areas as $area)
                <div class="list-group-item bg-transparent text-light border-secondary px-0 py-2 d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-block">{{ $area->name }}</strong>
                        <span class="text-muted small">{{ $area->vehicle_type }} &middot; Kapasitas {{ $area->capacity }}</span>
                    </div>
                    <div>
                        @if($area->available_count > 0)
                            <span class="badge badge-green">{{ $area->available_count }} Kosong</span>
                        @else
                            <span class="badge badge-red">Penuh</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Kendaraan Masuk Terakhir -->
        <div class="card-dark p-4">
            <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-info"></i> Baru Saja Masuk
            </h5>
            @if($recentEntries->count() > 0)
                <div class="list-group list-group-flush bg-transparent">
                    @foreach($recentEntries as $entry)
                    <div class="list-group-item bg-transparent text-light border-secondary px-0 py-2 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="font-monospace fw-bold text-white px-2 py-1 rounded bg-black border border-secondary small">
                                {{ $entry->plate_number }}
                            </span>
                            <span class="text-muted small ms-2">{{ $entry->area->name ?? '' }} ({{ $entry->slot->slot_code ?? '' }})</span>
                        </div>
                        <a href="{{ route('checkin.show', $entry->ticket_code) }}" class="btn btn-outline-custom btn-sm py-1 px-2" title="Lihat Tiket">
                            <i class="bi bi-qr-code"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted small mb-0">Belum ada kendaraan masuk.</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterAreas() {
        const selectedType = document.querySelector('input[name="vehicle_type"]:checked').value;
        const select = document.getElementById('parking_area_id');
        const options = select.querySelectorAll('option');

        options.forEach(opt => {
            if (!opt.value) return; // Keep auto option
            const optType = opt.getAttribute('data-type');
            if (optType === selectedType) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
                if (opt.selected) {
                    select.value = '';
                }
            }
        });
    }

    // Run on load
    filterAreas();
</script>
@endpush
@endsection
