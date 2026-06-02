@extends('layouts.app')

@section('title', 'Registrasi Guru - CBT System')

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

/* ==================== WRAPPER UTAMA ==================== */
.register-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 80px 20px 20px 20px;
}

/* Tombol Kembali - FIXED DI ATAS (TIDAK IKUT SCROLL) */
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

/* Container untuk konten yang di-scroll */
.register-container {
    width: 100%;
    max-width: 500px;
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

/* ==================== CARD REGISTRASI ==================== */
.register-card {
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

.register-card:hover {
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
    margin-bottom: 28px;
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

/* ==================== TOMBOL DAFTAR ==================== */
.btn-register {
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
    margin-top: 8px;
}

.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.5);
}

.btn-register:disabled {
    opacity: 0.7;
    transform: none;
    cursor: not-allowed;
}

.btn-register .spinner {
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

/* ==================== LOGIN LINK ==================== */
.login-section {
    text-align: center;
    margin-top: 8px;
}

.login-section p {
    color: #475569;
    margin-bottom: 0;
    font-size: 13px;
}

.login-section a {
    color: #667eea;
    text-decoration: none;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.login-section a:hover {
    color: #764ba2;
    gap: 10px;
}

/* ==================== ALERT STYLING ==================== */
.alert-modern {
    border-radius: 16px;
    border: none;
    padding: 12px 16px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
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
.register-wrapper::-webkit-scrollbar {
    width: 6px;
}

.register-wrapper::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.register-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}

.register-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    .register-wrapper {
        padding: 80px 16px 20px 16px;
    }

    .btn-back-home {
        top: 12px;
        left: 12px;
        padding: 8px 18px;
        font-size: 13px;
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
}

@media (max-width: 480px) {
    .register-card .card-body {
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

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        font-size: 12px;
    }
}
</style>
@endpush

@section('content')
<div class="register-wrapper">
    <!-- Background Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Particle Canvas -->
    <canvas id="particleCanvas"></canvas>

    <!-- Tombol Kembali ke Beranda - FIXED (TIDAK IKUT SCROLL) -->
    <a href="{{ route('welcome') }}" class="btn-back-home">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali ke Beranda</span>
    </a>

    <div class="register-container">
        <!-- Card Registrasi -->
        <div class="register-card">
            <div class="card-decoration"></div>
            <div class="card-body p-4 p-md-5">
                <!-- Logo & Title -->
                <div class="logo-wrapper">
                    <div class="logo-circle">
                        <i class="fas fa-chalkboard-user"></i>
                    </div>
                    <h3>Registrasi Guru</h3>
                    <p>Daftar untuk membuat ujian</p>
                </div>

                <!-- Alert Container -->
                <div id="alertMessage"></div>

                <!-- Form Registrasi -->
                <form id="registerForm" class="no-double-submit">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user text-primary"></i>
                            Nama Lengkap
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon-left"></i>
                            <input type="text" class="form-control" id="name" name="name"
                                   placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="invalid-feedback" id="nameError"></div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope text-primary"></i>
                            Email
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon-left"></i>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="Masukkan email" required>
                        </div>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock text-primary"></i>
                            Password
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon-left"></i>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Masukkan password (minimal 6 karakter)" required>
                            <button type="button" class="input-icon-right" onclick="togglePassword('password')">
                                <i class="fas fa-eye-slash" id="passwordIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock text-primary"></i>
                            Konfirmasi Password
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon-left"></i>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                   placeholder="Konfirmasi password" required>
                            <button type="button" class="input-icon-right" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye-slash" id="confirmPasswordIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="password_confirmationError"></div>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-book text-primary"></i>
                            Mata Pelajaran yang Diampu
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-book input-icon-left"></i>
                            <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran"
                                   placeholder="Masukkan mata pelajaran" required>
                        </div>
                        <div class="invalid-feedback" id="mata_pelajaranError"></div>
                    </div>

                    <!-- Tombol Daftar -->
                    <button type="submit" class="btn-register" id="submitBtn">
                        <i class="fas fa-user-plus"></i>
                        <span>Daftar</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider">
                    <span>atau</span>
                </div>

                <!-- Login Link -->
                <div class="login-section">
                    <p>Sudah punya akun?
                        <a href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i>
                            Login disini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle Password Visibility
function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const icon = fieldId === 'password' ? document.getElementById('passwordIcon') : document.getElementById('confirmPasswordIcon');

    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

// Show Alert Function
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertMessage');
    if (!alertContainer) return;

    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const alertHtml = `
        <div class="alert-modern ${alertClass}">
            <i class="fas ${icon} fa-lg"></i>
            <span>${message}</span>
            <button type="button" class="alert-close" data-bs-dismiss="alert">&times;</button>
        </div>
    `;

    alertContainer.innerHTML = alertHtml;

    setTimeout(() => {
        const alert = document.querySelector('#alertMessage .alert-modern');
        if (alert) {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);
}

function showFieldError(field, message) {
    const fieldMap = {
        'name': 'nameError',
        'email': 'emailError',
        'password': 'passwordError',
        'password_confirmation': 'password_confirmationError',
        'mata_pelajaran': 'mata_pelajaranError'
    };

    const errorId = fieldMap[field];
    if (errorId) {
        $(`#${field}`).addClass('is-invalid');
        $(`#${errorId}`).text(message);
    }
}

// Handle Register Form
$(document).ready(function() {
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();

        // Reset error messages
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#alertMessage').html('');

        // Get form data
        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            password: $('#password').val(),
            password_confirmation: $('#password_confirmation').val(),
            mata_pelajaran: $('#mata_pelajaran').val(),
            _token: $('input[name="_token"]').val()
        };

        // Validasi sederhana
        let hasError = false;

        if (!formData.name) {
            showFieldError('name', 'Nama lengkap harus diisi');
            hasError = true;
        }

        if (!formData.email) {
            showFieldError('email', 'Email harus diisi');
            hasError = true;
        }

        if (!formData.password) {
            showFieldError('password', 'Password harus diisi');
            hasError = true;
        } else if (formData.password.length < 6) {
            showFieldError('password', 'Password minimal 6 karakter');
            hasError = true;
        }

        if (formData.password !== formData.password_confirmation) {
            showFieldError('password_confirmation', 'Password tidak cocok');
            hasError = true;
        }

        if (!formData.mata_pelajaran) {
            showFieldError('mata_pelajaran', 'Mata pelajaran harus diisi');
            hasError = true;
        }

        if (hasError) return;

        // Disable submit button
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="spinner"></span><span>Memproses...</span>').prop('disabled', true);

        // Send AJAX request
        $.ajax({
            url: '{{ route("register") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Reset form
                    $('#registerForm')[0].reset();
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000);
                } else {
                    submitBtn.html(originalText).prop('disabled', false);
                    showAlert('danger', response.message || 'Registrasi gagal');
                }
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        showFieldError(field, errors[field][0]);
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    showAlert('danger', xhr.responseJSON.message);
                } else {
                    showAlert('danger', 'Terjadi kesalahan. Silahkan coba lagi.');
                }
            }
        });
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
@endpush