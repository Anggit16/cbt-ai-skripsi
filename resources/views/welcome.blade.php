<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBT System - Computer Based Test Platform</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

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
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, 20px); }
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        /* Card Login */
        .login-card {
            background: white;
            border-radius: 24px;
            padding: 35px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(0,0,0,0.3);
        }

        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea20, #764ba220);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .login-icon i {
            font-size: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-card h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1a1a2e;
        }

        .login-card p {
            color: #718096;
            margin-bottom: 20px;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102,126,234,0.4);
        }

        /* Feature Section */
        .feature-section {
            background: white;
            padding: 80px 0;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 50px;
            color: #1a1a2e;
        }

        .feature-card {
            text-align: center;
            padding: 30px;
            border-radius: 20px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea20, #764ba220);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .feature-icon i {
            font-size: 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-card h4 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #1a1a2e;
        }

        .feature-card p {
            color: #718096;
            font-size: 0.9rem;
        }

        /* Preview Section */
        .preview-section {
            background: #f0f4f8;
            padding: 80px 0;
        }

        .preview-tabs {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .preview-tab {
            padding: 12px 30px;
            border-radius: 40px;
            background: white;
            border: none;
            font-weight: 600;
            color: #718096;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .preview-tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        }

        .preview-tab:hover:not(.active) {
            background: #e2e8f0;
        }

        .preview-content {
            display: none;
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }

        .preview-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .preview-image {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Footer */
        .footer {
            background: #1a1a2e;
            color: white;
            padding: 40px 0;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
                text-align: center;
            }
            .hero-subtitle {
                text-align: center;
            }
            .login-card {
                margin-bottom: 20px;
            }
            .preview-tab {
                padding: 8px 20px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-graduation-cap me-2"></i>
            CBT System
        </a>
        <div>
            <a href="#features" class="text-decoration-none me-3" style="color: #4a5568;">Fitur</a>
            <a href="#preview" class="text-decoration-none me-3" style="color: #4a5568;">Preview</a>
            <a href="#contact" class="text-decoration-none" style="color: #4a5568;">Kontak</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="hero-title">Solusi Ujian Digital Modern & Terpercaya</h1>
                <p class="hero-subtitle">
                    Sistem Computer Based Test (CBT) dengan penilaian otomatis untuk soal essay menggunakan teknologi AI.
                    Praktis, efisien, dan akurat.
                </p>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="login-card" onclick="window.location.href='/login'">
                            <div class="login-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h3>Login Guru</h3>
                            <p>Buat ujian, kelola soal, dan lihat hasil peserta</p>
                            <button class="btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Login sebagai Guru
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="login-card" onclick="window.location.href='/login/siswa'">
                            <div class="login-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h3>Login Peserta</h3>
                            <p>Ikuti ujian, kerjakan soal, dan lihat hasil</p>
                            <button class="btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Login Peserta
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Section -->
<section class="feature-section" id="features">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Fitur Unggulan</h2>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h4>Penilaian AI</h4>
                    <p>Penilaian essay otomatis menggunakan algoritma Natural Language Processing (NLP) untuk akurasi maksimal</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Hasil Real-time</h4>
                    <p>Hasil ujian langsung keluar setelah selesai, dilengkapi dengan statistik dan peringkat</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Aman & Terpercaya</h4>
                    <p>Sistem keamanan berlapis dengan proteksi session dan validasi role pengguna</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <h4>Dukungan Gambar</h4>
                    <p>Soal dapat dilengkapi gambar dan opsi jawaban PG bergambar</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h4>Responsive Design</h4>
                    <p>Akses dari berbagai perangkat: Desktop, Tablet, maupun Smartphone</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-simple"></i>
                    </div>
                    <h4>Statistik Lengkap</h4>
                    <p>Laporan nilai, distribusi nilai, grafik, dan export data ke CSV</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Preview Section -->
<section class="preview-section" id="preview">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Preview Sistem</h2>

        <div class="preview-tabs" data-aos="fade-up">
            <button class="preview-tab active" onclick="showPreview('guru')">Dashboard Guru</button>
            <button class="preview-tab" onclick="showPreview('soal')">Buat Soal</button>
            <button class="preview-tab" onclick="showPreview('ujian')">Mengerjakan Ujian</button>
            <button class="preview-tab" onclick="showPreview('hasil')">Hasil Ujian</button>
        </div>

        <div id="preview-guru" class="preview-content active" data-aos="fade-up">
            <div class="text-center">
                <img src="{{ asset('images/preview/dashboard-guru.png') }}" alt="Dashboard Guru" class="preview-image"
                     onerror="this.src='https://via.placeholder.com/800x500?text=Dashboard+Guru'">
                <p class="mt-3 text-muted">Dashboard Guru - Kelola ujian, lihat statistik, dan akses cepat</p>
            </div>
        </div>

        <div id="preview-soal" class="preview-content">
            <div class="text-center">
                <img src="{{ asset('images/preview/buat-soal.png') }}" alt="Buat Soal" class="preview-image"
                     onerror="this.src='https://via.placeholder.com/800x500?text=Buat+Soal'">
                <p class="mt-3 text-muted">Buat Soal - Pilihan Ganda & Essay dengan dukungan gambar</p>
            </div>
        </div>

        <div id="preview-ujian" class="preview-content">
            <div class="text-center">
                <img src="{{ asset('images/preview/ujian.png') }}" alt="Mengerjakan Ujian" class="preview-image"
                     onerror="this.src='https://via.placeholder.com/800x500?text=Mengerjakan+Ujian'">
                <p class="mt-3 text-muted">Mengerjakan Ujian - Navigasi soal, timer, dan auto-save jawaban</p>
            </div>
        </div>

        <div id="preview-hasil" class="preview-content">
            <div class="text-center">
                <img src="{{ asset('images/preview/hasil.png') }}" alt="Hasil Ujian" class="preview-image"
                     onerror="this.src='https://via.placeholder.com/800x500?text=Hasil+Ujian'">
                <p class="mt-3 text-muted">Hasil Ujian - Detail nilai, peringkat, dan distribusi nilai</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="feature-section" id="contact">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center" data-aos="fade-up">
                <h2 class="section-title">Siap Menggunakan CBT System?</h2>
                <p class="text-muted mb-4">Daftar sekarang dan rasakan kemudahan ujian digital berbasis AI</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('register') }}" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-user-plus me-2"></i>Daftar sebagai Guru
                    </a>
                    <a href="{{ route('login.siswa') }}" class="btn btn-outline-primary px-4 py-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Login Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p class="mb-0">&copy; 2024 CBT System - Computer Based Test Platform. All rights reserved.</p>
        <div class="mt-2">
            <a href="#" class="text-white-50 text-decoration-none me-3">Tentang</a>
            <a href="#" class="text-white-50 text-decoration-none me-3">Kebijakan Privasi</a>
            <a href="#" class="text-white-50 text-decoration-none">Bantuan</a>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });

    function showPreview(type) {
        // Sembunyikan semua preview
        document.querySelectorAll('.preview-content').forEach(content => {
            content.classList.remove('active');
        });

        // Nonaktifkan semua tab
        document.querySelectorAll('.preview-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Tampilkan preview yang dipilih
        document.getElementById(`preview-${type}`).classList.add('active');

        // Aktifkan tab yang diklik
        event.target.classList.add('active');
    }
</script>
</body>
</html>