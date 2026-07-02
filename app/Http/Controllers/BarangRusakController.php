<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangRusak;
use App\Models\Produk;

class BarangRusakController extends Controller
{
    // TAMPIL HALAMAN BARANG RUSAK
    public function index()
    {
        $produk = Produk::all();

        $barangRusak = BarangRusak::with('produk')->get();

        return view(
            'gudang.barang_rusak',
            compact('produk', 'barangRusak')
        );
    }


    // SIMPAN DATA BARANG RUSAK
    public function store(Request $request)
    {
        BarangRusak::create([

            'produk_id' => $request->produk_id,

            'jumlah' => $request->jumlah,

            'keterangan' => $request->keterangan,

            'tanggal' => $request->tanggal

        ]);

        return redirect('/gudang/barang-rusak');
    }


    // FORM EDIT
    public function edit($id)
    {
        $barangRusak = BarangRusak::findOrFail($id);

        $produk = Produk::all();

        return view(
            'gudang.edit_barang_rusak',
            compact('barangRusak', 'produk')
        );
    }


    // UPDATE DATA
    public function update(Request $request, $id)
    {
        $barangRusak = BarangRusak::findOrFail($id);

        $barangRusak->update([

            'produk_id' => $request->produk_id,

            'jumlah' => $request->jumlah,

            'keterangan' => $request->keterangan,

            'tanggal' => $request->tanggal

        ]);

        return redirect('/gudang/barang-rusak');
    }


    // HAPUS DATA
    public function destroy($id)
    {
        BarangRusak::destroy($id);

        return redirect('/gudang/barang-rusak');
    }
}