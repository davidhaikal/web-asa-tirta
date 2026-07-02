<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\Produk;

class BarangKeluarController extends Controller
{
    // Tampilkan halaman barang keluar
    public function index()
    {
        $produk = Produk::all();

        $barangKeluar = BarangKeluar::with('produk')
                            ->latest()
                            ->paginate(5);

        return view(
            'gudang.barang_keluar',
            compact('produk', 'barangKeluar')
        );
    }

    // Simpan barang keluar
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required',
            'jumlah' => 'required',
            'tanggal_keluar' => 'required'
        ]);

        // Cari produk
        $produk = Produk::find($request->produk_id);

        // Cek stok cukup atau tidak
        if ($produk->stok < $request->jumlah) {

            return back()->with(
                'error',
                'Stok tidak cukup'
            );

        }

        // Simpan barang keluar
        BarangKeluar::create([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal_keluar' => $request->tanggal_keluar,
            'tujuan' => $request->tujuan
        ]);

        // Kurangi stok otomatis
        $produk->stok -= $request->jumlah;

        $produk->save();

        return redirect('/gudang/barang-keluar');
    }

    // FORM EDIT
    public function edit($id)
    {
        $barangKeluar = BarangKeluar::findOrFail($id);

        $produk = Produk::all();

        return view(
            'gudang.edit_barang_keluar',
            compact('barangKeluar', 'produk')
        );
    }

    // UPDATE DATA
    public function update(Request $request, $id)
    {
        $barangKeluar = BarangKeluar::findOrFail($id);

        $barangKeluar->update([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal_keluar' => $request->tanggal_keluar,
            'tujuan' => $request->tujuan
        ]);

        return redirect('/gudang/barang-keluar');
    }

    // HAPUS DATA
    public function destroy($id)
    {
        BarangKeluar::destroy($id);

        return redirect('/gudang/barang-keluar');
    }
}