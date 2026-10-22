@extends('layouts.app')

@section('title', 'Pengaturan - ParkSafe')

@section('content')
<!-- Page Header -->
<div class="d-flex align-items-center gap-3 mb-4">
    <div class="stat-icon-wrapper stat-icon-blue" style="width: 42px; height: 42px; border-radius: 10px;">
        <i class="bi bi-gear-fill"></i>
    </div>
    <div>
        <h2 class="page-title mb-1" style="font-size: 1.55rem;">Pengaturan</h2>
        <p class="page-subtitle">Atur preferensi dan konfigurasi sistem ParkSafe sesuai kebutuhan Anda.</p>
    </div>
</div>

<!-- Settings Layout: Left Sub-tabs, Right Config Panels -->
<div class="row g-4">
    <!-- Left Navigation Tabs (Screenshot 4 Match) -->
    <div class="col-12 col-lg-3">
        <div class="ps-card p-3">
            <div class="d-flex flex-column gap-2" role="tablist">
                <button type="button" class="btn text-start p-2 rounded-3 d-flex align-items-center gap-2 text-white fw-semibold" style="background-color: var(--primary-blue); box-shadow: 0 4px 12px var(--primary-blue-glow);">
                    <i class="bi bi-gear"></i> Umum
                </button>
                <button type="button" class="btn text-start p-2 rounded-3 d-flex align-items-center gap-2 text-muted" onclick="alert('Bagian Notifikasi siap dikonfigurasi.')">
                    <i class="bi bi-bell"></i> Notifikasi
                </button>
                <button type="button" class="btn text-start p-2 rounded-3 d-flex align-items-center gap-2 text-muted" onclick="alert('Bagian Keamanan siap dikonfigurasi.')">
                    <i class="bi bi-shield-check"></i> Keamanan
                </button>
                <button type="button" class="btn text-start p-2 rounded-3 d-flex align-items-center gap-2 text-muted" onclick="alert('Bagian Tampilan siap dikonfigurasi.')">
                    <i class="bi bi-display"></i> Tampilan
                </button>
                <button type="button" class="btn text-start p-2 rounded-3 d-flex align-items-center gap-2 text-muted" onclick="alert('Fitur Backup database otomatis berjalan setiap hari pukul 00:00 WIB.')">
                    <i class="bi bi-database"></i> Data & Backup
                </button>
            </div>
        </div>
    </div>

    <!-- Right Settings Content -->
    <div class="col-12 col-lg-9">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <!-- Section 1: Top Header Info Card -->
            <div class="ps-card mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-wrapper stat-icon-blue" style="width: 38px; height: 38px; border-radius: 9px; font-size: 1.05rem;">
                        <i class="bi bi-sliders"></i>
                    </div>
                    <div>
                        <h3 class="ps-card-title" style="font-size: 1.15rem;">Pengaturan Umum</h3>
                        <p class="text-muted small mb-0">Kelola informasi dasar sistem dan preferensi aplikasi.</p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Informasi Sistem & Bahasa Wilayah in 2 Columns -->
            <div class="row g-4 mb-4">
                <!-- Informasi Sistem Card -->
                <div class="col-12 col-md-6">
                    <div class="ps-card h-100">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-cpu text-primary"></i>
                            <h4 class="ps-card-title" style="font-size: 0.98rem;">Informasi Sistem</h4>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Nama Aplikasi</label>
                            <input type="text" name="app_name" class="form-control-ps" value="ParkSafe">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Versi Aplikasi</label>
                            <input type="text" name="app_version" class="form-control-ps" value="v1.0.0" readonly>
                        </div>

                        <div>
                            <label class="form-label text-muted small mb-1">Lokasi Parkir</label>
                            <input type="text" name="parking_location" class="form-control-ps" value="Gedung FILKOM UB, Jl. Veteran No. 8, Malang">
                        </div>
                    </div>
                </div>

                <!-- Bahasa & Wilayah Card -->
                <div class="col-12 col-md-6">
                    <div class="ps-card h-100">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-translate text-primary"></i>
                            <h4 class="ps-card-title" style="font-size: 0.98rem;">Bahasa & Wilayah</h4>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Bahasa</label>
                            <select name="language" class="form-select-ps">
                                <option value="id" selected>Bahasa Indonesia</option>
                                <option value="en">English (US)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Zona Waktu</label>
                            <select name="timezone" class="form-select-ps">
                                <option value="Asia/Jakarta" selected>WIB (GMT+7)</option>
                                <option value="Asia/Makassar">WITA (GMT+8)</option>
                                <option value="Asia/Jayapura">WIT (GMT+9)</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label text-muted small mb-1">Format Tanggal</label>
                            <select name="date_format" class="form-select-ps">
                                <option value="DD MMM YYYY" selected>DD MMM YYYY</option>
                                <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Preferensi Sistem (Theme, Sound, Auto-Logout) -->
            <div class="ps-card mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-sliders2-vertical text-primary"></i>
                    <h4 class="ps-card-title" style="font-size: 0.98rem;">Preferensi Sistem</h4>
                </div>

                <!-- Mode Tampilan -->
                <div class="row align-items-center mb-3 py-2 border-bottom border-secondary border-opacity-25">
                    <div class="col-sm-6 text-muted small">
                        Mode Tampilan
                    </div>
                    <div class="col-sm-6 d-flex gap-2 justify-content-sm-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-ps-outline">Terang</button>
                            <button type="button" class="btn btn-sm btn-ps-primary active">Gelap</button>
                        </div>
                    </div>
                </div>

                <!-- Notifikasi Suara -->
                <div class="row align-items-center mb-3 py-2 border-bottom border-secondary border-opacity-25">
                    <div class="col-sm-6 text-muted small">
                        Notifikasi Suara
                    </div>
                    <div class="col-sm-6 d-flex justify-content-sm-end">
                        <label class="switch-ps">
                            <input type="checkbox" name="sound_notification" checked>
                            <span class="slider-ps"></span>
                        </label>
                    </div>
                </div>

                <!-- Auto Logout -->
                <div class="row align-items-center py-2">
                    <div class="col-sm-6 text-muted small">
                        Auto Logout
                    </div>
                    <div class="col-sm-6 d-flex justify-content-sm-end">
                        <select name="auto_logout" class="form-select-ps" style="max-width: 180px;">
                            <option value="15">15 Menit</option>
                            <option value="30" selected>30 Menit</option>
                            <option value="60">60 Menit</option>
                            <option value="never">Jangan Pernah</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 4: Tombol Aksi (Screenshot 4 Match) -->
            <div class="ps-card">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-check2-circle text-primary"></i>
                    <h4 class="ps-card-title" style="font-size: 0.98rem;">Tombol Aksi</h4>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <button type="button" class="btn btn-ps-outline btn-sm d-flex align-items-center gap-2" onclick="location.reload()">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset ke Default
                    </button>
                    <button type="submit" class="btn btn-ps-primary btn-sm d-flex align-items-center gap-2">
                        <i class="bi bi-floppy"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
