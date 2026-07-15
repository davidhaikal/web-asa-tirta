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
            'produk_id'       => 'required|exists:produks,id',
            'jumlah'          => 'required|integer|min:1',
            'tanggal_keluar'  => 'required|date',
            'tujuan'          => 'nullable|string|max:255'
        ]);

        // Cari produk
        $produk = Produk::findOrFail($request->produk_id);

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

        return redirect('/gudang/barang-keluar')->with('success', 'Barang keluar berhasil ditambahkan.');
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

        return redirect('/gudang/barang-keluar')->with('success', 'Data barang keluar berhasil diperbarui.');
    }

    // HAPUS DATA
    public function destroy($id)
    {
        BarangKeluar::destroy($id);

        return redirect('/gudang/barang-keluar')->with('success', 'Data barang keluar berhasil dihapus.');
    }
}