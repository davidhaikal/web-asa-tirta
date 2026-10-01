<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PenagihanController extends Controller
{
    // Halaman Daftar Penagihan
    public function index()
    {
        $tagihans = Penjualan::where('status', '!=', 'lunas')->latest()->get();

        return view('keuangan.penagihan', compact('tagihans'));
    }

    // Detail Tagihan
    public function show($id)
    {
        $penjualan = Penjualan::findOrFail($id);

        $tagihan = (object) [
            'id' => $penjualan->id,
            'customer' => $penjualan->pelanggan ?? 'Walk-in Customer',
            'total_tagihan' => (float) $penjualan->total,
            'jatuh_tempo' => Carbon::parse($penjualan->tanggal)->format('d M Y'),
            'status' => $penjualan->status,
            'alamat' => '-',
            'telepon' => '-',
            'email' => '-',
        ];

        return view('keuangan.penagihan_detail', compact('tagihan'));
    }

    // Form Kirim Tagihan (data riil dari transaksi belum lunas)
    public function formKirim(Request $request)
    {
        $query = Penjualan::where('status', '!=', 'lunas')->latest();

        if ($request->filled('search')) {
            $query->where('pelanggan', 'like', '%'.$request->search.'%');
        }

        // Jatuh tempo dianggap 30 hari sejak tanggal transaksi
        $batas = Carbon::now()->subDays(30);

        if ($request->filled('status')) {
            if ($request->status === 'Menunggak') {
                $query->whereDate('tanggal', '<=', $batas->toDateString());
            } else {
                $query->whereDate('tanggal', '>', $batas->toDateString());
            }
        }

        $tagihans = $query->get()->map(function ($penjualan) {
            $jatuhTempo = Carbon::parse($penjualan->tanggal)->addDays(30);

            return (object) [
                'id' => $penjualan->id,
                'customer' => $penjualan->pelanggan ?? 'Walk-in Customer',
                'invoice' => $penjualan->kode,
                'total' => (float) $penjualan->total,
                'jatuh_tempo' => $jatuhTempo->format('d M Y'),
                'status' => $jatuhTempo->isPast() ? 'Menunggak' : 'Pending',
                'email' => '-',
                'telepon' => '-',
            ];
        });

        return view('keuangan.penagihan_kirim', compact('tagihans'));
    }

    // Proses Kirim — tandai tagihan telah dikirim
    public function prosesKirim(Request $request)
    {
        $ids = (array) $request->get('tagihan', []);

        if (empty($ids)) {
            return redirect()->route('penagihan.form.kirim')
                ->with('error', 'Pilih minimal satu tagihan untuk dikirim.');
        }

        $diperbarui = Penjualan::whereIn('id', $ids)
            ->where('status', '!=', 'lunas')
            ->update(['tagihan_dikirim_at' => now()]);

        return redirect()->route('penagihan.index')
            ->with('success', "{$diperbarui} tagihan berhasil dikirim.");
    }

    // Kirim semua tagihan yang belum lunas
    public function kirimSemua()
    {
        $diperbarui = Penjualan::where('status', '!=', 'lunas')
            ->update(['tagihan_dikirim_at' => now()]);

        return redirect()->route('penagihan.index')
            ->with('success', "Semua tagihan ({$diperbarui}) berhasil dikirim.");
    }

    // Kirim satu tagihan (tandai terkirim)
    public function kirim($id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if ($penjualan->status === 'lunas') {
            return back()->with('error', 'Tagihan sudah lunas, tidak perlu dikirim.');
        }

        $penjualan->tagihan_dikirim_at = now();
        $penjualan->save();

        return back()->with('success', "Tagihan {$penjualan->kode} dikirim.");
    }

    // Tandai tagihan sudah ditagih (follow-up)
    public function tagih($id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if ($penjualan->status === 'lunas') {
            return back()->with('error', 'Tagihan sudah lunas, tidak perlu ditagih.');
        }

        $penjualan->tagihan_ditagih_at = now();
        $penjualan->save();

        return back()
            ->with('success', "Tagihan {$penjualan->kode} ditandai sudah ditagih.");
    }
}
