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
        $query = Invoice::with('penjualan')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('pelanggan', 'like', "%{$search}%");
            });
        }

        $invoices = $query->paginate(10)->withQueryString();

        return view('invoice.index', compact('invoices'));
    }
}
