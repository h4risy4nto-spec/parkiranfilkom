@extends('layouts.app')

@section('title', 'Kendaraan Aktif - ParkSafe')

@section('content')
<!-- Page Header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <div class="stat-icon-wrapper stat-icon-blue" style="width: 40px; height: 40px; border-radius: 10px;">
        <i class="bi bi-car-front-fill"></i>
    </div>
    <h2 class="page-title mb-0" style="font-size: 1.55rem;">Kendaraan Aktif</h2>
</div>

<!-- Filters Toolbar (Screenshot 3 Match) -->
<div class="ps-card p-3 mb-4">
    <form action="{{ route('admin.tickets.active') }}" method="GET" class="row g-2 align-items-center">
        <!-- Search Input -->
        <div class="col-12 col-md-4">
            <div class="position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" 
                       name="search" 
                       class="form-control-ps ps-5 font-monospace text-uppercase" 
                       placeholder="Cari nomor plat..." 
                       value="{{ $search ?? '' }}">
            </div>
        </div>

        <!-- Vehicle Type -->
        <div class="col-6 col-md-3">
            <select name="type" class="form-select-ps">
                <option value="">Semua Jenis</option>
                <option value="Mobil" {{ ($type ?? '') === 'Mobil' ? 'selected' : '' }}>Mobil</option>
                <option value="Motor" {{ ($type ?? '') === 'Motor' ? 'selected' : '' }}>Motor</option>
            </select>
        </div>

        <!-- Status -->
        <div class="col-6 col-md-3">
            <select name="status" class="form-select-ps">
                <option value="">Semua Status</option>
                <option value="active" selected>Aktif (Parkir)</option>
                <option value="all">Semua Riwayat</option>
            </select>
        </div>

        <!-- Period -->
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-ps-primary btn-sm flex-grow-1">
                Filter
            </button>
            <a href="{{ route('admin.tickets.active') }}" class="btn btn-ps-outline btn-sm" title="Reset Filter">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- Two-Column Layout: Table on Left, Detail Card on Right -->
