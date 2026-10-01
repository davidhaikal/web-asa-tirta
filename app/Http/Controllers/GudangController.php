<?php

namespace App\Http\Controllers;

use App\Exports\GudangExport;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\BarangRusak;
use App\Models\PermintaanStok;
use App\Models\Produk;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
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

    /**
     * Laporan Gudang — ringkasan stok per produk
     * (total masuk/keluar/rusak akumulatif + stok & nilai saat ini)
     */
    public function laporan()
    {
        $masukPerProduk = BarangMasuk::selectRaw('produk_id, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('produk_id')
            ->pluck('total', 'produk_id');

        $keluarPerProduk = BarangKeluar::selectRaw('produk_id, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('produk_id')
            ->pluck('total', 'produk_id');

        $rusakPerProduk = BarangRusak::selectRaw('produk_id, COALESCE(SUM(jumlah), 0) as total')
            ->groupBy('produk_id')
            ->pluck('total', 'produk_id');

        $data = Produk::orderBy('nama_produk')->get()->map(function ($produk) use ($masukPerProduk, $keluarPerProduk, $rusakPerProduk) {
            $stok = (float) $produk->stok;
            $harga = (float) $produk->harga;

            return [
                'nama' => $produk->nama_produk,
                'masuk' => (float) ($masukPerProduk[$produk->id] ?? 0),
                'keluar' => (float) ($keluarPerProduk[$produk->id] ?? 0),
                'rusak' => (float) ($rusakPerProduk[$produk->id] ?? 0),
                'stok' => $stok,
                'harga' => $harga,
                'nilai' => $stok * $harga,
            ];
        });

        $ringkasan = [
            'totalProduk' => $data->count(),
            'totalStok' => $data->sum('stok'),
            'totalNilai' => $data->sum('nilai'),
            'totalRusak' => $data->sum('rusak'),
        ];

        return view('gudang.laporan', compact('data', 'ringkasan'));
    }

    // export laporan
    public function exportPdf()
    {
        $produk = Produk::all();
        $barangMasuk = BarangMasuk::with('produk')->get();
        $barangKeluar = BarangKeluar::with('produk')->get();
        $barangRusak = BarangRusak::with('produk')->get();

        $pdf = Pdf::loadView('gudang.dashboard_pdf', compact('produk', 'barangMasuk', 'barangKeluar', 'barangRusak'));

        return $pdf->download('Laporan_Gudang.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new GudangExport, 'Laporan_Gudang.xlsx');
    }
}
