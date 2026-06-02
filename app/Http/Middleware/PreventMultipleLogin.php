<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventMultipleLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user belum login, lanjutkan
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $userId = $user->id;
        $sessionId = session()->getId();

        // Cek apakah user sudah login di session lain
        $cachedSessionId = Cache::get('user_session_' . $userId);

        // Jika session ID berbeda, artinya user login di tab/window lain
        if ($cachedSessionId && $cachedSessionId !== $sessionId) {
            // Logout user dari session saat ini
            Auth::logout();
            session()->flush();

            // Redirect ke halaman login dengan pesan
            return redirect()->route('login')->withErrors([
                'email' => 'Anda sudah login di tab/window lain. Silahkan tutup tab lain dan login kembali.'
            ]);
        }

        // Simpan session ID ke cache
        Cache::put('user_session_' . $userId, $sessionId, now()->addMinutes(120));

        return $next($request);
    }
}