@extends('layouts.app')

@section('title', 'Login Guru - CBT System')

@section('content')
<style>
/* ==================== CEGAH SCROLL ==================== */
html, body {
    overflow: hidden;
    height: 100%;
    margin: 0;
    padding: 0;
}

/* ==================== VARIABEL WARNA ==================== */
:root {
    --primary-start: #667eea;
    --primary-end: #764ba2;
    --primary-light: #f093fb;
    --secondary: #4facfe;
}

/* ==================== BACKGROUND DINAMIS ==================== */
.login-wrapper {
    min-height: 100vh;
    height: 100vh;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

/* Animated Gradient Orbs */
.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    animation: floatOrb 20s infinite ease-in-out;
    opacity: 0.4;
}

.orb-1 {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, #667eea, #764ba2);
    top: -100px;
    left: -100px;
}

.orb-2 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, #4facfe, #f093fb);
    bottom: -150px;
    right: -150px;
    animation-delay: 5s;
}

.orb-3 {
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, #ff9a9e, #fecfef);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: 10s;
    opacity: 0.2;
}

@keyframes floatOrb {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(50px, -50px) scale(1.1); }
    66% { transform: translate(-30px, 40px) scale(0.9); }
}

/* Particle Canvas */
#particleCanvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

/* ==================== CARD LOGIN ==================== */
.login-card {
    position: relative;
    z-index: 10;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    border-radius: 48px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    width: 100%;
    max-width: 480px;
    overflow: hidden;
}

.card-decoration {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
}

/* ==================== TOMBOL KEMBALI ==================== */
.btn-back-home {
    position: relative;
    z-index: 15;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 10px 24px;
    border-radius: 40px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-bottom: 20px;
    text-decoration: none;
}

.btn-back-home:hover {
    background: white;
    color: #667eea;
    transform: translateX(-5px);
}

/* ==================== LOGO ==================== */
.logo-wrapper {
    text-align: center;
    margin-bottom: 24px;
}

.logo-circle {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    box-shadow: 0 15px 35px -8px rgba(102, 126, 234, 0.4);
}

.logo-circle i {
    font-size: 44px;
    color: white;
}

.logo-wrapper h3 {
    font-size: 26px;
    font-weight: 800;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 4px;
}

.logo-wrapper p {
    color: #6c757d;
    font-size: 14px;
}

/* ==================== FORM STYLING ==================== */
.form-group {
    margin-bottom: 24px;
}

.form-label {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 8px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Input dengan icon di dalam */
.input-wrapper {
    position: relative;
    width: 100%;
}

.input-icon-left {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    z-index: 2;
    pointer-events: none;
}

.input-icon-right {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    z-index: 2;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.input-icon-right:hover {
    color: #667eea;
}

.input-icon-right:focus {
    outline: none;
}

.input-wrapper .form-control {
    width: 100%;
    padding: 14px 48px;
    border: 2px solid #e2e8f0;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #f8fafc;
}

.input-wrapper .form-control:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.input-wrapper .form-control.error {
    border-color: #ef4444;
}

/* ==================== CHECKBOX ==================== */
.checkbox-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
}

.checkbox-custom {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-custom input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #667eea;
}

.checkbox-custom span {
    color: #475569;
    font-size: 14px;
}

.forgot-link {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

.forgot-link:hover {
    color: #764ba2;
    text-decoration: underline;
}

/* ==================== TOMBOL LOGIN ==================== */
.btn-login {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 14px 24px;
    border-radius: 40px;
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s ease;
    width: 100%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.5);
}

.btn-login:disabled {
    opacity: 0.7;
    transform: none;
    cursor: not-allowed;
}

.btn-login .spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ==================== DIVIDER ==================== */
.divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 28px 0;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e2e8f0;
}

.divider span {
    padding: 0 16px;
    color: #94a3b8;
    font-size: 13px;
}

/* ==================== REGISTER LINK ==================== */
.register-section {
    text-align: center;
    margin-top: 24px;
}

.register-section p {
    color: #475569;
    margin-bottom: 0;
}

