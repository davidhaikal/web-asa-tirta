<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Produk;

class BarangMasukController extends Controller
{
    // Tampilkan halaman barang masuk
    public function index()
    {
        $produk = Produk::all();

        $barangMasuk = BarangMasuk::with('produk')->get();

        return view('gudang.barang_masuk', compact('produk', 'barangMasuk'));
    }

    // Simpan barang masuk
    public function store(Request $request)
    {
        $request->validate([
            'produk_id'      => 'required|exists:produks,id',
            'jumlah'         => 'required|integer|min:1',
            'tanggal_masuk'  => 'required|date',
            'catatan'        => 'nullable|string|max:255'
        ]);

        // Simpan ke tabel barang_masuk
        BarangMasuk::create([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal_masuk' => $request->tanggal_masuk,
            'catatan' => $request->catatan
        ]);

        // Tambah stok produk otomatis
        $produk = Produk::find($request->produk_id);

        $produk->stok += $request->jumlah;

        $produk->save();

        return redirect('/gudang/barang-masuk')
        ->with('success','Barang masuk berhasil disimpan.');
    }
}