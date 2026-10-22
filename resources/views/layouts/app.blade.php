<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ParkSafe - Sistem Manajemen Parkir Digital')</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom ParkSafe Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/parkiran.css') }}">
    
    <!-- Chart.js & Html5-QRCode CDNs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    @stack('styles')
</head>
<body>
    @if(request()->routeIs('login'))
        @yield('content')
    @else
    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="app-sidebar" id="appSidebar">
            <a href="{{ route('dashboard') }}" class="brand-section">
                <div class="brand-logo-icon">P</div>
                <h1 class="brand-title">ParkSafe</h1>
            </a>

            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.areas.index') }}" class="sidebar-link {{ request()->routeIs('admin.areas.*') || request()->routeIs('dashboard.slots') ? 'active' : '' }}">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        <span>Kelola Parkir</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tickets.active') }}" class="sidebar-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                        <i class="bi bi-car-front-fill"></i>
                        <span>Kendaraan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.history.index') }}" class="sidebar-link {{ request()->routeIs('admin.history.index') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.history.report') }}" class="sidebar-link {{ request()->routeIs('admin.history.report') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-fill"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
            </ul>

            <!-- Quick Gate Access Links -->
            <div class="pt-3 border-top border-secondary border-opacity-25 mt-auto">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('checkin.index') }}" class="btn btn-outline-light btn-sm text-start py-2 d-flex align-items-center gap-2" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); color: var(--accent-green);">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="small fw-semibold">Pintu Masuk (Gate In)</span>
                    </a>
                    <a href="{{ route('checkout.index') }}" class="btn btn-outline-light btn-sm text-start py-2 d-flex align-items-center gap-2" style="background: rgba(29, 112, 243, 0.1); border-color: rgba(29, 112, 243, 0.3); color: var(--primary-blue);">
                        <i class="bi bi-qr-code-scan"></i>
                        <span class="small fw-semibold">Pintu Keluar (Gate Out)</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="app-main">
            <!-- Top Utility Bar -->
            <header class="app-topbar">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <form action="{{ route('checkout.index') }}" method="GET" class="topbar-search">
                        <i class="bi bi-search"></i>
                        <input type="text" name="q" placeholder="Cari nomor plat, nama, atau lokasi..." autocomplete="off">
                    </form>
                </div>

                <div class="topbar-right">
                    <!-- Scanner Modal Trigger -->
                    <button type="button" class="topbar-btn-icon" data-bs-toggle="modal" data-bs-target="#qrScanModal" title="Pindai QR Tiket (Kamera / Drag Foto)">
                        <i class="bi bi-qr-code-scan text-info"></i>
                    </button>

                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="topbar-btn-icon dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="topbar-badge-dot"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark p-2 shadow-lg" style="background-color: var(--bg-card); border-color: var(--border-color); width: 280px;">
                            <li class="dropdown-header text-white fw-bold">Notifikasi Sistem</li>
                            <li><hr class="dropdown-divider border-secondary opacity-25"></li>
                            <li class="p-2 small text-muted">
                                <i class="bi bi-check-circle-fill text-success me-1"></i> Sistem beroperasi normal.
                            </li>
                            <li class="p-2 small text-muted">
                                <i class="bi bi-shield-check text-info me-1"></i> Mode Parkir Gratis Kampus Aktif.
                            </li>
                        </ul>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="user-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-circle">
                                {{ substr(Auth::user()->name ?? 'Admin', 0, 1) }}
                            </div>
                            <span class="d-none d-sm-inline fw-semibold small">{{ Auth::user()->name ?? 'Admin' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow-lg p-2" style="background-color: var(--bg-card); border-color: var(--border-color);">
                            <li class="px-3 py-2 small border-bottom border-secondary border-opacity-25 mb-1">
                                <strong class="d-block text-white">{{ Auth::user()->name ?? 'Admin FILKOM' }}</strong>
                                <span class="text-muted">{{ Auth::user()->email ?? 'admin@filkom.ub.ac.id' }}</span>
                            </li>
                            <li>
                                <a class="dropdown-item rounded py-2 small" href="{{ route('admin.settings.index') }}">
                                    <i class="bi bi-gear me-2"></i> Pengaturan
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded py-2 small text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-container">
                <!-- Session Alerts -->
                @if(session('success'))
                    <div class="alert alert-dismissible fade show border-0 text-white d-flex align-items-center gap-3 mb-4 shadow" role="alert" style="background: rgba(16, 185, 129, 0.2); border: 1px solid var(--accent-green) !important; border-radius: 10px;">
                        <i class="bi bi-check-circle-fill fs-4" style="color: var(--accent-green);"></i>
                        <div class="flex-grow-1">
                            <strong class="d-block">Berhasil!</strong>
                            <span class="small">{{ session('success') }}</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-dismissible fade show border-0 text-white d-flex align-items-center gap-3 mb-4 shadow" role="alert" style="background: rgba(239, 68, 68, 0.2); border: 1px solid var(--accent-red) !important; border-radius: 10px;">
                        <i class="bi bi-exclamation-octagon-fill fs-4" style="color: var(--accent-red);"></i>
                        <div class="flex-grow-1">
                            <strong class="d-block">Pemberitahuan:</strong>
                            <span class="small">{{ session('error') }}</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @endif

    <!-- Global QR Scanner Modal (Camera + Drag & Drop Image) -->
    <div class="modal fade" id="qrScanModal" tabindex="-1" aria-labelledby="qrScanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; color: #fff;">
                <div class="modal-header border-bottom border-secondary border-opacity-25 pb-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="qrScanModalLabel">
                        <i class="bi bi-qr-code-scan text-primary"></i>
                        <span>Pindai QR Tiket Parkir</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stopGlobalScanner()"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <!-- Scanner Viewfinder Box -->
                    <div id="modalQrReader" class="mb-3 rounded overflow-hidden" style="min-height: 180px; background: #000;"></div>
                    
                    <!-- Drag & Drop Zone -->
                    <div class="qr-dropzone mb-3" id="modalDropzone" onclick="document.getElementById('modalQrFileInput').click()">
                        <i class="bi bi-cloud-arrow-up fs-2 d-block text-primary mb-1"></i>
                        <span class="d-block fw-semibold small text-white">Tarik & Lepas Foto QR di Sini</span>
                        <span class="text-muted small">atau <span class="text-primary text-decoration-underline">Klik untuk pilih gambar</span> (PNG, JPG)</span>
                        <input type="file" id="modalQrFileInput" accept="image/*" style="display: none;" onchange="handleModalFileSelect(this)">
                    </div>

                    <!-- Controls -->
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-ps-primary btn-sm" id="btnToggleCamera" onclick="toggleGlobalCamera()">
                            <i class="bi bi-camera me-1"></i> <span id="cameraBtnLabel">Aktifkan Kamera</span>
                        </button>
                        <button type="button" class="btn btn-ps-outline btn-sm" onclick="document.getElementById('modalQrFileInput').click()">
                            <i class="bi bi-image me-1"></i> Upload Foto
                        </button>
                    </div>

                    <div id="scannerMessage" class="alert alert-dark small mt-3 py-2 text-muted mb-0 border-0" style="background: rgba(255,255,255,0.05);">
                        Arahkan kamera ke QR Code tiket atau unggah foto tiket untuk scan otomatis.
                    </div>

                    <!-- Scan Result Card (Initially Hidden) -->
                    <div id="scanResultCard" class="mt-3 p-3 rounded text-start border border-primary border-opacity-50" style="display: none; background: rgba(29, 112, 243, 0.08);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success">QR Terdeteksi</span>
                            <span class="font-monospace fw-bold text-white small" id="resPlate">B 1234 KZ</span>
                        </div>
                        <div class="small text-muted mb-1">Kode: <strong class="text-white" id="resCode">-</strong></div>
                        <div class="small text-muted mb-1">Slot: <strong class="text-white" id="resSlot">-</strong> | Durasi: <strong class="text-info" id="resDuration">-</strong></div>
                        <div class="d-flex gap-2 mt-3">
                            <form id="autoExitForm" action="{{ route('checkout.process') }}" method="POST" class="flex-grow-1">
                                @csrf
                                <input type="hidden" name="ticket_code" id="hiddenExitCode">
                                <button type="submit" class="btn btn-ps-primary btn-sm w-100">
                                    <i class="bi bi-box-arrow-right me-1"></i> Proses Keluar Sekarang
                                </button>
                            </form>
                            <a id="resDetailLink" href="#" class="btn btn-ps-outline btn-sm">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global QR Scanner Logic -->
    <script>
        // Sidebar Toggle for Mobile
        const sidebar = document.getElementById('appSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        // Live Clock in Indonesian Format
        function updateLiveClock() {
            const clockEl = document.getElementById('liveClockText');
            if (!clockEl) return;
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const dayName = days[now.getDay()];
            const dayNum = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const mins = String(now.getMinutes()).padStart(2, '0');
            const secs = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${dayName}, ${dayNum} ${monthName} ${year} | ${hours}:${mins}:${secs} WIB`;
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // Global HTML5-QRCode Scanner Instance
        let globalHtml5Qr = null;
        let isCameraActive = false;

        function toggleGlobalCamera() {
            if (isCameraActive) {
                stopGlobalScanner();
            } else {
                startGlobalScanner();
            }
        }

        function startGlobalScanner() {
            if (!globalHtml5Qr) {
                globalHtml5Qr = new Html5Qrcode("modalQrReader");
            }
            const config = { fps: 10, qrbox: { width: 220, height: 220 } };
            globalHtml5Qr.start(
                { facingMode: "environment" },
                config,
                onQrScanSuccess,
                (errorMessage) => { /* ignore frame scan errors */ }
            ).then(() => {
                isCameraActive = true;
                document.getElementById('cameraBtnLabel').textContent = "Matikan Kamera";
                document.getElementById('scannerMessage').textContent = "Kamera aktif. Arahkan QR code ke kotak pemindai...";
            }).catch((err) => {
                console.warn("Camera start error:", err);
                document.getElementById('scannerMessage').textContent = "Tidak dapat mengakses kamera. Silakan drag/upload gambar QR tiket di bawah.";
            });
        }

        function stopGlobalScanner() {
            if (globalHtml5Qr && isCameraActive) {
                globalHtml5Qr.stop().then(() => {
                    isCameraActive = false;
                    document.getElementById('cameraBtnLabel').textContent = "Aktifkan Kamera";
                    document.getElementById('scannerMessage').textContent = "Kamera dinonaktifkan.";
                }).catch(err => console.error(err));
            }
        }

        function onQrScanSuccess(decodedText) {
            stopGlobalScanner();
            handleDetectedCode(decodedText);
        }

        // Handle Image File Drag & Drop / Selection
        function handleModalFileSelect(input) {
            if (input.files && input.files[0]) {
                scanQrFromFile(input.files[0]);
            }
        }

        const dropzone = document.getElementById('modalDropzone');
        if (dropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('drag-active');
                }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('drag-active');
                }, false);
            });
            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files[0]) {
                    scanQrFromFile(files[0]);
                }
            }, false);
        }

        function scanQrFromFile(file) {
            document.getElementById('scannerMessage').textContent = `Memindai gambar: ${file.name}...`;
            if (!globalHtml5Qr) {
                globalHtml5Qr = new Html5Qrcode("modalQrReader");
            }
            globalHtml5Qr.scanFile(file, true)
                .then(decodedText => {
                    handleDetectedCode(decodedText);
                })
                .catch(err => {
                    document.getElementById('scannerMessage').innerHTML = `<span class="text-danger">QR Code tidak terdeteksi pada gambar ini. Silakan coba gambar tiket yang lebih jelas.</span>`;
                });
        }

        function handleDetectedCode(code) {
            document.getElementById('scannerMessage').innerHTML = `<span class="text-info"><i class="bi bi-hourglass-split"></i> Memverifikasi tiket ${code}...</span>`;
            
            fetch("{{ route('checkout.lookup') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ code: code })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.ticket) {
                    const t = data.ticket;
                    document.getElementById('resPlate').textContent = t.plate;
                    document.getElementById('resCode').textContent = t.code;
                    document.getElementById('resSlot').textContent = `${t.area} (${t.slot})`;
                    document.getElementById('resDuration').textContent = t.duration;
                    document.getElementById('hiddenExitCode').value = t.code;
                    document.getElementById('resDetailLink').href = `/checkout?q=${encodeURIComponent(t.code)}`;
                    
                    document.getElementById('scanResultCard').style.display = 'block';
                    document.getElementById('scannerMessage').innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> Tiket aktif ditemukan!</span>`;
                } else {
                    document.getElementById('scannerMessage').innerHTML = `<span class="text-warning">${data.message || 'Tiket tidak ditemukan atau sudah keluar.'}</span>`;
                }
            })
            .catch(err => {
                // If not found as active, redirect to checkout lookup
                window.location.href = `/checkout?q=${encodeURIComponent(code)}`;
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
