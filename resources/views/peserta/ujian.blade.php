@extends('layouts.app')

@section('title', 'Mengerjakan Ujian')

@push('styles')
<style>

/* ==================== CSS UNTUK SOAL ESAI ==================== */

/* Container soal essay */
.essay-container {
    margin-top: 20px;
}

/* Area textarea jawaban essay */
.essay-answer {
    width: 100%;
    padding: 15px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    line-height: 1.6;
    transition: all 0.3s ease;
    resize: vertical;
    background: #fafbfc;
}

.essay-answer:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
}

/* Label untuk jawaban essay */
.essay-label {
    font-weight: 600;
    margin-bottom: 10px;
    color: #2d3748;
    display: flex;
    align-items: center;
    gap: 8px;
}

.essay-label i {
    color: #667eea;
}

/* Informasi tambahan untuk essay */
.essay-info {
    margin-top: 10px;
    font-size: 12px;
    color: #718096;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.essay-info span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Karakter counter */
.char-counter {
    font-size: 12px;
    color: #718096;
    margin-top: 8px;
    text-align: right;
}

.char-counter.warning {
    color: #f59e0b;
}

.char-counter.danger {
    color: #ef4444;
}
/* ==================== CSS UNTUK UJIAN ==================== */

/* Style untuk option item */
.options-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;    /* Tambah padding dari 12px menjadi 15px */
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
}

.option-item:hover {
    background: #f8fafc;
    border-color: #667eea;
    transform: translateX(5px);
}

.option-item.selected {
    background: #eef2ff;
    border-color: #667eea;
}

/* Radio button */
.option-radio {
    width: 20px;
    height: 20px;
    accent-color: #667eea;
    cursor: pointer;
    margin: 0;
}

/* Huruf opsi (A, B, C, D) */
.option-letter {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    border-radius: 50%;
    font-weight: 700;
    font-size: 16px;
    color: #475569;
    transition: all 0.2s ease;
}

.option-item.selected .option-letter {
    background: #667eea;
    color: white;
}

/* Teks opsi */
.option-text {
    flex: 1;
    font-size: 15px;
    color: #334155;
    line-height: 1.4;
}

/* Gambar opsi */
.option-image {
    max-height: 100px;      /* Ubah dari 50px menjadi 80px */
    max-width: 140px;      /* Ubah dari 100px menjadi 120px */
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 5px;
    background: white;
}

.option-image-large {
    max-height: 100px;
    max-width: 150px;
}

/* Container untuk gambar dan teks dalam satu baris */
.option-content {
    display: flex;
    align-items: center;
    gap: 15px;             /* Tambah gap dari 12px menjadi 15px */
    flex-wrap: wrap;
    flex: 1;
}

/* Question card */
.question-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eef2f6;
}

.question-text {
    font-size: 16px;
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 20px;
    line-height: 1.6;
}

/* Animasi pulse untuk timer saat tersisa sedikit */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* Timer saat tersisa <= 60 detik */
.timer-danger {
    animation: pulse 1s infinite;
}

/* Responsive untuk mobile */
@media (max-width: 768px) {
    .option-item {
        padding: 12px 15px;
        flex-wrap: wrap;
    }

    .option-letter {
        width: 30px;
        height: 30px;
        font-size: 14px;
    }

    /* Ukuran gambar di mobile */
    .option-image {
        max-height: 60px;
        max-width: 90px;
    }

    .option-content {
        gap: 10px;
    }
}

/* Untuk layar tablet */
@media (min-width: 769px) and (max-width: 1024px) {
    .option-image {
        max-height: 70px;
        max-width: 100px;
    }
}

