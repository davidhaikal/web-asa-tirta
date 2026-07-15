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
    private function dummyData(string $jenis): Collection
    {
        $dataset = [

            'masuk' => [
                ['tanggal' => '2025-05-02', 'no_transaksi' => 'BM-20250502-001', 'produk' => 'Air Mineral 330ml',  'jumlah' => 600, 'satuan' => 'Dus', 'pihak' => 'PT Tirta Abadi', 'nilai' => 8400000, 'catatan' => '-'],
                ['tanggal' => '2025-05-05', 'no_transaksi' => 'BM-20250505-001', 'produk' => 'Air Mineral 600ml',  'jumlah' => 800, 'satuan' => 'Dus', 'pihak' => 'PT Tirta Abadi', 'nilai' => 12800000, 'catatan' => '-'],
                ['tanggal' => '2025-05-07', 'no_transaksi' => 'BM-20250507-001', 'produk' => 'Air Mineral 1500ml', 'jumlah' => 400, 'satuan' => 'Dus', 'pihak' => 'PT Tirta Abadi', 'nilai' => 9200000, 'catatan' => '-'],
                ['tanggal' => '2025-05-12', 'no_transaksi' => 'BM-20250512-001', 'produk' => 'Air Mineral 330ml',  'jumlah' => 700, 'satuan' => 'Dus', 'pihak' => 'PT Sumber Jaya', 'nilai' => 9800000, 'catatan' => '-'],
                ['tanggal' => '2025-05-15', 'no_transaksi' => 'BM-20250515-001', 'produk' => 'Air Mineral 600ml',  'jumlah' => 900, 'satuan' => 'Dus', 'pihak' => 'PT Sumber Jaya', 'nilai' => 14400000, 'catatan' => '-'],
                ['tanggal' => '2025-05-20', 'no_transaksi' => 'BM-20250520-001', 'produk' => 'Air Mineral 1500ml', 'jumlah' => 600, 'satuan' => 'Dus', 'pihak' => 'PT Sumber Jaya', 'nilai' => 13800000, 'catatan' => '-'],
                ['tanggal' => '2025-05-25', 'no_transaksi' => 'BM-20250525-001', 'produk' => 'Air Mineral 330ml',  'jumlah' => 400, 'satuan' => 'Dus', 'pihak' => 'PT Tirta Abadi', 'nilai' => 5600000, 'catatan' => '-'],
                ['tanggal' => '2025-05-28', 'no_transaksi' => 'BM-20250528-001', 'produk' => 'Air Mineral 600ml',  'jumlah' => 500, 'satuan' => 'Dus', 'pihak' => 'PT Sumber Jaya', 'nilai' => 8000000, 'catatan' => '-'],
            ],

            'keluar' => [
                ['tanggal' => '2025-05-03', 'no_transaksi' => 'BK-20250503-001', 'produk' => 'Air Mineral 330ml',  'jumlah' => 300, 'satuan' => 'Dus', 'pihak' => 'Toko Sinar Jaya', 'nilai' => 4500000, 'catatan' => '-'],
                ['tanggal' => '2025-05-06', 'no_transaksi' => 'BK-20250506-001', 'produk' => 'Air Mineral 600ml',  'jumlah' => 450, 'satuan' => 'Dus', 'pihak' => 'Toko Barokah', 'nilai' => 7200000, 'catatan' => '-'],
                ['tanggal' => '2025-05-10', 'no_transaksi' => 'BK-20250510-001', 'produk' => 'Air Mineral 1500ml', 'jumlah' => 250, 'satuan' => 'Dus', 'pihak' => 'Toko Sinar Jaya', 'nilai' => 5750000, 'catatan' => '-'],
                ['tanggal' => '2025-05-14', 'no_transaksi' => 'BK-20250514-001', 'produk' => 'Air Mineral 330ml',  'jumlah' => 350, 'satuan' => 'Dus', 'pihak' => 'Toko Maju Bersama', 'nilai' => 4900000, 'catatan' => '-'],
                ['tanggal' => '2025-05-19', 'no_transaksi' => 'BK-20250519-001', 'produk' => 'Air Mineral 600ml',  'jumlah' => 500, 'satuan' => 'Dus', 'pihak' => 'Toko Barokah', 'nilai' => 8000000, 'catatan' => '-'],
                ['tanggal' => '2025-05-24', 'no_transaksi' => 'BK-20250524-001', 'produk' => 'Air Mineral 1500ml', 'jumlah' => 300, 'satuan' => 'Dus', 'pihak' => 'Toko Sinar Jaya', 'nilai' => 6900000, 'catatan' => '-'],
            ],

            'rusak' => [
                ['tanggal' => '2025-05-04', 'no_transaksi' => 'BR-20250504-001', 'produk' => 'Air Mineral 330ml',  'jumlah' => 20, 'satuan' => 'Dus', 'pihak' => 'Kemasan Rusak', 'nilai' => 280000,  'catatan' => 'Rusak saat bongkar muat'],
                ['tanggal' => '2025-05-11', 'no_transaksi' => 'BR-20250511-001', 'produk' => 'Air Mineral 600ml',  'jumlah' => 15, 'satuan' => 'Dus', 'pihak' => 'Bocor',          'nilai' => 240000,  'catatan' => '-'],
                ['tanggal' => '2025-05-21', 'no_transaksi' => 'BR-20250521-001', 'produk' => 'Air Mineral 1500ml', 'jumlah' => 10, 'satuan' => 'Dus', 'pihak' => 'Pecah',          'nilai' => 230000,  'catatan' => '-'],
            ],

            'penjualan' => [
                ['tanggal' => '2025-05-03', 'no_transaksi' => 'PJ-20250503-001', 'produk' => 'Air Mineral 330ml',  'jumlah' => 300, 'satuan' => 'Dus', 'pihak' => 'Toko Sinar Jaya', 'nilai' => 5100000, 'catatan' => '-'],
                ['tanggal' => '2025-05-08', 'no_transaksi' => 'PJ-20250508-001', 'produk' => 'Air Mineral 600ml',  'jumlah' => 450, 'satuan' => 'Dus', 'pihak' => 'Toko Barokah',   'nilai' => 8100000, 'catatan' => '-'],
                ['tanggal' => '2025-05-16', 'no_transaksi' => 'PJ-20250516-001', 'produk' => 'Air Mineral 1500ml', 'jumlah' => 250, 'satuan' => 'Dus', 'pihak' => 'Toko Maju Bersama', 'nilai' => 6250000, 'catatan' => '-'],
                ['tanggal' => '2025-05-23', 'no_transaksi' => 'PJ-20250523-001', 'produk' => 'Air Mineral 330ml',  'jumlah' => 350, 'satuan' => 'Dus', 'pihak' => 'Toko Sinar Jaya', 'nilai' => 5950000, 'catatan' => '-'],
            ],

        ];

        return collect($dataset[$jenis] ?? []);
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
    // DASHBOARD
    // ==========================
    public function dashboard()
    {
        $ringkasan = [];

        foreach (['masuk', 'keluar', 'rusak', 'penjualan'] as $jenis) {
            $data = $this->dummyData($jenis);

            $ringkasan[$jenis] = [
                'label'          => $this->labelJenis($jenis),
                'total_transaksi'=> $data->count(),
                'total_jumlah'   => $data->sum('jumlah'),
                'total_nilai'    => $data->sum('nilai'),
            ];
        }

        // Gabungan 5 transaksi terbaru dari semua jenis, buat aktivitas terkini
        $aktivitasTerbaru = collect(['masuk', 'keluar', 'rusak', 'penjualan'])
            ->flatMap(function ($jenis) {
                return $this->dummyData($jenis)->map(function ($row) use ($jenis) {
                    $row['jenis'] = $this->labelJenis($jenis);
                    return $row;
                });
            })
            ->sortByDesc('tanggal')
            ->take(6)
            ->values();

        return view('manajemen.dashboard', compact('ringkasan', 'aktivitasTerbaru'));
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

        $data = $this->dummyData($jenis);

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