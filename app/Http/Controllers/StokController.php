<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Stok;
use Illuminate\Http\Request;

class StokController extends Controller
{
    // Riwayat stok (halaman utama /stok)
    public function index(Request $request)
    {
        $stok = Stok::with('produk')->latest()->paginate(10)->withQueryString();

        return view('stok.index', compact('stok'));
    }

    // Filter riwayat stok per jenis (masuk/keluar)
    public function filter($jenis)
    {
        if (! in_array($jenis, ['masuk', 'keluar'], true)) {
            abort(404);
        }

        $stok = Stok::with('produk')
            ->where('jenis', $jenis)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('stok.index', compact('stok', 'jenis'));
    }

    public function masuk(Request $request)
    {
        $produk = Produk::find($request->produk_id);

        $produk->stok += $request->jumlah;
        $produk->save();

        Stok::create([
            'produk_id' => $produk->id,
            'jenis' => 'masuk',
            'jumlah' => $request->jumlah,
            'keterangan' => 'Barang masuk',
        ]);

        return 'Stok masuk berhasil';
    }

    public function keluar(Request $request)
    {
        $produk = Produk::find($request->produk_id);

        if (! $produk || $produk->stok < $request->jumlah) {
            return back()->with('error', 'Stok tidak mencukupi untuk barang keluar.');
        }

        $produk->stok -= $request->jumlah;
        $produk->save();

        Stok::create([
            'produk_id' => $produk->id,
            'jenis' => 'keluar',
            'jumlah' => $request->jumlah,
            'keterangan' => 'Barang keluar',
        ]);

        return 'Stok keluar berhasil';
    }

    public function riwayat()
    {
        $stok = Stok::all();

        return view('stok.index', compact('stok'));
    }
}
