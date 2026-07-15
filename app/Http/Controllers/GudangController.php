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
}