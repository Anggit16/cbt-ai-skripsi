<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // SUPER ADMIN BISA AKSES SEMUA HALAMAN
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Untuk user biasa (guru/siswa), cek role yang diizinkan
        $allowedRoles = explode('|', $role);

        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}