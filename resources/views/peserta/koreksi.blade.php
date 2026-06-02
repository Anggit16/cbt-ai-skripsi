@extends('layouts.app')

@section('title', 'Koreksi Ujian - ' . $exam->judul)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" rel="preload">
<style>
.correct-badge {
    background: #d4edda;
    color: #155724;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.incorrect-badge {
    background: #f8d7da;
    color: #721c24;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.score-badge {
    background: #e2e3e5;
    color: #383d41;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.answer-box-correct {
    background: #d4edda;
    border-left: 4px solid #28a745;
}
.answer-box-incorrect {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
}
.answer-box-partial {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
}
.essay-similarity {
    font-size: 12px;
    color: #6c757d;
}
/* Style untuk grafik kemiripan */
.similarity-chart-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    margin-top: 15px;
    border: 1px solid #e9ecef;
}
.similarity-chart-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #2c3e50;
}
.similarity-bar {
    background: linear-gradient(90deg, #28a745, #ffc107, #dc3545);
    height: 30px;
    border-radius: 15px;
    transition: width 0.5s ease;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 10px;
    color: white;
    font-weight: bold;
    font-size: 12px;
}
.similarity-stats {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 11px;
    color: #6c757d;
}
.progress-custom {
    height: 25px;
    border-radius: 12px;
    overflow: hidden;
}
</style>
@endpush

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <!-- Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h3 class="fw-bold mb-1">
                            <i class="fas fa-edit me-2 text-primary"></i>Koreksi Ujian
                        </h3>
                        <p class="text-muted mb-0">{{ $exam->judul }}</p>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">Nilai Akhir: <span class="text-primary">{{ $participant->total_score }}</span></div>
                        <small class="text-muted">Selesai: {{ $participant->finished_at ? $participant->finished_at->format('d/m/Y H:i') : '-' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Ringkasan -->
        @php
            $totalBenar = 0;
            $totalSalahPg = 0;
            $totalEssay = 0;
            foreach($questions as $q) {
                if($q['is_correct']) $totalBenar++;
                if(!$q['is_correct'] && $q['type'] == 'pg') $totalSalahPg++;
                if($q['type'] == 'essay') $totalEssay++;
            }
        @endphp

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <div class="h2 fw-bold text-primary">{{ $totalBenar }}</div>
                        <p class="text-muted mb-0">Benar (PG)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <div class="h2 fw-bold text-danger">{{ $totalSalahPg }}</div>
                        <p class="text-muted mb-0">Salah (PG)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <div class="h2 fw-bold text-warning">{{ $totalEssay }}</div>
                        <p class="text-muted mb-0">Soal Essay</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <div class="h2 fw-bold text-info">{{ $participant->total_score }}</div>
                        <p class="text-muted mb-0">Total Nilai</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Soal -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-list-ol me-2"></i>Jawaban dan Koreksi</h5>
            </div>
            <div class="card-body p-0">
                @foreach($questions as $item)
                <div class="p-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-secondary me-2">Soal {{ $item['number'] }}</span>
                            <span class="badge {{ $item['type'] == 'pg' ? 'bg-info' : 'bg-warning' }}">
                                {{ $item['type'] == 'pg' ? 'Pilihan Ganda' : 'Essay' }}
                            </span>
                            <span class="badge score-badge ms-2">Nilai: {{ $item['score'] }}/{{ $item['max_score'] }}</span>
                        </div>
                        @if($item['type'] == 'pg')
                            @if($item['is_correct'])
                                <span class="correct-badge"><i class="fas fa-check-circle me-1"></i>Benar</span>
                            @else
                                <span class="incorrect-badge"><i class="fas fa-times-circle me-1"></i>Salah</span>
                            @endif
                        @else
                            <span class="score-badge"><i class="fas fa-star me-1"></i>Skor: {{ $item['score'] }}</span>
                        @endif
                    </div>

                    <!-- Soal -->
                    <div class="mb-3">
                        <div class="fw-semibold mb-2">Soal:</div>
                        <div class="p-3 bg-light rounded">{{ $item['question']->question_text }}</div>
                        @if($item['question']->image)
                        <div class="mt-2 text-center">
                            <img src="{{ asset('storage/' . $item['question']->image) }}" class="img-fluid rounded" style="max-height: 150px;">
                        </div>
                        @endif
                    </div>

                    <!-- Jawaban Peserta -->
                    <div class="mb-3">
                        <div class="fw-semibold mb-2">Jawaban Anda:</div>
                        <div class="p-3 rounded
                            @if($item['type'] == 'pg')
                                {{ $item['is_correct'] ? 'answer-box-correct' : 'answer-box-incorrect' }}
                            @else
                                answer-box-partial
                            @endif
                        ">
                            @if($item['type'] == 'pg')
                                <strong>{{ $item['user_answer'] }}</strong>
                            @else
                                {!! nl2br(e($item['user_answer'])) !!}
                            @endif
                        </div>
                    </div>

                    <!-- Kunci Jawaban (untuk PG) -->
                    @if($item['type'] == 'pg')
                    <div class="mb-3">
                        <div class="fw-semibold mb-2">Kunci Jawaban:</div>
                        <div class="p-3 bg-light rounded">
                            <strong>{{ $item['correct_answer'] }}</strong>
                        </div>
                    </div>

                    <!-- Opsi Jawaban -->
                    <div>
                        <div class="fw-semibold mb-2">Semua Opsi:</div>
                        <div class="row">
                            @php
                                $options = is_array($item['options']) ? $item['options'] : json_decode($item['options'], true);
                                $letters = ['A', 'B', 'C', 'D'];
                            @endphp
                            @foreach($options as $idx => $opt)
                            <div class="col-md-6 mb-2">
                                <div class="p-2 rounded border
                                    @if($letters[$idx] == $item['correct_answer']) border-success bg-success bg-opacity-10
                                    @elseif($letters[$idx] == $item['user_answer']) border-danger bg-danger bg-opacity-10
                                    @endif
                                ">
                                    <strong>{{ $letters[$idx] }}.</strong> {{ $opt }}
                                    @if($letters[$idx] == $item['correct_answer'])
                                        <span class="badge bg-success ms-2">Kunci Jawaban</span>
                                    @endif
                                    @if($letters[$idx] == $item['user_answer'])
                                        <span class="badge bg-info ms-2">Jawaban Anda</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <!-- Kunci Jawaban untuk Essay dengan Grafik Kemiripan -->
                    <div>
                        <div class="fw-semibold mb-2">Kunci Jawaban:</div>
                        <div class="p-3 bg-light rounded mb-3">
                            {{ $item['question']->correct_answer }}
                        </div>

                        <!-- GRAFIK KEMIRIPAN UNTUK ESSAY -->
                        @if($item['similarity'])
                        <div class="similarity-chart-container">
                            <div class="similarity-chart-title">
                                <i class="fas fa-chart-line me-2 text-primary"></i>Analisis Kemiripan Jawaban
                            </div>

                            <!-- Progress Bar Kemiripan -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Tingkat Kemiripan</small>
                                    <small class="fw-bold">{{ $item['similarity'] }}%</small>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar
                                        @if($item['similarity'] >= 80) bg-success
                                        @elseif($item['similarity'] >= 60) bg-warning
                                        @else bg-danger
                                        @endif
                                    " style="width: {{ $item['similarity'] }}%">
                                        {{ $item['similarity'] >= 30 ? $item['similarity'] . '%' : '' }}
                                    </div>
                                </div>
                                <div class="similarity-stats">
                                    <span>0% (Tidak Mirip)</span>
                                    <span>50% (Cukup Mirip)</span>
                                    <span>100% (Sangat Mirip)</span>
                                </div>
                            </div>

                            <!-- Skor Visual -->
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Perolehan Skor</small>
                                    <small class="fw-bold">{{ $item['score'] }} / {{ $item['max_score'] }}</small>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-info" style="width: {{ ($item['score'] / $item['max_score']) * 100 }}%">
                                        {{ $item['score'] }}
                                    </div>
                                </div>
                            </div>

                            <!-- Donut Chart CSS Sederhana -->
                            <div class="text-center mt-3">
                                <div class="position-relative d-inline-block">
                                    <svg width="100" height="100" viewBox="0 0 120 120">
                                        <circle cx="60" cy="60" r="54" fill="none" stroke="#e9ecef" stroke-width="12"/>
                                        <circle cx="60" cy="60" r="54" fill="none" stroke="
                                            @if($item['similarity'] >= 80) #28a745
                                            @elseif($item['similarity'] >= 60) #ffc107
                                            @else #dc3545
                                            @endif
                                        " stroke-width="12"
                                        stroke-dasharray="339.292"
                                        stroke-dashoffset="{{ 339.292 * (1 - $item['similarity'] / 100) }}"
                                        transform="rotate(-90 60 60)"/>
                                        <text x="60" y="65" text-anchor="middle" font-size="18" font-weight="bold" fill="#2c3e50">{{ $item['similarity'] }}%</text>
                                        <text x="60" y="80" text-anchor="middle" font-size="10" fill="#6c757d">Kemiripan</text>
                                    </svg>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div class="mt-2 small text-muted text-center">
                                <i class="fas fa-info-circle me-1"></i>
                                Tingkat kemiripan dihitung menggunakan algoritma text similarity yang membandingkan jawaban Anda dengan kunci jawaban.
                                @if($item['similarity'] >= 80)
                                    <span class="text-success">✧ Jawaban Anda sangat baik dan hampir sesuai dengan kunci jawaban.</span>
                                @elseif($item['similarity'] >= 60)
                                    <span class="text-warning">✧ Jawaban Anda cukup baik, namun masih ada beberapa poin yang perlu diperbaiki.</span>
                                @else
                                    <span class="text-danger">✧ Jawaban Anda masih jauh dari kunci jawaban. Pelajari kembali materi yang diberikan.</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Tombol Kembali -->
        <div class="text-center mt-4">
            <a href="{{ route('peserta.hasil', $exam->id) }}" class="btn btn-secondary px-4">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Hasil
            </a>
            <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary px-4 ms-2">
                <i class="fas fa-home me-2"></i>Dashboard
            </a>
        </div>
    </div>
</div>
@endsection