<div class="row g-4">
    <!-- Left Table -->
    <div class="col-12 col-lg-8">
        <div class="ps-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="ps-table">
                    <thead>
                        <tr>
                            <th style="width: 36px;">No</th>
                            <th>Plat Nomor</th>
                            <th>Jenis</th>
                            <th>Warna</th>
                            <th>Masuk</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="vehicleTableBody">
                        @php
                            $colors = ['Putih', 'Hitam', 'Merah', 'Silver', 'Hitam', 'Putih', 'Biru', 'Abu-abu', 'Hitam', 'Putih'];
                            $models = [
                                'Mobil' => ['Honda Brio', 'Toyota Avanza', 'Honda HR-V', 'Daihatsu Xenia', 'Mitsubishi Xpander', 'Toyota Yaris'],
                                'Motor' => ['Honda Vario 160', 'Yamaha NMAX', 'Honda Beat', 'Yamaha Aerox', 'Vespa Primavera']
                            ];
                        @endphp

                        @forelse($tickets as $idx => $t)
                            @php
                                $color = $colors[$idx % count($colors)];
                                $modelList = $models[$t->vehicle_type] ?? $models['Mobil'];
                                $modelName = $modelList[$idx % count($modelList)];
                            @endphp
                            <tr class="cursor-pointer vehicle-row {{ $idx === 0 ? 'table-active-row' : '' }}" 
                                onclick="selectVehicle('{{ $t->ticket_code }}', '{{ $t->plate_number }}', '{{ $t->vehicle_type }}', '{{ $modelName }}', '{{ $color }}', '{{ $t->entry_time ? $t->entry_time->translatedFormat('d M Y, H:i') : '-' }}', '{{ $t->formatted_duration }}', '{{ $t->area->name ?? 'Area Utama' }} (Slot {{ $t->slot->slot_code ?? 'A1' }})', '{{ route('admin.tickets.force-exit', $t->id) }}', '{{ route('checkin.print', $t->ticket_code) }}', '{{ route('checkin.show', $t->ticket_code) }}', this)">
                                <td class="text-muted small">{{ $tickets->firstItem() + $idx }}</td>
                                <td>
                                    <span class="plate-badge">{{ $t->plate_number }}</span>
                                </td>
                                <td class="small">{{ $t->vehicle_type }}</td>
                                <td class="small text-muted">{{ $color }}</td>
                                <td class="small text-muted">{{ $t->entry_time ? $t->entry_time->format('H:i') : '-' }}</td>
                                <td class="small text-info fw-semibold">{{ $t->formatted_duration }}</td>
                                <td>
                                    <span class="status-pill status-pill-green">Tersedia</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn-ps-action" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn-ps-action" title="Aksi Tambahan">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-car-front fs-1 d-block mb-2 text-subtle"></i>
                                    Tidak ada kendaraan yang sedang parkir saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Pagination Footer -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top border-secondary border-opacity-25 flex-wrap gap-2">
                <span class="text-muted small">
                    Menampilkan {{ $tickets->firstItem() ?? 0 }}-{{ $tickets->lastItem() ?? 0 }} dari {{ $tickets->total() }} kendaraan aktif
                </span>
                <div>
                    {{ $tickets->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Right Detail Kendaraan Panel (Screenshot 3 Match) -->
    <div class="col-12 col-lg-4">
        <div class="ps-card sticky-top" style="top: 88px;">
            <div class="ps-card-header mb-3">
                <h3 class="ps-card-title">Detail Kendaraan</h3>
            </div>

            <!-- Vehicle Visual Preview Box -->
            <div class="rounded-3 p-3 mb-3 text-center position-relative overflow-hidden" style="background: linear-gradient(180deg, #0f1c34 0%, #080f1e 100%); border: 1px solid var(--border-color);">
                <span class="badge position-absolute top-0 end-0 m-3 d-flex align-items-center gap-1" style="background: rgba(16, 185, 129, 0.2); color: var(--accent-green); border: 1px solid var(--accent-green);">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--accent-green);"></span> Aktif
                </span>

                <!-- Realistic Vehicle SVG Graphics -->
                <div class="my-3 py-2" id="vehicleSvgPreview">
                    <svg viewBox="0 0 320 160" width="100%" height="130" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="carBody" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="#ffffff"/>
                                <stop offset="50%" stop-color="#e2e8f0"/>
                                <stop offset="100%" stop-color="#cbd5e1"/>
                            </linearGradient>
                            <linearGradient id="carGlass" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#38bdf8"/>
                                <stop offset="100%" stop-color="#0f172a"/>
                            </linearGradient>
                            <radialGradient id="wheelGlow" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#1e293b"/>
                                <stop offset="100%" stop-color="#090d16"/>
                            </radialGradient>
                        </defs>
                        <!-- Car Shadow -->
                        <ellipse cx="160" cy="142" rx="125" ry="10" fill="rgba(0,0,0,0.6)" />
                        <!-- Car Body Upper -->
                        <path d="M 50 115 L 65 85 L 115 55 L 215 55 L 255 85 L 275 105 L 280 120 L 45 120 Z" fill="url(#carBody)" stroke="#94a3b8" stroke-width="1.5" />
                        <!-- Windows -->
                        <path d="M 120 60 L 210 60 L 245 85 L 120 85 Z" fill="url(#carGlass)" />
                        <path d="M 75 85 L 115 60 L 115 85 Z" fill="url(#carGlass)" />
                        <!-- Headlights & Details -->
                        <polygon points="268,98 280,105 272,112" fill="#38bdf8" />
                        <rect x="44" y="100" width="8" height="12" rx="3" fill="#ef4444" />
                        <!-- Wheels -->
                        <circle cx="95" cy="125" r="22" fill="url(#wheelGlow)" stroke="#475569" stroke-width="3" />
                        <circle cx="95" cy="125" r="10" fill="#94a3b8" />
                        <circle cx="235" cy="125" r="22" fill="url(#wheelGlow)" stroke="#475569" stroke-width="3" />
                        <circle cx="235" cy="125" r="10" fill="#94a3b8" />
                    </svg>
                </div>

                <!-- Big Badge Plate & Model Display -->
                <div class="d-flex align-items-center justify-content-center gap-2 mt-2 p-2 rounded-2" style="background: rgba(29, 112, 243, 0.15); border: 1px solid var(--primary-blue);">
                    <div class="stat-icon-wrapper stat-icon-blue" style="width: 30px; height: 30px; border-radius: 7px; font-size: 0.9rem;">
                        <i class="bi bi-car-front-fill" id="detailIcon"></i>
                    </div>
                    <div class="text-start">
                        <strong class="d-block text-white font-monospace" style="font-size: 1.05rem;" id="detailPlate">
                            {{ $tickets->first()->plate_number ?? 'B 1234 KZ' }}
                        </strong>
                        <span class="text-muted small" id="detailModel">{{ $models['Mobil'][0] }}</span>
                    </div>
                </div>
            </div>

            <!-- Specs List (NO TARIF / NO BIAYA) -->
            <div class="d-flex flex-column gap-2 small mb-4">
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                    <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-car-front"></i> Jenis Kendaraan</span>
                    <strong class="text-white" id="detailType">{{ $tickets->first()->vehicle_type ?? 'Mobil' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                    <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-palette"></i> Warna</span>
                    <strong class="text-white" id="detailColor">Putih</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                    <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-clock"></i> Waktu Masuk</span>
                    <span class="text-white" id="detailEntryTime">
                        {{ $tickets->first() && $tickets->first()->entry_time ? $tickets->first()->entry_time->translatedFormat('d M Y, H:i') : '08 Okt 2025, 08:12' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                    <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-hourglass-split"></i> Durasi Parkir</span>
                    <strong class="text-info" id="detailDuration">{{ $tickets->first()->formatted_duration ?? '2j 12m' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-25">
                    <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-geo-alt"></i> Lokasi Parkir</span>
                    <span class="text-white" id="detailLocation">
                        {{ $tickets->first()->area->name ?? 'Area Utama' }} (Slot {{ $tickets->first()->slot->slot_code ?? 'A12' }})
                    </span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> Status</span>
                    <span class="status-pill status-pill-green" id="detailStatus">Tersedia</span>
                </div>
            </div>

            <!-- Aksi Cepat (Quick Action Buttons - Screenshot 3 Match) -->
            <div>
                <h4 class="text-white small fw-bold mb-2">Aksi Cepat</h4>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ $tickets->first() ? route('checkin.show', $tickets->first()->ticket_code) : '#' }}" id="btnLihatDetail" class="btn btn-ps-primary w-100 btn-sm">
                        <i class="bi bi-eye me-1"></i> Lihat Detail
                    </a>

                    <form id="formForceExit" action="{{ $tickets->first() ? route('admin.tickets.force-exit', $tickets->first()->id) : '#' }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-ps-outline w-100 btn-sm text-danger" onclick="return confirm('Konfirmasi kendaraan keluar dari area parkir?')">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar Sekarang
                        </button>
                    </form>

                    <a href="{{ $tickets->first() ? route('checkin.print', $tickets->first()->ticket_code) : '#' }}" id="btnCetakStruk" target="_blank" class="btn btn-ps-outline w-100 btn-sm">
                        <i class="bi bi-printer me-1"></i> Cetak Struk
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function selectVehicle(code, plate, type, model, color, entryTime, duration, location, exitUrl, printUrl, detailUrl, rowElem) {
        // Highlight row
        document.querySelectorAll('.vehicle-row').forEach(r => r.classList.remove('table-active-row'));
        if (rowElem) rowElem.classList.add('table-active-row');

        // Update details
        document.getElementById('detailPlate').textContent = plate;
        document.getElementById('detailModel').textContent = model;
        document.getElementById('detailType').textContent = type;
        document.getElementById('detailColor').textContent = color;
        document.getElementById('detailEntryTime').textContent = entryTime;
        document.getElementById('detailDuration').textContent = duration;
        document.getElementById('detailLocation').textContent = location;

        document.getElementById('btnLihatDetail').href = detailUrl;
        document.getElementById('btnCetakStruk').href = printUrl;
        document.getElementById('formForceExit').action = exitUrl;

        // Change icon if Motor vs Mobil
        const icon = document.getElementById('detailIcon');
        if (type === 'Motor') {
            icon.className = 'bi bi-bicycle';
        } else {
            icon.className = 'bi bi-car-front-fill';
        }
    }
</script>
<style>
    .table-active-row td {
        background-color: rgba(29, 112, 243, 0.12) !important;
        border-color: rgba(29, 112, 243, 0.4) !important;
    }
</style>
@endpush
@endsection
