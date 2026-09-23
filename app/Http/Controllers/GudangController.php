<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\BarangRusak;
use App\Models\PermintaanStok;
use App\Models\Supplier;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    /**
     * Dashboard Gudang
     */
    public function dashboard()
    {
        // ==========================
        // CARD DASHBOARD
        // ==========================

        $totalProduk = Produk::count();

        $barangMasuk = BarangMasuk::count();

        $barangKeluar = BarangKeluar::count();

        $barangRusak = BarangRusak::count();

        $totalSupplier = class_exists(Supplier::class)
            ? Supplier::count()
            : 0;

        $totalPermintaan = PermintaanStok::count();

        $totalStok = Produk::sum('stok');

        // ==========================
        // STOK MENIPIS
        // ==========================

        $stokMenipis = Produk::whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok')
            ->limit(5)
            ->get();

        // ==========================
        // AKTIVITAS TERBARU
        // ==========================

        $aktivitas = BarangMasuk::latest()
            ->take(5)
            ->get();

        // ==========================
        // GRAFIK 12 BULAN
        // ==========================

        $bulan = [
            'Jan','Feb','Mar','Apr','Mei','Jun',
            'Jul','Ags','Sep','Okt','Nov','Des'
        ];

        $grafikMasuk = [];
        $grafikKeluar = [];

        for ($i = 1; $i <= 12; $i++) {

            $grafikMasuk[] = BarangMasuk::whereMonth('created_at', $i)->count();

            $grafikKeluar[] = BarangKeluar::whereMonth('created_at', $i)->count();

        }

        return view('gudang.dashboard', compact(
            'totalProduk',
            'barangMasuk',
            'barangKeluar',
            'barangRusak',
            'totalSupplier',
            'totalPermintaan',
            'totalStok',
            'stokMenipis',
            'aktivitas',
            'bulan',
            'grafikMasuk',
            'grafikKeluar'
        ));
    }

    //export laporan
    public function exportPdf()
    {
        $produk = Produk::all();
        $barangMasuk = BarangMasuk::with('produk')->get();
        $barangKeluar = BarangKeluar::with('produk')->get();
        $barangRusak = BarangRusak::with('produk')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('gudang.dashboard_pdf', compact('produk', 'barangMasuk', 'barangKeluar', 'barangRusak'));
        
        return $pdf->download('Laporan_Gudang.pdf');
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\GudangExport, 'Laporan_Gudang.xlsx');
    }

    public function getLaporanData(Request $request)
    {
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $jenisLaporan = $request->input('jenis_laporan');
        $produkId = $request->input('produk_id');

        // Barang Masuk
        $queryMasuk = BarangMasuk::with('produk');
        if ($tanggalAwal) {
            $queryMasuk->where('tanggal_masuk', '>=', $tanggalAwal);
        }
        if ($tanggalAkhir) {
            $queryMasuk->where('tanggal_masuk', '<=', $tanggalAkhir);
        }
        if ($produkId) {
            $queryMasuk->where('produk_id', $produkId);
        }
        $masuk = $queryMasuk->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'tanggal' => $item->tanggal_masuk,
                'no_referensi' => 'BM-' . date('Ymd', strtotime($item->tanggal_masuk)) . '-' . str_pad($item->id, 3, '0', STR_PAD_LEFT),
                'jenis_laporan' => 'Barang Masuk',
                'produk' => $item->produk->nama_produk ?? '-',
                'qty' => $item->qty,
                'jumlah' => $item->jumlah,
                'status' => 'Selesai',
                'raw_tanggal' => $item->tanggal_masuk,
            ];
        });

        // Barang Keluar
        $queryKeluar = BarangKeluar::with('produk');
        if ($tanggalAwal) {
            $queryKeluar->where('tanggal_keluar', '>=', $tanggalAwal);
        }
        if ($tanggalAkhir) {
            $queryKeluar->where('tanggal_keluar', '<=', $tanggalAkhir);
        }
        if ($produkId) {
            $queryKeluar->where('produk_id', $produkId);
        }
        $keluar = $queryKeluar->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'tanggal' => $item->tanggal_keluar,
                'no_referensi' => 'BK-' . date('Ymd', strtotime($item->tanggal_keluar)) . '-' . str_pad($item->id, 3, '0', STR_PAD_LEFT),
                'jenis_laporan' => 'Barang Keluar',
                'produk' => $item->produk->nama_produk ?? '-',
                'qty' => $item->qty,
                'jumlah' => $item->jumlah,
                'status' => 'Selesai',
                'raw_tanggal' => $item->tanggal_keluar,
            ];
        });

        // Permintaan Stok
        $queryPermintaan = PermintaanStok::with('produk');
        if ($tanggalAwal) {
            $queryPermintaan->where('tanggal', '>=', $tanggalAwal);
        }
        if ($tanggalAkhir) {
            $queryPermintaan->where('tanggal', '<=', $tanggalAkhir);
        }
        if ($produkId) {
            $queryPermintaan->where('produk_id', $produkId);
        }
        $permintaan = $queryPermintaan->get()->map(function ($item) {
            return (object) [
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'no_referensi' => 'PRM-' . date('Ymd', strtotime($item->tanggal)) . '-' . str_pad($item->id, 3, '0', STR_PAD_LEFT),
                'jenis_laporan' => 'Permintaan Stok',
                'produk' => $item->produk->nama_produk ?? '-',
                'qty' => $item->qty,
                'jumlah' => $item->jumlah,
                'status' => $item->status ?? 'Menunggu',
                'raw_tanggal' => $item->tanggal,
            ];
        });

        // Barang Rusak
        $queryRusak = BarangRusak::with('produk');
        if ($tanggalAwal) {
            $queryRusak->where('tanggal_rusak', '>=', $tanggalAwal);
        }
        if ($tanggalAkhir) {
            $queryRusak->where('tanggal_rusak', '<=', $tanggalAkhir);
        }
        if ($produkId) {
            $queryRusak->where('produk_id', $produkId);
        }
        $rusak = $queryRusak->get()->map(function ($item) {
            $tgl = $item->tanggal_rusak ?? ($item->created_at ? $item->created_at->format('Y-m-d') : date('Y-m-d'));
            return (object) [
                'id' => $item->id,
                'tanggal' => $tgl,
                'no_referensi' => 'BR-' . date('Ymd', strtotime($tgl)) . '-' . str_pad($item->id, 3, '0', STR_PAD_LEFT),
                'jenis_laporan' => 'Barang Rusak',
                'produk' => $item->produk->nama_produk ?? '-',
                'qty' => $item->qty,
                'jumlah' => $item->jumlah,
                'status' => 'Selesai',
                'raw_tanggal' => $tgl,
            ];
        });

        // Combine
        $combined = collect();
        if (!$jenisLaporan || $jenisLaporan === 'Semua' || $jenisLaporan === 'Barang Masuk') {
            $combined = $combined->merge($masuk);
        }
        if (!$jenisLaporan || $jenisLaporan === 'Semua' || $jenisLaporan === 'Barang Keluar') {
            $combined = $combined->merge($keluar);
        }
        if (!$jenisLaporan || $jenisLaporan === 'Semua' || $jenisLaporan === 'Permintaan Stok') {
            $combined = $combined->merge($permintaan);
        }
        if (!$jenisLaporan || $jenisLaporan === 'Semua' || $jenisLaporan === 'Barang Rusak') {
            $combined = $combined->merge($rusak);
        }

        // Sort descending
        $combined = $combined->sortByDesc('raw_tanggal')->values();

        return [
            'data' => $combined,
            'stats' => [
                'total_produk' => Produk::count(),
                'total_masuk' => $masuk->sum('jumlah'),
                'total_keluar' => $keluar->sum('jumlah'),
                'total_rusak' => $rusak->sum('jumlah'),
                'total_permintaan' => $permintaan->count(),
            ]
        ];
    }

    public function laporan(Request $request)
    {
        // Default date range: awal bulan ini s/d akhir bulan ini
        if (!$request->has('tanggal_awal')) {
            $request->merge(['tanggal_awal' => date('Y-m-01')]);
        }
        if (!$request->has('tanggal_akhir')) {
            $request->merge(['tanggal_akhir' => date('Y-m-t')]);
        }

        $res = $this->getLaporanData($request);
        $data = $res['data'];
        $stats = $res['stats'];

        // Pagination
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $currentItems = $data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $data->count(), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        $produk = Produk::all();

        return view('gudang.laporan', compact('paginatedData', 'stats', 'produk', 'request'));
    }

    public function exportLaporanPdf(Request $request)
    {
        $res = $this->getLaporanData($request);
        $data = $res['data'];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('gudang.laporan_pdf', compact('data', 'request'));
        
        return $pdf->download('Laporan_Gudang_' . date('Ymd_His') . '.pdf');
    }

    public function exportLaporanExcel(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanGudangExport($request), 
            'Laporan_Gudang_' . date('Ymd_His') . '.xlsx'
        );
    }

    public function printLaporan(Request $request)
    {
        $res = $this->getLaporanData($request);
        $data = $res['data'];
        
        return view('gudang.laporan_pdf', compact('data', 'request'))->with('is_print', true);
    }
}