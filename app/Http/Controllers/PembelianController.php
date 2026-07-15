<?php

namespace App\Http\Controllers;

use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;
use App\Models\Pembelian;
use App\Models\Produk; 
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    // Daftar Pembelian
    public function index(Request $request)
    {
        $query = Pembelian::query()->latest('tanggal_pembelian');

        // Pencarian
        if ($request->filled('search')) {
            $query->where('no_transaksi', 'like', '%' . $request->search . '%');
        }

        $pembelians = $query->paginate(10)->withQueryString();

        // 🔥 pastikan file ini ada: resources/views/keuangan/pembelian.blade.php
        return view('keuangan.pembelian', compact('pembelians'));
    }

    // Form Tambah Pembelian
    public function create()
    {
        // 
        $produks = Produk::orderBy('nama_produk')->get();

        // 
        $kode = 'PB-' . str_pad(
            Pembelian::count() + 1,
            5,
            '0',
            STR_PAD_LEFT
        );

        // 
        return view('keuangan.pembelian_tambah', compact(
            'produks',
            'kode'
        ));
    }

    // Simpan Pembelian
    public function store(Request $request)
    {
        $request->validate([
            'no_transaksi'      => 'required',
            'supplier'          => 'required',
            'tanggal_pembelian' => 'required|date',
            'status'            => 'required',
            'produk'            => 'required|array',
            'qty'               => 'required|array',
            'harga'             => 'required|array',
        ]);

        DB::beginTransaction();

        try {

            $total = 0;

            foreach ($request->qty as $i => $qty) {
                $total += $qty * $request->harga[$i];
            }

            $pembelian = Pembelian::create([
                'no_transaksi'        => $request->no_transaksi,
                'supplier'            => $request->supplier,
                'tanggal_pembelian'   => $request->tanggal_pembelian,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'total_harga'         => $total,
                'total_bayar'         => 0,
                'status'              => $request->status,
                'keterangan'          => $request->keterangan,
            ]);

            foreach ($request->produk as $i => $produk) {

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'produk_id'    => $produk,
                    'qty'          => $request->qty[$i],
                    'harga_satuan' => $request->harga[$i],
                    'subtotal'     => $request->qty[$i] * $request->harga[$i],
                ]);

            }

            DB::commit();

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Pembelian berhasil disimpan.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());

        }
    }

    // Detail Pembelian
    public function show($id)
    {
        $pembelian = Pembelian::with('details')->findOrFail($id);

        return view(
            'keuangan.pembelian_show',
            compact('pembelian')
        );
    }

    // Form Edit Pembelian
    public function edit($id)
    {
        $pembelian = Pembelian::with('details')->findOrFail($id);

        $produks = Produk::orderBy('nama_produk')->get();

        return view(
            'keuangan.pembelian_edit',
            compact(
                'pembelian',
                'produks'
            )
        );
    }

    // Update Pembelian
    public function update(Request $request, $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $pembelian->update([

            'supplier' => $request->supplier,

            'tanggal_pembelian' => $request->tanggal_pembelian,

            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,

            'status' => $request->status,

            'keterangan' => $request->keterangan

        ]);

        return redirect()
                ->route('pembelian.index')
                ->with('success','Data berhasil diubah');
    }

    // Hapus Pembelian
    public function destroy($id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $pembelian->delete();

        return redirect()
                ->route('pembelian.index')
                ->with('success','Data berhasil dihapus');
    }
}