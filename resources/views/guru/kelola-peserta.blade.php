@extends('layouts.app')

@section('title', 'Kelola Peserta - Guru')

@push('styles')
<style>
/* Stats Cards */
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

.stat-card.primary { border-left: 4px solid #667eea; }
.stat-card.warning { border-left: 4px solid #ed8936; }
.stat-card.success { border-left: 4px solid #48bb78; }
.stat-card.purple { border-left: 4px solid #9b59b6; }
/* .stat-card.completed { background: linear-gradient(135deg, #10b981, #059669); color: white; border-left: none; }
.stat-card.completed .stat-value,
.stat-card.completed .stat-label,
.stat-card.completed small { color: white; }
.stat-card.completed .stat-icon { background: rgba(255,255,255,0.2); color: white; } */

.stat-info { flex: 1; }
.stat-value { font-size: 28px; font-weight: 800; color: #1a1a2e; line-height: 1.2; margin-bottom: 4px; }
.stat-label { font-size: 13px; color: #718096; font-weight: 500; }
.stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
.stat-card.primary .stat-icon { background: rgba(102,126,234,0.1); color: #667eea; }
.stat-card.warning .stat-icon { background: rgba(237,137,54,0.1); color: #ed8936; }
.stat-card.success .stat-icon { background: rgba(72,187,120,0.1); color: #48bb78; }
.stat-card.purple .stat-icon { background: rgba(155,89,182,0.1); color: #9b59b6; }

/* Progress Section */
.progress-section {
    background: #f8fafc;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 30px;
}
.progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.progress-title { font-weight: 600; font-size: 14px; color: #1e293b; }
.progress-percent { font-size: 20px; font-weight: 700; color: #667eea; }
.progress-bar-custom { height: 8px; background: #e2e8f0; border-radius: 10px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #667eea, #764ba2); transition: width 0.5s ease; }

/* Start Button */
.start-btn { background: #cbd5e0; color: #4a5568; border: none; padding: 12px 32px; border-radius: 40px; font-weight: 600; transition: all 0.3s ease; }
.start-btn.active { background: linear-gradient(135deg, #48bb78, #38a169); color: white; animation: pulse-green 2s infinite; }
.start-btn.active:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(72,187,120,0.4); }
.start-btn.completed { background: #10b981; color: white; cursor: default; animation: none; }
.start-btn.completed:hover { transform: none; box-shadow: none; }
@keyframes pulse-green { 0%, 100% { box-shadow: 0 0 0 0 rgba(72,187,120,0.4); } 50% { box-shadow: 0 0 0 10px rgba(72,187,120,0); } }

/* Exam Completed Banner */
.exam-completed-banner {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 20px;
    padding: 20px 24px;
    margin-bottom: 30px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    animation: slideDown 0.5s ease-out;
}
@keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
.exam-completed-banner .icon { width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; }
.exam-completed-banner .content { flex: 1; }
.exam-completed-banner .title { font-size: 20px; font-weight: 700; margin-bottom: 5px; }
.exam-completed-banner .subtitle { font-size: 14px; opacity: 0.9; }
.btn-view-results { background: white; color: #059669; border: none; padding: 10px 24px; border-radius: 40px; font-weight: 600; transition: all 0.3s ease; }
.btn-view-results:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); color: #047857; }

/* Link Box */
.link-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(102,126,234,0.3);
}
.link-box::before, .link-box::after { content: ''; position: absolute; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%; }
.link-box::before { top: -50%; right: -50%; }
.link-box::after { bottom: -50%; left: -50%; }
.link-box-header { position: relative; z-index: 1; margin-bottom: 20px; }
.link-icon { width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; }
.link-box-header h6 { color: white; margin-bottom: 4px; }
.link-box-header p { color: rgba(255,255,255,0.8); }
.link-url-container { position: relative; z-index: 1; background: rgba(255,255,255,0.15); border-radius: 16px; padding: 4px; margin-bottom: 16px; }
.link-url-wrapper { display: flex; align-items: center; gap: 12px; background: white; border-radius: 12px; padding: 4px 4px 4px 16px; }
.link-code { flex: 1; font-family: 'Courier New', monospace; font-size: 14px; color: #1a1a2e; background: transparent; padding: 12px 0; word-break: break-all; white-space: normal; }
.btn-copy { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 8px 20px; border-radius: 10px; font-size: 13px; font-weight: 500; transition: all 0.3s ease; white-space: nowrap; }
.btn-copy:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102,126,234,0.4); }
.link-footer { position: relative; z-index: 1; text-align: center; }
.link-footer small { color: rgba(255,255,255,0.8); font-size: 12px; }

/* Participant Table */
.participant-table {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #eef2f6;
}
.participant-table thead th {
    background: #f8fafc;
    padding: 14px 16px;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    border-bottom: 1px solid #eef2f6;
}
.participant-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.participant-table tbody tr:hover { background: #f8fafc; }
.participant-table tbody tr.completed { background: #ecfdf5; }
.participant-table tbody tr.completed:hover { background: #d1fae5; }

.participant-table tbody tr.ready-row {
    background: #fef3c7;
}
.participant-table tbody tr.ready-row:hover {
    background: #fde68a;
}
.participant-table tbody tr.completed-row {
    background: #ecfdf5;
}
.participant-table tbody tr.completed-row:hover {
    background: #d1fae5;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
}
.status-badge.waiting { background: #fef3e2; color: #f59e0b; }
.status-badge.ready { background: #d1fae5; color: #059669; }
.status-badge.completed { background: #d1fae5; color: #059669; }

/* Info Alert */
.info-alert {
    background: #fefce8;
    border-left: 4px solid #eab308;
    border-radius: 12px;
    padding: 16px;
    margin-top: 20px;
}
.info-alert.completed { background: #ecfdf5; border-left-color: #10b981; }

.btn-back {
    background: #64748b;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.btn-back:hover { background: #475569; transform: translateY(-2px); color: white; }
.btn-disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    flex-direction: column;
}
.spinner-custom {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(255,255,255,0.3);
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

@media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: 1fr; gap: 12px; }
    .link-url-wrapper { flex-direction: column; padding: 12px; }
    .btn-copy { width: 100%; }
    .link-code { text-align: center; }
    .exam-completed-banner { flex-direction: column; text-align: center; }
}
</style>
@endpush

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h4 class="fw-bold mb-1">
                            <i class="fas fa-users me-2" style="color: #667eea;"></i>Kelola Peserta Ujian
                        </h4>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-book-open me-1"></i> {{ $exam->judul }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('guru.dashboard') }}" class="btn btn-back">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        @if($exam->status != 'selesai')
                        <button class="btn btn-primary btn-sm" id="buatLinkBtn" onclick="buatLink()">
                            <i class="fas fa-link me-1"></i>Buat Link
                        </button>
                        @endif
                    </div>
                </div>

                <div id="alertMessage"></div>

                <!-- Exam Completed Banner -->
                @if($exam->status == 'selesai')
                <div class="exam-completed-banner">
                    <div class="icon"><i class="fas fa-trophy"></i></div>
                    <div class="content">
                        <div class="title">🎉 Ujian Telah Selesai! 🎉</div>
                        <div class="subtitle">Semua peserta telah menyelesaikan ujian. Anda dapat melihat hasil ujian di bawah.</div>
                    </div>
                    <a href="{{ route('guru.detail-hasil', $exam->id) }}" class="btn-view-results">
                        <i class="fas fa-chart-line me-2"></i>Lihat Hasil Ujian
                    </a>
                </div>
                @endif

                <!-- Link Info -->
                <div id="linkInfo" class="link-box" style="display: none;">
                    <div class="link-box-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="link-icon"><i class="fas fa-link"></i></div>
                            <div>
                                <h6 class="fw-bold mb-0">Link Pendaftaran Ujian</h6>
                                <p class="small mb-0" style="color: rgba(255,255,255,0.8);">Bagikan link ini kepada peserta untuk mendaftar ujian</p>
                            </div>
                        </div>
                    </div>
                    <div class="link-url-container">
                        <div class="link-url-wrapper">
                            <code class="link-code" id="ujianLink"></code>
                            <button class="btn-copy" onclick="copyLink()"><i class="fas fa-copy me-1"></i>Salin</button>
                        </div>
                    </div>
                    <div class="link-footer"><small><i class="fas fa-info-circle me-1"></i>Peserta akan diarahkan ke halaman pendaftaran setelah membuka link ini</small></div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <!-- Card Peserta Terdaftar (TETAP BIRU, TIDAK BERUBAH) -->
                    <div class="stat-card primary">
                        <div class="stat-info">
                            <div class="stat-value" id="jumlahTerdaftar">{{ $currentParticipants ?? 0 }}</div>
                            <div class="stat-label">Peserta Terdaftar</div>
                            <div class="stat-target">Target: <span id="targetPesertaDisplay">{{ $exam->jumlah_peserta }}</span></div>
                            @if($exam->status != 'selesai')
                            <button class="btn btn-sm btn-outline-primary mt-2" id="editTargetBtn" onclick="showEditTargetModal()" style="padding: 4px 12px; font-size: 12px;">
                                <i class="fas fa-edit me-1"></i>Edit Target
                            </button>
                            @endif
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>

                    <!-- Card Jumlah Soal (TETAP UNGU, TIDAK BERUBAH) -->
                    <div class="stat-card purple">
                        <div class="stat-info">
                            <div class="stat-value">{{ $totalQuestions ?? 0 }}</div>
                            <div class="stat-label">Total Soal</div>
                            <div class="stat-target">
                                <span class="text-info">{{ $pgCount ?? 0 }} Pilihan Ganda</span> |
                                <span class="text-warning">{{ $essayCount ?? 0 }} Essay</span>
                            </div>
                            @if($exam->status != 'selesai')
                            <a href="{{ route('guru.edit-soal', $exam->id) }}" class="btn btn-sm btn-outline-secondary mt-2" id="editSoalBtn" style="padding: 4px 12px; font-size: 12px;">
                                <i class="fas fa-edit me-1"></i>Edit Soal
                            </a>
                            @endif
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                    </div>

                    <!-- Card Belum Siap -->
                    <div class="stat-card warning">
                        <div class="stat-info">
                            <div class="stat-value" id="jumlahMenunggu">0</div>
                            <div class="stat-label">Belum Siap</div>
                            <div class="stat-target">Menunggu konfirmasi</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>

                    <!-- Card Sudah Siap -->
                    <div class="stat-card success">
                        <div class="stat-info">
                            <div class="stat-value" id="jumlahSiap">0</div>
                            <div class="stat-label">Sudah Siap</div>
                            <div class="stat-target">Siap mengikuti ujian</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <!-- Progress Section (sembunyikan jika selesai) -->
                @if($exam->status != 'selesai')
                <div class="progress-section">
                    <div class="progress-header">
                        <span class="progress-title"><i class="fas fa-chart-line me-2 text-primary"></i>Progress Kesiapan</span>
                        <span class="progress-percent" id="progressPersen">0%</span>
                    </div>
                    <div class="progress-bar-custom"><div id="progressBar" class="progress-fill" style="width: 0%;"></div></div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">0 peserta siap</small>
                        <small class="text-muted" id="targetProgressText">{{ $exam->jumlah_peserta }} peserta target</small>
                    </div>
                </div>
                @endif

                <!-- Start Button -->
                <div class="text-center mb-4">
                    @if($exam->status == 'selesai')
                        <button class="start-btn completed" disabled><i class="fas fa-check-circle me-2"></i>Ujian Telah Selesai</button>
                        <div class="small text-success mt-2"><i class="fas fa-trophy me-1"></i> Selamat! Semua peserta telah menyelesaikan ujian.</div>
                    @else
                        <button id="mulaiUjianBtn" class="start-btn" disabled onclick="mulaiUjian()"><i class="fas fa-play me-2"></i>Mulai Ujian</button>
                        <div id="startInfo" class="small text-muted mt-2">Menunggu semua peserta siap ({{ $exam->jumlah_peserta }} peserta)</div>
                    @endif
                </div>

                <!-- Participant Table -->
                <div class="participant-table">
                    <table class="table mb-0">
                        <thead>
                            <tr><th width="50">No</th><th>Nama Peserta</th><th>Kelas</th><th>Jurusan</th><th width="100">Absen</th><th width="120">Status</th></tr>
                        </thead>
                        <tbody id="participantList">
                            @if(isset($participants) && $participants->count() > 0)
                                @foreach($participants as $index => $participant)
                                <tr data-id="{{ $participant->id }}" class="{{ $participant->status == 'selesai' ? 'completed' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold"><i class="fas fa-user-circle me-2 text-primary"></i>{{ $participant->siswa->name }}</td>
                                    <td>{{ $participant->siswa->kelas ?? '-' }}</td>
                                    <td>{{ $participant->siswa->jurusan ?? '-' }}</td>
                                    <td>{{ $participant->siswa->nomor_absen ?? '-' }}</td>
                                    <td>
                                        @if($participant->status == 'selesai')
                                            <span class="status-badge completed"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                                        @else
                                            <span class="status-badge waiting" id="badge-{{ $participant->id }}"><i class="fas fa-clock me-1"></i>Menunggu</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="6" class="text-center py-5"><i class="fas fa-users-slash fa-3x text-muted mb-3 d-block"></i><p class="text-muted mb-0">Belum ada peserta yang mendaftar</p><small class="text-muted">Bagikan link ujian untuk mulai menerima pendaftaran</small></td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Info Alert -->
                <div class="info-alert {{ $exam->status == 'selesai' ? 'completed' : '' }}">
                    <div class="d-flex">
                        <i class="fas fa-info-circle text-warning me-3 fs-5"></i>
                        <div>
                            <div class="fw-semibold mb-1">Informasi</div>
                            <p class="mb-0 small">
                                @if($exam->status == 'selesai')
                                    ✅ Ujian telah selesai. Semua peserta telah menyelesaikan ujian.
                                    <a href="{{ route('guru.detail-hasil', $exam->id) }}" class="ms-2">Lihat hasil ujian →</a>
                                @else
                                    Peserta harus menekan tombol "Siap" di halaman menunggu.
                                    Ujian akan bisa dimulai ketika <strong>semua peserta sudah siap</strong>.
                                    Saat ini ada <strong id="sisaInfo">{{ $exam->jumlah_peserta - ($currentParticipants ?? 0) }}</strong> peserta yang harus siap.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Buat Link -->
<div class="modal fade" id="confirmLinkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Buat Link
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-link fa-4x text-warning"></i>
                </div>
                <h5 class="fw-bold mb-2">Apakah Anda yakin ingin membuat link?</h5>
                <p class="text-muted mb-2">
                    Setelah link dibuat, Anda <strong>TIDAK AKAN BISA</strong> mengedit:
                </p>
                <ul class="text-start d-inline-block text-muted mb-3">
                    <li><i class="fas fa-ban text-danger me-2"></i>Target Peserta</li>
                    <li><i class="fas fa-ban text-danger me-2"></i>Soal Ujian</li>
                </ul>
                <div class="alert alert-info mt-2">
                    <i class="fas fa-info-circle me-2"></i>
                    Pastikan semua data sudah sesuai sebelum membuat link!
                </div>
            </div>
            <div class="modal-footer justify-content-center gap-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-warning px-4" id="confirmLinkBtn">
                    <i class="fas fa-link me-2"></i>Ya, Buat Link
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Target -->
<div class="modal fade" id="editTargetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Target Peserta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Target Jumlah Peserta</label>
                    <input type="number" class="form-control" id="targetPesertaInput" min="1" value="{{ $exam->jumlah_peserta }}">
                    <small class="text-muted">Masukkan jumlah target peserta untuk ujian ini</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveTargetBtn" onclick="updateTargetPeserta()"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<script>
let examId = {{ $exam->id }};
let targetPeserta = {{ $exam->jumlah_peserta }};
let updateInterval;
let linkCreated = false;
let examStatus = '{{ $exam->status }}';

function updateData() {
    fetch('/guru/kelola-peserta/' + examId + '/ready-status')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update jumlah terdaftar
                document.getElementById('jumlahTerdaftar').innerText = data.total_registered;
                document.getElementById('jumlahMenunggu').innerText = data.total_waiting;
                document.getElementById('jumlahSiap').innerText = data.total_ready;

                // Update progress bar
                const persen = data.total_ready > 0 ? (data.total_ready / targetPeserta * 100).toFixed(1) : 0;
                document.getElementById('progressPersen').innerText = persen + '%';
                document.getElementById('progressBar').style.width = persen + '%';

                // Update sisa peserta
                const sisa = targetPeserta - data.total_ready;
                document.getElementById('sisaInfo').innerText = sisa + ' peserta lagi yang harus siap';

                // Update tombol mulai
                const startBtn = document.getElementById('mulaiUjianBtn');
                const startInfo = document.getElementById('startInfo');

                if (data.can_start) {
                    startBtn.disabled = false;
                    startBtn.classList.add('active');
                    startInfo.innerHTML = '<i class="fas fa-check-circle me-1 text-success"></i> Semua peserta sudah siap! Klik Mulai Ujian.';
                    startInfo.style.color = '#059669';
                } else {
                    startBtn.disabled = true;
                    startBtn.classList.remove('active');
                    startInfo.innerHTML = `Menunggu ${targetPeserta - data.total_ready} peserta lagi untuk siap`;
                    startInfo.style.color = '#718096';
                }

                // Update tabel peserta
                updateParticipantTable(data.participants);

                // ========== CEK APAKAH SEMUA PESERTA SUDAH SELESAI ==========
                const totalCompleted = data.total_completed || 0;

                if (totalCompleted >= targetPeserta && examStatus !== 'selesai') {
                    // Update status global
                    examStatus = 'selesai';

                    // Sembunyikan progress section
                    const progressSection = document.querySelector('.progress-section');
                    if (progressSection) progressSection.style.display = 'none';

                    // Sembunyikan tombol buat link
                    const buatLinkBtn = document.getElementById('buatLinkBtn');
                    if (buatLinkBtn) buatLinkBtn.style.display = 'none';

                    // ========== SEMBUNYIKAN LINK BOX ==========
                    const linkInfo = document.getElementById('linkInfo');
                    if (linkInfo) {
                        linkInfo.style.display = 'none';
                    }

                    // Hapus dari session storage
                    sessionStorage.removeItem('link_created_{{ $exam->id }}');

                    // Update start button menjadi completed
                    startBtn.disabled = true;
                    startBtn.classList.remove('active');
                    startBtn.classList.add('completed');
                    startBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Ujian Telah Selesai';

                    // Update startInfo
                    startInfo.innerHTML = '<i class="fas fa-trophy me-1"></i> Selamat! Semua peserta telah menyelesaikan ujian.';
                    startInfo.style.color = '#059669';

                    // Tampilkan banner completed jika belum ada
                    if (!document.querySelector('.exam-completed-banner')) {
                        const bannerHtml = `
                            <div class="exam-completed-banner">
                                <div class="icon"><i class="fas fa-trophy"></i></div>
                                <div class="content">
                                    <div class="title">🎉 Ujian Telah Selesai! 🎉</div>
                                    <div class="subtitle">Semua peserta telah menyelesaikan ujian. Anda dapat melihat hasil ujian di bawah.</div>
                                </div>
                                <a href="{{ route('guru.detail-hasil', $exam->id) }}" class="btn-view-results">
                                    <i class="fas fa-chart-line me-2"></i>Lihat Hasil Ujian
                                </a>
                            </div>
                        `;
                        const oldBanner = document.querySelector('.exam-completed-banner');
                        if (oldBanner) oldBanner.remove();
                        const alertMessage = document.getElementById('alertMessage');
                        alertMessage.insertAdjacentHTML('afterend', bannerHtml);
                    }

                    // Update info alert
                    const infoAlert = document.querySelector('.info-alert');
                    if (infoAlert) {
                        infoAlert.classList.add('completed');
                        infoAlert.innerHTML = `
                            <div class="d-flex">
                                <i class="fas fa-info-circle text-success me-3 fs-5"></i>
                                <div>
                                    <div class="fw-semibold mb-1">Informasi</div>
                                    <p class="mb-0 small">
                                        ✅ Ujian telah selesai. Semua peserta telah menyelesaikan ujian.
                                        <a href="{{ route('guru.detail-hasil', $exam->id) }}" class="ms-2">Lihat hasil ujian →</a>
                                    </p>
                                </div>
                            </div>
                        `;
                    }

                    // Nonaktifkan tombol edit target dan edit soal
                    const editTargetBtn = document.getElementById('editTargetBtn');
                    const editSoalBtn = document.getElementById('editSoalBtn');
                    if (editTargetBtn) {
                        editTargetBtn.disabled = true;
                        editTargetBtn.style.opacity = '0.5';
                        editTargetBtn.style.cursor = 'not-allowed';
                        editTargetBtn.onclick = null;
                    }
                    if (editSoalBtn) {
                        editSoalBtn.style.pointerEvents = 'none';
                        editSoalBtn.style.opacity = '0.5';
                    }

                    showAlert('success', '🎉 Semua peserta telah menyelesaikan ujian!');
                }
            }
        })
        .catch(error => console.log('Error updating data:', error));
}

function updateParticipantTable(participants) {
    const tbody = document.getElementById('participantList');
    if (!participants || participants.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5"><i class="fas fa-users-slash fa-3x text-muted mb-3 d-block"></i><p class="text-muted mb-0">Belum ada peserta yang mendaftar</p><small class="text-muted">Bagikan link ujian untuk mulai menerima pendaftaran</small></td></tr>`;
        return;
    }
    let html = '';
    participants.forEach((p, index) => {
        const isCompleted = p.status === 'selesai';
        const isReady = p.is_ready;
        let statusClass = 'waiting', statusIcon = 'fa-clock', statusText = 'Menunggu';
        let rowClass = '';
        if (isCompleted) {
            statusClass = 'completed';
            statusIcon = 'fa-check-circle';
            statusText = 'Selesai';
            rowClass = 'completed-row';
        } else if (isReady) {
            statusClass = 'ready';
            statusIcon = 'fa-check-circle';
            statusText = 'Siap';
            rowClass = 'ready-row';
        }
        const readyTime = isReady && p.ready_at ? `<small class="text-muted ms-1">(${p.ready_at})</small>` : '';
        html += `<tr data-id="${p.id}" class="${rowClass}">
                    <td>${index + 1}</td>
                    <td class="fw-semibold"><i class="fas fa-user-circle me-2 text-primary"></i>${escapeHtml(p.name)}</td>
                    <td>${escapeHtml(p.kelas || '-')}</td>
                    <td>${escapeHtml(p.jurusan || '-')}</td>
                    <td>${escapeHtml(p.nomor_absen || '-')}</td>
                    <td><span class="status-badge ${statusClass}"><i class="fas ${statusIcon} me-1"></i>${statusText} ${readyTime}</span></td>
                </tr>`;
    });
    tbody.innerHTML = html;
}

function escapeHtml(text) { if (!text) return ''; const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }

function showEditTargetModal() { if (linkCreated) { showAlert('warning', '⚠️ Link sudah dibuat! Tidak dapat mengedit target.'); return; } document.getElementById('targetPesertaInput').value = document.getElementById('targetPesertaDisplay').innerText; new bootstrap.Modal(document.getElementById('editTargetModal')).show(); }

function updateTargetPeserta() {
    if (linkCreated) { showAlert('warning', 'Link sudah dibuat!'); document.getElementById('editTargetModal')?.modal?.hide?.(); return; }
    const newTarget = parseInt(document.getElementById('targetPesertaInput').value);
    const currentTarget = parseInt(document.getElementById('targetPesertaDisplay').innerText);
    const currentRegistered = parseInt(document.getElementById('jumlahTerdaftar').innerText);
    if (isNaN(newTarget) || newTarget < 1) { showAlert('warning', 'Target minimal 1'); return; }
    if (newTarget < currentRegistered) { showAlert('warning', `Target tidak boleh kurang dari ${currentRegistered} peserta`); return; }
    if (newTarget === currentTarget) { showAlert('info', 'Tidak ada perubahan'); bootstrap.Modal.getInstance(document.getElementById('editTargetModal'))?.hide(); return; }
    const saveBtn = document.getElementById('saveTargetBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    saveBtn.disabled = true;
    fetch('{{ route("guru.update-target", $exam->id) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ jumlah_peserta: newTarget }) })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('targetPesertaDisplay').innerText = data.new_target;
                document.getElementById('targetProgressText').innerText = data.new_target + ' peserta target';
                document.getElementById('startInfo').innerHTML = `Menunggu semua peserta siap (${data.new_target} peserta)`;
                targetPeserta = data.new_target;
                showAlert('success', data.message);
                bootstrap.Modal.getInstance(document.getElementById('editTargetModal'))?.hide();
                updateData();
            } else { showAlert('danger', data.message); }
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        })
        .catch(error => { console.error(error); showAlert('danger', 'Terjadi kesalahan'); saveBtn.innerHTML = originalText; saveBtn.disabled = false; });
}


// ========== CEK PESAN DARI HALAMAN EDIT SOAL ==========
function checkEditSoalMessage() {
    const messageData = sessionStorage.getItem('edit_soal_message');
    if (messageData) {
        try {
            const message = JSON.parse(messageData);
            // Tampilkan alert
            showAlert(message.type, message.message);
            // Hapus dari sessionStorage setelah ditampilkan
            sessionStorage.removeItem('edit_soal_message');
        } catch(e) {
            console.log('Error parsing message:', e);
        }
    }
}

// Panggil fungsi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    checkEditSoalMessage();
});

function buatLink() {
    if (linkCreated) {
        showAlert('info', 'Link sudah dibuat.');
        return;
    }

    // Tampilkan modal konfirmasi
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmLinkModal'));
    confirmModal.show();

    // Setup tombol konfirmasi sekali
    const confirmBtn = document.getElementById('confirmLinkBtn');
    const originalHandler = confirmBtn.onclick;

    // Hapus event listener lama jika ada
    confirmBtn.replaceWith(confirmBtn.cloneNode(true));
    const newConfirmBtn = document.getElementById('confirmLinkBtn');

    newConfirmBtn.onclick = function() {
        // Tutup modal konfirmasi
        confirmModal.hide();

        // Tampilkan loading di tombol Buat Link
        const btn = document.getElementById('buatLinkBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
        btn.disabled = true;

        fetch('{{ route("guru.link", $exam->id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('ujianLink').innerText = data.link;
                    document.getElementById('linkInfo').style.display = 'block';
                    document.getElementById('linkInfo').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    linkCreated = true;
                    sessionStorage.setItem('link_created_{{ $exam->id }}', 'true');

                    // ========== NONAKTIFKAN TOMBOL EDIT TARGET DAN EDIT SOAL ==========
                    const editTarget = document.getElementById('editTargetBtn');
                    const editSoal = document.getElementById('editSoalBtn');

                    if (editTarget) {
                        editTarget.disabled = true;
                        editTarget.style.opacity = '0.5';
                        editTarget.style.cursor = 'not-allowed';
                        editTarget.onclick = null;
                        editTarget.title = 'Tidak dapat diedit setelah link dibuat';
                    }

                    if (editSoal) {
                        editSoal.style.pointerEvents = 'none';
                        editSoal.style.opacity = '0.5';
                        editSoal.title = 'Tidak dapat diedit setelah link dibuat';
                    }

                    showAlert('success', '✅ Link berhasil dibuat! Tombol edit telah dinonaktifkan.');
                } else {
                    showAlert('danger', 'Gagal membuat link');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error(error);
                showAlert('danger', 'Terjadi kesalahan');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    };
}

function copyLink() { const link = document.getElementById('ujianLink').innerText; navigator.clipboard.writeText(link).then(() => showAlert('success', '✅ Link berhasil disalin!')).catch(() => showAlert('danger', 'Gagal menyalin link')); }

function mulaiUjian() {
    if (!confirm('⚠️ Apakah Anda yakin ingin memulai ujian sekarang?\n\nPastikan semua peserta sudah siap.')) return;
    const loadingHtml = `<div class="loading-overlay"><div class="spinner-custom"></div><p class="text-white mt-4 fw-semibold">Memulai ujian...</p></div>`;
    document.body.insertAdjacentHTML('beforeend', loadingHtml);
    fetch('{{ route("guru.start-exam", $exam->id) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(response => response.json())
        .then(data => { document.querySelector('.loading-overlay')?.remove(); if (data.success) { showAlert('success', data.message); setTimeout(() => window.location.reload(), 2000); } else { showAlert('danger', data.message); } })
        .catch(error => { document.querySelector('.loading-overlay')?.remove(); showAlert('danger', 'Terjadi kesalahan'); });
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : (type === 'info' ? 'alert-info' : 'alert-danger'));
    const icon = type === 'success' ? 'fa-check-circle' : (type === 'warning' ? 'fa-exclamation-triangle' : (type === 'info' ? 'fa-info-circle' : 'fa-exclamation-circle'));
    document.getElementById('alertMessage').innerHTML = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert"><i class="fas ${icon} me-2"></i> ${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
    setTimeout(() => { const alert = document.querySelector('#alertMessage .alert'); if (alert) alert.remove(); }, 5000);
}

function checkIfLinkCreated() {
    const linkCreatedFlag = sessionStorage.getItem('link_created_{{ $exam->id }}');
    if (linkCreatedFlag === 'true' && examStatus !== 'selesai') {
        linkCreated = true;

        // ========== NONAKTIFKAN TOMBOL EDIT TARGET DAN EDIT SOAL ==========
        const editTarget = document.getElementById('editTargetBtn');
        const editSoal = document.getElementById('editSoalBtn');

        if (editTarget) {
            editTarget.disabled = true;
            editTarget.style.opacity = '0.5';
            editTarget.style.cursor = 'not-allowed';
            editTarget.onclick = null;
            editTarget.title = 'Tidak dapat diedit setelah link dibuat';
        }

        if (editSoal) {
            editSoal.style.pointerEvents = 'none';
            editSoal.style.opacity = '0.5';
            editSoal.title = 'Tidak dapat diedit setelah link dibuat';
        }

        // Sembunyikan tombol buat link
        const buatLinkBtn = document.getElementById('buatLinkBtn');
        if (buatLinkBtn) buatLinkBtn.style.display = 'none';

        // Tampilkan link yang sudah ada
        fetch('{{ route("guru.link", $exam->id) }}')
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    document.getElementById('ujianLink').innerText = d.link;
                    document.getElementById('linkInfo').style.display = 'block';
                }
            })
            .catch(e => console.log(e));
    } else if (examStatus === 'selesai') {
        // Jika ujian sudah selesai, sembunyikan link box
        const linkInfo = document.getElementById('linkInfo');
        if (linkInfo) linkInfo.style.display = 'none';
    }
}

// Start auto update setiap 5 detik
if (examStatus !== 'selesai') {
    if (updateInterval) clearInterval(updateInterval);
    updateInterval = setInterval(updateData, 5000);
    updateData();
}

if (examStatus !== 'selesai') {
    checkIfLinkCreated();
} else {
    // Jika ujian sudah selesai, pastikan link box tersembunyi
    const linkInfo = document.getElementById('linkInfo');
    if (linkInfo) linkInfo.style.display = 'none';

    const buatLinkBtn = document.getElementById('buatLinkBtn');
    if (buatLinkBtn) buatLinkBtn.style.display = 'none';
}
window.addEventListener('beforeunload', () => { if (updateInterval) clearInterval(updateInterval); });
</script>
@endsection