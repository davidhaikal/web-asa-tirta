<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Stok;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    //
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'penjualan');
        if (! in_array($tab, ['penjualan', 'stok'], true)) {
            $tab = 'penjualan';
        }

        // Ringkasan sistem (read-only)
        $totalPenjualan = Penjualan::where('status', '!=', 'batal')->sum('total');
        $piutangPending = Penjualan::where('status', 'pending')->sum('total');
        $totalStok = Produk::sum('stok');
        $jumlahMutasi = Stok::count();

        $penjualan = Penjualan::latest('tanggal')->paginate(10);
        $stok = Stok::with('produk')->latest('created_at')->paginate(10);

        return view('laporan.index', compact(
            'tab', 'penjualan', 'stok',
            'totalPenjualan', 'piutangPending', 'totalStok', 'jumlahMutasi'
        ));
    }
}
