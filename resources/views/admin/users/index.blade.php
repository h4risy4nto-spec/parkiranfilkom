@extends('layouts.app')

@section('title', 'Kelola Pengguna Sistem - Admin FILKOM')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="stat-icon-wrapper stat-icon-blue" style="width: 42px; height: 42px; border-radius: 10px;">
            <i class="bi bi-people-fill"></i>
        </div>
        <div>
            <h2 class="page-title mb-1" style="font-size: 1.55rem;">Kelola Pengguna Sistem</h2>
            <p class="page-subtitle">Kelola akun administrator dan operator gerbang parkir Fakultas Ilmu Komputer.</p>
        </div>
    </div>
    <div>
        <button type="button" class="btn btn-ps-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna Baru
        </button>
    </div>
</div>

<div class="ps-card p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="ps-table">
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Peran (Role)</th>
                    <th>Nomor Telepon</th>
                    <th>Status Akun</th>
                    <th>Terdaftar Sejak</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td class="fw-semibold text-white">
                        <i class="bi bi-person-circle me-1 text-muted"></i> {{ $u->name }}
                        @if($u->id === Auth::id())
                            <span class="badge bg-secondary ms-1">Anda</span>
                        @endif
                    </td>
                    <td>{{ $u->email }}</td>
                    <td>
                        <span class="badge {{ $u->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                            {{ strtoupper($u->role) }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $u->phone ?? '-' }}</td>
                    <td>
                        @if($u->is_active)
                            <span class="badge badge-green">Aktif</span>
                        @else
                            <span class="badge badge-red">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $u->created_at->format('d/m/Y') }}</td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <button type="button" class="btn btn-outline-custom btn-sm py-1 px-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm py-1 px-2" {{ $u->id === Auth::id() ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog text-start">
                                <div class="modal-content card-dark border-secondary">
                                    <div class="modal-header border-secondary">
                                        <h5 class="modal-title fw-bold text-white">Edit Pengguna: {{ $u->name }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.users.update', $u->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label text-light small fw-semibold">Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control form-control-dark" value="{{ $u->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-light small fw-semibold">Email</label>
                                                <input type="email" name="email" class="form-control form-control-dark" value="{{ $u->email }}" required>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label text-light small fw-semibold">Peran</label>
                                                    <select name="role" class="form-select form-select-dark">
                                                        <option value="operator" {{ $u->role === 'operator' ? 'selected' : '' }}>Operator</option>
                                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label text-light small fw-semibold">Telepon</label>
                                                    <input type="text" name="phone" class="form-control form-control-dark" value="{{ $u->phone }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-light small fw-semibold">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                                                <input type="password" name="password" class="form-control form-control-dark" placeholder="Minimal 6 karakter">
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="is_active" id="active{{ $u->id }}" class="form-check-input" value="1" {{ $u->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label text-light small" for="active{{ $u->id }}">Akun Aktif</label>
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
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada akun pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="p-3 border-top border-secondary">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- Modal Tambah Pengguna Baru -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-person-plus text-success me-2"></i>Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-light small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-dark" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-dark" placeholder="nama@filkom.ub.ac.id" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-light small fw-semibold">Peran (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-select form-select-dark" required>
                                <option value="operator">Operator Gerbang</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-light small fw-semibold">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control form-control-dark" placeholder="08123456789">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light small fw-semibold">Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control form-control-dark" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-neon-green">Daftarkan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
