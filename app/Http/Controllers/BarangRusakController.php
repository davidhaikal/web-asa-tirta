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
        $request->validate([
            'produk_id'   => 'required|exists:produks,id',
            'jumlah'      => 'required|integer|min:1',
            'keterangan'  => 'required|string',
            'tanggal'     => 'required|date'
        ]);

        // Cari produk
        $produk = Produk::findOrFail($request->produk_id);

        // Cek stok
        if ($produk->stok < $request->jumlah) {

            return back()
                ->withInput()
                ->with('error', 'Jumlah barang rusak melebihi stok yang tersedia.');

        }

        // Simpan data barang rusak
        BarangRusak::create([

            'produk_id'      => $request->produk_id,

            'qty'            => $request->qty,

            'jumlah'         => $request->jumlah,

            'keterangan'     => $request->keterangan,

            'tanggal_rusak'  => $request->tanggal

        ]);

        // Kurangi stok produk
        $produk->stok -= $request->jumlah;
        $produk->save();

        return redirect('/gudang/barang-rusak')
                ->with('success', 'Barang rusak berhasil disimpan.');
    }

    // TAMPIL DETAIL BARANG RUSAK
    public function show($id)
    {
        $barangRusak = BarangRusak::findOrFail($id);
        return view('gudang.detail_barang_rusak', compact('barangRusak'));
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
        $request->validate([
            'produk_id'   => 'required|exists:produks,id',
            'jumlah'      => 'required|integer|min:1',
            'keterangan'  => 'required|string',
            'tanggal'     => 'required|date'
        ]);

        $barangRusak = BarangRusak::findOrFail($id);

        // Kembalikan stok lama ke produk asal
        $produkLama = Produk::find($barangRusak->produk_id);
        if ($produkLama) {
            $produkLama->stok += $barangRusak->jumlah;
            $produkLama->save();
        }

        // Terapkan stok baru ke produk yang dipilih (bisa saja beda produk)
        $produkBaru = Produk::findOrFail($request->produk_id);

        if ($produkBaru->stok < $request->jumlah) {
            return back()
                ->withInput()
                ->with('error', 'Jumlah barang rusak melebihi stok yang tersedia.');
        }

        $produkBaru->stok -= $request->jumlah;
        $produkBaru->save();

        $barangRusak->update([

            'produk_id'     => $request->produk_id,

            'qty'           => $request->qty,

            'jumlah'        => $request->jumlah,

            'keterangan'    => $request->keterangan,

            'tanggal_rusak' => $request->tanggal

        ]);

        return redirect('/gudang/barang-rusak')
                ->with('success', 'Barang rusak berhasil diperbarui.');
    }


    // HAPUS DATA
    public function destroy($id)
    {
        $barangRusak = BarangRusak::findOrFail($id);

        // Kembalikan stok yang tadinya dikurangi
        $produk = Produk::find($barangRusak->produk_id);
        if ($produk) {
            $produk->stok += $barangRusak->jumlah;
            $produk->save();
        }

        $barangRusak->delete();

        return redirect('/gudang/barang-rusak')
                ->with('success', 'Data barang rusak berhasil dihapus.');
    }
}