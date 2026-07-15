<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenagihanController extends Controller
{

    // Halaman Daftar Penagihan
    public function index()
    {
        $tagihans = [

            (object)[
                'id' => 1,
                'customer' => 'PT Maju Jaya',
                'total' => 5000000,
                'jatuh_tempo' => '25 Mei 2026',
                'status' => 'Pending',
            ],

            (object)[
                'id' => 2,
                'customer' => 'CV Tirta Abadi',
                'total' => 8500000,
                'jatuh_tempo' => '20 Mei 2026',
                'status' => 'Lunas',
            ],

            (object)[
                'id' => 3,
                'customer' => 'PT Sumber Rejeki',
                'total' => 12000000,
                'jatuh_tempo' => '30 Mei 2026',
                'status' => 'Menunggak',
            ],

        ];

        return view('keuangan.penagihan', compact('tagihans'));
    }

    // Detail Tagihan
    public function show($id)
    {
        return view('keuangan.penagihan_detail', compact('id'));
    }

    // Form Kirim Tagihan
    public function formKirim()
    {
        $tagihans = [

            (object)[
                'id'=>1,
                'customer'=>'PT Maju Jaya',
                'invoice'=>'INV-00021',
                'total'=>5000000,
                'jatuh_tempo'=>'25 Mei 2026',
                'status'=>'Pending',
                'email'=>'majujaya@gmail.com',
                'telepon'=>'08123456789'
            ],

            (object)[
                'id'=>2,
                'customer'=>'PT Sumber Rejeki',
                'invoice'=>'INV-00022',
                'total'=>12000000,
                'jatuh_tempo'=>'30 Mei 2026',
                'status'=>'Menunggak',
                'email'=>'rejeki@gmail.com',
                'telepon'=>'08129876543'
            ],

        ];

        return view(
            'keuangan.penagihan_kirim',
            compact('tagihans')
        );
    }

    // Proses Kirim
    public function prosesKirim(Request $request)
    {
        return redirect()
            ->route('penagihan.index')
            ->with('success', 'Tagihan berhasil dikirim.');
    }

    public function kirimSemua()
    {
        return redirect()
            ->route('penagihan.index')
            ->with('success', 'Semua tagihan berhasil dikirim.');
    }

    // Tandai Sudah Ditagih
    public function tagih($id)
    {
        return redirect()
            ->route('penagihan.index')
            ->with('success', 'Status tagihan berhasil diperbarui.');
    }

    
}