.register-section a {
    color: #667eea;
    text-decoration: none;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.register-section a:hover {
    color: #764ba2;
    gap: 10px;
}

/* ==================== ALERT STYLING ==================== */
.alert-modern {
    border-radius: 20px;
    border: none;
    padding: 14px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideDown 0.4s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: #ecfdf5;
    color: #059669;
    border-left: 4px solid #10b981;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border-left: 4px solid #ef4444;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    opacity: 0.7;
}

.alert-close:hover {
    opacity: 1;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    .login-wrapper {
        padding: 16px;
    }

    .login-card {
        max-width: 100%;
        border-radius: 32px;
    }

    .logo-circle {
        width: 70px;
        height: 70px;
    }

    .logo-circle i {
        font-size: 34px;
    }

    .logo-wrapper h3 {
        font-size: 22px;
    }

    .input-wrapper .form-control {
        padding: 12px 44px;
    }

    .orb-1, .orb-2, .orb-3 {
        display: none;
    }

    .login-wrapper {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
}

@media (max-width: 480px) {
    .login-card .card-body {
        padding: 24px !important;
    }

    .checkbox-wrapper {
        flex-direction: column;
        align-items: flex-start;
    }

    .btn-back-home {
        padding: 8px 18px;
        font-size: 13px;
    }

    .logo-circle {
        width: 60px;
        height: 60px;
    }

    .logo-circle i {
        font-size: 28px;
    }
}
</style>

<div class="login-wrapper">
    <!-- Background Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Particle Canvas -->
    <canvas id="particleCanvas"></canvas>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <!-- Tombol Kembali -->
                <div>
                    <a href="{{ route('welcome') }}" class="btn-back-home">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>

                <!-- Card Login -->
                <div class="login-card">
                    <div class="card-decoration"></div>
                    <div class="card-body p-4 p-md-5">
                        <!-- Logo & Title -->
                        <div class="logo-wrapper">
                            <div class="logo-circle">
                                <i class="fas fa-chalkboard-user"></i>
                            </div>
                            <h3>Selamat Datang!</h3>
                            <p>Silahkan login untuk mengelola ujian</p>
                        </div>

                        <!-- Alert Messages -->
                        @if(session('success'))
                            <div class="alert-modern alert-success">
                                <i class="fas fa-check-circle fa-lg"></i>
                                <span>{{ session('success') }}</span>
                                <button type="button" class="alert-close" data-bs-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert-modern alert-danger">
                                <i class="fas fa-exclamation-circle fa-lg"></i>
                                <span>{{ session('error') }}</span>
                                <button type="button" class="alert-close" data-bs-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert-modern alert-danger">
                                <i class="fas fa-exclamation-circle fa-lg"></i>
                                <span>{{ $errors->first() }}</span>
                                <button type="button" class="alert-close" data-bs-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}" class="no-double-submit" id="loginForm">
                            @csrf
                            <input type="hidden" name="role" value="guru">

                            <!-- Email Field -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope text-primary"></i>
                                    Alamat Email
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon-left"></i>
                                    <input type="email" class="form-control @error('email') error @enderror"
                                           id="email" name="email" value="{{ old('email') }}"
                                           placeholder="contoh: guru@email.com" required autofocus>
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-lock text-primary"></i>
                                    Password
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon-left"></i>
                                    <input type="password" class="form-control @error('password') error @enderror"
                                           id="password" name="password" placeholder="Masukkan password" required>
                                    <button type="button" class="input-icon-right" onclick="togglePassword()">
                                        <i class="fas fa-eye-slash" id="passwordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Remember & Forgot -->
                            <div class="checkbox-wrapper">
                                <label class="checkbox-custom">
                                    <input type="checkbox" id="remember" name="remember">
                                    <span>Ingat saya</span>
                                </label>
                                <a href="#" class="forgot-link">Lupa password?</a>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn-login" id="loginBtn">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login sebagai Guru</span>
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="divider">
                            <span>atau</span>
                        </div>

                        <!-- Register Link -->
                        <div class="register-section">
                            <p>Belum punya akun?
                                <a href="{{ route('register') }}">
                                    <i class="fas fa-user-plus"></i>
                                    Daftar sebagai Guru
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle Password Visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('passwordIcon');

    if (!passwordInput || !icon) return;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

// Particle Animation
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('particleCanvas');
    if (canvas) {
        let ctx = canvas.getContext('2d');
        let particles = [];
        let particleCount = 50;

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        function createParticles() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    radius: Math.random() * 3 + 1,
                    alpha: Math.random() * 0.5 + 0.2,
                    speedX: (Math.random() - 0.5) * 0.5,
                    speedY: (Math.random() - 0.5) * 0.5
                });
            }
        }

        function drawParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = 0; i < particles.length; i++) {
                let p = particles[i];
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255, 255, 255, ${p.alpha})`;
                ctx.fill();

                p.x += p.speedX;
                p.y += p.speedY;

                if (p.x < 0) p.x = canvas.width;
                if (p.x > canvas.width) p.x = 0;
                if (p.y < 0) p.y = canvas.height;
                if (p.y > canvas.height) p.y = 0;
            }

            requestAnimationFrame(drawParticles);
        }

        resizeCanvas();
        createParticles();
        drawParticles();

        window.addEventListener('resize', function() {
            resizeCanvas();
            createParticles();
        });
    }

    // Prevent Double Submit
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (loginForm.classList.contains('submitted')) {
                e.preventDefault();
                return false;
            }
            loginForm.classList.add('submitted');

            const originalText = loginBtn.innerHTML;
            loginBtn.innerHTML = '<span class="spinner"></span><span>Memproses...</span>';
            loginBtn.disabled = true;

            setTimeout(() => {
                if (loginBtn.disabled) {
                    loginBtn.innerHTML = originalText;
                    loginBtn.disabled = false;
                    loginForm.classList.remove('submitted');
                }
            }, 5000);
        });
    }

    // Auto close alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-modern');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});
</script>
@endsection