@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Permintaan Stok</h2>

    {{-- FORM --}}
    <div class="card-form">

        <form action="/gudang/permintaan-stok/store"
              method="POST">

            @csrf

            <select name="produk_id" required>

                <option value="">
                    Pilih Produk
                </option>

                @foreach($produk as $p)

                    <option value="{{ $p->id }}">
                        {{ $p->nama_produk }}
                    </option>

                @endforeach

            </select>

            <input type="number"
                   name="jumlah"
                   placeholder="Total Permintaan (Pcs)"
                   min="1"
                   required>

            <input type="date"
                   name="tanggal"
                   required>

            <button type="submit">
                Kirim Permintaan
            </button>

        </form>

    </div>


    {{-- TABEL --}}
    <div class="table-card">

        <div class="table-responsive">
            <table class="asa-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Jumlah (Pcs)</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($permintaan as $item)

                <tr>

                    <td>{{ ($permintaan->currentPage() - 1) * $permintaan->perPage() + $loop->iteration }}</td>

                    <td>
                        {{ $item->produk->nama_produk ?? '-' }}
                    </td>

                    <td>
                        {{ $item->jumlah }}
                    </td>

                    <td>
                        {{ $item->tanggal }}
                    </td>

                    <td>
                        @if($item->status == 'Disetujui' || $item->status == 'Selesai')
                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> {{ $item->status }}
                            </span>
                        @elseif($item->status == 'Ditolak')
                            <span class="badge bg-danger px-3 py-2 rounded-pill">
                                <i class="bi bi-x-circle me-1"></i> {{ $item->status }}
                            </span>
                        @else
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                <i class="bi bi-hourglass-split me-1"></i> {{ $item->status ?? 'Menunggu' }}
                            </span>
                        @endif
                    </td>

                    <td class="text-center">
                        <div class="aksi-group">
                            {{-- Detail --}}
                            <button type="button"
                                    class="btn btn-primary btn-action"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetail{{ $item->id }}"
                                    title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </button>

                            {{-- Edit --}}
                            <a href="/gudang/permintaan-stok/edit/{{ $item->id }}"
                               class="btn btn-warning btn-action"
                               title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>

                            {{-- Hapus --}}
                            <form action="/gudang/permintaan-stok/delete/{{ $item->id }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus data permintaan stok ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-danger btn-action"
                                        title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>

                {{-- Modal Detail Permintaan Stok --}}
                <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold text-primary" id="modalDetailLabel{{ $item->id }}">
                                    <i class="bi bi-info-circle me-1"></i> Detail Permintaan Stok
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-3">
                                <div class="p-3 bg-light rounded-3 mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Nama Produk:</span>
                                        <span class="fw-bold">{{ $item->produk->nama_produk ?? '-' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Jumlah Permintaan:</span>
                                        <span class="fw-bold text-primary fs-6">{{ $item->jumlah }} Pcs</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tanggal:</span>
                                        <span>{{ $item->tanggal }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Status:</span>
                                        <span>
                                            @if($item->status == 'Disetujui' || $item->status == 'Selesai')
                                                <span class="badge bg-success">{{ $item->status }}</span>
                                            @elseif($item->status == 'Ditolak')
                                                <span class="badge bg-danger">{{ $item->status }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ $item->status ?? 'Menunggu' }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                @if($item->created_at)
                                <div class="small text-muted text-end">
                                    Dicatat pada: {{ $item->created_at->format('d M Y, H:i') }}
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data permintaan stok.</td>
                </tr>
                @endforelse
            </tbody>

            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Menampilkan {{ $permintaan->firstItem() ?? 0 }}–{{ $permintaan->lastItem() ?? 0 }} dari {{ $permintaan->total() ?? 0 }} data
            </div>
            <div>
                {{ $permintaan->withQueryString()->links() }}
            </div>
        </div>

    </div>

</div>

<style>

.container{
    padding:20px;
}

.card-form{
    background:white;
    padding:20px;
    border-radius:20px;
    margin-bottom:25px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.card-form form{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.card-form input,
.card-form select{
    padding:12px;
    border-radius:10px;
    border:1px solid #ddd;
    min-width:220px;
}

.card-form button{
    background:#127369;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    cursor:pointer;
}

.table-card{
    background:white;
    padding:20px;
    border-radius:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    padding:15px;
    border-bottom:1px solid #eee;
    text-align:left;
}

.status{
    background:orange;
    color:white;
    padding:8px 14px;
    border-radius:20px;
    font-size:13px;
}

/* ===========================
   BUTTON AKSI (SEPERTI BARANG MASUK)
=========================== */

.aksi-group{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:8px;
    flex-wrap:nowrap;
}

.aksi-group form{
    display:inline-block;
    margin:0;
}

.btn-action{
    width:38px;
    height:38px;
    display:inline-flex;
    justify-content:center;
    align-items:center;
    padding:0;
    border-radius:10px;
    text-decoration:none;
}

.btn-action i{
    font-size:15px;
    line-height:1;
}

/* RESPONSIVE */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .card-form form {
        flex-direction: column;
    }
    .card-form input,
    .card-form select,
    .card-form button {
        width: 100%;
        min-width: unset;
    }
}


</style>

@endsection