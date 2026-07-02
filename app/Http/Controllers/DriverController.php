<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{

    // Dashboard Driver
    public function dashboard()
    {
        return view('driver.dashboard');
    }

    // Halaman Data Invoice
    public function invoice()
    {
        return view('driver.invoice.index');
    }

    // Detail Invoice
    public function showInvoice($id)
    {
        return view('driver.invoice.show', compact('id'));
    }

    // Konfirmasi Terima Invoice
    public function terimaInvoice($id)
    {
        // Nanti proses update status invoice

        return redirect()
            ->route('driver.invoice.index')
            ->with('success', 'Invoice berhasil diterima.');
    }

    // Halaman Data Pengiriman
    public function pengiriman()
    {
        return view('driver.pengiriman.index');
    }

    //Form Upload Bukti Pengiriman
    public function uploadForm($id)
    {
        return view('driver.pengiriman.upload', compact('id'));
    }

    // Simpan Upload Bukti Pengiriman
    public function uploadStore(Request $request, $id)
    {
        // Validasi upload
        $request->validate([
            'bukti_pengiriman' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Nanti simpan file ke storage/database

        return redirect()
            ->route('driver.pengiriman.index')
            ->with('success', 'Bukti pengiriman berhasil diupload.');
    }
}