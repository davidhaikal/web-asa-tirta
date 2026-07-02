<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    // Tampilkan semua produk
    public function index(Request $request)
    {
        $search = $request->search;

        $produk = Produk::where(
                        'nama_produk',
                        'like',
                        "%".$search."%"
                    )
                    ->latest()
                    ->paginate(5);

        return view(
            'gudang.produk',
            compact('produk', 'search')
        );
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'harga' => 'required'
        ]);

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'kode_produk' => $request->kode_produk,
            'harga' => $request->harga,
            'stok' => $request->stok
        ]);

        return redirect('/gudang/produk');
    }

    // Edit produk
    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        return view('gudang.edit_produk', compact('produk'));
    }

    // Update produk
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $produk->update($request->all());
        return redirect('/gudang/produk');
    }

    // Hapus produk
    public function destroy($id)
    {
        Produk::destroy($id);

        return redirect('/gudang/produk');
    }
}