</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Kolom Kiri: Navigasi Soal -->
        <div class="col-md-3">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-list-ol me-2 text-primary"></i>Navigasi Soal
                    </h6>
                    <div id="questionNav" class="row g-2"></div>
                    <hr>
                    <div class="small text-muted">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-success" style="width: 20px;">&nbsp;</span>
                            <span>Sudah dijawab</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary" style="width: 20px;">&nbsp;</span>
                            <span>Soal aktif</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary" style="width: 20px;">&nbsp;</span>
                            <span>Belum dijawab</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Timer dan Soal -->
        <div class="col-md-9">
            <!-- Timer -->
            <div class="card shadow-sm mb-3">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-book-open text-primary me-2"></i>
                            <strong>Ujian: {{ $exam->judul }}</strong>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Sisa Waktu</div>
                            <div class="fw-bold fs-3" id="timer" style="font-family: monospace; line-height: 1;">00:00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Soal -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="questionContainer"></div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="previousQuestion()">
                            <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="nextBtn" onclick="nextQuestion()">
                            Selanjutnya<i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>

                    <hr>

                    <button type="button" class="btn btn-success w-100 py-2" id="submitBtn" onclick="submitExam()">
                        <i class="fas fa-paper-plane me-2"></i>Selesai & Submit
                    </button>
                </div>
            </div>
        </div>
        <!-- Modal Waktu Habis -->
        <div class="modal fade" id="timeoutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="timeoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="timeoutModalLabel">
                            <i class="fas fa-hourglass-end me-2"></i>Waktu Habis!
                        </h5>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-clock fa-4x text-danger mb-3"></i>
                        <p class="fs-5 fw-semibold">Maaf, waktu pengerjaan ujian telah habis.</p>
                        <p class="text-muted">Jawaban Anda akan disimpan dan dinilai secara otomatis.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary px-4" id="timeoutOkBtn" onclick="submitExamAfterTimeout()">
                            <i class="fas fa-check-circle me-2"></i>Lihat Hasil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ==================== INISIALISASI DATA ====================
const questions = @json($exam->questions);
let currentIndex = 0;
let answers = {};

// Pastikan timeLeft adalah integer
let timeLeft = Math.floor({{ isset($timeRemaining) ? (int)$timeRemaining : $exam->durasi * 60 }});
let timerInterval;
let timeoutModalShown = false;
let isSubmitting = false;

// Load jawaban yang sudah tersimpan
@if(isset($participant) && $participant->answers)
    @foreach($participant->answers as $answer)
        answers[{{ $answer->question_id }}] = '{{ addslashes($answer->answer_text) }}';
    @endforeach
@endif

// ==================== RENDER NAVIGASI SOAL ====================
function renderNavigation() {
    const nav = document.getElementById('questionNav');
    let html = '';

    questions.forEach((q, index) => {
        const isAnswered = answers[q.id] && answers[q.id].trim() !== '';
        const isCurrent = index === currentIndex;
        let btnClass = 'btn btn-sm w-100 mb-2';

        if (isAnswered) {
            btnClass += ' btn-success';
        } else if (isCurrent) {
            btnClass += ' btn-primary';
        } else {
            btnClass += ' btn-outline-secondary';
        }

        html += `
            <div class="col-3">
                <button type="button" class="${btnClass}" onclick="goToQuestion(${index})" style="font-weight: 600;">
                    ${index + 1}
                </button>
            </div>
        `;
    });

    nav.innerHTML = html;
}

