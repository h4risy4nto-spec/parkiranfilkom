@extends('layouts.app')

@section('title', 'Masuk ke Akun - ParkSafe')

@section('content')
<div class="login-wrapper">
    <div class="login-card-white shadow-lg">
        <h2 class="login-title">Masuk ke Akun</h2>
        <p class="login-subtitle">Kelola parkir Anda dengan mudah</p>

        @if($errors->any())
            <div class="alert alert-danger py-2 small mb-3">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary mb-1" for="emailInput">Alamat email</label>
                <input type="email" 
                       name="email" 
                       id="emailInput" 
                       class="login-input-white" 
                       placeholder="Masukkan alamat email Anda" 
                       value="{{ old('email', 'admin@filkom.ub.ac.id') }}" 
                       required 
                       autofocus>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary mb-1" for="passwordInput">Kata sandi</label>
                <div class="position-relative">
                    <input type="password" 
                           name="password" 
                           id="passwordInput" 
                           class="login-input-white pe-5" 
                           placeholder="Masukkan kata sandi Anda" 
                           value="password123" 
                           required>
                    <button type="button" 
                            class="btn btn-sm border-0 position-absolute end-0 top-50 translate-middle-y text-secondary me-2" 
                            onclick="togglePasswordVisibility()" 
                            aria-label="Tampilkan atau sembunyikan kata sandi">
                        <i class="bi bi-eye" id="passwordToggleIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember & Forgot -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                    <label class="form-check-label small text-secondary" for="rememberMe">
                        Tetap masuk
                    </label>
                </div>
                <a href="#" class="small text-decoration-none" style="color: var(--primary-blue);" onclick="alert('Silakan hubungi administrator IT untuk reset kata sandi.')">
                    Lupa kata sandi?
                </a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-ps-primary w-100 py-2 fw-bold mb-4" style="border-radius: 10px;">
                Masuk
            </button>

            <!-- Register Link -->
            <div class="text-center small text-secondary">
                Belum punya akun? <a href="#" style="color: var(--primary-blue); text-decoration: none;" onclick="fillCreds('operator@filkom.ub.ac.id', 'password123')">Daftar di sini</a>
            </div>
        </form>

        <!-- Quick Credentials Helper -->
        <div class="mt-4 pt-3 border-top text-center">
            <span class="text-muted small d-block mb-2">Akun Demo Cepat:</span>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="fillCreds('admin@filkom.ub.ac.id', 'password123')">
                    <strong>Admin</strong>
                </button>
                <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="fillCreds('operator@filkom.ub.ac.id', 'password123')">
                    <strong>Operator</strong>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePasswordVisibility() {
        const pass = document.getElementById('passwordInput');
        const icon = document.getElementById('passwordToggleIcon');
        if (pass.type === 'password') {
            pass.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            pass.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function fillCreds(email, pass) {
        document.getElementById('emailInput').value = email;
        document.getElementById('passwordInput').value = pass;
    }
</script>
@endpush
@endsection
