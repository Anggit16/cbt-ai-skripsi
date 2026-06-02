@extends('layouts.app')

@section('title', 'Edit Soal Ujian - Guru')

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

/* Container untuk preview gambar sementara */
.option-image-preview-container {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
    margin-top: 8px;
}

/* Tombol hapus sementara */
.temp-delete {
    margin-left: 10px;
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

/* Pesan belum ada gambar */
.text-muted.small {
    font-style: italic;
    color: #6c757d;
    font-size: 12px;
    margin-bottom: 8px;
}

/* Tombol hapus gambar lebih kecil */
.btn-sm.btn-outline-danger {
    padding: 4px 10px;
    font-size: 12px;
}

/* Container gambar dengan flex wrap */
.d-flex.flex-wrap {
    gap: 10px;
    align-items: center;
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
                            <i class="fas fa-edit me-2" style="color: #667eea;"></i>Edit Soal Ujian
                        </h3>
                        <p class="text-muted mb-0">
                            <i class="fas fa-book-open me-1"></i>{{ $exam->judul }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('guru.kelola-peserta', $exam->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>

                <!-- Tombol Tambah Soal -->
                <div class="d-flex gap-2 mb-4">
                    <button type="button" class="btn btn-success" onclick="tambahPilihanGanda()">
                        <i class="fas fa-plus me-2"></i>Tambah PG
                    </button>
                    <button type="button" class="btn btn-warning text-white" onclick="tambahEssay()">
                        <i class="fas fa-plus me-2"></i>Tambah Essay
                    </button>
                </div>

                <div id="alertMessage"></div>

                <!-- Tabs -->
                <ul class="nav nav-tabs-custom" id="questionTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" onclick="showTab('pg')">
                            <i class="fas fa-list-ul me-2"></i>Pilihan Ganda
                            <span class="badge bg-primary rounded-pill ms-2" id="pgCount">{{ $exam->questions->where('type', 'pg')->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showTab('essay')">
                            <i class="fas fa-file-alt me-2"></i>Essay
                            <span class="badge bg-primary rounded-pill ms-2" id="essayCount">{{ $exam->questions->where('type', 'essay')->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Pilihan Ganda Section -->
                    <div id="pg-section" style="display: block;">
                        <div id="pg-container">
                            @foreach($exam->questions->where('type', 'pg') as $index => $question)
                            @php
                                $uid = $index + 1;
                                $options = is_array($question->options) ? $question->options : json_decode($question->options, true);
                            @endphp
                            <div class="question-card" data-id="{{ $uid }}" data-type="pg" data-question-id="{{ $question->id }}">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="fw-bold mb-0">
                                        <span class="question-counter">Soal PG {{ $uid }}</span>
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this)">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>

                                <!-- Gambar Soal -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                                    </label>
                                    @if($question->image)
                                    <div class="mb-2 d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $question->image) }}" class="img-fluid rounded" style="max-height: 100px; border: 1px solid #ddd;">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarSoal({{ $question->id }}, this)">
                                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                                        </button>
                                    </div>
                                    @else
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-info-circle"></i> Belum ada gambar untuk soal ini
                                    </div>
                                    @endif
                                    <input type="file" class="form-control question-image" accept="image/*"
                                        onchange="previewImage(this, 'preview_soal_${uid}')">
                                    <div id="preview_soal_${uid}" class="mt-2" style="display: none;">
                                        <img src="" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>
                                </div>

                                <!-- Teks Soal -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-question-circle me-2 text-primary"></i>Soal
                                    </label>
                                    <textarea class="form-control question-text" rows="3" placeholder="Masukkan soal...">{{ $question->question_text }}</textarea>
                                </div>

                                <!-- Opsi Jawaban -->
                                <label class="form-label fw-semibold mb-2">
                                    <i class="fas fa-list-ul me-2 text-primary"></i>Opsi Jawaban
                                </label>
                                <div class="option-info">
                                    <span><i class="fas fa-info-circle"></i> Isi teks atau upload gambar (bisa keduanya)</span>
                                </div>

                                @php
                                    // Decode option_images dari JSON ke array
                                    $optionImages = is_array($question->option_images)
                                        ? $question->option_images
                                        : (json_decode($question->option_images, true) ?: []);
                                @endphp

                                <!-- Opsi A -->
                                <div class="option-group">
                                    <div class="option-group-header">
                                        <div class="option-letter-badge">A</div>
                                    </div>
                                    <input type="text" class="form-control option-text-input mb-2"
                                        placeholder="Teks opsi A" value="{{ $options[0] ?? '' }}">

                                    @if(isset($optionImages[0]) && $optionImages[0])
                                    <div class="mb-2 d-flex align-items-center gap-3 flex-wrap">
                                        <img src="{{ asset('storage/' . $optionImages[0]) }}"
                                            style="max-height: 60px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="hapusGambarOpsi({{ $question->id }}, 0, this)">
                                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                                        </button>
                                    </div>
                                    <hr>
                                    @else
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-info-circle"></i> Belum ada gambar untuk opsi A
                                    </div>
                                    @endif

                                    <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                                        onchange="previewOptionImage(this, 'preview_a_{{ $uid }}')">
                                    <div id="preview_a_{{ $uid }}" class="option-preview" style="display: none;">
                                        <img src="" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>
                                </div>

                                <!-- Opsi B -->
                                <div class="option-group">
                                    <div class="option-group-header">
                                        <div class="option-letter-badge">B</div>
                                    </div>
                                    <input type="text" class="form-control option-text-input mb-2"
                                        placeholder="Teks opsi B" value="{{ $options[1] ?? '' }}">

                                    @if(isset($optionImages[1]) && $optionImages[1])
                                    <div class="mb-2 d-flex align-items-center gap-3 flex-wrap">
                                        <img src="{{ asset('storage/' . $optionImages[1]) }}"
                                            style="max-height: 60px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="hapusGambarOpsi({{ $question->id }}, 1, this)">
                                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                                        </button>
                                    </div>
                                    <hr>
                                    @else
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-info-circle"></i> Belum ada gambar untuk opsi B
                                    </div>
                                    @endif

                                    <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                                        onchange="previewOptionImage(this, 'preview_b_{{ $uid }}')">
                                    <div id="preview_b_{{ $uid }}" class="option-preview" style="display: none;">
                                        <img src="" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>
                                </div>

                                <!-- Opsi C -->
                                <div class="option-group">
                                    <div class="option-group-header">
                                        <div class="option-letter-badge">C</div>
                                    </div>
                                    <input type="text" class="form-control option-text-input mb-2"
                                        placeholder="Teks opsi C" value="{{ $options[2] ?? '' }}">

                                    @if(isset($optionImages[2]) && $optionImages[2])
                                    <div class="mb-2 d-flex align-items-center gap-3 flex-wrap">
                                        <img src="{{ asset('storage/' . $optionImages[2]) }}"
                                            style="max-height: 60px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="hapusGambarOpsi({{ $question->id }}, 2, this)">
                                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                                        </button>
                                    </div>
                                    <hr>
                                    @else
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-info-circle"></i> Belum ada gambar untuk opsi C
                                    </div>
                                    @endif

                                    <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                                        onchange="previewOptionImage(this, 'preview_c_{{ $uid }}')">
                                    <div id="preview_c_{{ $uid }}" class="option-preview" style="display: none;">
                                        <img src="" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>
                                </div>

                                <!-- Opsi D -->
                                <div class="option-group">
                                    <div class="option-group-header">
                                        <div class="option-letter-badge">D</div>
                                    </div>
                                    <input type="text" class="form-control option-text-input mb-2"
                                        placeholder="Teks opsi D" value="{{ $options[3] ?? '' }}">

                                    @if(isset($optionImages[3]) && $optionImages[3])
                                    <div class="mb-2 d-flex align-items-center gap-3 flex-wrap">
                                        <img src="{{ asset('storage/' . $optionImages[3]) }}"
                                            style="max-height: 60px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="hapusGambarOpsi({{ $question->id }}, 3, this)">
                                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                                        </button>
                                    </div>
                                    <hr>
                                    @else
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-info-circle"></i> Belum ada gambar untuk opsi D
                                    </div>
                                    @endif

                                    <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                                        onchange="previewOptionImage(this, 'preview_d_{{ $uid }}')">
                                    <div id="preview_d_{{ $uid }}" class="option-preview" style="display: none;">
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
                                            <option value="A" {{ $question->correct_answer == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ $question->correct_answer == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ $question->correct_answer == 'C' ? 'selected' : '' }}>C</option>
                                            <option value="D" {{ $question->correct_answer == 'D' ? 'selected' : '' }}>D</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-star me-2 text-primary"></i>Nilai Soal
                                        </label>
                                        <input type="number" class="form-control score-input" placeholder="Nilai" value="{{ $question->score }}" min="1" max="100">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <hr style="border: 3px solid black">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div id="emptyPg" class="text-center py-5 text-muted" style="{{ $exam->questions->where('type', 'pg')->count() > 0 ? 'display: none;' : '' }}">
                            <i class="fas fa-plus-circle fa-3x mb-3 opacity-50"></i>
                            <p>Belum ada soal pilihan ganda</p>
                            <button type="button" class="btn btn-sm btn-primary" onclick="tambahPilihanGanda()">
                                <i class="fas fa-plus me-1"></i>Tambah Soal PG
                            </button>
                        </div>
                    </div>

                    <!-- Essay Section -->
                    <div id="essay-section" style="display: none;">
                        <div id="essay-container">
                            @foreach($exam->questions->where('type', 'essay') as $index => $question)
                            @php $uid = $index + 1; @endphp
                            <div class="question-card" data-id="{{ $uid }}" data-type="essay" data-question-id="{{ $question->id }}">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="fw-bold mb-0">
                                        <span class="question-counter">Soal Essay {{ $uid }}</span>
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this)">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>

                                <!-- Gambar Soal -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                                    </label>
                                    @if($question->image)
                                    <div class="mb-2 d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $question->image) }}" class="img-fluid rounded" style="max-height: 100px; border: 1px solid #ddd;">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusGambarSoal({{ $question->id }}, this)">
                                            <i class="fas fa-trash me-1"></i> Hapus Gambar
                                        </button>
                                    </div>
                                    @else
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-info-circle"></i> Belum ada gambar untuk soal ini
                                    </div>
                                    @endif
                                    <!-- Di bagian essay, gunakan ID yang berbeda -->
                                    <input type="file" class="form-control question-image" accept="image/*"
                                        onchange="previewImage(this, 'preview_soal_essay_{{ $uid }}')">
                                    <div id="preview_soal_essay_{{ $uid }}" class="mt-2" style="display: none;">
                                        <img src="" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-pen-alt me-2 text-primary"></i>Soal Essay
                                    </label>
                                    <textarea class="form-control question-text" rows="4" placeholder="Masukkan soal essay...">{{ $question->question_text }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-key me-2 text-primary"></i>Kunci Jawaban (untuk AI Similarity)
                                    </label>
                                    <textarea class="form-control correct-answer" rows="3" placeholder="Masukkan kunci jawaban...">{{ $question->correct_answer }}</textarea>
                                    <small class="text-muted">Jawaban peserta akan dibandingkan dengan kunci jawaban ini menggunakan AI text similarity</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-star me-2 text-primary"></i>Nilai Soal
                                    </label>
                                    <input type="number" class="form-control score-input" placeholder="Nilai" value="{{ $question->score }}" min="1" max="100">
                                </div>
                                <div class="row mt-3">
                                    <hr style="border: 3px solid black">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div id="emptyEssay" class="text-center py-5 text-muted" style="{{ $exam->questions->where('type', 'essay')->count() > 0 ? 'display: none;' : '' }}">
                            <i class="fas fa-plus-circle fa-3x mb-3 opacity-50"></i>
                            <p>Belum ada soal essay</p>
                            <button type="button" class="btn btn-sm btn-primary" onclick="tambahEssay()">
                                <i class="fas fa-plus me-1"></i>Tambah Soal Essay
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3">
                    <a href="{{ route('guru.kelola-peserta', $exam->id) }}" class="btn btn-secondary px-4">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <button type="button" id="simpanSoalBtn" class="btn btn-primary px-4" onclick="simpanSemuaSoal()">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variabel global
let pgCounter = {{ $exam->questions->where('type', 'pg')->count() }};
let essayCounter = {{ $exam->questions->where('type', 'essay')->count() }};
let isDataSaved = true;  // TAMBAHKAN - data sudah tersimpan saat load

// ========== PERINGATAN SEBELUM RELOAD ATAU TUTUP HALAMAN ==========
window.addEventListener('beforeunload', function(e) {
    if (!isDataSaved) {
        e.preventDefault();
        e.returnValue = '⚠️ PERINGATAN! ⚠️\n\nAda perubahan yang belum disimpan!\n\nKlik "Simpan Perubahan" terlebih dahulu sebelum meninggalkan halaman.\n\nPerubahan Anda akan hilang jika tidak disimpan!';
        return e.returnValue;
    }
});

// Tampilkan peringatan visual saat ada perubahan
function showSaveWarning() {
    const warning = document.getElementById('saveWarning');
    if (warning) {
        warning.style.display = 'block';
        setTimeout(() => {
            if (!isDataSaved) {
                warning.style.display = 'block';
            }
        }, 3000);
    }
}

// Sembunyikan peringatan saat sudah disimpan
function hideSaveWarning() {
    const warning = document.getElementById('saveWarning');
    if (warning) warning.style.display = 'none';
}


// Fungsi untuk menandai ada perubahan
function markAsUnsaved() {
    isDataSaved = false;
    showSaveWarning();
}

// Fungsi untuk menandai sudah disimpan
function markAsSaved() {
    isDataSaved = true;
    hideSaveWarning();
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

// Preview gambar soal - TAMPILKAN TOMBOL HAPUS SETELAH UPLOAD
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Tampilkan preview sementara
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.style.display = 'block';
                const img = preview.querySelector('img');
                if (img) img.src = e.target.result;
            }

            // ========== TAMBAHKAN: Tampilkan tombol hapus ==========
            const container = input.closest('.mb-3');
            if (container) {
                // Cek apakah sudah ada tombol hapus
                let existingDeleteBtn = container.querySelector('.btn-outline-danger');
                if (!existingDeleteBtn) {
                    // Buat tombol hapus
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'btn btn-sm btn-outline-danger mt-2';
                    deleteBtn.innerHTML = '<i class="fas fa-trash me-1"></i> Hapus Gambar (Baru)';
                    deleteBtn.onclick = function() {
                        if (confirm('Hapus gambar yang baru diupload?')) {
                            input.value = '';
                            preview.style.display = 'none';
                            if (preview.querySelector('img')) preview.querySelector('img').src = '';
                            this.remove();
                            // Tampilkan pesan belum ada gambar
                            const noImageMsg = container.querySelector('.text-muted.small');
                            if (noImageMsg) noImageMsg.style.display = 'block';
                        }
                    };
                    container.appendChild(deleteBtn);
                }
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview gambar opsi - TAMPILKAN TOMBOL HAPUS SETELAH UPLOAD
function previewOptionImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Tampilkan preview sementara
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.style.display = 'block';
                const img = preview.querySelector('img');
                if (img) img.src = e.target.result;
            }

            // ========== TAMBAHKAN: Tampilkan tombol hapus ==========
            const optionGroup = input.closest('.option-group');
            if (optionGroup) {
                // Sembunyikan pesan "Belum ada gambar"
                const noImageMsg = optionGroup.querySelector('.text-muted.small');
                if (noImageMsg) noImageMsg.style.display = 'none';

                // Cek apakah sudah ada tombol hapus
                let existingDeleteBtn = optionGroup.querySelector('.btn-outline-danger.temp-delete');
                if (!existingDeleteBtn) {
                    // Buat container untuk gambar preview
                    let imageContainer = optionGroup.querySelector('.option-image-preview-container');
                    if (!imageContainer) {
                        imageContainer = document.createElement('div');
                        imageContainer.className = 'option-image-preview-container mb-2';
                        const fileInput = optionGroup.querySelector('.option-image-input');
                        if (fileInput) {
                            fileInput.parentNode.insertBefore(imageContainer, fileInput);
                        }
                    }

                    // Tampilkan gambar preview dan tombol hapus
                    imageContainer.innerHTML = `
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <img src="${e.target.result}" style="max-height: 60px; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
                            <button type="button" class="btn btn-sm btn-outline-danger temp-delete" onclick="hapusGambarPreviewOpsiTemp(this, '${previewId}')">
                                <i class="fas fa-trash me-1"></i> Hapus Gambar
                            </button>
                        </div>
                        <hr>
                    `;
                }
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Fungsi hapus preview gambar opsi sementara
function hapusGambarPreviewOpsiTemp(btn, previewId) {
    if (!confirm('Hapus gambar yang baru diupload?')) return;

    const container = btn.closest('.option-group');
    const imageContainer = container.querySelector('.option-image-preview-container');
    if (imageContainer) imageContainer.remove();

    const fileInput = container.querySelector('.option-image-input');
    if (fileInput) fileInput.value = '';

    const preview = document.getElementById(previewId);
    if (preview) {
        preview.style.display = 'none';
        const img = preview.querySelector('img');
        if (img) img.src = '';
    }

    // Tampilkan pesan belum ada gambar
    const noImageMsg = container.querySelector('.text-muted.small');
    if (noImageMsg) noImageMsg.style.display = 'block';
}

// Fungsi tambah soal pilihan ganda (BARU)
function tambahPilihanGanda() {
    pgCounter++;
    const uid = pgCounter;
    const newQuestionId = 'new_' + Date.now() + '_' + Math.random();

    const newQuestion = `
        <div class="question-card" data-id="${uid}" data-type="pg" data-question-id="${newQuestionId}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-bold mb-0">
                    <span class="question-counter">Soal PG ${uid}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this)">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            <!-- Upload Gambar Soal -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                </label>
                <input type="file" class="form-control question-image" accept="image/*"
                       onchange="previewImage(this, 'preview_soal_${uid}')">
                <div id="preview_soal_${uid}" class="mt-2" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-question-circle me-2 text-primary"></i>Soal
                </label>
                <textarea class="form-control question-text" rows="3" placeholder="Masukkan soal..."></textarea>
            </div>

            <label class="form-label fw-semibold mb-2">
                <i class="fas fa-list-ul me-2 text-primary"></i>Opsi Jawaban
            </label>
            <div class="option-info">
                <span><i class="fas fa-info-circle"></i> Isi teks atau upload gambar (bisa keduanya)</span>
            </div>

            <!-- Opsi A -->
            <div class="option-group">
                <div class="option-group-header">
                    <div class="option-letter-badge">A</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi A (kosongkan jika hanya gambar)">
                <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                       onchange="previewOptionImage(this, 'preview_a_${uid}')">
                <div id="preview_a_${uid}" class="option-preview" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <!-- Opsi B -->
            <div class="option-group">
                <div class="option-group-header">
                    <div class="option-letter-badge">B</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi B (kosongkan jika hanya gambar)">
                <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                       onchange="previewOptionImage(this, 'preview_b_${uid}')">
                <div id="preview_b_${uid}" class="option-preview" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <!-- Opsi C -->
            <div class="option-group">
                <div class="option-group-header">
                    <div class="option-letter-badge">C</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi C (kosongkan jika hanya gambar)">
                <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                       onchange="previewOptionImage(this, 'preview_c_${uid}')">
                <div id="preview_c_${uid}" class="option-preview" style="display: none;">
                    <img src="" class="img-fluid rounded" style="max-height: 150px;">
                </div>
            </div>

            <!-- Opsi D -->
            <div class="option-group">
                <div class="option-group-header">
                    <div class="option-letter-badge">D</div>
                </div>
                <input type="text" class="form-control option-text-input mb-2" placeholder="Teks opsi D (kosongkan jika hanya gambar)">
                <input type="file" class="form-control option-image-input mb-2" accept="image/*"
                       onchange="previewOptionImage(this, 'preview_d_${uid}')">
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
}

// Fungsi tambah soal essay (BARU)
function tambahEssay() {
    essayCounter++;
    const uid = essayCounter;
    const newQuestionId = 'new_' + Date.now() + '_' + Math.random();

    const newEssay = `
        <div class="question-card" data-id="${uid}" data-type="essay" data-question-id="${newQuestionId}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-bold mb-0">
                    <span class="question-counter">Soal Essay ${uid}</span>
                </h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="hapusSoal(this)">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            <!-- Upload Gambar Soal Essay -->
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-image me-2 text-primary"></i>Gambar Soal (Opsional)
                </label>
                <input type="file" class="form-control question-image" accept="image/*"
                       onchange="previewImage(this, 'preview_soal_essay_${uid}')">
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
}

// Fungsi hapus soal - VERSI FINAL (Hapus dari Database dan Tampilan)
function hapusSoal(btn) {
    if(!confirm('⚠️ Hapus soal ini?\n\nSoal yang dihapus tidak dapat dikembalikan!')) {
        return;
    }

    const card = btn.closest('.question-card');
    const type = card.getAttribute('data-type');
    const questionId = card.getAttribute('data-question-id');

    console.log('Menghapus soal:', { questionId, type });

    // Tampilkan loading
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    // Hapus dari database via AJAX
    fetch('/guru/hapus-soal/' + questionId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response:', data);

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
            // Hapus elemen gambar dari DOM
            const imageContainer = btn.closest('.mb-2');
            if (imageContainer) {
                imageContainer.remove();
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
            // Hapus elemen gambar dari DOM
            const imageContainer = btn.closest('.mb-2');
            if (imageContainer) {
                imageContainer.remove();
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
    pgCounter = questions.length;
    questions.forEach((q, index) => {
        q.setAttribute('data-id', index + 1);
        const counter = q.querySelector('.question-counter');
        if (counter) counter.innerText = `Soal PG ${index + 1}`;
    });
    document.getElementById('pgCount').innerText = pgCounter;
}

// Renumber soal Essay
function renumberEssay() {
    const questions = document.querySelectorAll('#essay-container .question-card');
    essayCounter = questions.length;
    questions.forEach((q, index) => {
        q.setAttribute('data-id', index + 1);
        const counter = q.querySelector('.question-counter');
        if (counter) counter.innerText = `Soal Essay ${index + 1}`;
    });
    document.getElementById('essayCount').innerText = essayCounter;
}

// Update empty state
function updateEmptyState() {
    const pgContainer = document.getElementById('pg-container');
    const emptyPg = document.getElementById('emptyPg');
    const essayContainer = document.getElementById('essay-container');
    const emptyEssay = document.getElementById('emptyEssay');

    if (pgContainer.children.length === 0) {
        emptyPg.style.display = 'block';
        pgCounter = 0;
        document.getElementById('pgCount').innerText = '0';
    } else {
        emptyPg.style.display = 'none';
    }

    if (essayContainer.children.length === 0) {
        emptyEssay.style.display = 'block';
        essayCounter = 0;
        document.getElementById('essayCount').innerText = '0';
    } else {
        emptyEssay.style.display = 'none';
    }
}

// Simpan semua soal
function simpanSemuaSoal() {
    const formData = new FormData();
    let questionIndex = 0;

    // KUMPULKAN SOAL PG (termasuk yang baru)
    const pgQuestions = document.querySelectorAll('#pg-container .question-card');
    for (let q of pgQuestions) {
        const questionId = q.getAttribute('data-question-id');
        const questionText = q.querySelector('.question-text').value;

        if (!questionText) {
            showAlert('danger', 'Semua soal harus diisi!');
            return;
        }

        const options = [];
        const optionTextInputs = q.querySelectorAll('.option-text-input');
        for (let opt of optionTextInputs) {
            options.push(opt.value);
        }

        const correctAnswer = q.querySelector('.correct-answer').value;
        const score = q.querySelector('.score-input').value;

        // Upload gambar soal jika ada
        const imageInput = q.querySelector('.question-image');
        if (imageInput && imageInput.files[0]) {
            formData.append(`questions[${questionIndex}][image]`, imageInput.files[0]);
        }

        // Upload gambar opsi jika ada
        const optionImageInputs = q.querySelectorAll('.option-image-input');
        for (let i = 0; i < optionImageInputs.length; i++) {
            if (optionImageInputs[i].files[0]) {
                formData.append(`questions[${questionIndex}][option_images][${i}]`, optionImageInputs[i].files[0]);
            }
        }

        formData.append(`questions[${questionIndex}][id]`, questionId);
        formData.append(`questions[${questionIndex}][type]`, 'pg');
        formData.append(`questions[${questionIndex}][question_text]`, questionText);
        formData.append(`questions[${questionIndex}][options]`, JSON.stringify(options));
        formData.append(`questions[${questionIndex}][correct_answer]`, correctAnswer);
        formData.append(`questions[${questionIndex}][score]`, score);

        questionIndex++;
    }

    // KUMPULKAN SOAL ESSAY (termasuk yang baru)
    const essayQuestions = document.querySelectorAll('#essay-container .question-card');
    for (let q of essayQuestions) {
        const questionId = q.getAttribute('data-question-id');
        const questionText = q.querySelector('.question-text').value;
        const correctAnswer = q.querySelector('.correct-answer').value;
        const score = q.querySelector('.score-input').value;

        if (!questionText) {
            showAlert('danger', 'Semua soal harus diisi!');
            return;
        }

        if (!correctAnswer) {
            showAlert('danger', 'Kunci jawaban untuk soal essay harus diisi!');
            return;
        }

        // Upload gambar soal essay jika ada
        const imageInput = q.querySelector('.question-image');
        if (imageInput && imageInput.files[0]) {
            formData.append(`questions[${questionIndex}][image]`, imageInput.files[0]);
        }

        formData.append(`questions[${questionIndex}][id]`, questionId);
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

    fetch('{{ route("guru.update-soal", $exam->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            markAsSaved();
            // Tampilkan alert di halaman edit
            showAlert('success', data.message);

            // ========== SIMPAN PESAN KE SESSIONSTORAGE ==========
            sessionStorage.setItem('edit_soal_message', JSON.stringify({
                type: 'success',
                message: data.message
            }));

            // Redirect ke halaman kelola peserta
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
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

    document.getElementById('alertMessage').innerHTML = alertHtml;

    setTimeout(() => {
        const alert = document.querySelector('#alertMessage .alert');
        if (alert) alert.remove();
    }, 5000);
}

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    updateEmptyState();

    // ========== DETEKSI PERUBAHAN PADA FORM ==========
    const formInputs = document.querySelectorAll('.question-text, .option-text-input, .correct-answer, .score-input, .question-image, .option-image-input');
    formInputs.forEach(input => {
        input.addEventListener('input', markAsUnsaved);
        input.addEventListener('change', markAsUnsaved);
    });

    // Reset flag setelah simpan berhasil
    const originalSimpan = simpanSemuaSoal;
    window.simpanSemuaSoal = function() {
        originalSimpan();
        markAsSaved();
    };
});
</script>
@endsection