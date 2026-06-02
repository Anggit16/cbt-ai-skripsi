@extends('layouts.app')

@section('title', 'Manajemen Ujian - Super Admin')

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">
                    <i class="fas fa-file-alt me-2 text-warning"></i>Manajemen Ujian
                </h4>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Fitur ini hanya menampilkan data ujian. Untuk mengedit atau menghapus, silahkan hubungi developer.
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Judul Ujian</th>
                                <th>Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Durasi</th>
                                <th>Jumlah Peserta</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ujian as $index => $u)
                            <tr>
                                <td>{{ $ujian->firstItem() + $index }}</td>
                                <td class="fw-semibold">{{ $u->judul }}</td>
                                <td>{{ $u->guru->name ?? '-' }}</td>
                                <td>{{ $u->guru->mata_pelajaran ?? '-' }}</td>
                                <td>{{ $u->durasi }} menit</td>
                                <td>{{ $u->jumlah_peserta }}</td>
                                <td>
                                    @if($u->status == 'menunggu')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($u->status == 'berlangsung')
                                        <span class="badge bg-success">Berlangsung</span>
                                    @else
                                        <span class="badge bg-secondary">Selesai</span>
                                    @endif
                                </td>
                                <td>{{ $u->created_at->format('d/m/Y') }}</td>
                            </tr>                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Belum ada data ujian</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $ujian->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection