<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CBT System')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Navbar Styles - hanya tampil jika user sudah login */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: #2d3748 !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            background: none;
            border: none;
            padding: 8px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .user-dropdown .dropdown-toggle:hover {
            background: #f7fafc;
            color: #667eea;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102,126,234,0.4);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .modal-content {
            border-radius: 20px;
            overflow: hidden;
        }

        .modal-header {
            border-bottom: none;
            padding: 20px 25px;
        }

        .modal-body {
            padding: 30px 25px;
        }

        .modal-footer {
            border-top: none;
            padding: 20px 25px 30px;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            border: none;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229,62,62,0.4);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
            border: none;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }

        /* Form Styles */
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        /* Alert Styles */
        .alert {
            border-radius: 12px;
            border: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }

            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }

            .user-dropdown .dropdown-toggle span {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar - Hanya tampil jika user sudah login -->
    @auth
    <nav class="navbar-custom">
        <div class="navbar-container">
            <a class="navbar-brand" href="{{ Auth::user()->isGuru() ? '/guru/dashboard' : '/peserta/dashboard' }}">
                <i class="fas fa-graduation-cap me-2"></i>
                CBT System
            </a>

            <div class="user-dropdown">
                <div class="dropdown">
                    <button class="dropdown-toggle btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down ms-1" style="font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header text-muted px-3 py-2">
                            <small>{{ Auth::user()->email }}</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form-navbar">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger" id="logoutBtn">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; max-width: 400px;" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999; max-width: 400px;" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <!-- Modal Konfirmasi Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="logoutModalLabel">
                        <i class="fas fa-sign-out-alt me-2"></i>Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-question-circle fa-4x text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Apakah Anda yakin ingin logout?</h5>
                    <p class="text-muted mb-0">Anda akan keluar dari sistem dan perlu login kembali untuk mengakses akun Anda.</p>
                </div>
                <div class="modal-footer justify-content-center gap-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger px-4" id="confirmLogoutBtn">
                        <i class="fas fa-sign-out-alt me-2"></i>Ya, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto close alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Mencegah pengguna kembali ke halaman login setelah login
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };

        // Mencegah cache halaman
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || performance.getEntriesByType("navigation")[0].type === 'back_forward') {
                window.location.reload();
            }
        });
    </script>

    <script>
        // Deteksi multiple tab dengan localStorage
        (function() {
            const tabId = Math.random().toString(36).substring(2, 15);
            const storageKey = 'active_tab_id';

            localStorage.setItem(storageKey, tabId);

            window.addEventListener('storage', function(e) {
                if (e.key === storageKey && e.newValue !== tabId) {
                    alert('Tab baru terdeteksi! Halaman akan di-refresh.');
                    window.location.reload();
                }
            });

            window.addEventListener('beforeunload', function() {
                if (localStorage.getItem(storageKey) === tabId) {
                    localStorage.removeItem(storageKey);
                }
            });
        })();

        // Cegah submit form ganda
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.classList.contains('no-double-submit')) {
                if (form.dataset.submitted === 'true') {
                    e.preventDefault();
                    return false;
                }
                form.dataset.submitted = 'true';

                setTimeout(() => {
                    delete form.dataset.submitted;
                }, 5000);
            }
        });
    </script>

    <script>
        // ==================== LOGOUT DENGAN KONFIRMASI ====================
        let logoutFormToSubmit = null;

        // Fungsi untuk menampilkan modal konfirmasi logout
        function showLogoutConfirmation(event, formElement) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Simpan form yang akan disubmit
            logoutFormToSubmit = formElement;

            // Tampilkan modal
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            logoutModal.show();
        }

        // Fungsi untuk melakukan logout
        function executeLogout() {
            if (logoutFormToSubmit) {
                logoutFormToSubmit.submit();
            }
        }

        // Event listener setelah halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Cari form logout di navbar
            const logoutForm = document.getElementById('logout-form-navbar');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showLogoutConfirmation(e, this);
                });
            }

            // Tombol konfirmasi logout di modal
            const confirmBtn = document.getElementById('confirmLogoutBtn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    executeLogout();
                });
            }

            // Tombol batal di modal - reset form reference
            const cancelBtn = document.querySelector('#logoutModal .btn-secondary');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    logoutFormToSubmit = null;
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>