// ==================== RENDER SOAL SAAT INI ====================
function renderCurrentQuestion() {
    const question = questions[currentIndex];
    const container = document.getElementById('questionContainer');
    const savedAnswer = answers[question.id] || '';

    let answerHtml = '';

    // Parsing options
    let options = [];
    let optionImages = [];

    if (question.options) {
        if (typeof question.options === 'string') {
            try {
                options = JSON.parse(question.options);
            } catch(e) {
                options = [];
            }
        } else if (Array.isArray(question.options)) {
            options = question.options;
        }
    }

    if (question.option_images) {
        if (typeof question.option_images === 'string') {
            try {
                optionImages = JSON.parse(question.option_images);
            } catch(e) {
                optionImages = [];
            }
        } else if (Array.isArray(question.option_images)) {
            optionImages = question.option_images;
        }
    }

    // Gambar soal
    let imageHtml = '';
    if (question.image && question.image !== null) {
        let imageUrl = question.image;
        if (!imageUrl.startsWith('/storage/') && !imageUrl.startsWith('http')) {
            imageUrl = '/storage/' + imageUrl;
        }
        imageHtml = `
            <div class="text-center my-3 p-3 bg-light rounded">
                <img src="${imageUrl}" class="img-fluid rounded" style="max-height: 250px; border: 1px solid #ddd;">
            </div>
        `;
    }

    // Pilihan Ganda
    if (question.type === 'pg') {
        if (options.length === 0) {
            options = ['Opsi A', 'Opsi B', 'Opsi C', 'Opsi D'];
        }

        answerHtml = '<div class="options-list">';
        const letters = ['A', 'B', 'C', 'D'];

        for (let i = 0; i < letters.length; i++) {
            const letter = letters[i];
            const isSelected = savedAnswer === letter;
            const optionText = options[i] || `Opsi ${letter}`;
            const hasImage = optionImages[i] && optionImages[i] !== null && optionImages[i] !== '';

            let optionImageUrl = '';
            if (hasImage) {
                let imgPath = optionImages[i];
                if (!imgPath.startsWith('/storage/') && !imgPath.startsWith('http')) {
                    imgPath = '/storage/' + imgPath;
                }
                optionImageUrl = `<img src="${imgPath}" class="option-image" alt="Gambar ${letter}">`;
            }

            let contentHtml = '';
            if (optionText && optionText.trim() !== '' && hasImage) {
                contentHtml = `
                    <div class="option-content">
                        <span class="option-text">${escapeHtml(optionText)}</span>
                        ${optionImageUrl}
                    </div>
                `;
            } else if (optionText && optionText.trim() !== '') {
                contentHtml = `<span class="option-text">${escapeHtml(optionText)}</span>`;
            } else if (hasImage) {
                contentHtml = `<div class="option-content">${optionImageUrl}</div>`;
            } else {
                contentHtml = `<span class="option-text">${escapeHtml(optionText)}</span>`;
            }

            answerHtml += `
                <div class="option-item ${isSelected ? 'selected' : ''}" onclick="selectOption(${question.id}, '${letter}')">
                    <input type="radio" name="question_${question.id}" value="${letter}"
                           class="option-radio" ${isSelected ? 'checked' : ''}
                           onclick="event.stopPropagation(); selectOption(${question.id}, '${letter}')">
                    <div class="option-letter">${letter}</div>
                    ${contentHtml}
                </div>
            `;
        }
        answerHtml += '</div>';
    }
    // Essay
    else {
        answerHtml = `
            <div class="essay-container">
                <div class="essay-label">
                    <i class="fas fa-pen-alt"></i> Jawaban Anda:
                </div>
                <textarea class="essay-answer" rows="6"
                    placeholder="Tulis jawaban Anda di sini..."
                    oninput="saveEssayAnswer(${question.id}, this.value)">${escapeHtml(savedAnswer)}</textarea>
                <div class="essay-info">
                    <span><i class="fas fa-info-circle text-primary"></i> Jawaban akan dinilai berdasarkan kesesuaian dengan kunci jawaban</span>
                </div>
            </div>
        `;
    }

    // Gabungkan semua
    const html = `
        <div class="question-card">
            <div class="question-header">
                <span class="badge bg-primary">Soal ${currentIndex + 1} dari ${questions.length}</span>
                <span class="badge bg-secondary">${question.type === 'pg' ? 'Pilihan Ganda' : 'Essay'} | Nilai: ${question.score}</span>
            </div>
            <div class="question-text">${escapeHtml(question.question_text)}</div>
            ${imageHtml}
            ${answerHtml}
        </div>
    `;

    container.innerHTML = html;
    renderNavigation();
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ==================== PILIH JAWABAN ====================
function selectOption(questionId, value) {
    answers[questionId] = value;

    const optionItems = document.querySelectorAll('#questionContainer .option-item');
    optionItems.forEach(item => {
        const radio = item.querySelector('.option-radio');
        const letter = item.querySelector('.option-letter')?.innerText;

        if (letter === value) {
            item.classList.add('selected');
            if (radio) radio.checked = true;
        } else {
            item.classList.remove('selected');
            if (radio) radio.checked = false;
        }
    });

    renderNavigation();
    autoSaveAnswer(questionId, value);
}

function saveEssayAnswer(questionId, value) {
    answers[questionId] = value;
    autoSaveAnswer(questionId, value);
}

function autoSaveAnswer(questionId, value) {
    fetch('/peserta/submit-answer/' + {{ $exam->id }}, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            answers: [{
                question_id: questionId,
                answer: value
            }]
        })
    }).catch(error => console.log('Auto save error:', error));
}

