<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ManajemenController extends Controller
{
    /**
     * Sumber data dummy per jenis laporan.
     * Nanti tinggal diganti dengan query ke model asli
     * (BarangMasuk::with('produk')->get() dst) kalau sudah siap.
     */
    private function getLaporanData(string $jenis): Collection
    {
        $dataset = [];

        if ($jenis == 'masuk') {
            $dataset = \App\Models\BarangMasuk::with('produk')->get()->map(function($item) {
                return [
                    'tanggal' => $item->tanggal_masuk ?? $item->created_at->format('Y-m-d'),
                    'no_transaksi' => 'BM-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'produk' => $item->produk->nama_produk ?? '-',
                    'jumlah' => $item->jumlah,
                    'satuan' => 'Unit',
                    'pihak' => 'Internal / Supplier',
                    'nilai' => $item->jumlah * ($item->produk->harga ?? 0),
                    'catatan' => $item->catatan ?? '-'
                ];
            });
        } elseif ($jenis == 'keluar') {
            $dataset = \App\Models\BarangKeluar::with('produk')->get()->map(function($item) {
                return [
                    'tanggal' => $item->tanggal_keluar ?? $item->created_at->format('Y-m-d'),
                    'no_transaksi' => 'BK-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'produk' => $item->produk->nama_produk ?? '-',
                    'jumlah' => $item->jumlah,
                    'satuan' => 'Unit',
                    'pihak' => $item->tujuan ?? 'Customer',
                    'nilai' => $item->jumlah * ($item->produk->harga ?? 0),
                    'catatan' => $item->catatan ?? '-'
                ];
            });
        } elseif ($jenis == 'rusak') {
            $dataset = \App\Models\BarangRusak::with('produk')->get()->map(function($item) {
                return [
                    'tanggal' => $item->tanggal_rusak ?? $item->tanggal ?? $item->created_at->format('Y-m-d'),
                    'no_transaksi' => 'BR-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'produk' => $item->produk->nama_produk ?? '-',
                    'jumlah' => $item->jumlah,
                    'satuan' => 'Unit',
                    'pihak' => $item->penyebab ?? 'Internal',
                    'nilai' => $item->jumlah * ($item->produk->harga ?? 0),
                    'catatan' => $item->keterangan ?? '-'
                ];
            });
        } elseif ($jenis == 'penjualan') {
            $dataset = \App\Models\DetailPenjualan::with(['penjualan', 'produk'])->get()->map(function($item) {
                return [
                    'tanggal' => $item->penjualan->tanggal_penjualan ?? $item->penjualan->created_at->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'no_transaksi' => 'PJ-' . str_pad($item->penjualan_id, 4, '0', STR_PAD_LEFT),
                    'produk' => $item->produk->nama_produk ?? '-',
                    'jumlah' => $item->jumlah,
                    'satuan' => 'Unit',
                    'pihak' => $item->penjualan->pelanggan->nama_pelanggan ?? $item->penjualan->nama_pelanggan ?? 'Umum',
                    'nilai' => $item->subtotal,
                    'catatan' => '-'
                ];
            });
        }

        return collect($dataset);
    }

    /**
     * Label kolom "pihak" berubah tergantung jenis laporan
     * (Supplier utk masuk, Tujuan utk keluar, Penyebab utk rusak, Pelanggan utk penjualan)
     */
    private function labelPihak(string $jenis): string
    {
        return match ($jenis) {
            'masuk'     => 'Supplier',
            'keluar'    => 'Tujuan',
            'rusak'     => 'Penyebab',
            'penjualan' => 'Pelanggan',
            default     => 'Pihak',
        };
    }

    private function labelJenis(string $jenis): string
    {
        return match ($jenis) {
            'masuk'     => 'Barang Masuk',
            'keluar'    => 'Barang Keluar',
            'rusak'     => 'Barang Rusak',
            'penjualan' => 'Penjualan',
            default     => 'Laporan',
        };
    }

    // ==========================
    // ==========================
    // DASHBOARD
    // ==========================
    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'semua');
        $ringkasan = [];

        foreach (['masuk', 'keluar', 'rusak', 'penjualan'] as $jenis) {
            $data = $this->getLaporanData($jenis);

            if ($period == 'hari_ini') {
                $data = $data->filter(fn($row) => \Carbon\Carbon::parse($row['tanggal'])->isToday());
            } elseif ($period == 'bulan_ini') {
                $data = $data->filter(fn($row) => \Carbon\Carbon::parse($row['tanggal'])->isCurrentMonth());
            } elseif ($period == 'tahun_ini') {
                $data = $data->filter(fn($row) => \Carbon\Carbon::parse($row['tanggal'])->isCurrentYear());
            }

            $ringkasan[$jenis] = [
                'label'          => $this->labelJenis($jenis),
                'total_transaksi'=> $data->count(),
                'total_jumlah'   => $data->sum('jumlah'),
                'total_nilai'    => $data->sum('nilai'),
            ];
        }

        // Gabungan 5 transaksi terbaru dari semua jenis, buat aktivitas terkini
        $aktivitasTerbaru = collect(['masuk', 'keluar', 'rusak', 'penjualan'])
            ->flatMap(function ($jenis) use ($period) {
                $data = $this->getLaporanData($jenis);
                
                if ($period == 'hari_ini') {
                    $data = $data->filter(fn($row) => \Carbon\Carbon::parse($row['tanggal'])->isToday());
                } elseif ($period == 'bulan_ini') {
                    $data = $data->filter(fn($row) => \Carbon\Carbon::parse($row['tanggal'])->isCurrentMonth());
                } elseif ($period == 'tahun_ini') {
                    $data = $data->filter(fn($row) => \Carbon\Carbon::parse($row['tanggal'])->isCurrentYear());
                }

                return $data->map(function ($row) use ($jenis) {
                    $row['jenis'] = $this->labelJenis($jenis);
                    return $row;
                });
            })
            ->sortByDesc('tanggal')
            ->take(6)
            ->values();
            
        $subLabel = match($period) {
            'hari_ini' => 'Hari Ini',
            'bulan_ini' => 'Bulan Ini',
            'tahun_ini' => 'Tahun Ini',
            default => 'Semua Waktu'
        };

        return view('manajemen.dashboard', compact('ringkasan', 'aktivitasTerbaru', 'period', 'subLabel'));
    }

    /**
     * Ambil data dummy sesuai jenis + terapkan semua filter dari request.
     * Dipakai bersama oleh laporan(), exportPdf(), dan exportExcel()
     * supaya hasil filter selalu konsisten di ketiga tempat.
     */
    private function filteredData(Request $request): array
    {
        $jenis = $request->get('jenis', 'masuk');

        if (!in_array($jenis, ['masuk', 'keluar', 'rusak', 'penjualan'])) {
            $jenis = 'masuk';
        }

        $data = $this->getLaporanData($jenis);

        $daftarProduk = $data->pluck('produk')->unique()->values();
        $daftarSatuan = $data->pluck('satuan')->unique()->values();

        $search  = $request->get('search');
        $produk  = $request->get('produk');
        $satuan  = $request->get('satuan');
        $mulai   = $request->get('tanggal_mulai');
        $selesai = $request->get('tanggal_selesai');

        if ($search) {
            $data = $data->filter(function ($row) use ($search) {
                return str_contains(strtolower($row['produk']), strtolower($search))
                    || str_contains(strtolower($row['no_transaksi']), strtolower($search));
            });
        }

        if ($produk) {
            $data = $data->where('produk', $produk);
        }

        if ($satuan) {
            $data = $data->where('satuan', $satuan);
        }

        if ($mulai) {
            $data = $data->filter(fn ($row) => $row['tanggal'] >= $mulai);
        }

        if ($selesai) {
            $data = $data->filter(fn ($row) => $row['tanggal'] <= $selesai);
        }

        $data = $data->sortBy('tanggal')->values();

        return [
            'jenis'          => $jenis,
            'labelJenis'     => $this->labelJenis($jenis),
            'labelPihak'     => $this->labelPihak($jenis),
            'data'           => $data,
            'daftarProduk'   => $daftarProduk,
            'daftarSatuan'   => $daftarSatuan,
            'totalTransaksi' => $data->count(),
            'totalJumlah'    => $data->sum('jumlah'),
            'totalNilai'     => $data->sum('nilai'),
            'rataRata'       => $data->count() > 0 ? $data->sum('nilai') / $data->count() : 0,
        ];
    }

    // ==========================
    // LAPORAN (tampil di halaman)
    // ==========================
    public function laporan(Request $request)
    {
        $f = $this->filteredData($request);

        $perPage     = (int) $request->get('per_page', 10);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $items = new LengthAwarePaginator(
            $f['data']->forPage($currentPage, $perPage),
            $f['data']->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('manajemen.laporan', [
            'jenis'          => $f['jenis'],
            'labelJenis'     => $f['labelJenis'],
            'labelPihak'     => $f['labelPihak'],
            'items'          => $items,
            'daftarProduk'   => $f['daftarProduk'],
            'daftarSatuan'   => $f['daftarSatuan'],
            'totalTransaksi' => $f['totalTransaksi'],
            'totalJumlah'    => $f['totalJumlah'],
            'totalNilai'     => $f['totalNilai'],
            'rataRata'       => $f['rataRata'],
        ]);
    }

    // ==========================
    // EXPORT PDF
    // ==========================
    public function exportPdf(Request $request)
    {
        $f = $this->filteredData($request);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('manajemen.laporan_pdf', [
            'labelJenis'     => $f['labelJenis'],
            'labelPihak'     => $f['labelPihak'],
            'data'           => $f['data'],
            'totalTransaksi' => $f['totalTransaksi'],
            'totalJumlah'    => $f['totalJumlah'],
            'totalNilai'     => $f['totalNilai'],
            'rataRata'       => $f['rataRata'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-' . $f['jenis'] . '-' . now()->format('Y-m-d') . '.pdf');
    }

    // ==========================
    // EXPORT EXCEL
    // ==========================
    public function exportExcel(Request $request)
    {
        $f = $this->filteredData($request);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanManajemenExport($f['data'], $f['labelPihak']),
            'laporan-' . $f['jenis'] . '-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}