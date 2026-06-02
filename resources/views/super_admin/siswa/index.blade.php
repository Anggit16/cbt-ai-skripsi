@extends('layouts.app')

@section('title', 'Manajemen Siswa - Super Admin')

@section('content')
<div class="bg-light min-vh-100 py-4">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">
                    <i class="fas fa-users me-2 text-success"></i>Manajemen Siswa
                </h4>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Fitur ini hanya menampilkan data siswa. Untuk mengedit atau menghapus, silahkan hubungi developer.
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Nomor Absen</th>
                                <th>Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $index => $s)
                            <tr>
                                <td>{{ $siswa->firstItem() + $index }}</td>
                                <td class="fw-semibold">{{ $s->name }}</td>
                                <td>{{ $s->email }}</td>
                                <td>{{ $s->kelas ?? '-' }}</td>
                                <td>{{ $s->jurusan ?? '-' }}</td>
                                <td>{{ $s->nomor_absen ?? '-' }}</td>
                                <td>{{ $s->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">Belum ada data siswa</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $siswa->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection