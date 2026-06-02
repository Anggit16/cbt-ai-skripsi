@extends('layouts.app')

@section('title', 'Buat Ujian - Guru')

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h3 class="fw-bold mb-1" style="color: #2d3748;">
                            <i class="fas fa-plus-circle me-2" style="color: #667eea;"></i>Buat Ujian Baru
                        </h3>
                        <p class="text-muted mb-0">Lengkapi informasi ujian yang akan dibuat</p>
                    </div>
                    <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-danger">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>

                <div id="alertMessage"></div>

                <form id="buatUjianForm" class="no-double-submit">
                    @csrf
                    <div class="mb-3">
                        <label for="judul" class="form-label">
                            <i class="fas fa-heading me-2 text-primary"></i>Judul Ujian <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="judul" name="judul" placeholder="Contoh: Ujian Matematika Kelas XII" required>
                        <div class="invalid-feedback" id="judulError"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="durasi" class="form-label">
                                <i class="fas fa-clock me-2 text-primary"></i>Durasi Ujian (menit) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="durasi" name="durasi" placeholder="90" value="90" min="1" required>
                            <div class="invalid-feedback" id="durasiError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_peserta" class="form-label">
                                <i class="fas fa-users me-2 text-primary"></i>Jumlah Peserta <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="jumlah_peserta" name="jumlah_peserta" placeholder="30" value="30" min="1" required>
                            <div class="invalid-feedback" id="jumlah_pesertaError"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">
                            <i class="fas fa-align-left me-2 text-primary"></i>Deskripsi Ujian
                        </label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang ujian..."></textarea>
                        <div class="invalid-feedback" id="deskripsiError"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-cog me-2 text-primary"></i>Pengaturan Ujian
                        </label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="acak_soal" name="acak_soal">
                                    <label class="form-check-label" for="acak_soal">
                                        <i class="fas fa-random me-1"></i>Acak Soal
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="tampilkan_hasil" name="tampilkan_hasil" checked>
                                    <label class="form-check-label" for="tampilkan_hasil">
                                        <i class="fas fa-chart-line me-1"></i>Tampilkan Hasil Langsung
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-secondary px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Simpan & Buat Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#buatUjianForm').on('submit', function(e) {
        e.preventDefault();

        // Reset error messages
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#alertMessage').html('');

        // Get form data
        const formData = {
            judul: $('#judul').val(),
            durasi: $('#durasi').val(),
            jumlah_peserta: $('#jumlah_peserta').val(),
            deskripsi: $('#deskripsi').val(),
            acak_soal: $('#acak_soal').is(':checked'),
            tampilkan_hasil: $('#tampilkan_hasil').is(':checked'),
            _token: $('input[name="_token"]').val()
        };

        // Validate form
        if (!formData.judul) {
            showFieldError('judul', 'Judul ujian harus diisi');
            return;
        }

        if (!formData.durasi) {
            showFieldError('durasi', 'Durasi ujian harus diisi');
            return;
        }

        if (formData.durasi < 1) {
            showFieldError('durasi', 'Durasi minimal 1 menit');
            return;
        }

        if (!formData.jumlah_peserta) {
            showFieldError('jumlah_peserta', 'Jumlah peserta harus diisi');
            return;
        }

        if (formData.jumlah_peserta < 1) {
            showFieldError('jumlah_peserta', 'Jumlah peserta minimal 1');
            return;
        }

        // Disable submit button
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...').prop('disabled', true);

        // Send AJAX request
        $.ajax({
            url: '{{ route("guru.buat-ujian") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    submitBtn.html(originalText).prop('disabled', false);
                    showAlert('danger', response.message);
                }
            },
            error: function(xhr) {
                submitBtn.html(originalText).prop('disabled', false);

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        showFieldError(field, errors[field][0]);
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    showAlert('danger', xhr.responseJSON.message);
                } else {
                    showAlert('danger', 'Terjadi kesalahan. Silahkan coba lagi.');
                }
            }
        });
    });

    function showFieldError(field, message) {
        const fieldMap = {
            'judul': 'judulError',
            'durasi': 'durasiError',
            'jumlah_peserta': 'jumlah_pesertaError',
            'deskripsi': 'deskripsiError'
        };

        const errorId = fieldMap[field];
        if (errorId) {
            $(`#${field}`).addClass('is-invalid');
            $(`#${errorId}`).text(message);
        }
    }

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

        $('#alertMessage').html(alertHtml);

        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endpush