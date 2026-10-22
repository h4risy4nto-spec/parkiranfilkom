@extends('layouts.app')

@section('title', 'Dashboard - ParkSafe')

@section('content')
<!-- Page Header -->
<div class="page-header-row">
    <div>
        <h2 class="page-title">Dashboard</h2>
        <p class="page-subtitle">Selamat datang, Admin! Berikut adalah ringkasan aktivitas parkir hari ini.</p>
    </div>
    <div class="date-time-chip">
        <i class="bi bi-calendar3"></i>
        <span id="liveClockText">Senin, 6 Oktober 2025 | 10:24 WIB</span>
    </div>
</div>

<!-- 4 Top Metric Cards (Screenshot 1 Match - NO PRICES / BILLING) -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Slot -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-card-top">
                    <div class="stat-icon-wrapper stat-icon-blue">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                    <div class="stat-label-wrap">
                        <span class="stat-label">Total Slot</span>
                        <div class="stat-value">
                            {{ $totalCapacity > 0 ? $totalCapacity : 120 }}
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <!-- Dual color progress bar (blue/purple) -->
                <div class="stat-bar stat-bar-dual">
                    @php
                        $pctAvail = $totalCapacity > 0 ? round(($totalAvailable / $totalCapacity) * 100) : 65;
                        $pctOcc = 100 - $pctAvail;
                    @endphp
                    <div class="stat-bar-blue" style="width: {{ $pctAvail }}%;"></div>
                    <div class="stat-bar-purple" style="width: {{ $pctOcc }}%;"></div>
                </div>
                <div class="stat-subtext">
                    Slot Parkir Tersedia: <strong class="text-white">{{ $totalAvailable }}</strong> &middot; Terisi: <strong class="text-white">{{ $totalOccupied }}</strong>
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
                        <span class="stat-label">Kendaraan Masuk</span>
                        <div class="stat-value">
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
                <div class="stat-subtext">
                    Hari Ini
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
                        <span class="stat-label">Kendaraan Keluar</span>
                        <div class="stat-value">
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
                <div class="stat-subtext">
                    Hari Ini
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
                        <span class="stat-label">Rata-rata Durasi Parkir</span>
                        <div class="stat-value">
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
                <div class="stat-subtext">
                    Dibanding kemarin
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Middle Section: 3-column Layout (Peta Lokasi, Kendaraan Aktif, Scan QR & Ringkasan) -->
<div class="row g-3 mb-4">
    <!-- Column 1: Peta Lokasi Parkir (Visual 2D Lot Map) -->
    <div class="col-12 col-lg-5">
        <div class="ps-card h-100">
            <div class="ps-card-header">
                <h3 class="ps-card-title">
                    <i class="bi bi-geo-alt-fill text-primary"></i>
                    <span>Peta Lokasi Parkir</span>
                </h3>
                <div class="d-flex align-items-center gap-3 small">
                    <span class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.76rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--accent-green); display: inline-block;"></span> Tersedia
                    </span>
                    <span class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.76rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--accent-red); display: inline-block;"></span> Terisi
                    </span>
                    <span class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.76rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--accent-cyan); display: inline-block;"></span> Maintenance
                    </span>
                </div>
            </div>

            <!-- 2D Interactive Parking Lot Visual Map -->
            <div class="parking-map-container">
                <!-- Entrance Indicator -->
                <div class="d-flex justify-content-end mb-2">
                    <div class="gate-arrow">
                        <span>Pintu Masuk</span>
                        <i class="bi bi-arrow-down-circle-fill text-success"></i>
                    </div>
                </div>

                <div class="parking-road-grid">
                    @php
                        $rows = [
                            'A' => ['red', 'green', 'green', 'red', 'green', 'green', 'green', 'green', 'red'],
                            'B' => ['green', 'green', 'green', 'green', 'red', 'green', 'green', 'green', 'green'],
                            'C' => ['green', 'red', 'red', 'green', 'green', 'red', 'green', 'green', 'green'],
                            'D' => ['green', 'red', 'green', 'green', 'green', 'green', 'green', 'green', 'green'],
                            'E' => ['green', 'green', 'green', 'green', 'green', 'red', 'green', 'green', 'green'],
                            'F' => ['green', 'green', 'green', 'red', 'green', 'red', 'green', 'green', 'green'],
                        ];
                    @endphp

                    @foreach($rows as $rowLetter => $slots)
                    <div class="parking-row">
                        <span class="parking-row-label">{{ $rowLetter }}</span>
                        <div class="parking-bays">
                            @foreach($slots as $idx => $st)
                            <div class="parking-bay {{ $st === 'red' ? 'occupied' : ($idx === 7 && $rowLetter === 'B' ? 'maintenance' : 'available') }}" 
                                 title="Slot {{ $rowLetter }}{{ $idx + 1 }} - {{ $st === 'red' ? 'Terisi' : 'Tersedia' }}">
                                <i class="bi {{ $st === 'red' ? 'bi-car-front-fill' : 'bi-dash' }}"></i>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Exit Indicator -->
                <div class="d-flex justify-content-end mt-2">
                    <div class="gate-arrow">
                        <span>Pintu Keluar</span>
                        <i class="bi bi-arrow-up-circle-fill text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Column 2: Kendaraan Aktif Table (Screenshot 1 Match) -->
    <div class="col-12 col-lg-4">
        <div class="ps-card h-100 d-flex flex-direction-column">
            <div class="ps-card-header">
                <h3 class="ps-card-title">
                    <i class="bi bi-bar-chart-steps text-info"></i>
                    <span>Kendaraan Aktif</span>
                </h3>
                <a href="{{ route('admin.tickets.active') }}" class="text-decoration-none small text-primary fw-semibold d-flex align-items-center gap-1">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="table-responsive flex-grow-1">
                <table class="ps-table">
                    <thead>
                        <tr>
                            <th style="width: 32px;">No</th>
                            <th>Plat Nomor</th>
                            <th>Jenis</th>
                            <th>Masuk</th>
                            <th>Durasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTickets as $idx => $item)
                        <tr>
                            <td class="text-muted small">{{ $idx + 1 }}</td>
                            <td>
                                <span class="plate-badge">{{ $item->plate_number }}</span>
                            </td>
                            <td class="small">{{ $item->vehicle_type }}</td>
                            <td class="text-muted small">{{ $item->entry_time ? $item->entry_time->format('H:i') : '08:12' }}</td>
                            <td class="text-info small fw-semibold">{{ $item->formatted_duration ?? '2j 12m' }}</td>
                            <td>
                                <span class="status-pill status-pill-green">Tersedia</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">Tidak ada kendaraan aktif saat ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Column 3: Scan QR & Ringkasan Hari Ini -->
    <div class="col-12 col-lg-3 d-flex flex-column gap-3">
        <!-- Scan QR Masuk / Keluar Card -->
        <div class="ps-card text-center">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h3 class="ps-card-title" style="font-size: 0.95rem;">
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

            <div class="fw-bold text-white small mb-1">Scan QR Code</div>
            <p class="text-muted small mb-3" style="font-size: 0.76rem;">
                Arahkan kamera ke QR Code di pintu masuk atau keluar, atau drag foto tiket.
            </p>

            <button type="button" class="btn btn-ps-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#qrScanModal">
                <i class="bi bi-camera me-1"></i> Aktifkan Kamera
            </button>
        </div>

        <!-- Ringkasan Hari Ini Card -->
        <div class="ps-card flex-grow-1">
            <div class="ps-card-header mb-2">
                <h3 class="ps-card-title" style="font-size: 0.95rem;">
                    <i class="bi bi-calendar-check text-warning"></i>
                    <span>Ringkasan Hari Ini</span>
                </h3>
            </div>
            
            <div class="d-flex flex-column gap-2 small">
                <div class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-25">
                    <span class="text-muted"><i class="bi bi-car-front me-2"></i>Total Parkir</span>
                    <strong class="text-white">{{ $totalCapacity > 0 ? $totalCapacity : 120 }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-25">
                    <span class="text-muted"><i class="bi bi-box-arrow-in-right text-success me-2"></i>Kendaraan Masuk</span>
                    <div class="text-white fw-bold d-flex align-items-center gap-1">
                        {{ $todayInCount }} <i class="bi bi-arrow-up text-success small"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-25">
                    <span class="text-muted"><i class="bi bi-box-arrow-right text-danger me-2"></i>Kendaraan Keluar</span>
                    <div class="text-white fw-bold d-flex align-items-center gap-1">
                        {{ $todayOutCount }} <i class="bi bi-arrow-up text-danger small"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-25">
                    <span class="text-muted"><i class="bi bi-check2-circle text-info me-2"></i>Slot Tersedia</span>
                    <strong class="text-white">{{ $totalAvailable }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted"><i class="bi bi-slash-circle text-warning me-2"></i>Slot Terisi</span>
                    <strong class="text-white">{{ $totalOccupied }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: 4 Quick Navigation Cards (Screenshot 1 Match) -->
<div class="row g-3">
    <!-- Card 1: Kelola Slot Parkir -->
    <div class="col-12 col-sm-6 col-xl-3">
        <a href="{{ route('admin.areas.index') }}" class="quick-action-card">
            <div class="quick-action-icon" style="background-color: rgba(29, 112, 243, 0.15); color: var(--primary-blue);">
                <i class="bi bi-p-square"></i>
            </div>
            <div class="flex-grow-1">
                <div class="quick-action-title">Kelola Slot Parkir</div>
                <div class="quick-action-desc">Atur ketersediaan slot parkir</div>
            </div>
            <i class="bi bi-chevron-right text-muted small"></i>
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
                <div class="quick-action-desc">Lihat kendaraan di area parkir</div>
            </div>
            <i class="bi bi-chevron-right text-muted small"></i>
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
                <div class="quick-action-desc">Lihat laporan & aktivitas parkir</div>
            </div>
            <i class="bi bi-chevron-right text-muted small"></i>
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
                <div class="quick-action-desc">Kelola akun dan sistem</div>
            </div>
            <i class="bi bi-chevron-right text-muted small"></i>
        </a>
    </div>
</div>
@endsection
