@extends('layouts.app')

@section('title', 'Kelola Area & Slot Parkir - Admin FILKOM')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <span>Manajemen Area & Slot Parkir</span>
            <span class="badge badge-orange small fs-6">Admin</span>
        </h2>
        <p class="text-muted mb-0">Kelola master data area parkir, kapasitas slot motor dan mobil di lingkungan FILKOM.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" class="btn btn-neon-green" data-bs-toggle="modal" data-bs-target="#addAreaModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Area Baru
        </button>
    </div>
</div>

<div class="card-dark overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-dark-custom">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Area</th>
                    <th>Jenis</th>
                    <th>Kapasitas Slot</th>
                    <th>Terisi</th>
                    <th>Tersedia</th>
                    <th>Deskripsi Lokasi</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($areas as $area)
                <tr>
                    <td><strong class="font-monospace text-warning">{{ $area->code }}</strong></td>
                    <td class="fw-semibold text-white">{{ $area->name }}</td>
                    <td>
                        <span class="badge {{ $area->vehicle_type === 'Motor' ? 'bg-secondary' : 'bg-primary' }}">
                            {{ $area->vehicle_type }}
                        </span>
                    </td>
                    <td><span class="fs-6 fw-bold text-light">{{ $area->capacity }}</span> slot</td>
                    <td><span class="text-danger fw-bold">{{ $area->occupied_count }}</span></td>
                    <td><span class="text-success fw-bold">{{ $area->available_count }}</span></td>
                    <td class="text-muted small">{{ $area->location_description ?? '-' }}</td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <button type="button" class="btn btn-outline-custom btn-sm py-1 px-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal{{ $area->id }}" 
                                    title="Edit Area">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.areas.destroy', $area->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus area ini beserta seluruh slotnya?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm py-1 px-2" {{ $area->occupied_count > 0 ? 'disabled' : '' }} title="Hapus Area">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $area->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog text-start">
                                <div class="modal-content card-dark border-secondary">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title fw-bold text-white">Edit Area {{ $area->name }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.areas.update', $area->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-light small fw-semibold">Nama Area</label>
                                                <input type="text" name="name" class="form-control form-control-dark" value="{{ $area->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-light small fw-semibold">Kapasitas Slot</label>
                                                <input type="number" name="capacity" min="{{ $area->occupied_count }}" max="500" class="form-control form-control-dark" value="{{ $area->capacity }}" required>
                                                <small class="text-muted">Minimal {{ $area->occupied_count }} (karena sedang dipakai).</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-light small fw-semibold">Deskripsi Lokasi</label>
                                                <input type="text" name="location_description" class="form-control form-control-dark" value="{{ $area->location_description }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-secondary">
                                            <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-neon-green">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data area parkir.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Area Baru -->
<div class="modal fade" id="addAreaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-plus-circle text-success me-2"></i>Tambah Area Parkir Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.areas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-light small fw-semibold">Nama Area <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-dark" placeholder="Contoh: Area D - Basement" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-light small fw-semibold">Kode Area <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control form-control-dark font-monospace text-uppercase" placeholder="AREA-D" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-light small fw-semibold">Jenis Kendaraan <span class="text-danger">*</span></label>
                            <select name="vehicle_type" class="form-select form-select-dark" required>
                                <option value="Motor">Motor</option>
                                <option value="Mobil">Mobil</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small fw-semibold">Kapasitas Slot <span class="text-danger">*</span></label>
                        <input type="number" name="capacity" min="1" max="500" value="15" class="form-control form-control-dark" required>
                        <small class="text-muted">Nomor slot (misal D-01 s/d D-15) akan digenerate otomatis.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small fw-semibold">Deskripsi Lokasi (Opsional)</label>
                        <input type="text" name="location_description" class="form-control form-control-dark" placeholder="Contoh: Dekat lift gedung kuliah baru">
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-neon-green">Tambah & Buat Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
