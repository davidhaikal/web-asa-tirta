<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // SIMPAN DATA BARANG RUSAK
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date'
        ]);

        BarangRusak::create([

            'produk_id'      => $request->produk_id,

            'jumlah'         => $request->jumlah,

            'keterangan'     => $request->keterangan,

            'tanggal_rusak'  => $request->tanggal

        ]);

        return redirect('/gudang/barang-rusak')
                ->with('success', 'Barang rusak berhasil disimpan.');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_rusaks');
    }
};