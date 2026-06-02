<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form for Guru
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show login form for Siswa (bisa dengan parameter kode_ujian)
     */
    public function showSiswaLoginForm(Request $request)
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'siswa') {
                return redirect()->route('peserta.dashboard');
            }
            Auth::logout();
        }

        // Ambil parameter kode dari URL
        $kodeUjian = $request->query('kode');
        $exam = null;

        if ($kodeUjian) {
            $exam = Exam::where('kode_ujian', $kodeUjian)
                        ->whereIn('status', ['menunggu', 'berlangsung'])
                        ->first();
        }

        return view('auth.login-siswa', compact('exam'));
    }

    /**
     * Show login form for Super Admin
     */
    public function showSuperAdminLoginForm()
    {
        return view('auth.login-super-admin');
    }

    /**
     * Handle login for all users
     */
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $requestedRole = $request->input('role');

        if (Auth::attempt($credentials, $request->remember ? true : false)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Validasi role
            if ($requestedRole === 'super_admin' && $user->role !== 'super_admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini bukan Super Admin!']);
            }

            if ($requestedRole === 'guru' && $user->role !== 'guru') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini bukan Guru!']);
            }

            if ($requestedRole === 'siswa' && $user->role !== 'siswa') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini bukan Siswa!']);
            }

            // Cegah Super Admin login lewat form biasa
            if (($requestedRole === 'guru' || $requestedRole === 'siswa') && $user->role === 'super_admin') {
                Auth::logout();
                return redirect('/login/super-admin')->with('error', 'Super Admin harus login melalui halaman khusus!');
            }

            // Redirect berdasarkan role
            if ($user->role === 'super_admin') {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('super_admin.dashboard')
                    ]);
                }
                return redirect()->intended(route('super_admin.dashboard'));
            }

            if ($user->role === 'guru') {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('guru.dashboard')
                    ]);
                }
                return redirect()->intended(route('guru.dashboard'));
            }

            if ($user->role === 'siswa') {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('peserta.dashboard')
                    ]);
                }
                return redirect()->intended(route('peserta.dashboard'));
            }
        }

        $error = 'Email atau password salah!';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $error
            ], 401);
        }

        return back()->withErrors(['email' => $error])->withInput();
    }

    /**
     * Show register form for Guru
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle register for Guru
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'mata_pelajaran' => 'required|string'
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'guru',
                'mata_pelajaran' => $request->mata_pelajaran
            ]);

            if ($user) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Registrasi berhasil! Silahkan login.',
                        'redirect' => route('login')
                    ]);
                }
                return redirect()->route('login')->with('success', 'Registrasi berhasil! Silahkan login.');
            }

        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $role = $user ? $user->role : null;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($role === 'super_admin') {
            return redirect('/login/super-admin');
        }

        if ($role === 'guru') {
            return redirect('/login');
        }

        if ($role === 'siswa') {
            return redirect('/login/siswa');
        }

        return redirect('/login');
    }
}