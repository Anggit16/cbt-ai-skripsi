<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\AIEssayScoringService;

class PesertaController extends Controller
{
    protected $aiEssayScoring;

    public function __construct()
    {
        // Inisialisasi AI service
        $this->aiEssayScoring = new AIEssayScoringService();
    }

    /**
     * Dashboard peserta
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Ambil semua partisipasi ujian
        $participations = ExamParticipant::where('siswa_id', $user->id)
                                        ->with('exam')
                                        ->orderBy('created_at', 'desc')
                                        ->get();

        // Hitung statistik
        $totalMenunggu = $participations->filter(function($p) {
            return $p->exam->status === 'menunggu' && $p->status !== 'selesai';
        })->count();

        $totalSelesai = $participations->where('status', 'selesai')->count();

        // Ujian Menunggu (belum dimulai)
        $waitingExams = $participations->filter(function($p) {
            return $p->exam->status === 'menunggu' && $p->status !== 'selesai';
        });

        // Riwayat Ujian (Selesai)
        $historyExams = $participations->where('status', 'selesai');

        return view('peserta.dashboard', compact(
            'participations',
            'totalMenunggu',
            'totalSelesai',
            'waitingExams',
            'historyExams'
        ));
    }

    /**
     * Daftar ujian dari dashboard (peserta yang sudah login)
     */
    public function daftarUjian($examId)
    {
        $exam = Exam::findOrFail($examId);
        $user = Auth::user();

        // Cek apakah sudah terdaftar
        $existing = ExamParticipant::where('exam_id', $examId)
                                ->where('siswa_id', $user->id)
                                ->exists();

        if (!$existing) {
            ExamParticipant::create([
                'exam_id' => $examId,
                'siswa_id' => $user->id,
                'status' => 'terdaftar'
            ]);
        }

        if ($exam->status === 'berlangsung') {
            return redirect()->route('peserta.ujian', $examId);
        }

        return redirect()->route('peserta.waiting', $examId);
    }

    /**
     * Show registration form for exam
     */
    public function registerExamForm($kodeUjian)
    {
        try {
            $exam = Exam::where('kode_ujian', $kodeUjian)
                        ->whereIn('status', ['menunggu', 'berlangsung'])
                        ->firstOrFail();

            // JIKA SUDAH LOGIN
            if (Auth::check()) {
                $user = Auth::user();

                // Pastikan role adalah siswa
                if ($user->role !== 'siswa') {
                    Auth::logout();
                    return redirect()->route('login');
                }

                // Cek apakah sudah terdaftar di ujian ini
                $existingParticipant = ExamParticipant::where('exam_id', $exam->id)
                                                    ->where('siswa_id', $user->id)
                                                    ->first();

                if (!$existingParticipant) {
                    // Otomatis daftarkan ke ujian
                    ExamParticipant::create([
                        'exam_id' => $exam->id,
                        'siswa_id' => $user->id,
                        'status' => 'terdaftar'
                    ]);
                }

                // Redirect ke dashboard
                return redirect()->route('peserta.dashboard');
            }

            // Jika belum login, tampilkan form registrasi dengan tab login & daftar
            return view('peserta.register-ujian', compact('exam'));

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Link ujian tidak valid');
        }
    }

    /**
     * Register existing user to exam (peserta yang sudah punya akun)
     */
    private function registerExistingUser($examId, $user)
    {
        $exam = Exam::findOrFail($examId);

        // Cek kuota
        $currentParticipants = $exam->participants()->count();
        if ($currentParticipants >= $exam->jumlah_peserta) {
            return redirect()->back()->with('error', 'Maaf, kuota peserta sudah penuh!');
        }

        // Cek apakah sudah terdaftar (double check)
        $existing = ExamParticipant::where('exam_id', $examId)
                                ->where('siswa_id', $user->id)
                                ->exists();

        if ($existing) {
            // Jika sudah terdaftar, redirect ke halaman yang sesuai
            if ($exam->status === 'berlangsung') {
                return redirect()->route('peserta.ujian', $examId);
            }
            return redirect()->route('peserta.waiting', $examId);
        }

        // Daftarkan peserta
        ExamParticipant::create([
            'exam_id' => $examId,
            'siswa_id' => $user->id,
            'status' => 'terdaftar'
        ]);

        // Redirect setelah berhasil daftar
        if ($exam->status === 'berlangsung') {
            return redirect()->route('peserta.ujian', $examId);
        }

        return redirect()->route('peserta.waiting', $examId)->with('success', 'Berhasil mendaftar ke ujian!');
    }

