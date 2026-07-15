<?php

namespace App\Exports;

use App\Models\Produk;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\BarangRusak;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GudangExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        return view('gudang.dashboard_pdf', [
            'produk' => Produk::all(),
            'barangMasuk' => BarangMasuk::with('produk')->get(),
            'barangKeluar' => BarangKeluar::with('produk')->get(),
            'barangRusak' => BarangRusak::with('produk')->get(),
        ]);
    }
}
