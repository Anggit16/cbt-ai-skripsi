@extends('layouts.app')

@section('title', 'Detail Hasil Ujian - ' . $exam->judul)

@push('styles')
<style>
/* Stats Card - SAMA SEPERTI SEMUA-HASIL */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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

.stat-card.blue { border-left: 4px solid #667eea; }
.stat-card.green { border-left: 4px solid #48bb78; }
.stat-card.orange { border-left: 4px solid #ed8936; }
.stat-card.purple { border-left: 4px solid #9b59b6; }

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

.stat-card.blue .stat-icon { background: rgba(102,126,234,0.1); color: #667eea; }
.stat-card.green .stat-icon { background: rgba(72,187,120,0.1); color: #48bb78; }
.stat-card.orange .stat-icon { background: rgba(237,137,54,0.1); color: #ed8936; }
.stat-card.purple .stat-icon { background: rgba(155,89,182,0.1); color: #9b59b6; }

/* Distribusi Nilai */
.distribution-item {
    margin-bottom: 12px;
}

.distribution-label {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-bottom: 6px;
}

.distribution-label span:first-child { font-weight: 500; color: #334155; }
.distribution-label span:last-child { font-weight: 600; color: #1e293b; }

.distribution-bar {
    height: 8px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}

.distribution-fill {
    height: 100%;
    border-radius: 10px;
}

/* Ranking Table */
.ranking-table {
    font-size: 14px;
}

.ranking-table thead th {
    font-weight: 600;
    font-size: 13px;
    color: #475569;
    padding: 12px 16px;
}

.ranking-table tbody td {
    padding: 12px 16px;
    vertical-align: middle;
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 14px;
}

.btn-export {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 8px 20px;
    font-size: 13px;
    border-radius: 10px;
    font-weight: 500;
}

.btn-export:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    color: white;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
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
                <h4 class="fw-bold mb-1">📋 Detail Hasil Ujian</h4>
                <p class="text-muted small mb-0">
                    <i class="fas fa-book-open me-1"></i> {{ $exam->judul }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('guru.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                </a>
                <a href="{{ route('guru.export-hasil', $exam->id) }}" class="btn btn-export">
                    <i class="fas fa-download me-1"></i>Export CSV
                </a>
            </div>
        </div>

        <!-- Statistik Ringkasan - SAMA SEPERTI SEMUA-HASIL -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-info">
                    <div class="stat-value">{{ $totalParticipants }}</div>
                    <div class="stat-label">Total Peserta</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-card green">
                <div class="stat-info">
                    <div class="stat-value">{{ $completedParticipants }}</div>
                    <div class="stat-label">Peserta Selesai</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-card orange">
                <div class="stat-info">
                    <div class="stat-value">{{ number_format($averageScore, 1) }}</div>
                    <div class="stat-label">Rata-rata Nilai</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-card purple">
                <div class="stat-info">
                    <div class="stat-value">{{ $highestScore }} / {{ $lowestScore }}</div>
                    <div class="stat-label">Tertinggi / Terendah</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>

        <!-- Distribusi Nilai -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 px-4 py-3">
                <div>
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>Distribusi Nilai
                    </h6>
                    <small class="text-muted">Berdasarkan persentase dari total nilai maksimal ({{ $totalMaxScore }} poin)</small>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <!-- Grade A -->
                        <div class="distribution-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="fw-bold text-success">A (Sangat Baik)</span>
                                    <span class="text-muted small">- 85% - 100%</span>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold">{{ $scoreDistribution['A'] }}</span>
                                    <span class="text-muted small">peserta</span>
                                    <span class="badge bg-success ms-2">{{ $totalParticipants > 0 ? round(($scoreDistribution['A'] / $totalParticipants) * 100) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $totalParticipants > 0 ? ($scoreDistribution['A'] / $totalParticipants) * 100 : 0 }}%"></div>
                            </div>
                            <div class="small text-muted">
                                Nilai: {{ $gradeRanges['A']['min'] }} - {{ $gradeRanges['A']['max'] }} poin
                            </div>
                        </div>

                        <!-- Grade B -->
                        <div class="distribution-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="fw-bold text-info">B (Baik)</span>
                                    <span class="text-muted small">- 70% - 84%</span>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold">{{ $scoreDistribution['B'] }}</span>
                                    <span class="text-muted small">peserta</span>
                                    <span class="badge bg-info ms-2">{{ $totalParticipants > 0 ? round(($scoreDistribution['B'] / $totalParticipants) * 100) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: {{ $totalParticipants > 0 ? ($scoreDistribution['B'] / $totalParticipants) * 100 : 0 }}%"></div>
                            </div>
                            <div class="small text-muted">
                                Nilai: {{ $gradeRanges['B']['min'] }} - {{ $gradeRanges['B']['max'] }} poin
                            </div>
                        </div>

                        <!-- Grade C -->
                        <div class="distribution-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="fw-bold text-warning">C (Cukup)</span>
                                    <span class="text-muted small">- 60% - 69%</span>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold">{{ $scoreDistribution['C'] }}</span>
                                    <span class="text-muted small">peserta</span>
                                    <span class="badge bg-warning ms-2">{{ $totalParticipants > 0 ? round(($scoreDistribution['C'] / $totalParticipants) * 100) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: {{ $totalParticipants > 0 ? ($scoreDistribution['C'] / $totalParticipants) * 100 : 0 }}%"></div>
                            </div>
                            <div class="small text-muted">
                                Nilai: {{ $gradeRanges['C']['min'] }} - {{ $gradeRanges['C']['max'] }} poin
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <!-- Grade D -->
                        <div class="distribution-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="fw-bold text-danger">D (Kurang)</span>
                                    <span class="text-muted small">- 50% - 59%</span>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold">{{ $scoreDistribution['D'] }}</span>
                                    <span class="text-muted small">peserta</span>
                                    <span class="badge bg-danger ms-2">{{ $totalParticipants > 0 ? round(($scoreDistribution['D'] / $totalParticipants) * 100) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: {{ $totalParticipants > 0 ? ($scoreDistribution['D'] / $totalParticipants) * 100 : 0 }}%"></div>
                            </div>
                            <div class="small text-muted">
                                Nilai: {{ $gradeRanges['D']['min'] }} - {{ $gradeRanges['D']['max'] }} poin
                            </div>
                        </div>

                        <!-- Grade E -->
                        <div class="distribution-item mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="fw-bold text-dark">E (Sangat Kurang)</span>
                                    <span class="text-muted small">- < 50%</span>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold">{{ $scoreDistribution['E'] }}</span>
                                    <span class="text-muted small">peserta</span>
                                    <span class="badge bg-dark ms-2">{{ $totalParticipants > 0 ? round(($scoreDistribution['E'] / $totalParticipants) * 100) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-dark" style="width: {{ $totalParticipants > 0 ? ($scoreDistribution['E'] / $totalParticipants) * 100 : 0 }}%"></div>
                            </div>
                            <div class="small text-muted">
                                Nilai: 0 - {{ $gradeRanges['E']['max'] }} poin
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Total -->
                <div class="alert alert-light mt-3 mb-0">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="small text-muted">Total Peserta</div>
                            <div class="fw-bold">{{ $totalParticipants }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Nilai Maksimal</div>
                            <div class="fw-bold text-success">{{ $totalMaxScore }}</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Rata-rata</div>
                            <div class="fw-bold text-primary">{{ number_format($averageScore, 1) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking Peserta -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 px-4 py-3">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-trophy me-2 text-primary"></i>Papan Peringkat
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table ranking-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="70">Peringkat</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th width="80">Nilai</th>
                                <th width="130">Selesai</th>
                                <th width="90">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($participants as $index => $p)
                            @php
                                $rank = $index + 1;
                                $score = $p->total_score ?? 0;
                                $scoreColor = $score >= 85 ? 'text-success' : ($score >= 70 ? 'text-info' : ($score >= 60 ? 'text-warning' : 'text-danger'));
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if($rank == 1)
                                        <span class="rank-badge rank-1">🥇</span>
                                    @elseif($rank == 2)
                                        <span class="rank-badge rank-2">🥈</span>
                                    @elseif($rank == 3)
                                        <span class="rank-badge rank-3">🥉</span>
                                    @else
                                        <span class="fw-bold text-muted">{{ $rank }}</span>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $p->siswa->name }}</td>
                                <td>{{ $p->siswa->kelas ?? '-' }}</td>
                                <td>{{ $p->siswa->jurusan ?? '-' }}</td>
                                <td class="fw-bold {{ $scoreColor }}">{{ $score }}</td>
                                <td class="small">{{ $p->finished_at ? $p->finished_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" style="padding: 4px 10px; font-size: 12px;" onclick="alert('Lihat detail jawaban dari halaman koreksi siswa')">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Belum ada peserta yang mengikuti ujian</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection