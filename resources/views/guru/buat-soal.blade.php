@extends('layouts.app')

@section('title', 'Buat Soal Ujian - Guru')

@push('styles')
<style>
.question-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.question-card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border-color: #667eea;
}

.nav-tabs-custom {
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 24px;
}

.nav-tabs-custom .nav-link {
    border: none;
    padding: 12px 24px;
    font-weight: 600;
    color: #718096;
    transition: all 0.3s ease;
    cursor: pointer;
}

.nav-tabs-custom .nav-link:hover {
    color: #667eea;
}

.nav-tabs-custom .nav-link.active {
    color: #667eea;
    border-bottom: 3px solid #667eea;
    background: transparent;
}

/* Style untuk opsi jawaban */
.option-group {
    background: #f8fafc;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #e2e8f0;
}

.option-group-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 10px;
}

.option-letter-badge {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    font-weight: bold;
    font-size: 18px;
}

.option-text-input {
    width: 100%;
    padding: 10px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 10px;
}

.option-text-input:focus {
    outline: none;
    border-color: #667eea;
}

.option-image-input {
    width: 100%;
    padding: 8px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: white;
}

.option-preview {
    margin-top: 10px;
    padding: 10px;
    background: white;
    border-radius: 8px;
    border: 1px dashed #cbd5e1;
    text-align: center;
}

.option-preview img {
    max-height: 60px;
    max-width: 100px;
    border-radius: 8px;
}

.question-counter {
    background: #667eea;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Info kecil */
.option-info {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.option-info i {
    margin-right: 3px;
}

/* Tombol hapus gambar */
.btn-outline-danger {
    transition: all 0.2s ease;
}
.btn-outline-danger:hover {
    transform: translateY(-1px);
}

/* Container gambar dengan flex wrap */
.d-flex.flex-wrap {
    gap: 10px;
}

/* Perbaikan ukuran gambar opsi */
.option-img {
    max-height: 60px;
    width: auto;
    border-radius: 8px;
    border: 1px solid #ddd;
    object-fit: contain;
    background: #f8f9fa;
    padding: 4px;
}

/* Perbaikan container gambar opsi */
.option-image-wrapper {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
}

/* Tombol hapus gambar lebih kecil */
.btn-sm.btn-outline-danger {
    padding: 4px 10px;
    font-size: 12px;
}

/* Pesan belum ada gambar */
.text-muted.small {
    font-style: italic;
    color: #6c757d;
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
                        <h3 class="fw-bold mb-1" style="color: #2d3748;">
                            <i class="fas fa-pencil-alt me-2" style="color: #667eea;"></i>Buat Soal Ujian
                        </h3>
                        <p class="text-muted mb-0">
                            <i class="fas fa-book-open me-1"></i>{{ $exam->judul }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="tambahPilihanGanda()">
                            <i class="fas fa-plus me-2"></i>Tambah PG
                        </button>
                        <button type="button" class="btn btn-warning text-white" onclick="tambahEssay()">
                            <i class="fas fa-plus me-2"></i>Tambah Essay
                        </button>

                        <div class="dropdown">
                            <button class="btn btn-info text-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-file-import me-2"></i>Import Soal
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button type="button" class="dropdown-item" onclick="downloadExcelTemplate()">
                                        <i class="fas fa-file-excel me-2 text-success"></i> Download Template Excel
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <label class="dropdown-item" style="cursor: pointer;">
                                        <i class="fas fa-upload me-2 text-warning"></i> Import Excel
                                        <input type="file" id="excelFileInput" accept=".xlsx,.xls" style="display: none;" onchange="importExcel(this)">
                                    </label>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                <div id="alertMessage"></div>

                <!-- Tabs -->
                <ul class="nav nav-tabs-custom" id="questionTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" onclick="showTab('pg')">
                            <i class="fas fa-list-ul me-2"></i>Pilihan Ganda
                            <span class="badge bg-primary rounded-pill ms-2" id="pgCount">0</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showTab('essay')">
                            <i class="fas fa-file-alt me-2"></i>Essay
                            <span class="badge bg-primary rounded-pill ms-2" id="essayCount">0</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Pilihan Ganda Section -->
                    <div id="pg-section" style="display: block;">
                        <div id="pg-container"></div>
                        <div id="emptyPg" class="text-center py-5 text-muted">
                            <i class="fas fa-plus-circle fa-3x mb-3 opacity-50"></i>
                            <p>Belum ada soal pilihan ganda</p>
                            <button type="button" class="btn btn-sm btn-primary" onclick="tambahPilihanGanda()">
                                <i class="fas fa-plus me-1"></i>Tambah Soal PG
                            </button>
                        </div>
                    </div>

                    <!-- Essay Section -->
                    <div id="essay-section" style="display: none;">
                        <div id="essay-container"></div>
                        <div id="emptyEssay" class="text-center py-5 text-muted">
                            <i class="fas fa-plus-circle fa-3x mb-3 opacity-50"></i>
                            <p>Belum ada soal essay</p>
                            <button type="button" class="btn btn-sm btn-primary" onclick="tambahEssay()">
                                <i class="fas fa-plus me-1"></i>Tambah Soal Essay
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3">
                    <button type="button" id="simpanSoalBtn" class="btn btn-primary px-4" onclick="simpanSemuaSoal()">
                        <i class="fas fa-save me-2"></i>Simpan Semua Soal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variabel global
let pgCounter = 0;
let essayCounter = 0;
let isDataSaved = false;  // TAMBAHKAN - untuk tracking status penyimpanan

// Fungsi untuk menandai bahwa ada perubahan yang belum disimpan
function markAsUnsaved() {
    isDataSaved = false;
    console.log('⚠️ Status: Data BELUM disimpan');
}

// Fungsi untuk menandai bahwa data sudah disimpan
function markAsSaved() {
    isDataSaved = true;
    console.log('✅ Status: Data SUDAH disimpan');
}

// ==================== PERINGATAN SEBELUM RELOAD ====================
window.addEventListener('beforeunload', function(e) {
    // Cek apakah ada soal di halaman
    const pgCount = document.querySelectorAll('#pg-container .question-card').length;
    const essayCount = document.querySelectorAll('#essay-container .question-card').length;
    const hasQuestions = (pgCount > 0 || essayCount > 0);

    // Jika ada soal dan belum disimpan, tampilkan peringatan
    if (hasQuestions && !isDataSaved) {
        e.preventDefault();
        e.returnValue = '⚠️ PERINGATAN! ⚠️\n\nAda soal yang belum disimpan!\n\nKlik "Simpan Semua Soal" terlebih dahulu sebelum meninggalkan halaman.\n\nSoal yang Anda buat akan hilang jika tidak disimpan!';
        return e.returnValue;
    }
});

// Fungsi untuk mendeteksi perubahan pada input di dalam card
function setupChangeDetectionOnCard(card) {
    if (!card) return;

    const inputs = card.querySelectorAll('.question-text, .option-text-input, .correct-answer, .score-input, .question-image, .option-image-input');
    inputs.forEach(input => {
        // Hapus event listener lama untuk mencegah duplikasi
        input.removeEventListener('input', markAsUnsaved);
        input.removeEventListener('change', markAsUnsaved);
        // Tambahkan event listener baru
        input.addEventListener('input', markAsUnsaved);
        input.addEventListener('change', markAsUnsaved);
    });
}

// Fungsi untuk mendeteksi perubahan pada semua soal yang sudah ada
function setupAllChangeDetection() {
    const allCards = document.querySelectorAll('#pg-container .question-card, #essay-container .question-card');
    allCards.forEach(card => {
        setupChangeDetectionOnCard(card);
    });
}


// Fungsi untuk menampilkan tab
function showTab(tab) {
    if (tab === 'pg') {
        document.getElementById('pg-section').style.display = 'block';
        document.getElementById('essay-section').style.display = 'none';
    } else {
        document.getElementById('pg-section').style.display = 'none';
        document.getElementById('essay-section').style.display = 'block';
    }
}

// Preview gambar soal
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.style.display = 'block';
                const img = preview.querySelector('img');
                if (img) img.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview gambar opsi
function previewOptionImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.style.display = 'block';
                const img = preview.querySelector('img');
                if (img) img.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ==================== FUNGSI PREVIEW DAN HAPUS GAMBAR UNTUK SOAL SEMENTARA (PG) ====================

// Preview gambar soal untuk soal sementara (PG)
function previewGambarSoalTemp(input, uid) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Tampilkan preview permanen
            const previewContainer = document.getElementById(`image-preview-container-soal-${uid}`);
            const previewImg = document.getElementById(`preview-img-soal-${uid}`);

            if (previewContainer && previewImg) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            }

            // Juga tampilkan preview sementara
            const previewElem = document.getElementById(`preview_soal_${uid}`);
            if (previewElem) {
                previewElem.style.display = 'block';
                const img = previewElem.querySelector('img');
                if (img) img.src = e.target.result;
            }

            // Simpan data gambar ke attribute
            const fileInput = input;
            fileInput.setAttribute('data-has-image', 'true');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview gambar opsi untuk soal sementara (PG)
function previewGambarOpsiTemp(input, uid, option) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Tampilkan preview permanen
            const previewContainer = document.getElementById(`option-image-container-${option}-${uid}`);
            const previewImg = document.getElementById(`option-preview-img-${option}-${uid}`);

            if (previewContainer && previewImg) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            }

            // Simpan data gambar ke attribute
            const fileInput = input;
            fileInput.setAttribute('data-has-image', 'true');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Hapus gambar preview soal (untuk soal sementara PG)
function hapusGambarPreviewSoal(uid) {
    if (!confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
        return;
    }

    // Sembunyikan container preview
    const previewContainer = document.getElementById(`image-preview-container-soal-${uid}`);
    if (previewContainer) {
        previewContainer.style.display = 'none';
    }

    // Kosongkan preview img
    const previewImg = document.getElementById(`preview-img-soal-${uid}`);
    if (previewImg) {
        previewImg.src = '';
    }

    // Kosongkan file input
    const fileInput = document.querySelector(`#pg-container .question-card[data-id="${uid}"] .question-image`);
    if (fileInput) {
        fileInput.value = '';
        fileInput.removeAttribute('data-has-image');
    }

    // Sembunyikan preview sementara
    const previewElem = document.getElementById(`preview_soal_${uid}`);
    if (previewElem) {
        previewElem.style.display = 'none';
        const img = previewElem.querySelector('img');
        if (img) img.src = '';
    }

    showAlert('success', 'Gambar dihapus dari tampilan');
}

// Hapus gambar preview opsi (untuk soal sementara PG)
function hapusGambarPreviewOpsi(uid, option) {
    if (!confirm('Apakah Anda yakin ingin menghapus gambar opsi ini?')) {
        return;
    }

    // Sembunyikan container preview
    const previewContainer = document.getElementById(`option-image-container-${option}-${uid}`);
    if (previewContainer) {
        previewContainer.style.display = 'none';
    }

    // Kosongkan preview img
    const previewImg = document.getElementById(`option-preview-img-${option}-${uid}`);
    if (previewImg) {
        previewImg.src = '';
    }

    // Kosongkan file input
    const fileInput = document.querySelector(`#pg-container .question-card[data-id="${uid}"] .option-group[id="option-container-${option}-${uid}"] .option-image-input`);
    if (fileInput) {
        fileInput.value = '';
        fileInput.removeAttribute('data-has-image');
    }

    showAlert('success', 'Gambar opsi dihapus dari tampilan');
}


// ==================== FUNGSI PREVIEW DAN HAPUS GAMBAR UNTUK SOAL SEMENTARA (ESSAY) ====================

// Preview gambar soal untuk soal essay sementara
function previewGambarEssayTemp(input, uid) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Tampilkan preview permanen
            const previewContainer = document.getElementById(`image-preview-container-essay-${uid}`);
            const previewImg = document.getElementById(`preview-img-essay-${uid}`);

            if (previewContainer && previewImg) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            }

            // Juga tampilkan preview sementara
            const previewElem = document.getElementById(`preview_soal_essay_${uid}`);
            if (previewElem) {
                previewElem.style.display = 'block';
                const img = previewElem.querySelector('img');
                if (img) img.src = e.target.result;
            }

            // Simpan data gambar ke attribute
            const fileInput = input;
            fileInput.setAttribute('data-has-image', 'true');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Hapus gambar preview soal essay (untuk soal sementara)
function hapusGambarPreviewEssay(uid) {
    if (!confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
        return;
    }

    // Sembunyikan container preview
    const previewContainer = document.getElementById(`image-preview-container-essay-${uid}`);
    if (previewContainer) {
        previewContainer.style.display = 'none';
    }

    // Kosongkan preview img
    const previewImg = document.getElementById(`preview-img-essay-${uid}`);
    if (previewImg) {
        previewImg.src = '';
    }

    // Kosongkan file input
    const fileInput = document.querySelector(`#essay-container .question-card[data-id="${uid}"] .question-image`);
    if (fileInput) {
        fileInput.value = '';
        fileInput.removeAttribute('data-has-image');
    }

    // Sembunyikan preview sementara
    const previewElem = document.getElementById(`preview_soal_essay_${uid}`);
    if (previewElem) {
        previewElem.style.display = 'none';
        const img = previewElem.querySelector('img');
        if (img) img.src = '';
    }

    showAlert('success', 'Gambar dihapus dari tampilan');
}

// Fungsi tambah soal pilihan ganda
function tambahPilihanGanda() {
    pgCounter++;
    const uid = pgCounter;
    const newQuestionId = 'new_' + Date.now() + '_' + uid;

    const newQuestion = `
        <div class="question-card" data-id="${uid}" data-question-id="${newQuestionId}" data-type="pg" data-existing="false">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-bold mb-0">
                    <span class="question-counter">Soal PG ${uid}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this)">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            <!-- Upload Gambar Soal -->
            <div class="mb-3" id="image-container-soal-${uid}">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                </label>
                <div id="image-preview-container-soal-${uid}" style="display: none;" class="mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <img id="preview-img-soal-${uid}" src="" class="img-fluid rounded" style="max-height: 100px; border: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarPreviewSoal(${uid})">
                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                        </button>
                    </div>
                    <hr>
                    <label class="form-label fw-semibold">Ganti dengan Gambar Baru</label>
                </div>
                <input type="file" class="form-control question-image" accept="image/*"
                       onchange="previewGambarSoalTemp(this, ${uid})">
                <div id="preview_soal_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <!-- Teks Soal -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-question-circle me-2 text-primary"></i>Soal
                </label>
                <textarea class="form-control question-text" rows="3" placeholder="Masukkan soal..."></textarea>
            </div>

            <!-- Opsi Jawaban -->
            <label class="form-label fw-semibold mb-2">
                <i class="fas fa-list-ul me-2 text-primary"></i>Opsi Jawaban
            </label>
            <div class="option-info">
                <span><i class="fas fa-info-circle"></i> Isi teks atau upload gambar (bisa keduanya)</span>
            </div>

            <!-- Opsi A -->
            <div class="option-group" id="option-container-a-${uid}">
                <div class="option-group-header">
                    <div class="option-letter-badge">A</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi A (kosongkan jika hanya gambar)">
                <div id="option-image-container-a-${uid}" style="display: none;" class="mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <img id="option-preview-img-a-${uid}" src="" class="option-img" style="max-height: 60px; width: auto; border-radius: 8px; border: 1px solid #ddd;">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarPreviewOpsi(${uid}, 'a')">
                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                        </button>
                    </div>
                    <hr class="mt-2 mb-2">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Ganti dengan Gambar Baru</label>
                </div>
                <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                       onchange="previewGambarOpsiTemp(this, ${uid}, 'a')">
                <div id="preview_a_${uid}" class="option-preview" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <!-- Opsi B -->
            <div class="option-group" id="option-container-b-${uid}">
                <div class="option-group-header">
                    <div class="option-letter-badge">B</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi B (kosongkan jika hanya gambar)">
                <div id="option-image-container-b-${uid}" style="display: none;" class="mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <img id="option-preview-img-b-${uid}" src="" class="option-img" style="max-height: 60px; width: auto; border-radius: 8px; border: 1px solid #ddd;">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarPreviewOpsi(${uid}, 'b')">
                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                        </button>
                    </div>
                    <hr class="mt-2 mb-2">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Ganti dengan Gambar Baru</label>
                </div>
                <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                       onchange="previewGambarOpsiTemp(this, ${uid}, 'b')">
                <div id="preview_b_${uid}" class="option-preview" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <!-- Opsi C -->
            <div class="option-group" id="option-container-c-${uid}">
                <div class="option-group-header">
                    <div class="option-letter-badge">C</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi C (kosongkan jika hanya gambar)">
                <div id="option-image-container-c-${uid}" style="display: none;" class="mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <img id="option-preview-img-c-${uid}" src="" class="option-img" style="max-height: 60px; width: auto; border-radius: 8px; border: 1px solid #ddd;">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarPreviewOpsi(${uid}, 'c')">
                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                        </button>
                    </div>
                    <hr class="mt-2 mb-2">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Ganti dengan Gambar Baru</label>
                </div>
                <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                       onchange="previewGambarOpsiTemp(this, ${uid}, 'c')">
                <div id="preview_c_${uid}" class="option-preview" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <!-- Opsi D -->
            <div class="option-group" id="option-container-d-${uid}">
                <div class="option-group-header">
                    <div class="option-letter-badge">D</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi D (kosongkan jika hanya gambar)">
                <div id="option-image-container-d-${uid}" style="display: none;" class="mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <img id="option-preview-img-d-${uid}" src="" class="option-img" style="max-height: 60px; width: auto; border-radius: 8px; border: 1px solid #ddd;">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarPreviewOpsi(${uid}, 'd')">
                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                        </button>
                    </div>
                    <hr class="mt-2 mb-2">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Ganti dengan Gambar Baru</label>
                </div>
                <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                       onchange="previewGambarOpsiTemp(this, ${uid}, 'd')">
                <div id="preview_d_${uid}" class="option-preview" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <!-- Kunci Jawaban dan Nilai -->
            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-key me-2 text-primary"></i>Kunci Jawaban
                    </label>
                    <select class="form-select correct-answer">
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-star me-2 text-primary"></i>Nilai Soal
                    </label>
                    <input type="number" class="form-control score-input" placeholder="Nilai" value="5" min="1" max="100">
                </div>
            </div>
            <div class="row mt-3">
                <hr style="border: 3px solid black">
            </div>
        </div>
    `;

    document.getElementById('pg-container').insertAdjacentHTML('beforeend', newQuestion);
    document.getElementById('pgCount').innerText = pgCounter;
    document.getElementById('emptyPg').style.display = 'none';

    // ========== TAMBAHKAN DETEKSI PERUBAHAN ==========
    const newCard = document.querySelector(`.question-card[data-id="${uid}"]`);
    if (newCard) {
        setupChangeDetectionOnCard(newCard);
    }
    markAsUnsaved(); // Tandai ada perubahan
}



// Fungsi tambah soal essay
function tambahEssay() {
    essayCounter++;
    const uid = essayCounter;
    const newQuestionId = 'new_' + Date.now() + '_' + uid;

    const newEssay = `
        <div class="question-card" data-id="${uid}" data-question-id="${newQuestionId}" data-type="essay" data-existing="false">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-bold mb-0">
                    <span class="question-counter">Soal Essay ${uid}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this)">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            <!-- Upload Gambar Soal Essay -->
            <div class="mb-3" id="image-container-essay-${uid}">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                </label>
                <div id="image-preview-container-essay-${uid}" style="display: none;" class="mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <img id="preview-img-essay-${uid}" src="" class="img-fluid rounded" style="max-height: 100px; border: 1px solid #e2e8f0;">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarPreviewEssay(${uid})">
                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                        </button>
                    </div>
                    <hr>
                    <label class="form-label fw-semibold">Ganti dengan Gambar Baru</label>
                </div>
                <input type="file" class="form-control question-image" accept="image/*"
                       onchange="previewGambarEssayTemp(this, ${uid})">
                <div id="preview_soal_essay_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-pen-alt me-2 text-primary"></i>Soal Essay
                </label>
                <textarea class="form-control question-text" rows="4" placeholder="Masukkan soal essay..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-key me-2 text-primary"></i>Kunci Jawaban (untuk AI Similarity)
                </label>
                <textarea class="form-control correct-answer" rows="3" placeholder="Masukkan kunci jawaban..."></textarea>
                <small class="text-muted">Jawaban peserta akan dibandingkan dengan kunci jawaban ini menggunakan AI text similarity</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-star me-2 text-primary"></i>Nilai Soal
                </label>
                <input type="number" class="form-control score-input" placeholder="Nilai" value="10" min="1" max="100">
            </div>
            <div class="row mt-3">
                <hr style="border: 3px solid black">
            </div>
        </div>
    `;

    document.getElementById('essay-container').insertAdjacentHTML('beforeend', newEssay);
    document.getElementById('essayCount').innerText = essayCounter;
    document.getElementById('emptyEssay').style.display = 'none';

    // ========== TAMBAHKAN DETEKSI PERUBAHAN ==========
    const newCard = document.querySelector(`.question-card[data-id="${uid}"]`);
    if (newCard) {
        setupChangeDetectionOnCard(newCard);
    }
    markAsUnsaved(); // Tandai ada perubahan
}

// Fungsi hapus soal - VERSI FINAL (Hapus dari Database, Storage, dan Tampilan)
function hapusSoal(btn, questionId = null) {
    if (!confirm('⚠️ Hapus soal ini?\n\nSoal yang dihapus tidak dapat dikembalikan!')) {
        return;
    }

    const card = btn.closest('.question-card');
    const type = card.getAttribute('data-type');
    const isExisting = card.getAttribute('data-existing') === 'true';

    // Ambil ID dari parameter atau dari attribute
    let finalQuestionId = questionId;
    if (!finalQuestionId) {
        finalQuestionId = card.getAttribute('data-question-id');
    }

    console.log('Menghapus soal:', { finalQuestionId, type, isExisting });

    // ========== CEK APAKAH INI SOAL SEMENTARA (BELUM DISIMPAN) ==========
    const isTemporary = !finalQuestionId ||
                        finalQuestionId.toString().startsWith('temp_') ||
                        finalQuestionId.toString().startsWith('new_') ||
                        isNaN(parseInt(finalQuestionId));

    if (isTemporary || !isExisting) {
        // Soal sementara (belum tersimpan di database), hapus langsung dari DOM
        console.log('Menghapus soal sementara dari tampilan (ID: ' + finalQuestionId + ')');
        card.remove();

        if (type === 'pg') {
            renumberPG();
        } else {
            renumberEssay();
        }

        updateEmptyState();
        updateCounters();
        markAsUnsaved(); // Tandai ada perubahan
        showAlert('success', 'Soal dihapus dari tampilan (belum tersimpan)');
        return;
    }

    // ========== HAPUS SOAL YANG SUDAH TERSIMPAN DI DATABASE ==========
    // Tampilkan loading
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    fetch('/guru/hapus-soal/' + finalQuestionId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response hapus:', data);

        if (data.success) {
            // Hapus dari DOM
            card.remove();

            // Renumber ulang soal
            if (type === 'pg') {
                renumberPG();
            } else {
                renumberEssay();
            }

            updateEmptyState();
            updateCounters();
            markAsUnsaved(); // Tandai ada perubahan (soal dihapus)
            showAlert('success', '✅ Soal berhasil dihapus dari database!');
        } else {
            showAlert('danger', data.message || 'Gagal menghapus soal');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Terjadi kesalahan: ' + error);
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Hapus gambar soal
function hapusGambarSoal(questionId, btn) {
    if (!confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
        return;
    }

    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    fetch('/guru/hapus-gambar-soal/' + questionId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hapus seluruh container gambar
            const imageContainer = btn.closest('.mb-3');
            if (imageContainer) {
                // Ganti dengan form upload gambar baru
                imageContainer.innerHTML = `
                    <label class="form-label fw-semibold">
                        <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                    </label>
                    <input type="file" class="form-control question-image" accept="image/*"
                           onchange="previewImage(this, '${btn.closest('.question-card').querySelector('.question-image')?.id || ''}')">
                    <div class="mt-2" style="display: none;">
                        <img src="" class="img-fluid rounded" style="max-height: 150px;">
                    </div>
                `;
            }
            showAlert('success', data.message);
        } else {
            showAlert('danger', data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        showAlert('danger', 'Terjadi kesalahan: ' + error);
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Hapus gambar opsi
function hapusGambarOpsi(questionId, optionIndex, btn) {
    if (!confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
        return;
    }

    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    fetch(`/guru/hapus-gambar-opsi/${questionId}/${optionIndex}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hapus container gambar opsi
            const imageContainer = btn.closest('.option-group');
            if (imageContainer) {
                // Hapus elemen gambar dan tombol hapus, tampilkan form upload baru
                const existingImageDiv = imageContainer.querySelector('.mb-2.d-flex');
                if (existingImageDiv) {
                    existingImageDiv.remove();
                }
                // Tambahkan hr dan label jika diperlukan
                const hr = imageContainer.querySelector('hr');
                if (hr) hr.remove();
            }
            showAlert('success', data.message);
        } else {
            showAlert('danger', data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        showAlert('danger', 'Terjadi kesalahan: ' + error);
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}


// Renumber soal PG
function renumberPG() {
    const questions = document.querySelectorAll('#pg-container .question-card');
    questions.forEach((q, index) => {
        const newId = index + 1;
        q.setAttribute('data-id', newId);
        const counter = q.querySelector('.question-counter');
        if (counter) counter.innerText = `Soal PG ${newId}`;
    });
    pgCounter = questions.length;
    document.getElementById('pgCount').innerText = pgCounter;
    console.log('Renumber PG selesai, total:', pgCounter);
}

// Renumber soal Essay
function renumberEssay() {
    const questions = document.querySelectorAll('#essay-container .question-card');
    questions.forEach((q, index) => {
        const newId = index + 1;
        q.setAttribute('data-id', newId);
        const counter = q.querySelector('.question-counter');
        if (counter) counter.innerText = `Soal Essay ${newId}`;
    });
    essayCounter = questions.length;
    document.getElementById('essayCount').innerText = essayCounter;
    console.log('Renumber Essay selesai, total:', essayCounter);
}

// Update empty state
function updateEmptyState() {
    const pgContainer = document.getElementById('pg-container');
    const emptyPg = document.getElementById('emptyPg');
    const essayContainer = document.getElementById('essay-container');
    const emptyEssay = document.getElementById('emptyEssay');

    const pgCount = pgContainer ? pgContainer.children.length : 0;
    const essayCount = essayContainer ? essayContainer.children.length : 0;

    if (pgCount === 0) {
        if (emptyPg) emptyPg.style.display = 'block';
        if (document.getElementById('pgCount')) document.getElementById('pgCount').innerText = '0';
    } else {
        if (emptyPg) emptyPg.style.display = 'none';
    }

    if (essayCount === 0) {
        if (emptyEssay) emptyEssay.style.display = 'block';
        if (document.getElementById('essayCount')) document.getElementById('essayCount').innerText = '0';
    } else {
        if (emptyEssay) emptyEssay.style.display = 'none';
    }
}

// Simpan semua soal
function simpanSemuaSoal() {
    const formData = new FormData();
    let questionIndex = 0;

    // KUMPULKAN SOAL PG
    const pgQuestions = document.querySelectorAll('#pg-container .question-card');
    for (let q of pgQuestions) {
        let questionId = q.getAttribute('data-question-id');
        const isExisting = q.getAttribute('data-existing') === 'true';
        const questionText = q.querySelector('.question-text').value;

        if (!questionText) {
            showAlert('danger', 'Semua soal harus diisi!');
            return;
        }

        const options = [];
        const optionTextInputs = q.querySelectorAll('.option-text-input');
        for (let i = 0; i < optionTextInputs.length; i++) {
            options.push(optionTextInputs[i].value || '');
        }

        const correctAnswer = q.querySelector('.correct-answer').value;
        const score = q.querySelector('.score-input').value;

        if (questionId && questionId.toString().startsWith('new_')) {
            questionId = 'new';
        }

        const imageInput = q.querySelector('.question-image');
        if (imageInput && imageInput.files[0]) {
            formData.append(`questions[${questionIndex}][image]`, imageInput.files[0]);
        }

        const optionImageInputs = q.querySelectorAll('.option-image-input');
        for (let i = 0; i < optionImageInputs.length; i++) {
            if (optionImageInputs[i].files[0]) {
                formData.append(`questions[${questionIndex}][option_images][${i}]`, optionImageInputs[i].files[0]);
            }
        }

        formData.append(`questions[${questionIndex}][id]`, questionId || 'new');
        formData.append(`questions[${questionIndex}][is_existing]`, isExisting);
        formData.append(`questions[${questionIndex}][type]`, 'pg');
        formData.append(`questions[${questionIndex}][question_text]`, questionText);
        formData.append(`questions[${questionIndex}][options]`, JSON.stringify(options));
        formData.append(`questions[${questionIndex}][correct_answer]`, correctAnswer);
        formData.append(`questions[${questionIndex}][score]`, score);

        questionIndex++;
    }

    // KUMPULKAN SOAL ESSAY
    const essayQuestions = document.querySelectorAll('#essay-container .question-card');
    for (let q of essayQuestions) {
        let questionId = q.getAttribute('data-question-id');
        const isExisting = q.getAttribute('data-existing') === 'true';
        const questionText = q.querySelector('.question-text').value;
        const correctAnswer = q.querySelector('.correct-answer').value;
        const score = q.querySelector('.score-input').value;

        if (!questionText) {
            showAlert('danger', 'Semua soal harus diisi!');
            return;
        }

        if (questionId && questionId.toString().startsWith('new_')) {
            questionId = 'new';
        }

        const imageInput = q.querySelector('.question-image');
        if (imageInput && imageInput.files[0]) {
            formData.append(`questions[${questionIndex}][image]`, imageInput.files[0]);
        }

        formData.append(`questions[${questionIndex}][id]`, questionId || 'new');
        formData.append(`questions[${questionIndex}][is_existing]`, isExisting);
        formData.append(`questions[${questionIndex}][type]`, 'essay');
        formData.append(`questions[${questionIndex}][question_text]`, questionText);
        formData.append(`questions[${questionIndex}][correct_answer]`, correctAnswer);
        formData.append(`questions[${questionIndex}][score]`, score);

        questionIndex++;
    }

    if (questionIndex === 0) {
        showAlert('danger', 'Minimal buat 1 soal terlebih dahulu!');
        return;
    }

    const submitBtn = document.getElementById('simpanSoalBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;

    console.log('Mengirim data ke server...');

    fetch('/guru/store-questions/{{ $exam->id }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response dari server:', data);

        if (data.success) {
            // ========== TANDAI DATA SUDAH DISIMPAN ==========
            markAsSaved();

            showAlert('success', data.message);

            if (data.redirect) {
                console.log('Redirect ke:', data.redirect);
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        } else {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        showAlert('danger', 'Terjadi kesalahan: ' + error);
    });
}

// Show alert
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    const alertContainer = document.getElementById('alertMessage');
    if (alertContainer) {
        alertContainer.innerHTML = alertHtml;
    } else {
        // Fallback if alert container doesn't exist
        const container = document.querySelector('.card-body');
        if (container) {
            const div = document.createElement('div');
            div.id = 'alertMessage';
            div.innerHTML = alertHtml;
            container.insertBefore(div, container.firstChild);
        }
    }

    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => alert.remove());
    }, 5000);
}


// ==================== FUNGSI IMPORT CSV ====================
let importedQuestions = [];

// Download template Excel
function downloadExcelTemplate() {
    window.location.href = '/guru/download-template-excel/{{ $exam->id }}';
    showAlert('success', 'Download template Excel dimulai...');
}

// Import file Excel
function importExcel(input) {
    const file = input.files[0];
    if (!file) return;

    // Validasi ekstensi file
    const validExtensions = ['.xlsx', '.xls'];
    const fileName = file.name.toLowerCase();
    const isValid = validExtensions.some(ext => fileName.endsWith(ext));

    if (!isValid) {
        showAlert('danger', 'File harus berformat Excel (.xlsx atau .xls)');
        input.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('import_file', file);

    // Tampilkan loading
    const importBtn = document.querySelector('.dropdown-item:has(input)');
    const originalText = importBtn ? importBtn.innerHTML : 'Import Excel';
    if (importBtn) importBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengimport...';

    console.log('🚀 Mengirim request import Excel...');

    fetch('/guru/import-soal-excel/{{ $exam->id }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('📦 Response import:', data);

        if (data.success) {
            showAlert('success', data.message);
            console.log(`✅ Import berhasil: ${data.questions ? data.questions.length : 0} soal`);

            // Tampilkan detail soal yang diimport
            if (data.questions && data.questions.length > 0) {
                console.log('📋 Detail soal yang diimport:');
                data.questions.forEach((q, idx) => {
                    console.log(`  ${idx + 1}. [${q.type}] ID:${q.id} - ${q.question_text.substring(0, 50)}...`);
                    if (q.image) console.log(`     Gambar soal: ${q.image}`);
                    if (q.option_images) console.log(`     Gambar opsi: ${JSON.stringify(q.option_images)}`);
                });

                // Tampilkan soal ke halaman
                tampilkanSoalImport(data.questions);

                // ========== TAMBAHKAN DETEKSI PERUBAHAN ==========
                setupAllChangeDetection();
            } else {
                console.log('⚠️ Tidak ada data questions dalam response');
            }

            // Update counter
            updateCounters();
            markAsUnsaved();
        } else {
            console.error('❌ Import gagal:', data.message);
            showAlert('danger', data.message);
        }

        // Reset file input
        input.value = '';
        if (importBtn) importBtn.innerHTML = originalText;
    })
    .catch(error => {
        console.error('❌ Error saat import:', error);
        showAlert('danger', 'Terjadi kesalahan: ' + error.message);
        input.value = '';
        if (importBtn) importBtn.innerHTML = originalText;
    });
}


// Fungsi untuk menampilkan soal hasil import
function tampilkanSoalImport(questions) {
    console.log('📥 Menampilkan soal import:', questions);

    let pgAdded = 0;
    let essayAdded = 0;

    questions.forEach(question => {
        // CEK APAKAH SOAL SUDAH ADA DI DOM (untuk menghindari duplikasi)
        const existingQuestions = document.querySelectorAll(`.question-card[data-question-id="${question.id}"]`);

        if (existingQuestions.length > 0) {
            console.log(`⚠️ Soal sudah ada, skip: ID ${question.id}`);
            return;
        }

        console.log(`➕ Menambahkan soal baru: ID ${question.id}, Tipe ${question.type}`);

        if (question.type === 'pg') {
            pgCounter++;
            addPGQuestionFromImport(question.id, question);
            pgAdded++;
        } else if (question.type === 'essay') {
            essayCounter++;
            addEssayQuestionFromImport(question.id, question);
            essayAdded++;
        }
    });

    // Update counter display
    const pgCountElem = document.getElementById('pgCount');
    const essayCountElem = document.getElementById('essayCount');
    const emptyPgElem = document.getElementById('emptyPg');
    const emptyEssayElem = document.getElementById('emptyEssay');

    if (pgCountElem) pgCountElem.innerText = pgCounter;
    if (essayCountElem) essayCountElem.innerText = essayCounter;
    if (emptyPgElem) emptyPgElem.style.display = pgCounter > 0 ? 'none' : 'block';
    if (emptyEssayElem) emptyEssayElem.style.display = essayCounter > 0 ? 'none' : 'block';

    console.log(`✅ Import selesai - PG: ${pgAdded}, Essay: ${essayAdded}`);

    if (pgAdded > 0 || essayAdded > 0) {
        showAlert('success', `Berhasil menampilkan ${pgAdded} soal PG dan ${essayAdded} soal essay!`);
    }
}

// Fungsi untuk menampilkan panduan format Excel
function showExcelFormatGuide() {
    const guideHtml = `
        <div class="modal fade" id="excelGuideModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-table me-2"></i>Panduan Mengisi Template di Excel
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Tabel Contoh -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>TIPE SOAL</th>
                                        <th>SOAL</th>
                                        <th>NILAI</th>
                                        <th>KUNCI JAWABAN</th>
                                        <th>OPSI A</th>
                                        <th>OPSI B</th>
                                        <th>OPSI C</th>
                                        <th>OPSI D</th>
                                        <th>KUNCI ESSAY</th>
                                    </tr>
                                    <tr class="table-secondary">
                                        <td><small>(pg/essay)</small></td>
                                        <td><small>(Tulis soal)</small></td>
                                        <td><small>(1-100)</small></td>
                                        <td><small>(A/B/C/D)</small></td>
                                        <td><small>(Teks Opsi A)</small></td>
                                        <td><small>(Teks Opsi B)</small></td>
                                        <td><small>(Teks Opsi C)</small></td>
                                        <td><small>(Teks Opsi D)</small></td>
                                        <td><small>(Untuk Essay)</small></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-info">
                                        <td><span class="badge bg-primary">pg</span></td>
                                        <td>Apa kepanjangan dari PHP?</td>
                                        <td>5</td>
                                        <td><span class="badge bg-success">B</span></td>
                                        <td>Personal Home Page</td>
                                        <td>PHP: Hypertext Preprocessor</td>
                                        <td>Preprocessed Hypertext Page</td>
                                        <td>Pre Hypertext Processor</td>
                                        <td>-</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><span class="badge bg-primary">pg</span></td>
                                        <td>Bahasa pemrograman untuk Laravel?</td>
                                        <td>5</td>
                                        <td><span class="badge bg-success">C</span></td>
                                        <td>Python</td>
                                        <td>Java</td>
                                        <td>PHP</td>
                                        <td>Ruby</td>
                                        <td>-</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><span class="badge bg-warning">essay</span></td>
                                        <td>Jelaskan apa yang dimaksud dengan OOP!</td>
                                        <td>20</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>Pemrograman berorientasi objek adalah paradigma...</td>
                                    </tr>
                                    <tr>
                                        <td colspan="9" class="text-center bg-light">
                                            <i class="fas fa-arrow-down text-success me-2"></i>
                                            <strong class="text-success">ISIKAN SOAL ANDA DI BAWAH INI</strong>
                                            <i class="fas fa-arrow-down text-success ms-2"></i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="pg/essay"></td>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="Tulis soal..."></td>
                                        <td><input type="number" class="form-control form-control-sm" placeholder="Nilai"></td>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="A/B/C/D"></td>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="Opsi A"></td>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="Opsi B"></td>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="Opsi C"></td>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="Opsi D"></td>
                                        <td><input type="text" class="form-control form-control-sm" placeholder="Kunci Essay"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Cara Penggunaan:</strong>
                            <ol class="mt-2 mb-0">
                                <li>Download template CSV dengan klik tombol di bawah</li>
                                <li>Buka file CSV dengan Microsoft Excel</li>
                                <li>Isi data soal pada baris kosong (di bawah contoh)</li>
                                <li>Untuk soal PG: tulis <code>pg</code> di kolom TIPE SOAL</li>
                                <li>Untuk soal Essay: tulis <code>essay</code> di kolom TIPE SOAL</li>
                                <li>Simpan file (File → Save As → Pilih CSV UTF-8)</li>
                                <li>Upload kembali file CSV yang sudah diisi</li>
                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Tutup
                        </button>
                        <button type="button" class="btn btn-primary" onclick="downloadTemplate()">
                            <i class="fas fa-download me-2"></i>Download Template CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('excelGuideModal');
    if (existingModal) existingModal.remove();

    document.body.insertAdjacentHTML('beforeend', guideHtml);
    const modal = new bootstrap.Modal(document.getElementById('excelGuideModal'));
    modal.show();
}

// Fungsi untuk menambah soal PG dari import ke tampilan
function addPGQuestionFromImport(questionId, data) {
    const options = data.options || ['', '', '', ''];
    const correctAnswer = data.correct_answer || 'A';
    const score = data.score || 5;
    const questionText = data.question_text || '';
    const hasImage = data.image && data.image !== null && data.image !== '';
    const hasOptionImages = data.option_images && Array.isArray(data.option_images);

    // Gunakan ID DATABASE untuk data-id
    const uid = pgCounter;

    // ========== GAMBAR SOAL IMPORT ==========
    let imageHtml = '';
    if (hasImage) {
        const imageUrl = data.image.startsWith('/storage/') ? data.image : '/storage/' + data.image;
        imageHtml = `
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal Saat Ini
                </label>
                <div class="mb-2 d-flex align-items-center gap-3 flex-wrap">
                    <img src="${imageUrl}" class="img-fluid rounded" style="max-height: 100px; border: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarSoal(${questionId}, this)">
                        <i class="fas fa-trash me-1"></i> Hapus Gambar
                    </button>
                </div>
                <hr>
                <label class="form-label fw-semibold">Ganti dengan Gambar Baru</label>
                <input type="file" class="form-control question-image" accept="image/*" onchange="previewImage(this, 'preview_soal_${uid}')">
                <div id="preview_soal_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    } else {
        imageHtml = `
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                </label>
                <div class="text-muted small mb-2">
                    <i class="fas fa-info-circle"></i> Belum ada gambar untuk soal ini
                </div>
                <input type="file" class="form-control question-image" accept="image/*" onchange="previewImage(this, 'preview_soal_${uid}')">
                <div id="preview_soal_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    }

    // ========== GAMBAR OPSI IMPORT ==========
    const letters = ['A', 'B', 'C', 'D'];
    let optionsHtml = '';

    for (let i = 0; i < letters.length; i++) {
        const letter = letters[i];
        const optImage = hasOptionImages && data.option_images[i] ? data.option_images[i] : null;

        let optionImageHtml = '';
        if (optImage && optImage !== null && optImage !== '') {
            const optImageUrl = optImage.startsWith('/storage/') ? optImage : '/storage/' + optImage;
            optionImageHtml = `
                <div class="mb-2 d-flex align-items-center gap-3 flex-wrap">
                    <img src="${optImageUrl}" style="max-height: 50px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarOpsi(${questionId}, ${i}, this)">
                        <i class="fas fa-trash me-1"></i> Hapus Gambar
                    </button>
                </div>
                <hr>
                <label class="form-label fw-semibold" style="font-size: 12px;">Ganti dengan Gambar Baru</label>
            `;
        } else {
            optionImageHtml = `
                <div class="text-muted small mb-2">
                    <i class="fas fa-info-circle"></i> Belum ada gambar untuk opsi ${letter}
                </div>
            `;
        }

        optionsHtml += `
            <div class="option-group">
                <div class="option-group-header">
                    <div class="option-letter-badge">${letter}</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi ${letter}" value="${escapeHtml(options[i] || '')}">
                ${optionImageHtml}
                <input type="file" class="form-control option-image-input mb-2" accept="image/*" onchange="previewOptionImage(this, 'preview_${letter.toLowerCase()}_${uid}')">
                <div id="preview_${letter.toLowerCase()}_${uid}" class="option-preview" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    }

    const newQuestion = `
        <div class="question-card" data-id="${uid}" data-question-id="${questionId}" data-type="pg" data-existing="true">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-bold mb-0">
                    <span class="question-counter">Soal PG ${pgCounter}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this, ${questionId})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            ${imageHtml}

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-question-circle me-2 text-primary"></i>Soal
                </label>
                <textarea class="form-control question-text" rows="3" placeholder="Masukkan soal...">${escapeHtml(questionText)}</textarea>
            </div>

            <label class="form-label fw-semibold mb-2">
                <i class="fas fa-list-ul me-2 text-primary"></i>Opsi Jawaban
            </label>
            <div class="option-info">
                <span><i class="fas fa-info-circle"></i> Isi teks atau upload gambar (bisa keduanya)</span>
            </div>

            ${optionsHtml}

            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-key me-2 text-primary"></i>Kunci Jawaban
                    </label>
                    <select class="form-select correct-answer">
                        <option value="A" ${correctAnswer === 'A' ? 'selected' : ''}>A</option>
                        <option value="B" ${correctAnswer === 'B' ? 'selected' : ''}>B</option>
                        <option value="C" ${correctAnswer === 'C' ? 'selected' : ''}>C</option>
                        <option value="D" ${correctAnswer === 'D' ? 'selected' : ''}>D</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-star me-2 text-primary"></i>Nilai Soal
                    </label>
                    <input type="number" class="form-control score-input" placeholder="Nilai" value="${score}" min="1" max="100">
                </div>
            </div>
            <div class="row mt-3">
                <hr style="border: 3px solid black">
            </div>
        </div>
    `;

    const pgContainer = document.getElementById('pg-container');
    if (pgContainer) {
        pgContainer.insertAdjacentHTML('beforeend', newQuestion);
        console.log(`✅ Soal PG import ${uid} berhasil ditambahkan ke DOM`);
    }
}


// Fungsi untuk menambah soal Essay dari import ke tampilan
function addEssayQuestionFromImport(questionId, data) {
    const questionText = data.question_text || '';
    const essayKeyword = data.correct_answer || '';
    const score = data.score || 10;
    const hasImage = data.image && data.image !== null && data.image !== '';

    // Gunakan ID DATABASE untuk data-question-id
    const uid = essayCounter;

    // ========== GAMBAR SOAL IMPORT ==========
    let imageHtml = '';
    if (hasImage) {
        const imageUrl = data.image.startsWith('/storage/') ? data.image : '/storage/' + data.image;
        imageHtml = `
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal Saat Ini
                </label>
                <div class="mb-2 d-flex align-items-center gap-3 flex-wrap">
                    <img src="${imageUrl}" class="img-fluid rounded" style="max-height: 100px; border: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarSoal(${questionId}, this)">
                        <i class="fas fa-trash me-1"></i> Hapus Gambar
                    </button>
                </div>
                <hr>
                <label class="form-label fw-semibold">Ganti dengan Gambar Baru</label>
                <input type="file" class="form-control question-image" accept="image/*" onchange="previewImage(this, 'preview_soal_essay_${uid}')">
                <div id="preview_soal_essay_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    } else {
        imageHtml = `
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                </label>
                <div class="text-muted small mb-2">
                    <i class="fas fa-info-circle"></i> Belum ada gambar untuk soal ini
                </div>
                <input type="file" class="form-control question-image" accept="image/*" onchange="previewImage(this, 'preview_soal_essay_${uid}')">
                <div id="preview_soal_essay_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    }

    const newEssay = `
        <div class="question-card" data-id="${uid}" data-question-id="${questionId}" data-type="essay" data-existing="true">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-bold mb-0">
                    <span class="question-counter">Soal Essay ${essayCounter}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this, ${questionId})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            ${imageHtml}

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-pen-alt me-2 text-primary"></i>Soal Essay
                </label>
                <textarea class="form-control question-text" rows="4" placeholder="Masukkan soal essay...">${escapeHtml(questionText)}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-key me-2 text-primary"></i>Kunci Jawaban (untuk AI Similarity)
                </label>
                <textarea class="form-control correct-answer" rows="3" placeholder="Masukkan kunci jawaban...">${escapeHtml(essayKeyword)}</textarea>
                <small class="text-muted">Jawaban peserta akan dibandingkan dengan kunci jawaban ini menggunakan AI text similarity</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-star me-2 text-primary"></i>Nilai Soal
                </label>
                <input type="number" class="form-control score-input" placeholder="Nilai" value="${score}" min="1" max="100">
            </div>
            <div class="row mt-3">
                <hr style="border: 3px solid black">
            </div>
        </div>
    `;

    const essayContainer = document.getElementById('essay-container');
    if (essayContainer) {
        essayContainer.insertAdjacentHTML('beforeend', newEssay);
        console.log(`✅ Soal Essay import ${uid} berhasil ditambahkan ke DOM`);
    }
}

// ==================== FUNGSI UNTUK MEMUAT SOAL DARI LOCALSTORAGE ====================





// Fungsi untuk update counter (sederhana dan efektif)
function updateCounters() {
    const pgCount = document.querySelectorAll('#pg-container .question-card').length;
    const essayCount = document.querySelectorAll('#essay-container .question-card').length;

    pgCounter = pgCount;
    essayCounter = essayCount;

    document.getElementById('pgCount').innerText = pgCounter;
    document.getElementById('essayCount').innerText = essayCounter;

    document.getElementById('emptyPg').style.display = pgCounter > 0 ? 'none' : 'block';
    document.getElementById('emptyEssay').style.display = essayCounter > 0 ? 'none' : 'block';

    console.log(`📊 Update counters - PG: ${pgCounter}, Essay: ${essayCounter}`);
}



// Helper function escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


// ==================== LOAD SOAL YANG SUDAH ADA DI DATABASE ====================
let existingQuestions = @json($existingQuestions ?? []);

// Fungsi untuk memuat soal yang sudah ada ke tampilan
function loadExistingQuestions() {
    console.log('=== LOAD EXISTING QUESTIONS ===');
    console.log('Raw existingQuestions data:', existingQuestions);

    if (!existingQuestions || existingQuestions.length === 0) {
        console.log('Tidak ada soal yang sudah ada');
        const pgContainer = document.getElementById('pg-container');
        const essayContainer = document.getElementById('essay-container');
        if (pgContainer) pgContainer.innerHTML = '';
        if (essayContainer) essayContainer.innerHTML = '';
        updateEmptyState();
        updateCounters();
        markAsSaved(); // Tidak ada data, anggap sudah tersimpan
        return;
    }

    console.log('Memuat ' + existingQuestions.length + ' soal yang sudah ada...');

    const pgContainer = document.getElementById('pg-container');
    const essayContainer = document.getElementById('essay-container');

    if (pgContainer) pgContainer.innerHTML = '';
    if (essayContainer) essayContainer.innerHTML = '';

    pgCounter = 0;
    essayCounter = 0;

    let pgLoaded = 0;
    let essayLoaded = 0;

    existingQuestions.forEach(question => {
        if (question.type === 'pg') {
            pgCounter++;
            addExistingPGQuestion(pgCounter, question);
            pgLoaded++;
        } else if (question.type === 'essay') {
            essayCounter++;
            addExistingEssayQuestion(essayCounter, question);
            essayLoaded++;
        }
    });

    const pgCountElem = document.getElementById('pgCount');
    const essayCountElem = document.getElementById('essayCount');
    const emptyPgElem = document.getElementById('emptyPg');
    const emptyEssayElem = document.getElementById('emptyEssay');

    if (pgCountElem) pgCountElem.innerText = pgCounter;
    if (essayCountElem) essayCountElem.innerText = essayCounter;
    if (emptyPgElem) emptyPgElem.style.display = pgCounter > 0 ? 'none' : 'block';
    if (emptyEssayElem) emptyEssayElem.style.display = essayCounter > 0 ? 'none' : 'block';

    if (pgLoaded > 0 || essayLoaded > 0) {
        console.log(`✅ Berhasil memuat ${pgLoaded} soal PG dan ${essayLoaded} soal essay`);
    } else {
        console.log('⚠️ Tidak ada soal yang berhasil dimuat');
    }

    // ========== TAMBAHKAN DETEKSI PERUBAHAN ==========
    setupAllChangeDetection();

    // Reset flag karena data dimuat dari database
    markAsSaved();
}


// Fungsi untuk menambah soal PG yang sudah ada dari database
function addExistingPGQuestion(uid, question) {
    console.log(`=== addExistingPGQuestion - UID: ${uid} ===`);
    console.log('Question data:', question);

    let options = ['', '', '', ''];

    if (question.options) {
        if (Array.isArray(question.options)) {
            options = question.options;
        } else if (typeof question.options === 'string') {
            try {
                const parsed = JSON.parse(question.options);
                options = Array.isArray(parsed) ? parsed : ['', '', '', ''];
            } catch(e) {
                console.error('Error parsing options:', e);
                options = ['', '', '', ''];
            }
        }
    }

    const correctAnswer = question.correct_answer || 'A';
    const score = question.score || 5;
    const questionText = question.question_text || '';
    const questionId = question.id;

    console.log('Question Image path:', question.image);
    console.log('Question Option Images:', question.option_images);

    // ========== GAMBAR SOAL ==========
    let imageHtml = '';
    if (question.image && question.image !== null && question.image !== '') {
        const imageUrl = question.image.startsWith('/storage/') ? question.image : '/storage/' + question.image;
        console.log('Menampilkan gambar soal dengan URL:', imageUrl);
        imageHtml = `
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal Saat Ini
                </label>
                <div class="mb-2 d-flex align-items-center gap-3 flex-wrap">
                    <img src="${imageUrl}" class="img-fluid rounded" style="max-height: 100px; border: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarSoal(${questionId}, this)">
                        <i class="fas fa-trash me-1"></i> Hapus Gambar
                    </button>
                </div>
                <hr>
                <label class="form-label fw-semibold">Ganti dengan Gambar Baru</label>
                <input type="file" class="form-control question-image" accept="image/*" onchange="previewImage(this, 'preview_soal_${uid}')">
                <div id="preview_soal_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    } else {
        imageHtml = `
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                </label>
                <div class="text-muted small mb-2">
                    <i class="fas fa-info-circle"></i> Belum ada gambar untuk soal ini
                </div>
                <input type="file" class="form-control question-image" accept="image/*" onchange="previewImage(this, 'preview_soal_${uid}')">
                <div id="preview_soal_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    }

    // ========== GAMBAR OPSI (DIPERBAIKI) ==========
    const letters = ['A', 'B', 'C', 'D'];
    let optionsHtml = '';

    for (let i = 0; i < letters.length; i++) {
        const letter = letters[i];
        let optImage = null;

        // Parse option_images dengan benar
        if (question.option_images) {
            if (Array.isArray(question.option_images)) {
                optImage = question.option_images[i];
            } else if (typeof question.option_images === 'string') {
                try {
                    const parsed = JSON.parse(question.option_images);
                    optImage = parsed[i];
                } catch(e) {
                    console.error('Error parsing option_images:', e);
                }
            }
        }

        console.log(`Opsi ${letter} - Gambar:`, optImage);

        let optionImageHtml = '';
        if (optImage && optImage !== null && optImage !== '') {
            const optImageUrl = optImage.startsWith('/storage/') ? optImage : '/storage/' + optImage;
            optionImageHtml = `
                <div class="option-image-wrapper mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <img src="${optImageUrl}" class="option-img" style="max-height: 60px; width: auto; border-radius: 8px; border: 1px solid #ddd; object-fit: contain; background: #f8f9fa; padding: 4px;">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarOpsi(${questionId}, ${i}, this)">
                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                        </button>
                    </div>
                    <hr class="mt-2 mb-2">
                    <label class="form-label fw-semibold" style="font-size: 12px;">Ganti dengan Gambar Baru</label>
                </div>
            `;
        } else {
            optionImageHtml = `
                <div class="text-muted small mb-2">
                    <i class="fas fa-info-circle"></i> Belum ada gambar untuk opsi ${letter}
                </div>
            `;
        }

        optionsHtml += `
            <div class="option-group" id="option-container-${letter.toLowerCase()}-${uid}">
                <div class="option-group-header">
                    <div class="option-letter-badge">${letter}</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi ${letter}" value="${escapeHtml(options[i] || '')}">
                ${optionImageHtml}
                <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                       onchange="previewOptionImage(this, 'preview_${letter.toLowerCase()}_${uid}')">
                <div id="preview_${letter.toLowerCase()}_${uid}" class="option-preview" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    }

    const newQuestion = `
        <div class="question-card" data-id="${uid}" data-question-id="${questionId}" data-type="pg" data-existing="true">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-bold mb-0">
                    <span class="question-counter">Soal PG ${pgCounter}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this, ${questionId})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            ${imageHtml}

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-question-circle me-2 text-primary"></i>Soal
                </label>
                <textarea class="form-control question-text" rows="3" placeholder="Masukkan soal...">${escapeHtml(questionText)}</textarea>
            </div>

            <label class="form-label fw-semibold mb-2">
                <i class="fas fa-list-ul me-2 text-primary"></i>Opsi Jawaban
            </label>
            <div class="option-info">
                <span><i class="fas fa-info-circle"></i> Isi teks atau upload gambar (bisa keduanya)</span>
            </div>

            ${optionsHtml}

            <div class="row mt-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-key me-2 text-primary"></i>Kunci Jawaban
                    </label>
                    <select class="form-select correct-answer">
                        <option value="A" ${correctAnswer === 'A' ? 'selected' : ''}>A</option>
                        <option value="B" ${correctAnswer === 'B' ? 'selected' : ''}>B</option>
                        <option value="C" ${correctAnswer === 'C' ? 'selected' : ''}>C</option>
                        <option value="D" ${correctAnswer === 'D' ? 'selected' : ''}>D</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-star me-2 text-primary"></i>Nilai Soal
                    </label>
                    <input type="number" class="form-control score-input" value="${score}" min="1" max="100">
                </div>
            </div>
            <div class="row mt-3">
                <hr style="border: 3px solid black">
            </div>
        </div>
    `;

    const pgContainer = document.getElementById('pg-container');
    if (pgContainer) {
        pgContainer.insertAdjacentHTML('beforeend', newQuestion);
        console.log(`✅ Soal PG ${uid} berhasil ditambahkan ke DOM`);
    } else {
        console.error('❌ pg-container tidak ditemukan!');
    }
}


// Fungsi untuk menambah soal Essay yang sudah ada dari database
function addExistingEssayQuestion(uid, question) {
    console.log(`=== addExistingEssayQuestion - UID: ${uid} ===`);
    console.log('Question data:', question);

    const questionText = question.question_text || '';
    const essayKeyword = question.correct_answer || '';
    const score = question.score || 10;
    const questionId = question.id;

    console.log('Question Image path:', question.image);

    // ========== GAMBAR SOAL ==========
    let imageHtml = '';
    if (question.image && question.image !== null && question.image !== '') {
        const imageUrl = question.image.startsWith('/storage/') ? question.image : '/storage/' + question.image;
        console.log('Menampilkan gambar soal dengan URL:', imageUrl);
        imageHtml = `
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal Saat Ini
                </label>
                <div class="mb-2 d-flex align-items-center gap-3 flex-wrap">
                    <img src="${imageUrl}" class="img-fluid rounded" style="max-height: 100px; border: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarSoal(${questionId}, this)">
                        <i class="fas fa-trash me-1"></i> Hapus Gambar
                    </button>
                </div>
                <hr>
                <label class="form-label fw-semibold">Ganti dengan Gambar Baru</label>
                <input type="file" class="form-control question-image" accept="image/*" onchange="previewImage(this, 'preview_soal_essay_${uid}')">
                <div id="preview_soal_essay_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    } else {
        imageHtml = `
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                </label>
                <div class="text-muted small mb-2">
                    <i class="fas fa-info-circle"></i> Belum ada gambar untuk soal ini
                </div>
                <input type="file" class="form-control question-image" accept="image/*" onchange="previewImage(this, 'preview_soal_essay_${uid}')">
                <div id="preview_soal_essay_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>
        `;
    }

    const newEssay = `
        <div class="question-card" data-id="${uid}" data-question-id="${questionId}" data-type="essay" data-existing="true">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-bold mb-0">
                    <span class="question-counter">Soal Essay ${essayCounter}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this, ${questionId})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            ${imageHtml}

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-pen-alt me-2 text-primary"></i>Soal Essay
                </label>
                <textarea class="form-control question-text" rows="4" placeholder="Masukkan soal essay...">${escapeHtml(questionText)}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-key me-2 text-primary"></i>Kunci Jawaban (untuk AI Similarity)
                </label>
                <textarea class="form-control correct-answer" rows="3" placeholder="Masukkan kunci jawaban...">${escapeHtml(essayKeyword)}</textarea>
                <small class="text-muted">Jawaban peserta akan dibandingkan dengan kunci jawaban ini menggunakan AI text similarity</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-star me-2 text-primary"></i>Nilai Soal
                </label>
                <input type="number" class="form-control score-input" value="${score}" min="1" max="100">
            </div>
            <div class="row mt-3">
                <hr style="border: 3px solid black">
            </div>
        </div>
    `;

    const essayContainer = document.getElementById('essay-container');
    if (essayContainer) {
        essayContainer.insertAdjacentHTML('beforeend', newEssay);
        console.log(`✅ Soal Essay ${uid} berhasil ditambahkan ke DOM`);
    } else {
        console.error('❌ essay-container tidak ditemukan!');
    }
}



// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INITIALIZATION START ===');

    // Load soal yang sudah ada dari database
    loadExistingQuestions();

    updateEmptyState();
    updateCounters();

    console.log('=== INITIALIZATION COMPLETE ===');
});



</script>
@endsection