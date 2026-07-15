<?php

namespace App\Exports;

use App\Models\Qc;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QcExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Qc::with('produksi.produk')
            ->get()
            ->map(function ($q) {

                return [

                    'Tanggal' => $q->created_at->format('d-m-Y'),

                    'Produk' => $q->produksi->produk->nama_produk ?? '-',

                    'Keterangan' => $q->keterangan,

                    'Pemeriksa' => 'QC User',

                    'Status' => $q->hasil == 'Layak'
                        ? 'Lolos'
                        : 'Reject',

                ];

            });

    }

    public function headings(): array
    {
        return [

            'Tanggal',

            'Produk',

            'Keterangan',

            'Pemeriksa',

            'Status'

        ];
    }
}