// ==================== NAVIGASI SOAL ====================
function goToQuestion(index) {
    if (index >= 0 && index < questions.length) {
        currentIndex = index;
        renderCurrentQuestion();
    }
}

function previousQuestion() {
    if (currentIndex > 0) {
        currentIndex--;
        renderCurrentQuestion();
    }
}

function nextQuestion() {
    if (currentIndex < questions.length - 1) {
        currentIndex++;
        renderCurrentQuestion();
    }
}

// ==================== TIMER ====================
function updateTimer() {
    if (timeLeft <= 0 && !timeoutModalShown) {
        clearInterval(timerInterval);
        showTimeoutModal();
        return;
    }

    if (timeLeft <= 0 && timeoutModalShown) {
        return;
    }

    let remainingSeconds = Math.floor(timeLeft);
    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    const timerElement = document.getElementById('timer');

    timerElement.innerHTML = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

    if (remainingSeconds <= 60) {
        timerElement.style.color = '#f44336';
        timerElement.style.fontWeight = 'bold';
        timerElement.style.animation = 'pulse 1s infinite';
    } else if (remainingSeconds <= 300) {
        timerElement.style.color = '#ff9800';
        timerElement.style.fontWeight = 'bold';
        timerElement.style.animation = 'none';
    } else {
        timerElement.style.color = '#f56565';
        timerElement.style.fontWeight = 'normal';
        timerElement.style.animation = 'none';
    }

    timeLeft--;
}

// Fungsi untuk menampilkan modal waktu habis
function showTimeoutModal() {
    timeoutModalShown = true;
    const modalElement = document.getElementById('timeoutModal');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        // Event listener untuk tombol OK
        const okBtn = document.getElementById('timeoutOkBtn');
        if (okBtn) {
            okBtn.onclick = function() {
                submitExamAfterTimeout();
            };
        }
    } else {
        submitExamAfterTimeout();
    }
}

// Fungsi untuk submit setelah waktu habis
function submitExamAfterTimeout() {
    if (isSubmitting) return;
    isSubmitting = true;

    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan hasil...';
        submitBtn.disabled = true;
    }

    const answersData = [];
    for (const [questionId, answer] of Object.entries(answers)) {
        if (answer && answer.trim() !== '') {
            answersData.push({
                question_id: parseInt(questionId),
                answer: answer
            });
        }
    }

    fetch('/peserta/submit-exam/' + {{ $exam->id }}, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ answers: answersData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('timeoutModal'));
            if (modal) modal.hide();
            window.location.href = data.redirect;
        } else {
            alert('❌ ' + (data.message || 'Gagal menyimpan ujian'));
            isSubmitting = false;
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Selesai & Submit';
                submitBtn.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Submit error:', error);
        alert('❌ Terjadi kesalahan: ' + error);
        isSubmitting = false;
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Selesai & Submit';
            submitBtn.disabled = false;
        }
    });
}

// ==================== SUBMIT UJIAN MANUAL ====================
function submitExam() {
    if (isSubmitting) return;

    if (confirm('⚠️ Apakah Anda yakin ingin mengakhiri ujian?\n\nPastikan semua jawaban sudah terisi.')) {
        isSubmitting = true;
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
        submitBtn.disabled = true;

        const answersData = [];
        for (const [questionId, answer] of Object.entries(answers)) {
            if (answer && answer.trim() !== '') {
                answersData.push({
                    question_id: parseInt(questionId),
                    answer: answer
                });
            }
        }

        fetch('/peserta/submit-exam/' + {{ $exam->id }}, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ answers: answersData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Ujian berhasil disubmit! Nilai Anda: ' + data.total_score);
                window.location.href = data.redirect;
            } else {
                alert('❌ ' + (data.message || 'Gagal mengirim ujian'));
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                isSubmitting = false;
            }
        })
        .catch(error => {
            console.error('Submit error:', error);
            alert('❌ Terjadi kesalahan: ' + error);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            isSubmitting = false;
        });
    }
}

// ==================== START ====================
renderCurrentQuestion();
timerInterval = setInterval(updateTimer, 1000);
updateTimer();
</script>
@endsection