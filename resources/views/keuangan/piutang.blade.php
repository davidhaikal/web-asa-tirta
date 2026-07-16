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
                        Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}
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
                        {{ $belumDibayar ?? 0 }} Customer
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
                        {{ $sudahLunas ?? 0 }} Customer
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

        <button class="btn btn-danger rounded-pill px-4">
            <i class="bi bi-file-earmark-pdf"></i>
            Export PDF
        </button>

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
                    <th>Total Tagihan</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                @forelse($piutangList ?? [] as $piutang)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $piutang->pelanggan ?? 'Walk-in Customer' }}</div>
                        <small class="text-muted">{{ $piutang->kode }}</small>
                    </td>
                    <td class="fw-bold text-danger">Rp {{ number_format($piutang->total, 0, ',', '.') }}</td>
                    <td>{{ \Carbon\Carbon::parse($piutang->tanggal)->format('d M Y') }}</td>
                    <td><span class="badge bg-warning">Pending</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill mb-1" data-bs-toggle="modal" data-bs-target="#modalView{{ $piutang->id }}">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button type="button" class="btn btn-sm btn-warning rounded-pill mb-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $piutang->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-danger rounded-pill mb-1" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $piutang->id }}">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </td>
                </tr>

                <!-- Modal View -->
                <div class="modal fade" id="modalView{{ $piutang->id }}" tabindex="-1" aria-labelledby="modalView{{ $piutang->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalView{{ $piutang->id }}Label">Detail Piutang</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Customer:</strong> {{ $piutang->pelanggan ?? 'Walk-in Customer' }}</p>
                                <p><strong>No Invoice:</strong> {{ $piutang->kode }}</p>
                                <p><strong>Total Tagihan:</strong> Rp {{ number_format($piutang->total, 0, ',', '.') }}</p>
                                <p><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($piutang->tanggal)->format('d M Y') }}</p>
                                <p><strong>Status:</strong> {{ $piutang->status }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEdit{{ $piutang->id }}" tabindex="-1" aria-labelledby="modalEdit{{ $piutang->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('keuangan.piutang.update', $piutang->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEdit{{ $piutang->id }}Label">Edit Piutang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Customer</label>
                                        <input type="text" class="form-control" name="pelanggan" value="{{ $piutang->pelanggan }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Total Tagihan (Rp)</label>
                                        <input type="number" class="form-control" name="total" value="{{ $piutang->total }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jatuh Tempo</label>
                                        <input type="date" class="form-control" name="tanggal" value="{{ \Carbon\Carbon::parse($piutang->tanggal)->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="pending" {{ $piutang->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="lunas" {{ $piutang->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
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
                <div class="modal fade" id="modalDelete{{ $piutang->id }}" tabindex="-1" aria-labelledby="modalDelete{{ $piutang->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('keuangan.piutang.destroy', $piutang->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalDelete{{ $piutang->id }}Label">Hapus Piutang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus tagihan piutang dari <strong>{{ $piutang->pelanggan }}</strong> sejumlah Rp {{ number_format($piutang->total, 0, ',', '.') }}?
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
                    <td colspan="5" class="text-center text-muted">Tidak ada piutang pending saat ini.</td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection