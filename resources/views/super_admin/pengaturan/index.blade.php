@extends('layouts.app')

@section('title', 'Pengaturan Sistem - Super Admin')

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">
                    <i class="fas fa-cog me-2 text-secondary"></i>Pengaturan Sistem
                </h4>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Fitur ini sedang dalam pengembangan. Untuk pengaturan lengkap, silahkan hubungi developer.
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-building me-2 text-primary"></i>Pengaturan Umum
                                </h5>
                                <p class="text-muted">Nama sekolah, logo, alamat, dll</p>
                                <button class="btn btn-outline-primary" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-book me-2 text-success"></i>Manajemen Mata Pelajaran
                                </h5>
                                <p class="text-muted">Tambah, edit, hapus mata pelajaran</p>
                                <button class="btn btn-outline-success" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-graduation-cap me-2 text-warning"></i>Manajemen Kelas & Jurusan
                                </h5>
                                <p class="text-muted">Tambah, edit, hapus kelas dan jurusan</p>
                                <button class="btn btn-outline-warning" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-database me-2 text-danger"></i>Backup Database
                                </h5>
                                <p class="text-muted">Backup dan restore database</p>
                                <button class="btn btn-outline-danger" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection