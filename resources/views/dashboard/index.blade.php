@extends('layouts.app')

@section('title', 'Dashboard - ParkSafe')

@section('content')
<!-- Page Header -->
<div class="page-header-row">
    <div>
        <h2 class="page-title text-white">Dashboard</h2>
        <p class="page-subtitle text-slate-light">Selamat datang, Admin! Berikut adalah ringkasan aktivitas dan ketersediaan slot parkir hari ini.</p>
    </div>
    <div class="date-time-chip">
        <i class="bi bi-calendar3 text-primary"></i>
        <span id="liveClockText" class="text-white fw-semibold">Senin, 6 Oktober 2025 | 10:24 WIB</span>
    </div>
</div>

<!-- 4 Top Metric Cards (High Contrast & Clean) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Slot -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-wrapper stat-icon-blue">
                        <i class="bi bi-p-square-fill"></i>
                    </div>
                    <div class="stat-label-wrap">
                        <span class="stat-label text-slate-light">Total Kapasitas Slot</span>
                        <div class="stat-value text-white">
                            {{ $totalCapacity > 0 ? $totalCapacity : 45 }}
                        </div>
                    </div>
                </div>
            </div>
            <div>
                @php
                    $pctAvail = $totalCapacity > 0 ? round(($totalAvailable / $totalCapacity) * 100) : 75;
                    $pctOcc = 100 - $pctAvail;
                @endphp
                <div class="stat-bar stat-bar-dual">
                    <div class="stat-bar-green" style="width: {{ $pctAvail }}%;"></div>
                    <div class="stat-bar-red" style="width: {{ $pctOcc }}%;"></div>
                </div>
                <div class="stat-subtext text-slate-light mt-2 d-flex justify-content-between">
                    <span>Tersedia: <strong class="text-success fw-bold">{{ $totalAvailable }}</strong></span>
                    <span>Terisi: <strong class="text-danger fw-bold">{{ $totalOccupied }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Kendaraan Masuk -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-wrapper stat-icon-green">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </div>
                    <div class="stat-label-wrap">
                        <span class="stat-label text-slate-light">Kendaraan Masuk</span>
                        <div class="stat-value text-white">
                            {{ $todayInCount }}
                            <span class="stat-badge stat-badge-green"><i class="bi bi-arrow-up-short"></i>12%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="stat-bar">
                    <div class="stat-bar-green" style="width: 78%;"></div>
                </div>
                <div class="stat-subtext text-slate-light mt-2">
                    Total transaksi masuk hari ini
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Kendaraan Keluar -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-wrapper stat-icon-red">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <div class="stat-label-wrap">
                        <span class="stat-label text-slate-light">Kendaraan Keluar</span>
                        <div class="stat-value text-white">
                            {{ $todayOutCount }}
                            <span class="stat-badge stat-badge-red"><i class="bi bi-arrow-up-short"></i>8%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="stat-bar">
                    <div class="stat-bar-red" style="width: 42%;"></div>
                </div>
                <div class="stat-subtext text-slate-light mt-2">
                    Total transaksi keluar hari ini
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Rata-rata Durasi Parkir -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-wrapper stat-icon-purple">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-label-wrap">
                        <span class="stat-label text-slate-light">Rata-rata Durasi</span>
                        <div class="stat-value text-white">
                            {{ $avgDurationFormatted }}
                            <span class="stat-badge stat-badge-green"><i class="bi bi-arrow-up-short"></i>15%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="stat-bar">
                    <div class="stat-bar-purple" style="width: 60%;"></div>
                </div>
                <div class="stat-subtext text-slate-light mt-2">
                    Rata-rata waktu parkir kendaraan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Middle Section: 2-Column Layout (Peta Lokasi Interaktif di Kiri, Tabel Kendaraan di Kanan) -->
<div class="row g-3 mb-4">
    <!-- Kolom Kiri: Peta Lokasi Parkir dengan Dropdown Pemilih Area & Kotak Slot Real-Time -->
    <div class="col-12 col-xl-7 col-lg-7">
        <div class="ps-card h-100">
            <!-- Header Peta & Dropdown Pemilih Area -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-3 border-bottom border-secondary border-opacity-25">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-primary fs-5"></i>
                    <h3 class="ps-card-title mb-0">Peta Lokasi Parkir</h3>
                </div>

                <!-- Dropdown Pemilih Area Parkir -->
                <div class="d-flex align-items-center gap-2">
                    <label for="parkingAreaSelect" class="small fw-bold text-slate-light mb-0 d-none d-sm-inline">
                        <i class="bi bi-funnel text-primary me-1"></i>Pilih Area:
                    </label>
                    <select id="parkingAreaSelect" class="form-select form-select-sm" style="background-color: #081224; color: #ffffff; border: 1.5px solid #1f3454; font-weight: 700; border-radius: 8px; width: auto; min-width: 210px;">
                        @foreach($areasData as $ad)
                            <option value="{{ $ad['id'] }}">
                                {{ $ad['name'] }} ({{ $ad['vehicle_type'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Area Meta & Summary Badges -->
            <div class="area-meta-bar mb-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="badge text-white px-2 py-1" style="background: rgba(29, 112, 243, 0.2); border: 1px solid #1d70f3;">
                        <i id="metaVehicleIcon" class="bi bi-bicycle me-1"></i>
                        <span id="metaVehicleType">Motor</span>
                    </span>
                    <span class="small text-slate-light">
                        Kapasitas: <strong class="text-white" id="metaCapacity">20</strong> Slot
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                        <i class="bi bi-check-circle me-1"></i>Tersedia: <strong id="metaAvailable">16</strong>
                    </span>
                    <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                        <i class="bi bi-slash-circle me-1"></i>Terisi: <strong id="metaOccupied">4</strong>
                    </span>
                </div>

                <!-- Legend Keterangan Warna Slot -->
                <div class="d-flex align-items-center gap-3 small">
                    <span class="d-flex align-items-center gap-1 text-slate-light" style="font-size: 0.78rem;">
                        <span style="width: 10px; height: 10px; border-radius: 3px; background-color: var(--accent-green); display: inline-block;"></span> Tersedia
                    </span>
                    <span class="d-flex align-items-center gap-1 text-slate-light" style="font-size: 0.78rem;">
                        <span style="width: 10px; height: 10px; border-radius: 3px; background-color: var(--accent-red); display: inline-block;"></span> Terisi
                    </span>
                </div>
            </div>

            <!-- Visual Parking Lot Area Map Container -->
            <div class="parking-map-container" style="background: #08101d; border-color: #1a2a44;">
                <!-- Entrance Indicator -->
                <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                    <div class="small text-slate-light" id="metaLocation">
                        <i class="bi bi-info-circle text-info me-1"></i>
                        <span>Samping Gedung F (Pascasarjana & Lab FILKOM)</span>
                    </div>
                    <div class="gate-arrow text-success">
                        <span>Pintu Masuk</span>
                        <i class="bi bi-arrow-down-circle-fill fs-6"></i>
                    </div>
                </div>

                <!-- Dynamic Grid Slot Boxes (Rendered by JS based on area capacity) -->
                <div class="dynamic-slot-grid" id="slotGridContainer">
                    <!-- Slot boxes populated dynamically -->
                </div>

                <!-- Exit Indicator -->
                <div class="d-flex justify-content-end align-items-center mt-2 px-1">
                    <div class="gate-arrow text-danger">
                        <span>Pintu Keluar</span>
                        <i class="bi bi-arrow-up-circle-fill fs-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel Kendaraan Parkir (Clean, High Contrast, Tanpa Glitch Judul Samping) -->
    <div class="col-12 col-xl-5 col-lg-5">
        <div class="ps-card h-100 d-flex flex-column justify-content-between">
            <div class="table-responsive flex-grow-1">
                <table class="ps-table">
                    <thead>
                        <tr>
                            <th style="width: 38px;">NO</th>
                            <th>PLAT NOMOR</th>
                            <th>JENIS</th>
                            <th>MASUK</th>
                            <th>DURASI</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTickets as $idx => $item)
                        <tr>
                            <td class="fw-bold" style="color: #cbd5e1;">{{ $idx + 1 }}</td>
                            <td>
                                <span class="plate-badge">{{ $item->plate_number }}</span>
                            </td>
                            <td>
                                <span class="badge text-white fw-semibold" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.18);">
                                    <i class="bi {{ $item->vehicle_type === 'Mobil' ? 'bi-car-front-fill' : 'bi-bicycle' }} me-1 text-info"></i>{{ $item->vehicle_type }}
                                </span>
                            </td>
                            <td class="fw-semibold" style="color: #e2e8f0;">
                                {{ $item->entry_time ? $item->entry_time->format('H:i') : '09:00' }}
                            </td>
                            <td class="fw-bold" style="color: #38bdf8;">
                                {{ $item->formatted_duration ?? '1j 30m' }}
                            </td>
                            <td>
                                <span class="status-pill status-pill-green">
                                    <i class="bi bi-check-circle-fill"></i> Aktif
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-slate-light">
                                <i class="bi bi-inbox fs-3 d-block mb-1 opacity-50"></i>
                                Belum ada kendaraan yang sedang parkir saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-3 border-top border-secondary border-opacity-25 mt-2">
                <a href="{{ route('admin.tickets.active') }}" class="btn btn-sm btn-ps-outline w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-card-list"></i>
                    <span>Buka Seluruh Kendaraan Aktif</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Scan QR & Ringkasan Hari Ini Section -->
<div class="row g-3 mb-4">
    <!-- Card Scan QR Masuk / Keluar -->
    <div class="col-12 col-md-5 col-lg-4">
        <div class="ps-card text-center h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h3 class="ps-card-title" style="font-size: 1rem;">
                        <i class="bi bi-qr-code text-primary"></i>
                        <span>Scan QR Masuk / Keluar</span>
                    </h3>
                </div>

                <!-- Viewfinder Graphic with Corner Accents -->
                <div class="qr-viewfinder-box mt-3" data-bs-toggle="modal" data-bs-target="#qrScanModal" title="Klik untuk membuka Kamera / Drag Foto">
                    <div class="qr-corner tl"></div>
                    <div class="qr-corner tr"></div>
                    <div class="qr-corner bl"></div>
                    <div class="qr-corner br"></div>
                    <i class="bi bi-qr-code fs-1 text-primary opacity-75"></i>
                </div>

                <div class="fw-bold text-white small mb-1">Kamera / File QR Code</div>
                <p class="text-slate-light small mb-3" style="font-size: 0.8rem;">
                    Arahkan kamera ke QR Code tiket di gerbang, atau drag foto tiket untuk verifikasi.
                </p>
            </div>

            <button type="button" class="btn btn-ps-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#qrScanModal">
                <i class="bi bi-camera me-1"></i> Buka Scanner QR
            </button>
        </div>
    </div>

    <!-- Ringkasan Hari Ini Card -->
    <div class="col-12 col-md-7 col-lg-8">
        <div class="ps-card h-100">
            <div class="ps-card-header mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                <h3 class="ps-card-title" style="font-size: 1rem;">
                    <i class="bi bi-calendar-check text-warning"></i>
                    <span>Ringkasan Aktivitas Parkir Hari Ini</span>
                </h3>
            </div>
            
            <div class="row g-3">
                <div class="col-6 col-sm-4">
                    <div class="p-3 rounded" style="background: #091222; border: 1px solid #1a2a44;">
                        <span class="text-slate-light small d-block mb-1"><i class="bi bi-car-front me-1 text-primary"></i>Total Kapasitas</span>
                        <strong class="text-white fs-5">{{ $totalCapacity > 0 ? $totalCapacity : 45 }}</strong> <span class="small text-slate-light">Slot</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="p-3 rounded" style="background: #091222; border: 1px solid #1a2a44;">
                        <span class="text-slate-light small d-block mb-1"><i class="bi bi-box-arrow-in-right text-success me-1"></i>Kendaraan Masuk</span>
                        <strong class="text-success fs-5">{{ $todayInCount }}</strong> <span class="small text-slate-light">Hari ini</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="p-3 rounded" style="background: #091222; border: 1px solid #1a2a44;">
                        <span class="text-slate-light small d-block mb-1"><i class="bi bi-box-arrow-right text-danger me-1"></i>Kendaraan Keluar</span>
                        <strong class="text-danger fs-5">{{ $todayOutCount }}</strong> <span class="small text-slate-light">Hari ini</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="p-3 rounded" style="background: #091222; border: 1px solid #1a2a44;">
                        <span class="text-slate-light small d-block mb-1"><i class="bi bi-check2-circle text-info me-1"></i>Slot Tersedia</span>
                        <strong class="text-white fs-5">{{ $totalAvailable }}</strong> <span class="small text-slate-light">Slot Kosong</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="p-3 rounded" style="background: #091222; border: 1px solid #1a2a44;">
                        <span class="text-slate-light small d-block mb-1"><i class="bi bi-slash-circle text-warning me-1"></i>Slot Terisi</span>
                        <strong class="text-white fs-5">{{ $totalOccupied }}</strong> <span class="small text-slate-light">Slot Terpakai</span>
                    </div>
                </div>
                <div class="col-6 col-sm-4">
                    <div class="p-3 rounded" style="background: #091222; border: 1px solid #1a2a44;">
                        <span class="text-slate-light small d-block mb-1"><i class="bi bi-clock-history text-purple me-1" style="color: #a855f7;"></i>Rata Durasi</span>
                        <strong class="text-white fs-5">{{ $avgDurationFormatted }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: 4 Quick Navigation Cards -->
<div class="row g-3">
    <!-- Card 1: Kelola Slot Parkir -->
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('admin.areas.index') }}" class="quick-action-card">
            <div class="quick-action-icon" style="background-color: rgba(29, 112, 243, 0.15); color: var(--primary-blue);">
                <i class="bi bi-p-square"></i>
            </div>
            <div class="flex-grow-1">
                <div class="quick-action-title">Kelola Slot Parkir</div>
                <div class="quick-action-desc text-slate-light">Atur ketersediaan slot parkir</div>
            </div>
            <i class="bi bi-chevron-right text-slate-light small"></i>
        </a>
    </div>

    <!-- Card 2: Kendaraan Aktif -->
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('admin.tickets.active') }}" class="quick-action-card">
            <div class="quick-action-icon" style="background-color: rgba(16, 185, 129, 0.15); color: var(--accent-green);">
                <i class="bi bi-car-front"></i>
            </div>
            <div class="flex-grow-1">
                <div class="quick-action-title">Kendaraan Aktif</div>
                <div class="quick-action-desc text-slate-light">Lihat kendaraan di area parkir</div>
            </div>
            <i class="bi bi-chevron-right text-slate-light small"></i>
        </a>
    </div>

    <!-- Card 3: Laporan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('admin.history.index') }}" class="quick-action-card">
            <div class="quick-action-icon" style="background-color: rgba(139, 92, 246, 0.15); color: var(--accent-purple);">
                <i class="bi bi-bar-chart-line"></i>
            </div>
            <div class="flex-grow-1">
                <div class="quick-action-title">Laporan</div>
                <div class="quick-action-desc text-slate-light">Lihat laporan & aktivitas parkir</div>
            </div>
            <i class="bi bi-chevron-right text-slate-light small"></i>
        </a>
    </div>

    <!-- Card 4: Pengaturan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('admin.settings.index') }}" class="quick-action-card">
            <div class="quick-action-icon" style="background-color: rgba(6, 182, 212, 0.15); color: var(--accent-cyan);">
                <i class="bi bi-gear"></i>
            </div>
            <div class="flex-grow-1">
                <div class="quick-action-title">Pengaturan</div>
                <div class="quick-action-desc text-slate-light">Kelola akun dan sistem</div>
            </div>
            <i class="bi bi-chevron-right text-slate-light small"></i>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Data Area & Slot Parkir untuk Visual 2D Map Interaktif
    const PARKING_AREAS = @json($areasData ?? []);

    function renderParkingAreaMap(areaId) {
        const selectedArea = PARKING_AREAS.find(a => a.id == areaId) || PARKING_AREAS[0];
        if (!selectedArea) return;

        // Update Meta Elements
        const vehicleIcon = document.getElementById('metaVehicleIcon');
        const vehicleType = document.getElementById('metaVehicleType');
        const metaCapacity = document.getElementById('metaCapacity');
        const metaAvailable = document.getElementById('metaAvailable');
        const metaOccupied = document.getElementById('metaOccupied');
        const metaLocation = document.getElementById('metaLocation');

        if (vehicleIcon) {
            vehicleIcon.className = selectedArea.vehicle_type === 'Mobil' ? 'bi bi-car-front-fill me-1' : 'bi bi-bicycle me-1';
        }
        if (vehicleType) vehicleType.textContent = selectedArea.vehicle_type;
        if (metaCapacity) metaCapacity.textContent = selectedArea.capacity;
        if (metaAvailable) metaAvailable.textContent = selectedArea.available_count;
        if (metaOccupied) metaOccupied.textContent = selectedArea.occupied_count;
        if (metaLocation) {
            metaLocation.innerHTML = `<i class="bi bi-info-circle text-info me-1"></i><span>${selectedArea.location_description || 'Area Parkir FILKOM UB'}</span>`;
        }

        // Render Slot Boxes based on area's capacity
        const container = document.getElementById('slotGridContainer');
        if (!container) return;

        // Dispose previous tooltips if any
        container.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            const inst = bootstrap.Tooltip.getInstance(el);
            if (inst) inst.dispose();
        });

        container.innerHTML = '';

        selectedArea.slots.forEach(slot => {
            const box = document.createElement('div');
            const isOcc = slot.is_occupied;
            box.className = `slot-box ${isOcc ? 'occupied' : 'available'}`;

            const vehicleIconClass = slot.vehicle_type === 'Mobil' ? 'bi-car-front-fill' : 'bi-bicycle';
            const tooltipTitle = isOcc 
                ? `Slot ${slot.slot_code} | Terisi: ${slot.plate_number || 'Kendaraan'} (${slot.vehicle_type || selectedArea.vehicle_type})${slot.entry_time ? ' | Masuk: ' + slot.entry_time : ''}`
                : `Slot ${slot.slot_code} | Tersedia / Kosong`;

            box.setAttribute('data-bs-toggle', 'tooltip');
            box.setAttribute('data-bs-placement', 'top');
            box.setAttribute('title', tooltipTitle);

            box.innerHTML = `
                <div class="slot-box-code">${slot.slot_code}</div>
                <div class="slot-box-status">
                    <i class="bi ${isOcc ? vehicleIconClass : 'bi-check2'}"></i>
                    <span>${isOcc ? 'TERISI' : 'KOSONG'}</span>
                </div>
                ${isOcc && slot.plate_number ? `<div class="slot-box-plate">${slot.plate_number}</div>` : ''}
            `;

            container.appendChild(box);
        });

        // Re-initialize tooltips
        container.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const areaSelect = document.getElementById('parkingAreaSelect');
        if (areaSelect) {
            // Render first area on load
            renderParkingAreaMap(areaSelect.value);

            // Listen for area change
            areaSelect.addEventListener('change', function() {
                renderParkingAreaMap(this.value);
            });
        }
    });
</script>
@endpush
