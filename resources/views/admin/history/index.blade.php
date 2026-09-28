@extends('layouts.app')

@section('title', 'Riwayat Transaksi & Laporan Parkir - Admin FILKOM')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span>Riwayat & Laporan Transaksi</span>
            <span class="badge badge-green small fs-6">Arsip Transaksi</span>
        </h2>
        <p class="text-muted mb-0">Catatan lengkap kendaraan yang telah menyelesaikan sesi parkir dan pembayaran.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('admin.history.report', request()->query()) }}" target="_blank" class="btn btn-neon-green d-flex align-items-center gap-2">
            <i class="bi bi-printer-fill"></i> Cetak Laporan Resmi
        </a>
    </div>
</div>

<!-- Summary Cards of Filtered History -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-box orange h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted small fw-semibold text-uppercase">Total Pendapatan</span>
                <i class="bi bi-cash-coin fs-4" style="color: var(--neon-orange);"></i>
            </div>
            <div class="number my-1 fs-3">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <span class="badge badge-orange">Total Akumulasi</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-box blue h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted small fw-semibold text-uppercase">Total Transaksi</span>
                <i class="bi bi-receipt-cutoff fs-4" style="color: var(--neon-blue);"></i>
            </div>
            <div class="number my-1">{{ $totalCount }}</div>
            <span class="badge badge-blue">Selesai Keluar</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-box green h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted small fw-semibold text-uppercase">Motor Terparkir</span>
                <i class="bi bi-bicycle fs-4" style="color: var(--neon-green);"></i>
            </div>
            <div class="number my-1">{{ $totalMotor }}</div>
            <span class="badge badge-green">Unit Motor</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-box red h-100">
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted small fw-semibold text-uppercase">Mobil Terparkir</span>
                <i class="bi bi-car-front-fill fs-4" style="color: var(--neon-red);"></i>
            </div>
            <div class="number my-1">{{ $totalMobil }}</div>
            <span class="badge badge-red">Unit Mobil</span>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="card-dark p-3 mb-4">
    <form action="{{ route('admin.history.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-3">
            <label class="form-label text-muted small mb-1">Cari Plat / Tiket</label>
            <input type="text" name="search" class="form-control form-control-dark form-control-sm font-monospace" placeholder="N 1234 AB / TKT-..." value="{{ $search ?? '' }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small mb-1">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control form-control-dark form-control-sm" value="{{ $startDate ?? '' }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small mb-1">Tanggal Selesai</label>
            <input type="date" name="end_date" class="form-control form-control-dark form-control-sm" value="{{ $endDate ?? '' }}">
        </div>
        <div class="col-md-2">
            <label class="form-label text-muted small mb-1">Jenis Kendaraan</label>
            <select name="type" class="form-select form-select-dark form-select-sm">
                <option value="">Semua</option>
                <option value="Motor" {{ ($type ?? '') === 'Motor' ? 'selected' : '' }}>Motor</option>
                <option value="Mobil" {{ ($type ?? '') === 'Mobil' ? 'selected' : '' }}>Mobil</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2 align-self-end mt-2 mt-md-0">
            <button type="submit" class="btn btn-neon-green btn-sm flex-grow-1">
                <i class="bi bi-filter me-1"></i> Terapkan Filter
            </button>
            <a href="{{ route('admin.history.index') }}" class="btn btn-outline-custom btn-sm">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
    </form>
</div>

<!-- History Table -->
<div class="card-dark overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-dark-custom">
            <thead>
                <tr>
                    <th>Kode Tiket</th>
                    <th>Plat Nomor</th>
                    <th>Jenis</th>
                    <th>Area</th>
                    <th>Waktu Masuk</th>
                    <th>Waktu Keluar</th>
                    <th>Durasi</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th class="text-end">Struk</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $item)
                <tr>
                    <td class="font-monospace text-muted small">{{ $item->ticket_code }}</td>
                    <td>
                        <span class="font-monospace fw-bold text-white px-2 py-1 rounded bg-black border border-secondary">
                            {{ $item->plate_number }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $item->vehicle_type === 'Motor' ? 'bg-secondary' : 'bg-primary' }}">
                            {{ $item->vehicle_type }}
                        </span>
                    </td>
                    <td>{{ $item->area->name ?? '-' }}</td>
                    <td class="text-muted small">{{ $item->entry_time->translatedFormat('d M H:i') }}</td>
                    <td class="text-muted small">{{ $item->exit_time ? $item->exit_time->translatedFormat('d M H:i') : '-' }}</td>
                    <td><span class="text-info">{{ $item->formatted_duration }}</span></td>
                    <td>
                        <strong class="font-monospace" style="color: var(--neon-orange);">
                            Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td>
                        @if($item->status === 'paid')
                            <span class="badge badge-green">Lunas</span>
                        @elseif($item->status === 'lost')
                            <span class="badge badge-red">Denda Hilang</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('checkout.receipt', $item->ticket_code) }}" class="btn btn-outline-custom btn-sm py-1 px-2" title="Lihat Struk">
                            <i class="bi bi-receipt"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">
                        Tidak ada catatan riwayat transaksi yang cocok dengan filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($history->hasPages())
    <div class="p-3 border-top border-secondary">
        {{ $history->links() }}
    </div>
    @endif
</div>
@endsection
