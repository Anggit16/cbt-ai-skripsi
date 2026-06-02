<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventNavigationDuringExam
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sedang login sebagai siswa
        if (Auth::check() && Auth::user()->role === 'siswa') {

            // CEK SESSION UJIAN DI SERVER
            $examInProgress = session('exam_in_progress', false);
            $currentPath = $request->path();

            // TAMBAHKAN: Cek juga dari request header (untuk keamanan ekstra)
            $isExamPage = str_contains($currentPath, 'peserta/ujian/') ||
                          str_contains($currentPath, 'peserta/submit-answer/') ||
                          str_contains($currentPath, 'peserta/submit-exam/');

            // Jika sedang ujian dan bukan di halaman yang diizinkan
            if ($examInProgress && !$isExamPage) {

                // Redirect untuk non-AJAX request
                if (!$request->ajax()) {
                    $examId = session('exam_id');
                    if ($examId) {
                        return redirect()->route('peserta.ujian', $examId)
                            ->with('error', '⚠️ Anda sedang dalam sesi ujian! Tidak diperbolehkan membuka halaman lain.');
                    }
                }
            }
        }

        return $next($request);
    }
}