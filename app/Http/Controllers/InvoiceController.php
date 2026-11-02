<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Daftar invoice yang dibuat sistem saat transaksi lunas
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['penjualan.detailPenjualans.produk', 'penjualan.pengiriman', 'penjualan.user'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('pelanggan', 'like', "%{$search}%")
                    ->orWhereHas('penjualan', function ($qp) use ($search) {
                        $qp->where('kode', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('metode')) {
            $metode = $request->metode;
            $query->whereHas('penjualan', function ($qp) use ($metode) {
                $qp->where('metode', $metode);
            });
        }

        if ($request->filled('status_kirim')) {
            $statusKirim = $request->status_kirim;
            $query->whereHas('penjualan.pengiriman', function ($qg) use ($statusKirim) {
                $qg->where('status', $statusKirim);
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Statistik Ringkasan
        $totalInvoice = Invoice::count();
        $totalNominal = Invoice::sum('total');
        $invoiceHariIni = Invoice::whereDate('created_at', now()->toDateString())->count();
        $nominalHariIni = Invoice::whereDate('created_at', now()->toDateString())->sum('total');

        return view('invoice.index', compact(
            'invoices',
            'totalInvoice',
            'totalNominal',
            'invoiceHariIni',
            'nominalHariIni'
        ));
    }

    /**
     * Cetak dokumen resmi Invoice
     */
    public function cetak($id)
    {
        $invoice = Invoice::with(['penjualan.detailPenjualans.produk', 'penjualan.pengiriman', 'penjualan.user'])->findOrFail($id);

        return view('invoice.cetak', compact('invoice'));
    }
}
