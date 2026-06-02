@extends('layouts.app')

@section('title', 'Manajemen Guru - Super Admin')

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">
                        <i class="fas fa-chalkboard-user me-2 text-primary"></i>Manajemen Guru
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('super_admin.dashboard') }}" class="btn back-btn">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                        <a href="{{ route('super_admin.guru.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Guru
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Mata Pelajaran</th>
                                <th>Jumlah Ujian</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guru as $index => $g)
                            <tr>
                                <td>{{ $guru->firstItem() + $index }}</td>
                                <td class="fw-semibold">{{ $g->name }}</td>
                                <td>{{ $g->email }}</td>
                                <td>{{ $g->mata_pelajaran ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">{{ $g->exams_count ?? $g->exams->count() }}</span>
                                </td>
                                <td>{{ $g->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('super_admin.guru.edit', $g->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-info" onclick="resetPassword({{ $g->id }})">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusGuru({{ $g->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Belum ada data guru</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $guru->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetPassword(id) {
    if(confirm('Reset password guru ini? Password baru: password123')) {
        fetch('/super-admin/guru/' + id + '/reset-password', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + (data.message || 'Gagal reset password'));
            }
        });
    }
}

function hapusGuru(id) {
    if(confirm('Apakah Anda yakin ingin menghapus guru ini? Semua ujian dan data terkait akan ikut terhapus!')) {

        // Ambil tombol yang diklik
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;

        // Ubah tombol menjadi loading
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        fetch('/super-admin/guru/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Hapus baris tabel
                const row = btn.closest('tr');
                if(row) {
                    row.remove();
                }

                // Update nomor urut
                updateRowNumbers();

                // Cek apakah tabel kosong
                checkEmptyTable();

                // Tampilkan alert sukses
                showAlert('success', data.message);

            } else {
                showAlert('danger', data.message || 'Gagal menghapus guru');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(error => {
            showAlert('danger', 'Terjadi kesalahan saat menghapus data');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
}

// Update nomor urut setelah hapus
function updateRowNumbers() {
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach((row, index) => {
        const noCell = row.querySelector('td:first-child');
        if(noCell) {
            noCell.textContent = index + 1;
        }
    });
}

// Cek apakah tabel kosong
function checkEmptyTable() {
    const tbody = document.querySelector('table tbody');
    if(tbody.children.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">Belum ada data guru</p>
                </td>
            </tr>
        `;
    }
}

// Tampilkan alert seperti session success
function showAlert(type, message) {
    // Hapus alert yang sudah ada sebelumnya
    const existingAlert = document.querySelector('.alert-custom');
    if(existingAlert) {
        existingAlert.remove();
    }

    // Buat elemen alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-custom`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.maxWidth = '400px';
    alertDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';

    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas ${icon} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    document.body.appendChild(alertDiv);

    // Auto hilang setelah 4 detik
    setTimeout(() => {
        if(alertDiv) {
            alertDiv.classList.remove('show');
            setTimeout(() => {
                if(alertDiv) alertDiv.remove();
            }, 300);
        }
    }, 4000);
}
</script>
@endsection