@extends('layouts.app')

@section('title', 'Dashboard Super Admin')

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <!-- Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="fw-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h3>
                        <p class="text-muted mb-0">
                            <i class="fas fa-shield-alt me-2 text-primary"></i>Super Administrator
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-danger p-2">
                            <i class="fas fa-crown me-1"></i> Full Access
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Utama -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 opacity-75">Total Guru</p>
                                <h2 class="fw-bold mb-0">{{ $totalGuru }}</h2>
                            </div>
                            <i class="fas fa-chalkboard-user fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 opacity-75">Total Siswa</p>
                                <h2 class="fw-bold mb-0">{{ $totalSiswa }}</h2>
                            </div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-warning text-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 opacity-75">Total Ujian</p>
                                <h2 class="fw-bold mb-0">{{ $totalUjian }}</h2>
                            </div>
                            <i class="fas fa-file-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 opacity-75">Rata-rata Nilai</p>
                                <h2 class="fw-bold mb-0">{{ number_format($rataRataNilai, 1) }}</h2>
                            </div>
                            <i class="fas fa-chart-line fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Ujian -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Ujian Selesai</p>
                                <h3 class="fw-bold mb-0 text-success">{{ $totalUjianSelesai }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Ujian Berlangsung</p>
                                <h3 class="fw-bold mb-0 text-warning">{{ $totalUjianBerlangsung }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-play-circle fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Card -->
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-chalkboard-user fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Manajemen Guru</h5>
                        <p class="text-muted small">Kelola data guru, tambah, edit, hapus, reset password</p>
                        <a href="{{ route('super_admin.guru') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-arrow-right me-2"></i>Kelola Guru
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-users fa-3x text-success"></i>
                        </div>
                        <h5 class="fw-bold">Manajemen Siswa</h5>
                        <p class="text-muted small">Lihat data siswa, reset password, hapus siswa</p>
                        <a href="{{ route('super_admin.siswa') }}" class="btn btn-success mt-2">
                            <i class="fas fa-arrow-right me-2"></i>Kelola Siswa
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-file-alt fa-3x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Manajemen Ujian</h5>
                        <p class="text-muted small">Lihat semua ujian dari semua guru</p>
                        <a href="{{ route('super_admin.ujian') }}" class="btn btn-warning mt-2">
                            <i class="fas fa-arrow-right me-2"></i>Kelola Ujian
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-chart-bar fa-3x text-info"></i>
                        </div>
                        <h5 class="fw-bold">Laporan & Statistik</h5>
                        <p class="text-muted small">Lihat laporan per guru, per mata pelajaran, export data</p>
                        <a href="{{ route('super_admin.laporan') }}" class="btn btn-info mt-2">
                            <i class="fas fa-arrow-right me-2"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                            <i class="fas fa-cog fa-3x text-secondary"></i>
                        </div>
                        <h5 class="fw-bold">Pengaturan Sistem</h5>
                        <p class="text-muted small">Pengaturan umum, manajemen mata pelajaran, backup database</p>
                        <a href="{{ route('super_admin.pengaturan') }}" class="btn btn-secondary mt-2">
                            <i class="fas fa-arrow-right me-2"></i>Pengaturan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection