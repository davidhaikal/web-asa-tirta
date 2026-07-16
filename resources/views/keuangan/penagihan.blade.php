@extends('layouts.app')

@section('content')

<div class="table-modern p-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold">
                Penagihan Customer
            </h4>

            <small class="text-muted">
                Monitoring penagihan dan pembayaran customer
            </small>

        </div>

        <a href="{{ route('penagihan.form.kirim') }}"
            class="btn btn-danger rounded-pill px-4">

            <i class="bi bi-send"></i>
            Kirim Tagihan

        </a>

    </div>

    <div class="row mb-4">

        <div class="col-md-4">

            <input
                type="text"
                class="form-control rounded-pill"
                placeholder="Cari customer..."
            >

        </div>

    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

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

                @forelse($tagihans as $tagihan)
                <tr>
                    <td class="fw-bold">
                        {{ $tagihan->pelanggan ?? 'Walk-in Customer' }}
                        <div class="small text-muted fw-normal">{{ $tagihan->kode }}</div>
                    </td>
                    <td class="text-danger fw-bold">
                        Rp {{ number_format($tagihan->total, 0, ',', '.') }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($tagihan->tanggal)->format('d M Y') }}
                    </td>
                    <td>
                        <span class="badge {{ $tagihan->status == 'pending' ? 'bg-warning' : 'bg-danger' }}">
                            {{ ucfirst($tagihan->status) }}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill mb-1" data-bs-toggle="modal" data-bs-target="#modalView{{ $tagihan->id }}">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button type="button" class="btn btn-sm btn-warning rounded-pill mb-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $tagihan->id }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-danger rounded-pill mb-1" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $tagihan->id }}">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </td>
                </tr>

                <!-- Modal View -->
                <div class="modal fade" id="modalView{{ $tagihan->id }}" tabindex="-1" aria-labelledby="modalView{{ $tagihan->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalView{{ $tagihan->id }}Label">Detail Penagihan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Customer:</strong> {{ $tagihan->pelanggan ?? 'Walk-in Customer' }}</p>
                                <p><strong>No Invoice:</strong> {{ $tagihan->kode }}</p>
                                <p><strong>Total Tagihan:</strong> Rp {{ number_format($tagihan->total, 0, ',', '.') }}</p>
                                <p><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($tagihan->tanggal)->format('d M Y') }}</p>
                                <p><strong>Status:</strong> {{ $tagihan->status }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEdit{{ $tagihan->id }}" tabindex="-1" aria-labelledby="modalEdit{{ $tagihan->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('keuangan.penagihan.update', $tagihan->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEdit{{ $tagihan->id }}Label">Edit Penagihan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Customer</label>
                                        <input type="text" class="form-control" name="pelanggan" value="{{ $tagihan->pelanggan }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Total Tagihan (Rp)</label>
                                        <input type="number" class="form-control" name="total" value="{{ $tagihan->total }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jatuh Tempo</label>
                                        <input type="date" class="form-control" name="tanggal" value="{{ \Carbon\Carbon::parse($tagihan->tanggal)->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="pending" {{ $tagihan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="lunas" {{ $tagihan->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
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
                <div class="modal fade" id="modalDelete{{ $tagihan->id }}" tabindex="-1" aria-labelledby="modalDelete{{ $tagihan->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('keuangan.penagihan.destroy', $tagihan->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalDelete{{ $tagihan->id }}Label">Hapus Penagihan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus tagihan penagihan dari <strong>{{ $tagihan->pelanggan }}</strong> sejumlah Rp {{ number_format($tagihan->total, 0, ',', '.') }}?
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
                    <td colspan="5" class="text-center text-muted">Tidak ada data penagihan.</td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection