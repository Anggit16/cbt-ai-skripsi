<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\PesertaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Route untuk halaman utama (landing page)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');
// ==================== HALAMAN UTAMA ====================
Route::get('/masuk', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'super_admin') {
            return redirect('/super-admin/dashboard');
        }
        if ($user->role === 'guru') {
            return redirect('/guru/dashboard');
        }
        return redirect('/peserta/dashboard');
    }
    return redirect('/login');
});

// ==================== ROUTE UNTUK GUEST (BELUM LOGIN) ====================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login/siswa', [AuthController::class, 'showSiswaLoginForm'])->name('login.siswa');
    Route::get('/login/super-admin', [AuthController::class, 'showSuperAdminLoginForm'])->name('login.super-admin');
    Route::post('/login', [AuthController::class, 'login']);

    // Register Guru
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // ROUTE PUBLIC UNTUK UJIAN
    Route::get('/ujian/register/{kodeUjian}', [PesertaController::class, 'registerExamForm'])->name('peserta.register-ujian');
    Route::post('/ujian/register/{kodeUjian}', [PesertaController::class, 'registerExam']);
    Route::post('/ujian/login/{kodeUjian}', [PesertaController::class, 'loginFromExam'])->name('peserta.login-exam');
});

// ==================== ROUTE UNTUK USER YANG SUDAH LOGIN ====================
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ==================== SUPER ADMIN ROUTES ====================
    Route::prefix('super-admin')
        ->middleware(['role:super_admin'])
        ->name('super_admin.')
        ->group(function () {
            Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

            // Manajemen Guru (Full CRUD)
            Route::get('/guru', [SuperAdminController::class, 'guruIndex'])->name('guru');
            Route::get('/guru/create', [SuperAdminController::class, 'guruCreate'])->name('guru.create');
            Route::post('/guru', [SuperAdminController::class, 'guruStore'])->name('guru.store');
            Route::get('/guru/{id}/edit', [SuperAdminController::class, 'guruEdit'])->name('guru.edit');
            Route::put('/guru/{id}', [SuperAdminController::class, 'guruUpdate'])->name('guru.update');
            Route::delete('/guru/{id}', [SuperAdminController::class, 'guruDestroy'])->name('guru.destroy');
            Route::post('/guru/{id}/reset-password', [SuperAdminController::class, 'guruResetPassword'])->name('guru.reset-password');

            // Manajemen Siswa (View Only)
            Route::get('/siswa', [SuperAdminController::class, 'siswaIndex'])->name('siswa');

            // Manajemen Ujian (View Only)
            Route::get('/ujian', [SuperAdminController::class, 'ujianIndex'])->name('ujian');

            // Laporan (View Only)
            Route::get('/laporan', [SuperAdminController::class, 'laporanIndex'])->name('laporan');

            // Pengaturan (View Only)
            Route::get('/pengaturan', [SuperAdminController::class, 'pengaturanIndex'])->name('pengaturan');
        });

    // ==================== GURU ROUTES ====================
    Route::prefix('guru')
        ->middleware(['role:guru'])
        ->name('guru.')
        ->group(function () {
            Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
            Route::get('/buat-ujian', [GuruController::class, 'createExam'])->name('buat-ujian');
            Route::post('/buat-ujian', [GuruController::class, 'storeExam']);
            Route::get('/buat-soal/{examId}', [GuruController::class, 'createQuestions'])->name('buat-soal');

            Route::post('/store-questions/{examId}', [GuruController::class, 'storeQuestions'])->name('guru.store-questions');

            Route::post('/import-soal-excel/{examId}', [GuruController::class, 'importQuestionsFromExcel'])->name('import-soal-excel');

            Route::get('/download-template-excel/{examId}', [GuruController::class, 'downloadTemplateExcel'])->name('download-template-excel');

            Route::get('/edit-soal/{examId}', [GuruController::class, 'editQuestions'])->name('edit-soal');
            Route::post('/update-soal/{examId}', [GuruController::class, 'updateQuestions'])->name('update-soal');

            Route::delete('/hapus-gambar-soal/{questionId}', [GuruController::class, 'deleteQuestionImage'])->name('guru.hapus-gambar-soal');
            Route::delete('/hapus-gambar-opsi/{questionId}/{optionIndex}', [GuruController::class, 'deleteOptionImage'])->name('guru.hapus-gambar-opsi');
            Route::delete('/hapus-soal/{questionId}', [GuruController::class, 'deleteQuestion'])->name('guru.hapus-soal');
            Route::get('/kelola-peserta/{examId}', [GuruController::class, 'manageParticipants'])->name('kelola-peserta');
            Route::get('/kelola-peserta/{examId}/current', [GuruController::class, 'getCurrentParticipants']);
            Route::get('/link/{examId}', [GuruController::class, 'getExamLink'])->name('link');
            Route::post('/start-exam/{examId}', [GuruController::class, 'startExam'])->name('start-exam');
            Route::get('/hasil-ujian/{examId}', [GuruController::class, 'examResults'])->name('hasil-ujian');
            Route::get('/kelola-peserta/{examId}/ready-status', [GuruController::class, 'checkReadyParticipants']);
            // Di dalam route group guru, tambahkan:
            Route::post('/update-target/{examId}', [GuruController::class, 'updateTargetPeserta'])->name('update-target');
            // Di dalam route group guru, tambahkan:
            Route::get('/hasil-ujian', [GuruController::class, 'semuaHasilUjian'])->name('hasil-semua');
            Route::get('/hasil-ujian/{examId}', [GuruController::class, 'detailHasilUjian'])->name('detail-hasil');
            Route::get('/export-hasil/{examId}', [GuruController::class, 'exportHasilUjian'])->name('export-hasil');
        });

    // ==================== PESERTA (SISWA) ROUTES ====================
    Route::prefix('peserta')
        ->middleware(['role:siswa'])
        ->name('peserta.')
        ->group(function () {
            Route::get('/dashboard', [PesertaController::class, 'dashboard'])->name('dashboard');
            Route::get('/waiting/{examId}', [PesertaController::class, 'waiting'])->name('waiting');
            Route::get('/check-status/{examId}', [PesertaController::class, 'checkExamStatus'])->name('check-status');
            Route::get('/check-ready/{examId}', [PesertaController::class, 'checkReady'])->name('check-ready');
            Route::get('/ujian/{examId}', [PesertaController::class, 'takeExam'])->name('ujian');
            Route::post('/submit-answer/{examId}', [PesertaController::class, 'submitAnswer'])->name('submit-answer');
            Route::post('/submit-exam/{examId}', [PesertaController::class, 'submitExam'])->name('submit-exam');
            Route::get('/hasil/{examId}', [PesertaController::class, 'examResults'])->name('hasil');
            Route::post('/mark-ready/{examId}', [PesertaController::class, 'markReady']);
            Route::post('/daftar-ujian/{examId}', [PesertaController::class, 'daftarUjian'])->name('daftar-ujian');
            // Di dalam route group peserta, tambahkan:
            Route::get('/get-leaderboard/{examId}', [PesertaController::class, 'getLeaderboard'])->name('peserta.get-leaderboard');
            // Di dalam route group peserta, tambahkan:
            Route::get('/koreksi/{examId}', [PesertaController::class, 'koreksi'])->name('koreksi');
        });
});