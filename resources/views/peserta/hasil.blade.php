@extends('layouts.app')

@section('title', 'Hasil Ujian - CBT System')

@push('styles')
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.rank-update {
    animation: fadeIn 0.5s ease;
}
.leaderboard-row-new {
    background-color: #e8f5e9 !important;
    animation: fadeIn 0.5s ease;
}
.rank-badge {
    transition: all 0.3s ease;
}
.rank-improve {
    color: #4caf50;
    font-weight: bold;
}
.rank-decline {
    color: #f44336;
    font-weight: bold;
}
.rank-steady {
    color: #ff9800;
    font-weight: bold;
}
</style>
@endpush

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <!-- Kartu Nilai Utama dengan Auto Update -->
                <div class="card shadow-lg border-0 rounded-4 mb-4">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-trophy fa-4x text-warning"></i>
                        </div>
                        <h3 class="fw-bold mb-2">Hasil Ujian</h3>
                        <p class="text-muted">{{ $exam->judul }}</p>

                        <div class="display-1 fw-bold text-primary mb-3" id="totalScore">{{ $total_score }}</div>
                        <p class="text-muted">Nilai Akhir</p>

                        <div class="row mt-4">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="h4 fw-bold text-success" id="rankDisplay">
                                        <span class="rank-badge" id="rankNumber">{{ $rank }}</span>
                                        <span id="rankChange" class="ms-2"></span>
                                    </div>
                                    <small class="text-muted">Peringkat</small>
                                    <div class="small text-muted" id="totalParticipantsDisplay">
                                        {{ $leaderboard->count() }} dari {{ $target_participants }} peserta
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="h4 fw-bold text-info" id="answeredQuestions">{{ $answered_questions }}/{{ $total_questions }}</div>
                                <small class="text-muted">Soal Terjawab</small>
                            </div>
                        </div>

                        <!-- Auto Update Status -->
                        <div class="alert alert-info mt-3 mb-0" id="updateStatus" style="display: none;">
                            <i class="fas fa-sync-alt fa-spin me-2"></i>
                            <span>Memperbarui data...</span>
                        </div>
                    </div>
                </div>

                <!-- Detail Nilai per Kategori -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-chart-pie me-2 text-primary"></i>Detail Nilai
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="bg-light rounded-3 p-3 text-center">
                                    <div class="text-muted small">Pilihan Ganda</div>
                                    <div class="h3 fw-bold text-success">{{ $pg_score }}</div>
                                    <small>dari {{ $pg_total }} soal</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="bg-light rounded-3 p-3 text-center">
                                    <div class="text-muted small">Essay</div>
                                    <div class="h3 fw-bold text-warning">{{ $essay_score }}</div>
                                    <small>dari {{ $essay_total }} soal</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Papan Peringkat dengan Auto Update -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-ranking-star me-2"></i>Papan Peringkat
                        </h5>
                        <div>
                            <i class="fas fa-sync-alt me-1" id="autoRefreshIcon" style="cursor: pointer;" onclick="manualRefresh()"></i>
                            <small class="ms-1" id="lastUpdateTime"></small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="leaderboardTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Peringkat</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Nilai</th>
                                        <th>Selesai</th>
                                    </tr>
                                </thead>
                                <tbody id="leaderboardBody">
                                    @foreach($leaderboard as $index => $lb)
                                    <tr class="{{ $lb->id == $participant->id ? 'table-primary' : '' }}" data-user-id="{{ $lb->id }}">
                                        <td>
                                            @if($index + 1 == 1)
                                                <i class="fas fa-crown text-warning"></i>
                                            @endif
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="fw-semibold">{{ $lb->siswa->name }}</td>
                                        <td>{{ $lb->siswa->kelas ?? '-' }}</td>
                                        <td class="fw-bold">{{ $lb->total_score }}</td>
                                        <td>{{ $lb->finished_at ? $lb->finished_at->format('H:i:s') : '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex justify-content-center gap-3 mb-5">
                    <a href="{{ route('peserta.dashboard') }}" class="btn btn-primary px-5 py-2">
                        <i class="fas fa-home me-2"></i>Kembali ke Dashboard
                    </a>
                    <a href="{{ route('peserta.koreksi', $exam->id) }}" class="btn btn-info px-5 py-2 text-white">
                        <i class="fas fa-edit me-2"></i>Lihat Koreksi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let examId = {{ $exam->id }};
let currentUserRank = {{ $rank }};
let updateInterval;
let isUpdating = false;
let allParticipantsCompleted = false;
let totalParticipants = {{ $leaderboard->count() }};
let targetParticipants = {{ $exam->jumlah_peserta }};

// Format waktu
function formatTime(date) {
    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

// Update waktu terakhir
function updateLastUpdateTime() {
    document.getElementById('lastUpdateTime').innerHTML = formatTime(new Date());
}

// Tampilkan status update
function showUpdateStatus(show) {
    const statusDiv = document.getElementById('updateStatus');
    if (show) {
        statusDiv.style.display = 'block';
    } else {
        setTimeout(() => {
            statusDiv.style.display = 'none';
        }, 1000);
    }
}

// Hentikan auto update
function stopAutoUpdate() {
    if (updateInterval) {
        clearInterval(updateInterval);
        updateInterval = null;
    }

    // Tampilkan pesan bahwa semua peserta sudah selesai
    const statusDiv = document.getElementById('updateStatus');
    statusDiv.className = 'alert alert-success mt-3 mb-0';
    statusDiv.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        <span>✅ Semua peserta telah menyelesaikan ujian. Data sudah final.</span>
    `;
    statusDiv.style.display = 'block';

    // Ubah icon refresh menjadi nonaktif
    const refreshIcon = document.getElementById('autoRefreshIcon');
    if (refreshIcon) {
        refreshIcon.style.opacity = '0.5';
        refreshIcon.style.cursor = 'not-allowed';
        refreshIcon.onclick = null;
    }
}

// Update peringkat dengan animasi
function updateRankWithAnimation(newRank) {
    const rankElement = document.getElementById('rankNumber');
    const rankChangeSpan = document.getElementById('rankChange');
    const oldRank = currentUserRank;

    if (oldRank !== newRank) {
        rankElement.classList.add('rank-update');

        let changeText = '';
        let changeClass = '';

        if (newRank < oldRank) {
            changeText = `▲ ${oldRank - newRank}`;
            changeClass = 'rank-improve';
        } else if (newRank > oldRank) {
            changeText = `▼ ${newRank - oldRank}`;
            changeClass = 'rank-decline';
        }

        rankChangeSpan.innerHTML = changeText;
        rankChangeSpan.className = `ms-2 ${changeClass}`;

        setTimeout(() => {
            rankChangeSpan.innerHTML = '';
        }, 3000);
    } else if (oldRank === newRank && allParticipantsCompleted) {
        rankChangeSpan.innerHTML = '✓ Final';
        rankChangeSpan.className = 'ms-2 rank-steady';
        setTimeout(() => {
            rankChangeSpan.innerHTML = '';
        }, 2000);
    }

    rankElement.innerText = newRank;
    currentUserRank = newRank;
    setTimeout(() => {
        rankElement.classList.remove('rank-update');
    }, 500);
}

// Update leaderboard table
function updateLeaderboardTable(leaderboardData) {
    const tbody = document.getElementById('leaderboardBody');
    let html = '';

    for (let item of leaderboardData) {
        const isCurrentUser = item.is_current_user;
        const rowClass = isCurrentUser ? 'table-primary' : '';

        html += `
            <tr class="${rowClass}" data-user-id="${item.user_id}">
                <td>${item.rank} ${item.rank === 1 ? '<i class="fas fa-crown text-warning"></i>' : ''}</td>
                <td class="fw-semibold">${escapeHtml(item.name)}</td>
                <td class="fw-semibold">${escapeHtml(item.kelas)}</td>
                <td class="fw-bold">${item.total_score}</td>
                <td>${item.finished_at}</td>
            </tr>
        `;
    }

    tbody.innerHTML = html;
}

// Update total peserta
function updateTotalParticipants(count) {
    totalParticipants = count;
    document.getElementById('totalParticipantsDisplay').innerHTML = `dari ${count} peserta`;

    // Cek apakah semua peserta sudah selesai
    if (count >= targetParticipants && !allParticipantsCompleted) {
        allParticipantsCompleted = true;
        stopAutoUpdate();
    }
}

// Fetch data terbaru dari server
function fetchLeaderboard() {
    if (isUpdating) return;
    if (allParticipantsCompleted) return;

    isUpdating = true;
    showUpdateStatus(true);

    fetch('/peserta/get-leaderboard/' + examId, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update peringkat user
            if (data.current_user_rank !== currentUserRank) {
                updateRankWithAnimation(data.current_user_rank);
            }

            // Update total peserta
            updateTotalParticipants(data.total_participants);

            // Update leaderboard table
            updateLeaderboardTable(data.leaderboard);

            // Update last update time
            updateLastUpdateTime();
        }
    })
    .catch(error => console.error('Error fetching leaderboard:', error))
    .finally(() => {
        isUpdating = false;
        showUpdateStatus(false);
    });
}

// Manual refresh (hanya jika belum semua peserta selesai)
function manualRefresh() {
    if (allParticipantsCompleted) {
        alert('Semua peserta sudah menyelesaikan ujian. Data sudah final.');
        return;
    }

    const icon = document.getElementById('autoRefreshIcon');
    icon.classList.add('fa-spin');
    fetchLeaderboard();
    setTimeout(() => {
        icon.classList.remove('fa-spin');
    }, 1000);
}

// Escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Auto update setiap 10 detik (hanya jika belum semua selesai)
function startAutoUpdate() {
    if (allParticipantsCompleted) return;
    updateInterval = setInterval(fetchLeaderboard, 10000);
}

// Stop auto update saat halaman ditutup
window.addEventListener('beforeunload', function() {
    if (updateInterval) {
        clearInterval(updateInterval);
    }
});

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    updateLastUpdateTime();

    // Cek apakah semua peserta sudah selesai dari awal
    if (totalParticipants >= targetParticipants) {
        allParticipantsCompleted = true;
        stopAutoUpdate();
    } else {
        startAutoUpdate();
    }

    fetchLeaderboard(); // Fetch pertama kali
});
</script>
@endsection