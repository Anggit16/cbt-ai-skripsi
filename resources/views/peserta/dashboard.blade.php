@extends('layouts.app')

@section('title', 'Dashboard Peserta')

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <!-- Welcome Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="fw-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h3>
                        <p class="text-muted mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>Kelas: {{ Auth::user()->kelas ?? 'Belum diisi' }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-primary p-2">
                            <i class="fas fa-user-graduate me-1"></i> Siswa
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik (Hanya Menunggu dan Selesai) -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-warning text-dark">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-clock fa-2x mb-2 opacity-75"></i>
                        <h3 class="fw-bold mb-0">{{ $totalMenunggu }}</h3>
                        <p class="mb-0">Ujian Menunggu</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-check-circle fa-2x mb-2 opacity-75"></i>
                        <h3 class="fw-bold mb-0">{{ $totalSelesai }}</h3>
                        <p class="mb-0">Ujian Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ujian Menunggu (Akan Datang) -->
        @if(isset($waitingExams) && $waitingExams->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-hourglass-half me-2"></i>Ujian Menunggu ({{ $waitingExams->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Judul Ujian</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($waitingExams as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->exam->judul }}</td>
                                <td>{{ $item->exam->durasi }} menit</td>
                                <td>
                                    <span class="badge bg-warning">Menunggu dimulai</span>
                                </td>
                                <td>
                                    <a href="{{ route('peserta.waiting', $item->exam->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-clock me-1"></i>Menunggu
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <!-- Tampilkan pesan jika tidak ada ujian menunggu -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-hourglass-half me-2"></i>Ujian Menunggu (0)
                </h5>
            </div>
            <div class="card-body text-center py-4">
                <i class="fas fa-check-circle fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">Tidak ada ujian yang menunggu</p>
                <p class="text-muted small">Silahkan gunakan link ujian dari guru untuk mengikuti ujian baru</p>
            </div>
        </div>
        @endif

        <!-- Riwayat Ujian (Selesai) -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Riwayat Ujian ({{ isset($historyExams) ? $historyExams->count() : 0 }})
                </h5>
            </div>
            <div class="card-body">
                @if(isset($historyExams) && $historyExams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Judul Ujian</th>
                                <th>Tanggal Pengerjaan</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historyExams as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->exam->judul }}</td>
                                <td>{{ $item->finished_at ? $item->finished_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $item->total_score ?? 0 }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('peserta.hasil', $item->exam->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-chart-line me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Belum ada riwayat ujian</p>
                    <p class="text-muted small">Silahkan ikuti ujian yang tersedia</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection