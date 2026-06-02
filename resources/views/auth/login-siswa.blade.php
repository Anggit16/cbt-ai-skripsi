@extends('layouts.app')

@section('title', 'Login Siswa - CBT System')

@push('styles')
<style>
/* ==================== RESET BACKGROUND DARI LAYOUT ==================== */
body {
    background: transparent !important;
    margin: 0;
    padding: 0;
    overflow: hidden !important;
}

html {
    overflow: hidden !important;
}

/* ==================== WRAPPER UTAMA - BISA SCROLL ==================== */
.login-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

/* Tombol Kembali - Hanya muncul jika tidak ada info ujian */
.btn-back-home {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 100;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    color: #667eea;
    border: 1px solid rgba(255, 255, 255, 0.5);
    padding: 10px 24px;
    border-radius: 40px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.btn-back-home:hover {
    background: white;
    color: #764ba2;
    transform: translateX(-5px);
    border-color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

/* Sembunyikan tombol kembali jika ada info ujian */
.has-exam-info .btn-back-home {
    display: none !important;
}

/* Container untuk konten */
.login-container {
    width: 100%;
    max-width: 520px;
    margin: auto;
    position: relative;
    z-index: 10;
}

/* Animated Gradient Orbs */
.orb {
    position: fixed;
    border-radius: 50%;
    filter: blur(60px);
    animation: floatOrb 20s infinite ease-in-out;
    opacity: 0.4;
    pointer-events: none;
    z-index: 0;
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
    position: fixed;
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
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.2);
    width: 100%;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
}

.card-decoration {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
}

/* ==================== LOGO & HEADER ==================== */
.logo-wrapper {
    text-align: center;
    margin-bottom: 24px;
    margin-top: 10px;
}

.logo-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    box-shadow: 0 15px 35px -8px rgba(102, 126, 234, 0.4);
}

.logo-circle i {
    font-size: 38px;
    color: white;
}

.logo-wrapper h3 {
    font-size: 24px;
    font-weight: 800;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 6px;
}

.logo-wrapper p {
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 0;
}

/* ==================== INFO UJIAN CARD ==================== */
.exam-info-card {
    background: linear-gradient(135deg, #f8fafc, #eef2ff);
    border-radius: 20px;
    padding: 16px 20px;
    margin-bottom: 24px;
    border: 1px solid rgba(102, 126, 234, 0.2);
}

.info-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(102, 126, 234, 0.2);
}

.info-header i {
    font-size: 18px;
    color: #667eea;
}

.info-header strong {
    color: #1e293b;
    font-size: 14px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.info-row:last-child {
    margin-bottom: 0;
}

.info-label {
    color: #64748b;
    font-size: 12px;
}

.info-value {
    font-weight: 600;
    color: #1e293b;
    font-size: 13px;
}

/* ==================== FORM STYLING ==================== */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 8px;
    font-size: 13px;
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
    font-size: 14px;
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
    font-size: 14px;
}

.input-icon-right:hover {
    color: #667eea;
}

.input-icon-right:focus {
    outline: none;
}

.input-wrapper .form-control {
    width: 100%;
    padding: 12px 48px;
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

.input-wrapper .form-control.is-invalid {
    border-color: #ef4444;
}

.input-wrapper .form-control.is-invalid:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.invalid-feedback {
    color: #ef4444;
    font-size: 11px;
    margin-top: 6px;
    display: none;
}

.invalid-feedback:not(:empty) {
    display: block;
}

/* ==================== CHECKBOX ==================== */
.checkbox-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
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
    font-size: 13px;
}

/* ==================== TOMBOL LOGIN ==================== */
.btn-login {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 40px;
    font-weight: 700;
    font-size: 15px;
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
    width: 18px;
    height: 18px;
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
    margin: 24px 0 20px;
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
    font-size: 12px;
}

/* ==================== REGISTER LINK ==================== */
.register-section {
    text-align: center;
    margin-top: 8px;
}

.register-section p {
    color: #475569;
    margin-bottom: 0;
    font-size: 13px;
}