    /**
     * Handle login from exam registration page
     * Peserta yang sudah punya akun cukup login, otomatis didaftarkan ke ujian
     */
    public function loginFromExam(Request $request, $kodeUjian)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $exam = Exam::where('kode_ujian', $kodeUjian)
                    ->whereIn('status', ['menunggu', 'berlangsung'])
                    ->firstOrFail();

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Pastikan role adalah siswa
            if ($user->role !== 'siswa') {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun ini bukan akun siswa!'
                ], 401);
            }

            // ========== PERBAIKAN ==========
            // CEK APAKAH SUDAH TERDAFTAR DI UJIAN INI
            $existingParticipant = ExamParticipant::where('exam_id', $exam->id)
                                                ->where('siswa_id', $user->id)
                                                ->first();

            // JIKA BELUM TERDAFTAR, OTOMATIS DAFTARKAN
            if (!$existingParticipant) {
                // Cek kuota
                $currentParticipants = $exam->participants()->count();
                if ($currentParticipants >= $exam->jumlah_peserta) {
                    Auth::logout();
                    return response()->json([
                        'success' => false,
                        'message' => 'Maaf, kuota peserta sudah penuh!'
                    ], 400);
                }

                // Daftarkan ke ujian
                ExamParticipant::create([
                    'exam_id' => $exam->id,
                    'siswa_id' => $user->id,
                    'status' => 'terdaftar'
                ]);
            }
            // ========== END PERBAIKAN ==========

            // Redirect ke dashboard
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'redirect' => route('peserta.dashboard')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah!'
        ], 401);
    }

    /**
     * Register participant to exam (siswa baru)
     */
    public function registerExam(Request $request, $kodeUjian)
    {
        $exam = Exam::where('kode_ujian', $kodeUjian)
                    ->whereIn('status', ['menunggu', 'berlangsung'])
                    ->firstOrFail();

        $currentParticipants = $exam->participants()->count();

        if ($currentParticipants >= $exam->jumlah_peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, kuota peserta sudah penuh!'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'kelas' => 'required|string',
            'jurusan' => 'nullable|string',
            'nomor_absen' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $siswa = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa',
                'kelas' => $request->kelas,
                'jurusan' => $request->jurusan,
                'nomor_absen' => $request->nomor_absen
            ]);

            ExamParticipant::create([
                'exam_id' => $exam->id,
                'siswa_id' => $siswa->id,
                'status' => 'terdaftar'
            ]);

            DB::commit();

            // Redirect ke halaman login dengan parameter kode ujian
            return response()->json([
                'success' => true,
                'message' => '✅ Registrasi berhasil! Silahkan login.',
                'redirect' => route('login.siswa', ['kode' => $kodeUjian])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Waiting page
     */
    public function waiting($examId)
    {
        $exam = Exam::findOrFail($examId);
        $participant = ExamParticipant::where('exam_id', $examId)
                                      ->where('siswa_id', Auth::id())
                                      ->firstOrFail();

        return view('peserta.waiting', compact('exam', 'participant'));
    }

    /**
     * Check exam status (AJAX)
     */
    public function checkExamStatus($examId)
    {
        $exam = Exam::findOrFail($examId);

        return response()->json([
            'status' => $exam->status,
            'can_start' => $exam->status === 'berlangsung'
        ]);
    }

    /**
     * Take exam
     */
    public function takeExam($examId)
    {
        $exam = Exam::with('questions')->findOrFail($examId);
        $participant = ExamParticipant::where('exam_id', $examId)
                                      ->where('siswa_id', Auth::id())
                                      ->firstOrFail();

        if ($exam->status !== 'berlangsung') {
            return redirect()->route('peserta.waiting', $examId);
        }

        if ($participant->status === 'selesai') {
            return redirect()->route('peserta.hasil', $examId)
                           ->with('error', 'Anda sudah menyelesaikan ujian ini.');
        }

        // Set started time if not already
        if (!$participant->started_at) {
            $participant->update(['started_at' => now(), 'status' => 'sedang_mengerjakan']);
        }

        // Hitung sisa waktu
        $timeRemaining = $this->calculateTimeRemaining($participant->started_at, $exam->durasi);

        if ($timeRemaining <= 0) {
            return redirect()->route('peserta.submit-exam', $examId);
        }

        return view('peserta.ujian', compact('exam', 'participant', 'timeRemaining'));
    }

    /**
     * Submit answer (AJAX) - untuk auto save
     */
    public function submitAnswer(Request $request, $examId)
    {
        $participant = ExamParticipant::where('exam_id', $examId)
                                    ->where('siswa_id', Auth::id())
                                    ->firstOrFail();

        if ($participant->status === 'selesai') {
            return response()->json(['error' => 'Ujian sudah selesai'], 400);
        }

        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            foreach ($request->answers as $answerData) {
                Answer::updateOrCreate(
                    [
                        'exam_participant_id' => $participant->id,
                        'question_id' => $answerData['question_id']
                    ],
                    ['answer_text' => $answerData['answer']]
                );
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jawaban: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit exam (final submission) dengan AI scoring
     */
    public function submitExam(Request $request, $examId)
    {
        $participant = ExamParticipant::where('exam_id', $examId)
                                    ->where('siswa_id', Auth::id())
                                    ->firstOrFail();

        if ($participant->status === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Ujian sudah selesai'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Set waktu selesai jika belum
            if (!$participant->started_at) {
                $participant->started_at = now();
            }

            // Ambil semua jawaban peserta
            $answers = Answer::where('exam_participant_id', $participant->id)->get();

            $totalScore = 0;
            $details = [];

            foreach ($answers as $answer) {
                $question = $answer->question;
                $score = 0;
                $similarity = 0;

                if ($question->type === 'pg') {
                    // Penilaian Pilihan Ganda
                    if (strtoupper(trim($answer->answer_text)) === strtoupper(trim($question->correct_answer))) {
                        $score = $question->score;
                    }
                    $answer->update(['score' => $score]);

                } else {
                    // Penilaian Essay dengan NLP AI
                    $result = $this->aiEssayScoring->nilai(
                        $answer->answer_text,
                        $question->correct_answer,
                        $question->score
                    );

                    $score = $result['score'];
                    $similarity = $result['similarity'];

                    $answer->update([
                        'score' => $score,
                        'similarity_score' => $similarity
                    ]);

                    $details[] = [
                        'question_id' => $question->id,
                        'similarity' => $similarity,
                        'score' => $score
                    ];
                }

                $totalScore += $score;
            }

            // Update total nilai peserta
            $participant->update([
                'total_score' => $totalScore,
                'status' => 'selesai',
                'finished_at' => now()
            ]);

            DB::commit();

            // ========== PERBARUI STATUS UJIAN JIKA SEMUA PESERTA SELESAI ==========
            $this->updateExamStatusIfComplete($examId);
            // ========== SAMPAI SINI ==========

            return response()->json([
                'success' => true,
                'message' => 'Ujian berhasil disubmit!',
                'total_score' => $totalScore,
                'details' => $details,
                'redirect' => route('peserta.hasil', $examId)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status ujian menjadi selesai jika semua peserta sudah selesai
     */
    private function updateExamStatusIfComplete($examId)
    {
        $exam = Exam::find($examId);
        if (!$exam || $exam->status !== 'berlangsung') {
            return;
        }

        $totalParticipants = $exam->participants()->count();
        $completedParticipants = $exam->participants()->where('status', 'selesai')->count();

        // Jika semua peserta sudah selesai
        if ($totalParticipants > 0 && $totalParticipants === $completedParticipants) {
            $exam->update(['status' => 'selesai']);

        }
    }

    /**
     * Exam results
     */
    public function examResults($examId)
    {
        $exam = Exam::findOrFail($examId);
        $participant = ExamParticipant::where('exam_id', $examId)
                                      ->where('siswa_id', Auth::id())
                                      ->firstOrFail();

        // Ambil semua jawaban dengan detail soal
        $answers = Answer::where('exam_participant_id', $participant->id)
                         ->with('question')
                         ->get();

        // Hitung statistik
        $totalQuestions = $exam->questions()->count();
        $answeredQuestions = $answers->count();
        $pgQuestions = $exam->questions()->where('type', 'pg')->count();
        $essayQuestions = $exam->questions()->where('type', 'essay')->count();

        $pgScore = $answers->filter(function($a) {
            return $a->question->type === 'pg';
        })->sum('score');

        $essayScore = $answers->filter(function($a) {
            return $a->question->type === 'essay';
        })->sum('score');

        // Dapatkan semua peserta untuk leaderboard
        $leaderboard = ExamParticipant::where('exam_id', $examId)
                                      ->where('status', 'selesai')
                                      ->with('siswa')
                                      ->orderBy('total_score', 'desc')
                                      ->orderBy('finished_at', 'asc')
                                      ->get();

        // Hitung peringkat
        $rank = 1;
        foreach ($leaderboard as $index => $p) {
            $p->rank = $index + 1;
            if ($p->id === $participant->id) {
                $rank = $p->rank;
            }
        }

        // Siapkan data untuk view
        $results = [
            'participant' => $participant,
            'exam' => $exam,
            'answers' => $answers,
            'total_score' => $participant->total_score ?? 0,
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'pg_score' => $pgScore,
            'essay_score' => $essayScore,
            'pg_total' => $pgQuestions,
            'essay_total' => $essayQuestions,
            'rank' => $rank,
            'leaderboard' => $leaderboard,
            'finished_at' => $participant->finished_at,
            'target_participants' => $exam->jumlah_peserta
        ];

        return view('peserta.hasil', $results);
    }

    /**
     * Mark participant as ready (siap mengikuti ujian)
     */
    public function markReady($examId)
    {
        $participant = ExamParticipant::where('exam_id', $examId)
                                    ->where('siswa_id', Auth::id())
                                    ->firstOrFail();

        if (!$participant->is_ready) {
            $participant->update([
                'is_ready' => true,
                'ready_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status siap diperbarui'
        ]);
    }

    /**
     * Check if participant is ready (AJAX)
     */
    public function checkReady($examId)
    {
        $participant = ExamParticipant::where('exam_id', $examId)
                                      ->where('siswa_id', Auth::id())
                                      ->first();

        if (!$participant) {
            return response()->json([
                'is_ready' => false,
                'message' => 'Participant not found'
            ]);
        }

        return response()->json([
            'is_ready' => $participant->is_ready ?? false
        ]);
    }

    /**
     * Calculate time remaining
     */
    private function calculateTimeRemaining($startedAt, $duration)
    {
        $endTime = $startedAt->copy()->addMinutes($duration);
        $remaining = now()->diffInSeconds($endTime, false);
        return max(0, $remaining);
    }

    /**
     * Get leaderboard data via AJAX (real-time update)
     */
    public function getLeaderboard($examId)
    {
        $exam = Exam::findOrFail($examId);
        $participant = ExamParticipant::where('exam_id', $examId)
                                    ->where('siswa_id', Auth::id())
                                    ->firstOrFail();

        // Ambil semua peserta yang sudah selesai
        $leaderboard = ExamParticipant::where('exam_id', $examId)
                                    ->where('status', 'selesai')
                                    ->with('siswa')
                                    ->orderBy('total_score', 'desc')
                                    ->orderBy('finished_at', 'asc')
                                    ->get();

        // Hitung peringkat
        $rank = 1;
        $currentUserRank = null;
        $leaderboardData = [];

        foreach ($leaderboard as $index => $p) {
            $p->rank = $index + 1;
            if ($p->id === $participant->id) {
                $currentUserRank = $p->rank;
            }

            $leaderboardData[] = [
                'rank' => $p->rank,
                'name' => $p->siswa->name,
                'kelas' => $p->siswa->kelas ?? '-',
                'total_score' => $p->total_score ?? 0,
                'finished_at' => $p->finished_at ? $p->finished_at->format('H:i:s') : '-',
                'is_current_user' => $p->id === $participant->id
            ];
        }

        return response()->json([
            'success' => true,
            'leaderboard' => $leaderboardData,
            'current_user_rank' => $currentUserRank,
            'total_participants' => $leaderboard->count()
        ]);
    }

    /**
     * Tampilkan halaman koreksi untuk peserta
     */
    public function koreksi($examId)
    {
        $exam = Exam::with('questions')->findOrFail($examId);
        $participant = ExamParticipant::where('exam_id', $examId)
                                    ->where('siswa_id', Auth::id())
                                    ->firstOrFail();

        // Ambil semua jawaban peserta
        $answers = Answer::where('exam_participant_id', $participant->id)
                        ->with('question')
                        ->get();

        // Siapkan data untuk setiap soal sebagai COLLECTION (bukan array)
        $questions = collect(); // Buat collection kosong

        foreach ($exam->questions as $index => $question) {
            $answer = $answers->firstWhere('question_id', $question->id);

            $isCorrect = false;
            $userAnswer = $answer ? $answer->answer_text : '(Tidak dijawab)';
            $score = $answer ? $answer->score : 0;
            $similarity = $answer ? $answer->similarity_score : null;

            if ($question->type === 'pg') {
                $isCorrect = $userAnswer === $question->correct_answer;
            }

            // Push ke collection
            $questions->push([
                'number' => $index + 1,
                'question' => $question,
                'user_answer' => $userAnswer,
                'correct_answer' => $question->correct_answer,
                'options' => $question->options,
                'is_correct' => $isCorrect,
                'score' => $score,
                'max_score' => $question->score,
                'similarity' => $similarity,
                'type' => $question->type
            ]);
        }

        return view('peserta.koreksi', compact('exam', 'participant', 'questions'));
    }
}