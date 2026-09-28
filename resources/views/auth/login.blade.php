@extends('layouts.app')

@section('title', 'Login Petugas / Administrator - Parkiran FILKOM')

@section('content')
<div class="row justify-content-center align-items-center py-4">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card-dark p-4 shadow-lg border-secondary">
            <div class="text-center mb-4">
                <div class="fs-1 mb-2">🅿️</div>
                <h3 class="fw-bold text-white mb-1">Login Petugas</h3>
                <p class="text-muted small">Sistem Digitalisasi Parkiran FILKOM UB</p>
            </div>

            <form action="{{ route('login.process') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label text-light small fw-semibold text-uppercase">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-muted"><i class="bi bi-envelope"></i></span>
                        <input type="email" 
                               name="email" 
                               id="loginEmail" 
                               class="form-control form-control-dark @error('email') is-invalid @enderror" 
                               placeholder="nama@filkom.ub.ac.id" 
                               value="{{ old('email', 'admin@filkom.ub.ac.id') }}" 
                               required 
                               autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label text-light small fw-semibold text-uppercase">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" 
                               name="password" 
                               id="loginPassword" 
                               class="form-control form-control-dark @error('password') is-invalid @enderror" 
                               placeholder="Kata sandi akun" 
                               value="password123" 
                               required>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label text-muted small" for="remember">
                        Ingat sesi saya
                    </label>
                </div>

                <button type="submit" class="btn btn-neon-green btn-lg w-100 fw-bold py-2 mb-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> MASUK KE SISTEM
                </button>
            </form>

            <!-- Demo Credentials Helper -->
            <div class="p-3 rounded border border-secondary mt-3" style="background: rgba(0, 0, 0, 0.4);">
                <div class="text-warning small fw-bold mb-2 d-flex align-items-center gap-1">
                    <i class="bi bi-key-fill"></i> Akun Bawaan Seeder:
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-custom btn-sm flex-grow-1" onclick="fillCreds('admin@filkom.ub.ac.id', 'password123')">
                        <strong class="d-block text-white" style="font-size: 0.75rem;">Admin</strong>
                        <span style="font-size: 0.68rem;">admin@filkom...</span>
                    </button>
                    <button type="button" class="btn btn-outline-custom btn-sm flex-grow-1" onclick="fillCreds('operator@filkom.ub.ac.id', 'password123')">
                        <strong class="d-block text-white" style="font-size: 0.75rem;">Operator</strong>
                        <span style="font-size: 0.68rem;">operator@filkom...</span>
                    </button>
                </div>
                <div class="text-muted mt-2 text-center" style="font-size: 0.7rem;">Password: <code>password123</code></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillCreds(email, pass) {
        document.getElementById('loginEmail').value = email;
        document.getElementById('loginPassword').value = pass;
    }
</script>
@endpush
@endsection
