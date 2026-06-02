@extends('layouts.app')

@section('title', 'Menunggu Ujian')

@section('content')
<div class="container">
    <div class="row justify-content-center" style="min-height: 100vh; align-items: center;">
        <div class="col-md-6">
            <div class="card text-center shadow-lg border-0 rounded-4">
                <div class="card-body p-5">

                    <!-- Icon -->
                    <div class="mb-4">
                        <i class="fas fa-hourglass-half fa-4x text-warning"></i>
                    </div>

                    <!-- Judul -->
                    <h3 class="fw-bold mb-2">Menunggu Ujian Dimulai</h3>
                    <p class="text-muted mb-4">
                        Ujian akan dimulai oleh guru. Konfirmasikan kesiapan Anda.
                    </p>

                    <!-- Informasi Ujian - Pastikan data statis, tidak berubah -->
                    <div class="alert alert-light text-start mb-4" style="background: #f8f9fa; border: 1px solid #e2e8f0;">
                        <div class="fw-semibold mb-3">
                            <i class="fas fa-info-circle me-2 text-primary"></i>Informasi Ujian
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Mata Pelajaran:</span>
                            <span class="fw-semibold" id="judulUjian">{{ $exam->judul }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Durasi:</span>
                            <span class="fw-semibold" id="durasiUjian">{{ $exam->durasi }} menit</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Jumlah Soal:</span>
                            <span class="fw-semibold" id="jumlahSoalUjian">{{ $exam->questions()->count() }} soal</span>
                        </div>
                    </div>

                    <!-- Tombol Siap Ujian -->
                    <button id="readyBtn" class="btn btn-success btn-lg w-100 mb-3" onclick="markReady()" style="padding: 12px; font-size: 16px; font-weight: 600;">
                        <i class="fas fa-thumbs-up me-2"></i>Saya Siap Ujian
                    </button>

                    <!-- Status Box -->
                    <div id="statusBox" class="alert alert-warning" style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 12px;">
                        <i class="fas fa-clock"></i>
                        <span id="statusText" class="mb-0">Menunggu konfirmasi kesiapan Anda</span>
                    </div>

                    <!-- Footer Note -->
                    <div class="text-muted small mt-3">
                        <i class="fas fa-sync-alt me-1"></i>
                        Halaman akan otomatis refresh saat ujian dimulai
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Ambil examId dari data server (data statis dari Blade)
let examId = {{ $exam->id }};
let checkInterval;
let isReady = false;
let isStarting = false;

console.log('Waiting page loaded - Exam ID:', examId);

// Cek apakah user sudah menandai siap
function checkIfReady() {
    fetch('/peserta/check-ready/' + examId)
        .then(response => response.json())
        .then(data => {
            console.log('Check ready response:', data);
            if (data.is_ready && !isReady) {
                isReady = true;
                // Ubah tombol
                const readyBtn = document.getElementById('readyBtn');
                readyBtn.disabled = true;
                readyBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Sudah Siap';
                readyBtn.classList.remove('btn-success');
                readyBtn.classList.add('btn-secondary');

                // Ubah status box
                const statusBox = document.getElementById('statusBox');
                statusBox.classList.remove('alert-warning');
                statusBox.classList.add('alert-success');
                statusBox.innerHTML = '<i class="fas fa-check-circle"></i><span id="statusText" class="mb-0">Anda sudah siap! Menunggu guru memulai ujian...</span>';
            }
        })
        .catch(error => console.log('Error checking ready:', error));
}

// Mark as ready
function markReady() {
    if (isReady) return;

    const btn = document.getElementById('readyBtn');
    const statusBox = document.getElementById('statusBox');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';

    statusBox.classList.remove('alert-warning');
    statusBox.classList.add('alert-info');
    statusBox.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span id="statusText" class="mb-0">Mengirim konfirmasi...</span>';

    fetch('/peserta/mark-ready/' + examId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Mark ready response:', data);
        if (data.success) {
            isReady = true;
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Sudah Siap';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');

            statusBox.classList.remove('alert-info');
            statusBox.classList.add('alert-success');
            statusBox.innerHTML = '<i class="fas fa-check-circle"></i><span id="statusText" class="mb-0">Anda sudah siap! Menunggu guru memulai ujian...</span>';
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-thumbs-up me-2"></i>Saya Siap Ujian';
            statusBox.classList.remove('alert-info');
            statusBox.classList.add('alert-warning');
            statusBox.innerHTML = '<i class="fas fa-clock"></i><span id="statusText" class="mb-0">' + (data.message || 'Gagal mengirim konfirmasi. Silahkan coba lagi.') + '</span>';

            setTimeout(() => {
                if (!isReady) {
                    statusBox.classList.remove('alert-danger');
                    statusBox.classList.add('alert-warning');
                    statusBox.innerHTML = '<i class="fas fa-clock"></i><span id="statusText" class="mb-0">Menunggu konfirmasi kesiapan Anda</span>';
                }
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error marking ready:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-thumbs-up me-2"></i>Saya Siap Ujian';
        statusBox.classList.remove('alert-info');
        statusBox.classList.add('alert-danger');
        statusBox.innerHTML = '<i class="fas fa-exclamation-circle"></i><span id="statusText" class="mb-0">Terjadi kesalahan. Silahkan coba lagi.</span>';

        setTimeout(() => {
            if (!isReady) {
                statusBox.classList.remove('alert-danger');
                statusBox.classList.add('alert-warning');
                statusBox.innerHTML = '<i class="fas fa-clock"></i><span id="statusText" class="mb-0">Menunggu konfirmasi kesiapan Anda</span>';
            }
        }, 3000);
    });
}

// Check status ujian setiap 3 detik - TANPA RELOAD HALAMAN
function checkExamStatus() {
    // Jangan lanjutkan jika sudah dalam proses redirect
    if (isStarting) return;

    fetch('/peserta/check-status/' + examId)
        .then(response => response.json())
        .then(data => {
            console.log('Exam status check:', data.status, 'can_start:', data.can_start);
            if (data.can_start && !isStarting) {
                isStarting = true;
                clearInterval(checkInterval);

                const statusBox = document.getElementById('statusBox');
                statusBox.classList.remove('alert-warning', 'alert-success');
                statusBox.classList.add('alert-primary');
                statusBox.innerHTML = '<i class="fas fa-play-circle"></i><span id="statusText" class="mb-0">Ujian dimulai! Mengarahkan ke halaman ujian...</span>';

                setTimeout(function() {
                    window.location.href = '/peserta/ujian/' + examId;
                }, 1500);
            }
        })
        .catch(error => console.log('Error checking exam status:', error));
}

// Initial check - mulai interval
checkIfReady();
checkInterval = setInterval(checkExamStatus, 3000);

// Cleanup interval saat halaman ditutup
window.addEventListener('beforeunload', function() {
    if (checkInterval) {
        clearInterval(checkInterval);
    }
});
</script>
@endsection