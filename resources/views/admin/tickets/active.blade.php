@extends('layouts.app')

@section('title', 'Kendaraan Sedang Parkir - Admin FILKOM')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span>Kendaraan Sedang Parkir</span>
            <span class="badge badge-green small fs-6">Tiket Aktif ({{ $tickets->total() }})</span>
        </h2>
        <p class="text-muted mb-0">Daftar seluruh kendaraan yang saat ini masih berada di dalam area parkir FILKOM UB.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="{{ route('checkin.index') }}" class="btn btn-neon-green">
            <i class="bi bi-plus-lg me-1"></i> Input Masuk Baru
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card-dark p-3 mb-4">
    <form action="{{ route('admin.tickets.active') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-black border-secondary text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control form-control-dark" placeholder="Cari Plat atau Kode Tiket..." value="{{ $search ?? '' }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="area_id" class="form-select form-select-dark">
                <option value="">Semua Area</option>
                @foreach($areas as $a)
                    <option value="{{ $a->id }}" {{ ($areaId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select form-select-dark">
                <option value="">Semua Jenis Kendaraan</option>
                <option value="Motor" {{ ($type ?? '') == 'Motor' ? 'selected' : '' }}>Motor</option>
                <option value="Mobil" {{ ($type ?? '') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-custom flex-grow-1">Filter</button>
            <a href="{{ route('admin.tickets.active') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Tickets Table -->
<div class="card-dark overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-dark-custom">
            <thead>
                <tr>
                    <th>Kode Tiket</th>
                    <th>Plat Nomor</th>
                    <th>Jenis</th>
                    <th>Area & Slot</th>
                    <th>Waktu Masuk</th>
                    <th>Durasi Berjalan</th>
                    <th>Petugas Masuk</th>
                    <th class="text-end">Aksi Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                <tr>
                    <td>
                        <a href="{{ route('checkin.show', $t->ticket_code) }}" class="fw-bold font-monospace text-decoration-none" style="color: var(--neon-green);">
                            {{ $t->ticket_code }}
                        </a>
                    </td>
                    <td>
                        <span class="font-monospace fw-bold text-white px-2 py-1 rounded bg-black border border-secondary">
                            {{ $t->plate_number }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $t->vehicle_type === 'Motor' ? 'bg-secondary' : 'bg-primary' }}">
                            {{ $t->vehicle_type }}
                        </span>
                    </td>
                    <td>
                        {{ $t->area->name ?? '-' }}
                        <span class="badge bg-dark border border-secondary text-warning ms-1">
                            Slot {{ $t->slot->slot_code ?? '-' }}
                        </span>
                    </td>
                    <td class="text-muted small">
                        {{ $t->entry_time->translatedFormat('d M H:i:s') }}
                    </td>
                    <td>
                        <span class="text-info fw-semibold">{{ $t->formatted_duration }}</span>
                    </td>
                    <td class="text-muted small">
                        {{ $t->operatorIn->name ?? 'Sistem' }}
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('checkout.index', ['q' => $t->ticket_code]) }}" class="btn btn-neon-orange btn-sm py-1 px-2" title="Proses Kasir">
                                <i class="bi bi-box-arrow-right"></i> Kasir
                            </a>
                            <form action="{{ route('admin.tickets.force-exit', $t->id) }}" method="POST" onsubmit="return confirm('Peringatan: Keluarkan paksa kendaraan {{ $t->plate_number }}? Slot akan langsung dikosongkan.');">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm py-1 px-2" title="Keluarkan Paksa">
                                    <i class="bi bi-x-octagon"></i> Paksa
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        Tidak ada kendaraan parkir aktif yang sesuai kriteria filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
    <div class="p-3 border-top border-secondary">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
