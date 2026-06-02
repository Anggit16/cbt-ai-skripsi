@extends('layouts.app')

@section('title', 'Semua Hasil Ujian - Guru')

@push('styles')
<style>
/* Stats Card Modern */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid #eef2f6;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.stat-card.primary { border-left: 4px solid #667eea; }
.stat-card.success { border-left: 4px solid #48bb78; }
.stat-card.warning { border-left: 4px solid #ed8936; }

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 28px;
    font-weight: 800;
    color: #1a1a2e;
    line-height: 1.2;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 13px;
    color: #718096;
    font-weight: 500;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.stat-card.primary .stat-icon { background: rgba(102,126,234,0.1); color: #667eea; }
.stat-card.success .stat-icon { background: rgba(72,187,120,0.1); color: #48bb78; }
.stat-card.warning .stat-icon { background: rgba(237,137,54,0.1); color: #ed8936; }

/* Exam Card */
.exam-card {
    background: white;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
    border: 1px solid #eef2f6;
}

.exam-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border-color: #e2e8f0;
}

.exam-title {
    font-weight: 700;
    font-size: 16px;
    color: #1a1a2e;
    margin-bottom: 4px;
}

.exam-date {
    font-size: 12px;
    color: #94a3b8;
}

.progress-section {
    margin-top: 6px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #94a3b8;
    margin-bottom: 4px;
}

.progress-bar-custom {
    height: 6px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 10px;
    background: linear-gradient(90deg, #48bb78, #38a169);
}

.avg-score {
    font-weight: 700;
    font-size: 20px;
    color: #667eea;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.status-badge.menunggu { background: #fef3e2; color: #f59e0b; }
.status-badge.berlangsung { background: #e0f2fe; color: #0284c7; }
.status-badge.selesai { background: #d1fae5; color: #059669; }

.btn-sm-custom {
    padding: 6px 14px;
    font-size: 12px;
    border-radius: 10px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
}
</style>
@endpush

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">📊 Hasil Ujian</h4>
                <p class="text-muted small mb-0">Lihat dan kelola hasil ujian yang telah dibuat</p>
            </div>
            <a href="{{ route('guru.dashboard') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <!-- Statistik Ringkasan -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-info">
                    <div class="stat-value">{{ $exams->count() }}</div>
                    <div class="stat-label">Total Ujian</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stat-card success">
                <div class="stat-info">
                    <div class="stat-value">{{ $exams->where('status', 'selesai')->count() }}</div>
                    <div class="stat-label">Ujian Selesai</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-card warning">
                <div class="stat-info">
                    <div class="stat-value">{{ $exams->sum(function($e) { return $e->participants->count(); }) }}</div>
                    <div class="stat-label">Total Peserta</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Daftar Ujian -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 px-4 py-3">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>Daftar Ujian
                </h6>
            </div>
            <div class="card-body p-4 pt-0">
                @forelse($exams as $exam)
                @php
                    $stats = $examStats[$exam->id] ?? [
                        'completion_rate' => 0,
                        'completed_participants' => 0,
                        'total_participants' => 0,
                        'average_score' => 0
                    ];
                @endphp
                <div class="exam-card">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="exam-title">{{ $exam->judul }}</div>
                            <div class="exam-date">
                                <i class="far fa-calendar-alt me-1"></i>{{ $exam->created_at->format('d M Y') }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="progress-section">
                                <div class="progress-label">
                                    <span>Progress</span>
                                    <span>{{ $stats['completion_rate'] }}%</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-fill" style="width: {{ $stats['completion_rate'] }}%"></div>
                                </div>
                                <div class="small text-muted mt-1">
                                    {{ $stats['completed_participants'] }}/{{ $stats['total_participants'] }} peserta
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <div class="avg-score">{{ number_format($stats['average_score'], 1) }}</div>
                            <div class="small text-muted">Rata-rata</div>
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="status-badge {{ $exam->status }}">
                                {{ $exam->status == 'menunggu' ? 'Menunggu' : ($exam->status == 'berlangsung' ? 'Berlangsung' : 'Selesai') }}
                            </span>
                            <a href="{{ route('guru.detail-hasil', $exam->id) }}" class="btn btn-sm btn-primary ms-2">
                                <i class="fas fa-chart-line me-1"></i>Detail
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Belum ada ujian yang dibuat</p>
                    <a href="{{ route('guru.buat-ujian') }}" class="btn btn-primary btn-sm mt-3">
                        <i class="fas fa-plus-circle me-1"></i>Buat Ujian
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection