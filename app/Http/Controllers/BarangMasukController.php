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
            'qty' => $request->qty,
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
    // Tampilkan detail
    public function show($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        return view('gudang.detail_barang_masuk', compact('barangMasuk'));
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);
        $produk = Produk::all();
        return view('gudang.edit_barang_masuk', compact('barangMasuk', 'produk'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_id'      => 'required|exists:produks,id',
            'jumlah'         => 'required|integer|min:1',
            'tanggal_masuk'  => 'required|date',
            'catatan'        => 'nullable|string|max:255'
        ]);

        $barangMasuk = BarangMasuk::findOrFail($id);

        // Kurangi stok dari produk lama
        $produkLama = Produk::find($barangMasuk->produk_id);
        if ($produkLama) {
            $produkLama->stok -= $barangMasuk->jumlah;
            $produkLama->save();
        }

        // Tambah stok ke produk baru
        $produkBaru = Produk::findOrFail($request->produk_id);
        $produkBaru->stok += $request->jumlah;
        $produkBaru->save();

        $barangMasuk->update([
            'produk_id' => $request->produk_id,
            'qty' => $request->qty,
            'jumlah' => $request->jumlah,
            'tanggal_masuk' => $request->tanggal_masuk,
            'catatan' => $request->catatan
        ]);

        return redirect('/gudang/barang-masuk')->with('success', 'Data berhasil diperbarui.');
    }

    // Hapus data
    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        // Kembalikan stok
        $produk = Produk::find($barangMasuk->produk_id);
        if ($produk) {
            $produk->stok -= $barangMasuk->jumlah;
            $produk->save();
        }

        $barangMasuk->delete();

        return redirect('/gudang/barang-masuk')->with('success', 'Data berhasil dihapus.');
    }
}