.register-section a {
    color: #667eea;
    text-decoration: none;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.register-section a:hover {
    color: #764ba2;
    gap: 10px;
}

.info-note {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e2e8f0;
}

.info-note small {
    color: #94a3b8;
    font-size: 12px;
}

/* ==================== ALERT CUSTOM ==================== */
.alert-custom {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    max-width: 400px;
    border-radius: 16px;
    border: none;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideInRight 0.3s ease-out;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.alert-success {
    background: #ecfdf5;
    color: #059669;
    border-left: 3px solid #10b981;
}

.alert-danger {
    background: #fef2f2;
    color: #dc2626;
    border-left: 3px solid #ef4444;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.alert-close:hover {
    opacity: 1;
}

/* Custom Scrollbar */
.login-wrapper::-webkit-scrollbar {
    width: 6px;
}

.login-wrapper::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.login-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}

.login-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    .login-wrapper {
        padding: 20px 16px;
    }

    .btn-back-home {
        top: 12px;
        left: 12px;
        padding: 8px 18px;
        font-size: 13px;
    }

    .login-card {
        max-width: 100%;
        border-radius: 32px;
    }

    .logo-circle {
        width: 65px;
        height: 65px;
    }

    .logo-circle i {
        font-size: 30px;
    }

    .logo-wrapper h3 {
        font-size: 20px;
    }

    .logo-wrapper p {
        font-size: 12px;
    }

    .input-wrapper .form-control {
        padding: 10px 44px;
        font-size: 13px;
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

    .btn-back-home {
        top: 10px;
        left: 10px;
        padding: 6px 14px;
        font-size: 11px;
    }

    .logo-circle {
        width: 55px;
        height: 55px;
    }

    .logo-circle i {
        font-size: 26px;
    }

    .checkbox-wrapper {
        flex-direction: column;
        align-items: flex-start;
    }

    .exam-info-card {
        padding: 14px;
    }
}
</style>
@endpush

@section('content')
<div class="login-wrapper {{ isset($exam) && $exam ? 'has-exam-info' : '' }}">
    <!-- Background Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Particle Canvas -->
    <canvas id="particleCanvas"></canvas>

    <!-- Tombol Kembali ke Beranda - HANYA MUNCUL JIKA TIDAK ADA INFORMASI UJIAN -->
    @if(!isset($exam) || !$exam)
    <a href="{{ route('welcome') }}" class="btn-back-home">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali ke Beranda</span>
    </a>
    @endif

    <div class="login-container">
        <!-- Card Login -->
        <div class="login-card">
            <div class="card-decoration"></div>
            <div class="card-body p-4 p-md-5">
                <!-- Logo & Title -->
                <div class="logo-wrapper">
                    <div class="logo-circle">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Login Peserta</h3>
                    <p>Silahkan login untuk mengikuti ujian</p>
                </div>

                <!-- Informasi Ujian (jika ada parameter kode_ujian) -->
                @if(isset($exam) && $exam)
                <div class="exam-info-card">
                    <div class="info-header">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi Ujian</strong>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Judul Ujian:</span>
                        <span class="info-value">{{ $exam->judul }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Durasi:</span>
                        <span class="info-value">{{ $exam->durasi }} menit</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kuota Tersedia:</span>
                        <span class="info-value">{{ $exam->jumlah_peserta - $exam->participants()->count() }}/{{ $exam->jumlah_peserta }}</span>
                    </div>
                </div>
                @endif

                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <!-- Login Form -->
                <form id="loginForm">
                    @csrf
                    @if(isset($exam) && $exam)
                        <input type="hidden" name="kode_ujian" value="{{ $exam->kode_ujian }}">
                    @endif

                    <!-- Email Field -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope text-primary"></i>
                            Alamat Email
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon-left"></i>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="Masukkan email" required>
                        </div>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock text-primary"></i>
                            Password
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon-left"></i>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Masukkan password" required>
                            <button type="button" class="input-icon-right" onclick="togglePassword()">
                                <i class="fas fa-eye-slash" id="passwordIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="checkbox-wrapper">
                        <label class="checkbox-custom">
                            <input type="checkbox" id="remember" name="remember">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider">
                    <span>atau</span>
                </div>

                <!-- Register Link -->
                <div class="register-section">
                    @if(isset($exam) && $exam)
                        <p>Belum punya akun?
                            <a href="{{ route('peserta.register-ujian', $exam->kode_ujian) }}">
                                <i class="fas fa-user-plus"></i>
                                Daftar disini
                            </a>
                        </p>
                        <div class="info-note">
                            <small>
                                <i class="fas fa-link me-1"></i>
                                Mendaftar untuk ujian: <strong>{{ $exam->judul }}</strong>
                            </small>
                        </div>
                    @else
                        <p class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Gunakan link ujian yang dibagikan untuk mendaftar
                        </p>
                    @endif
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

// Show Alert Function
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) return;

    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const alertHtml = `
        <div class="alert-custom ${alertClass}">
            <i class="fas ${icon} fa-lg"></i>
            <span>${message}</span>
            <button type="button" class="alert-close" data-bs-dismiss="alert">&times;</button>
        </div>
    `;

    alertContainer.innerHTML = alertHtml;

    setTimeout(() => {
        const alert = document.querySelector('#alertContainer .alert-custom');
        if (alert) {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);
}

// Handle Login Form
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Reset errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.getElementById('emailError').innerHTML = '';
    document.getElementById('passwordError').innerHTML = '';

    const formData = {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        remember: document.getElementById('remember').checked ? 1 : 0,
        role: 'siswa',
        _token: document.querySelector('input[name="_token"]').value
    };

    // Jika ada kode ujian (dari link ujian)
    const kodeUjian = document.querySelector('input[name="kode_ujian"]')?.value;
    if (kodeUjian) {
        formData.kode_ujian = kodeUjian;
    }

    const loginBtn = document.getElementById('loginBtn');
    const originalText = loginBtn.innerHTML;
    loginBtn.innerHTML = '<span class="spinner"></span><span>Loading...</span>';
    loginBtn.disabled = true;

    let loginUrl = '{{ route("login") }}';

    // Jika ada kode ujian, gunakan endpoint khusus
    if (kodeUjian) {
        loginUrl = '{{ url("/ujian/login") }}/' + kodeUjian;
    }

    fetch(loginUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': formData._token,
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', '✅ Login berhasil! Mengarahkan...');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            loginBtn.innerHTML = originalText;
            loginBtn.disabled = false;

            if (data.errors) {
                if (data.errors.email) {
                    document.getElementById('email').classList.add('is-invalid');
                    document.getElementById('emailError').innerHTML = data.errors.email[0];
                }
                if (data.errors.password) {
                    document.getElementById('password').classList.add('is-invalid');
                    document.getElementById('passwordError').innerHTML = data.errors.password[0];
                }
            } else {
                showAlert('danger', data.message || 'Login gagal');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        loginBtn.innerHTML = originalText;
        loginBtn.disabled = false;
        showAlert('danger', 'Terjadi kesalahan. Silahkan coba lagi.');
    });
});

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
});
</script>
@endsection