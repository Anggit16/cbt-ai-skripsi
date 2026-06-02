@extends('layouts.app')

@section('title', 'Dashboard Guru - CBT System')

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
                            <i class="fas fa-book me-2"></i>{{ Auth::user()->mata_pelajaran ?? 'Mata Pelajaran' }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('guru.buat-ujian') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Buat Ujian Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Ujian</p>
                                <h3 class="fw-bold mb-0">{{ $totalExams ?? 0 }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-file-alt fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Peserta</p>
                                <h3 class="fw-bold mb-0">{{ $totalParticipants ?? 0 }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Rata-rata Nilai</p>
                                <h3 class="fw-bold mb-0">{{ $averageScore ?? 0 }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-chart-line fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Aksi Cepat</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('guru.buat-ujian') }}" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-plus-circle me-2"></i>Buat Ujian Baru
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('guru.hasil-semua') }}" class="btn btn-success w-100 py-3">
                            <i class="fas fa-chart-line me-2"></i>Lihat Hasil Ujian
                        </a>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-warning w-100 py-3" onclick="alert('Fitur sedang dalam pengembangan')">
                            <i class="fas fa-chart-bar me-2"></i>Laporan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Exams -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="p-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Ujian Terbaru</h5>
                        @if(isset($exams) && $exams->count() > 3)
                            <a href="#" class="text-decoration-none">Lihat Semua</a>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3">Judul Ujian</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Durasi</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($exams) && $exams->count() > 0)
                                @foreach($exams->take(5) as $exam)
                                <tr>
                                    <td class="py-3 fw-semibold">{{ $exam->judul }}</td>
                                    <td class="py-3">{{ $exam->created_at->format('d M Y') }}</td>
                                    <td class="py-3">{{ $exam->durasi }} menit</td>
                                    <td class="py-3">
                                        @if($exam->status == 'menunggu')
                                            <span class="badge bg-warning">Menunggu</span>
                                        @elseif($exam->status == 'berlangsung')
                                            <span class="badge bg-success">Berlangsung</span>
                                        @else
                                            <span class="badge bg-secondary">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($exam->status == 'selesai')
                                            <a href="{{ route('guru.kelola-peserta', $exam->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-chart-line me-1"></i>Lihat
                                            </a>
                                        @else
                                            <a href="{{ route('guru.kelola-peserta', $exam->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-users me-1"></i>Kelola
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-folder-open fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">Belum ada ujian yang dibuat</p>
                                        <a href="{{ route('guru.buat-ujian') }}" class="btn btn-sm btn-primary mt-3">
                                            Buat Ujian Sekarang
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Jika ada error yang ditampilkan dari session
@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endpush