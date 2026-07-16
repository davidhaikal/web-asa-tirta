@extends('layouts.app')

@section('content')

<div class="row g-4">
    @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Card Total Piutang -->
    <div class="col-md-4">

        <div class="card-modern bg-red">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <p class="mb-1">Total Piutang</p>

                    <h2 class="fw-bold">
                        Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                    </h2>
                </div>

                <div>
                    <i class="bi bi-wallet2 fs-1"></i>
                </div>

            </div>

        </div>

    </div>

    <!-- Card Belum Bayar -->
    <div class="col-md-4">

        <div class="card-modern bg-orange">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <p class="mb-1">Belum Dibayar</p>

                    <h2 class="fw-bold">
                        {{ $belumDibayar }} Customer
                    </h2>
                </div>

                <div>
                    <i class="bi bi-exclamation-circle fs-1"></i>
                </div>

            </div>

        </div>

    </div>

    <!-- Card Lunas -->
    <div class="col-md-4">

        <div class="card-modern bg-green">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <p class="mb-1">Sudah Lunas</p>

                    <h2 class="fw-bold">
                        {{ $sudahLunas }} Customer
                    </h2>
                </div>

                <div>
                    <i class="bi bi-check-circle fs-1"></i>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- Tabel Piutang -->

<div class="table-modern mt-4 p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Data Piutang Customer
            </h4>

            <small class="text-muted">
                Monitoring pembayaran customer
            </small>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Pelanggan
            </button>
            <button class="btn btn-danger rounded-pill px-4">
                <i class="bi bi-file-earmark-pdf"></i>
                Export PDF
            </button>
        </div>

    </div>

    <!-- Search -->

    <div class="row mb-4">

        <div class="col-md-4">

            <input
                type="text"
                class="form-control rounded-pill"
                placeholder="Cari customer..."
            >

        </div>

        <div class="col-md-3">

            <select class="form-select rounded-pill">

                <option>Semua Status</option>
                <option>Pending</option>
                <option>Lunas</option>

            </select>

        </div>

    </div>

    <!-- Table -->

    <div class="table-responsive">

        <table class="table align-middle table-hover">

            <thead class="table-light">

                <tr>
                    <th>Customer</th>
                    <th>Kota</th>
                    <th>No Telp</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                @forelse ($pelanggans as $p)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $p->nama_pelanggan }}</div>
                        <small class="text-muted">{{ $p->alamat ?? '-' }}</small>
                    </td>
                    <td>{{ $p->kota ?? '-' }}</td>
                    <td>{{ $p->no_telp ?? '-' }}</td>
                    <td>
                        @if(strtolower($p->status) === 'aktif')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">{{ $p->status }}</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning rounded-pill" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $p->id }}">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1" aria-labelledby="modalEdit{{ $p->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('keuangan.pelanggan.update', $p->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEdit{{ $p->id }}Label">Edit Pelanggan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Pelanggan</label>
                                        <input type="text" class="form-control" name="nama_pelanggan" value="{{ $p->nama_pelanggan }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kota</label>
                                        <input type="text" class="form-control" name="kota" value="{{ $p->kota }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">No Telp</label>
                                        <input type="text" class="form-control" name="no_telp" value="{{ $p->no_telp }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" name="alamat">{{ $p->alamat }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="Aktif" {{ $p->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Nonaktif" {{ $p->status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Delete -->
                <div class="modal fade" id="modalDelete{{ $p->id }}" tabindex="-1" aria-labelledby="modalDelete{{ $p->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('keuangan.pelanggan.destroy', $p->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalDelete{{ $p->id }}Label">Hapus Pelanggan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus pelanggan <strong>{{ $p->nama_pelanggan }}</strong>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Belum ada data pelanggan</td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('keuangan.pelanggan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Pelanggan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" name="nama_pelanggan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kota</label>
                        <input type="text" class="form-control" name="kota">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Telp</label>
                        <input type="text" class="form-control" name="no_telp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection