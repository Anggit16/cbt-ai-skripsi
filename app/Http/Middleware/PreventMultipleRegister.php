<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PreventMultipleRegister
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya berlaku untuk halaman register
        if ($request->is('register') || $request->is('ujian/register/*')) {

            // Ambil IP address user
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
            $uniqueKey = 'register_attempt_' . md5($ipAddress . $userAgent);

            // Cek apakah user sudah dalam proses register
            $isRegistering = Cache::get($uniqueKey);

            if ($isRegistering) {
                return back()->withErrors([
                    'email' => 'Anda sedang dalam proses registrasi. Silahkan tunggu beberapa saat.'
                ])->withInput();
            }

            // Tandai user sedang register (lock selama 30 detik)
            Cache::put($uniqueKey, true, now()->addSeconds(30));
        }

        return $next($request);
    }
}