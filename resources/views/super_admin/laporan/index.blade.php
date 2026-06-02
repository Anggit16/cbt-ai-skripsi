@extends('layouts.app')

@section('title', 'Laporan & Statistik - Super Admin')

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">
                    <i class="fas fa-chart-bar me-2 text-info"></i>Laporan & Statistik
                </h4>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Fitur ini hanya menampilkan statistik. Untuk export data, silahkan hubungi developer.
                </div>

                <!-- Statistik Ringkasan -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0">{{ $totalGuru }}</h3>
                                <p class="mb-0 opacity-75">Total Guru</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0">{{ $totalSiswa }}</h3>
                                <p class="mb-0 opacity-75">Total Siswa</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0">{{ $totalUjian }}</h3>
                                <p class="mb-0 opacity-75">Total Ujian</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0">{{ number_format($rataRataNilai, 1) }}</h3>
                                <p class="mb-0 opacity-75">Rata-rata Nilai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik per Mata Pelajaran -->
                <h5 class="fw-bold mb-3">Statistik per Mata Pelajaran</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Total Ujian</th>
                                <th>Rata-rata Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statistikMataPelajaran as $stat)
                            <tr>
                                <td class="fw-semibold">{{ $stat->mata_pelajaran ?? 'Tidak diketahui' }}</td>
                                <td>{{ $stat->total_ujian }}</td>
                                <td>{{ number_format($stat->rata_rata, 1) }}</td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <p class="text-muted mb-0">Belum ada data</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Export -->
                <div class="mt-4 text-center">
                    <button class="btn btn-success" disabled>
                        <i class="fas fa-file-excel me-2"></i>Export ke Excel (Coming Soon)
                    </button>
                    <button class="btn btn-danger ms-2" disabled>
                        <i class="fas fa-file-pdf me-2"></i>Export ke PDF (Coming Soon)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection