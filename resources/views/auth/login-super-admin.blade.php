@extends('layouts.app')

@section('title', 'Login Super Admin - CBT System')

@section('content')
<div class="container">
    <div class="row justify-content-center" style="min-height: 100vh; align-items: center;">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-crown fa-3x" style="color: #fbbf24;"></i>
                        </div>
                        <h3 class="fw-bold mb-2" style="color: #2d3748;">Login Super Admin</h3>
                        <p class="text-muted">Akses khusus administrator</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="no-double-submit">
                        @csrf
                        <input type="hidden" name="role" value="super_admin">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Super Admin</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}"
                                   placeholder="admin@cbt.com" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" placeholder="********" required>
                                <button type="button" class="btn-toggle-password" onclick="togglePassword()"
                                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8;">
                                    <i class="fas fa-eye-slash" id="passwordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-2" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border: none; color: white; font-weight: 600;">
                            <i class="fas fa-sign-in-alt me-2"></i>Login sebagai Super Admin
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">
                            <a href="{{ route('login') }}" class="text-decoration-none">Login sebagai Guru</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('passwordIcon');

    if (!icon) return;

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
</script>
@endsection