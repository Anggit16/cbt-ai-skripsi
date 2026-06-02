<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Exam;
use App\Models\ExamParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    // Langsung ke method, tanpa constructor

    public function dashboard()
    {
        $totalGuru = User::where('role', 'guru')->count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalUjian = Exam::count();
        $totalUjianSelesai = Exam::where('status', 'selesai')->count();
        $totalUjianBerlangsung = Exam::where('status', 'berlangsung')->count();
        $rataRataNilai = ExamParticipant::where('status', 'selesai')->avg('total_score') ?? 0;

        $recentExams = Exam::with('guru')->orderBy('created_at', 'desc')->limit(10)->get();

        return view('super_admin.dashboard', compact(
            'totalGuru', 'totalSiswa', 'totalUjian',
            'totalUjianSelesai', 'totalUjianBerlangsung',
            'rataRataNilai', 'recentExams'
        ));
    }

    // ==================== MANAJEMEN GURU (FULL FUNCTION) ====================

    public function guruIndex()
    {
        $guru = User::where('role', 'guru')->orderBy('created_at', 'desc')->paginate(10);
        return view('super_admin.guru.index', compact('guru'));
    }

    public function guruCreate()
    {
        return view('super_admin.guru.create');
    }

    public function guruStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'mata_pelajaran' => 'required|string'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
            'mata_pelajaran' => $request->mata_pelajaran
        ]);

        return redirect()->route('super_admin.guru')->with('success', 'Guru berhasil ditambahkan');
    }

    public function guruEdit($id)
    {
        $guru = User::findOrFail($id);
        return view('super_admin.guru.edit', compact('guru'));
    }

    public function guruUpdate(Request $request, $id)
    {
        $guru = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'mata_pelajaran' => 'required|string'
        ]);

        $guru->update([
            'name' => $request->name,
            'email' => $request->email,
            'mata_pelajaran' => $request->mata_pelajaran
        ]);

        if ($request->filled('password')) {
            $guru->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('super_admin.guru')->with('success', 'Guru berhasil diupdate');
    }

    /**
     * Hapus guru
     */
    public function guruDestroy($id)
    {
        try {
            $guru = User::findOrFail($id);

            foreach ($guru->exams as $exam) {
                $exam->questions()->delete();
                $exam->participants()->delete();
                $exam->delete();
            }

            $guru->delete();

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus guru'
            ], 500);
        }
    }

    public function guruResetPassword($id)
    {
        $guru = User::findOrFail($id);
        $newPassword = 'password123';
        $guru->update(['password' => Hash::make($newPassword)]);

        return redirect()->route('super_admin.guru')->with('success', 'Password guru berhasil direset menjadi: ' . $newPassword);
    }

    // ==================== MANAJEMEN SISWA (VIEW ONLY) ====================

    public function siswaIndex()
    {
        $siswa = User::where('role', 'siswa')->orderBy('created_at', 'desc')->paginate(10);
        return view('super_admin.siswa.index', compact('siswa'));
    }

    // ==================== MANAJEMEN UJIAN (VIEW ONLY) ====================

    public function ujianIndex()
    {
        $ujian = Exam::with('guru')->orderBy('created_at', 'desc')->paginate(10);
        return view('super_admin.ujian.index', compact('ujian'));
    }

    // ==================== LAPORAN (VIEW ONLY) ====================

    public function laporanIndex()
    {
        $totalGuru = User::where('role', 'guru')->count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalUjian = Exam::count();
        $rataRataNilai = ExamParticipant::where('status', 'selesai')->avg('total_score') ?? 0;

        // Statistik per mata pelajaran
        $statistikMataPelajaran = DB::table('users')
            ->join('exams', 'users.id', '=', 'exams.guru_id')
            ->join('exam_participants', 'exams.id', '=', 'exam_participants.exam_id')
            ->where('users.role', 'guru')
            ->select('users.mata_pelajaran', DB::raw('COUNT(DISTINCT exams.id) as total_ujian'), DB::raw('AVG(exam_participants.total_score) as rata_rata'))
            ->groupBy('users.mata_pelajaran')
            ->get();

        return view('super_admin.laporan.index', compact('totalGuru', 'totalSiswa', 'totalUjian', 'rataRataNilai', 'statistikMataPelajaran'));
    }

    // ==================== PENGATURAN (VIEW ONLY) ====================

    public function pengaturanIndex()
    {
        return view('super_admin.pengaturan.index');
    }
}