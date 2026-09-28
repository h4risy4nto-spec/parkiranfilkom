<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digitalisasi Parkiran FILKOM UB')</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/parkiran.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top no-print">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <span class="fs-4">🅿️</span>
                <span class="fw-bold tracking-tight">PARKIRAN <span style="color: var(--neon-green);">FILKOM</span></span>
                <span class="brand-badge">PTI D</span>
            </a>
            
            <button class="navbar-toggler border-secondary text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <i class="bi bi-list fs-3"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->routeIs('dashboard.slots') ? 'active' : '' }}" href="{{ route('dashboard.slots') }}">
                            <i class="bi bi-grid-3x3-gap-fill"></i> Peta Slot
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->routeIs('checkin.*') ? 'active' : '' }}" href="{{ route('checkin.index') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Kendaraan Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->routeIs('checkout.*') ? 'active' : '' }}" href="{{ route('checkout.index') }}">
                            <i class="bi bi-qr-code-scan"></i> Kendaraan Keluar
                        </a>
                    </li>
                    
                    @auth
                        @if(Auth::user()->isAdmin())
                        <li class="nav-item dropdown">
                            <a class="nav-link-custom dropdown-toggle {{ request()->is('admin*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-shield-lock-fill" style="color: var(--neon-orange);"></i> Admin Panel
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark border-secondary shadow-lg">
                                <li><a class="dropdown-item py-2" href="{{ route('admin.areas.index') }}"><i class="bi bi-geo-alt me-2 text-warning"></i> Kelola Area & Slot</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.tickets.active') }}"><i class="bi bi-car-front-fill me-2 text-info"></i> Kendaraan Sedang Parkir</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.history.index') }}"><i class="bi bi-clock-history me-2 text-success"></i> Riwayat & Laporan Transaksi</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.rates.index') }}"><i class="bi bi-cash-stack me-2 text-warning"></i> Pengaturan Tarif</a></li>
                                <li><hr class="dropdown-divider border-secondary"></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.users.index') }}"><i class="bi bi-people-fill me-2 text-primary"></i> Kelola Pengguna</a></li>
                            </ul>
                        </li>
                        @endif
                    @endauth
                </ul>
                
                <!-- User Status / Auth Menu -->
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-custom dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5" style="color: var(--neon-green);"></i>
                                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                                <span class="badge {{ Auth::user()->isAdmin() ? 'bg-danger' : 'bg-success' }} ms-1">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark border-secondary shadow-lg">
                                <li class="px-3 py-2 text-muted small border-bottom border-secondary">
                                    Masuk sebagai: <strong class="text-light">{{ Auth::user()->email }}</strong>
                                </li>
                                @if(Auth::user()->isAdmin())
                                <li><a class="dropdown-item py-2" href="{{ route('admin.areas.index') }}"><i class="bi bi-gear-fill me-2"></i> Pengaturan Admin</a></li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-neon-green btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-person-lock"></i> Login Petugas / Admin
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container-fluid px-lg-4 py-4 flex-grow-1">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="alert alert-dismissible fade show card-dark border-success text-light d-flex align-items-center gap-3 mb-4 no-print shadow" role="alert" style="background: rgba(0, 255, 136, 0.12);">
                <i class="bi bi-check-circle-fill fs-3" style="color: var(--neon-green);"></i>
                <div class="flex-grow-1">
                    <strong class="d-block text-white">Sukses!</strong>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-dismissible fade show card-dark border-danger text-light d-flex align-items-center gap-3 mb-4 no-print shadow" role="alert" style="background: rgba(255, 51, 102, 0.12);">
                <i class="bi bi-exclamation-octagon-fill fs-3" style="color: var(--neon-red);"></i>
                <div class="flex-grow-1">
                    <strong class="d-block text-white">Perhatian / Gagal!</strong>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-dismissible fade show card-dark border-warning text-light d-flex align-items-center gap-3 mb-4 no-print shadow" role="alert" style="background: rgba(255, 132, 0, 0.12);">
                <i class="bi bi-exclamation-triangle-fill fs-3" style="color: var(--neon-orange);"></i>
                <div class="flex-grow-1">
                    <strong class="d-block text-white">Terjadi Kesalahan Validasi:</strong>
                    <ul class="mb-0 ps-3 mt-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 border-top no-print" style="background-color: #090d13; border-color: var(--border-color) !important;">
        <div class="container-fluid px-lg-4 d-flex flex-wrap justify-content-between align-items-center text-muted small">
            <div>
                <span class="text-light fw-semibold">Digitalisasi Parkiran FILKOM UB</span> &copy; {{ date('Y') }} &middot; Kelompok 1 PTI D
            </div>
            <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                <span><i class="bi bi-cpu text-success"></i> Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span>
                <span><i class="bi bi-database text-warning"></i> MySQL</span>
                <span><i class="bi bi-qr-code text-info"></i> Simple QrCode</span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
