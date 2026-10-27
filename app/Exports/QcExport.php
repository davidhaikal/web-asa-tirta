<?php

namespace App\Exports;

use App\Models\Qc;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QcExport implements FromCollection, WithHeadings
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Qc::with('produksi.produk')->latest();

        if (!empty($this->filters['tanggal'])) {
            $query->whereDate('created_at', $this->filters['tanggal']);
        }

        if (!empty($this->filters['bulan']) && str_contains($this->filters['bulan'], '-')) {
            [$year, $month] = explode('-', $this->filters['bulan']);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        }

        if (!empty($this->filters['tanggal_awal'])) {
            $query->whereDate('created_at', '>=', $this->filters['tanggal_awal']);
        }

        if (!empty($this->filters['tanggal_akhir'])) {
            $query->whereDate('created_at', '<=', $this->filters['tanggal_akhir']);
        }

        if (!empty($this->filters['hasil'])) {
            $query->where('hasil', $this->filters['hasil']);
        }

        $pemeriksa = auth()->check() ? auth()->user()->name : 'QC User';

        return $query->get()->map(function ($q) use ($pemeriksa) {
            return [
                'Tanggal' => $q->created_at ? $q->created_at->format('d-m-Y H:i') : '-',
                'Produk' => $q->produksi->produk->nama_produk ?? '-',
                'Jumlah Produksi' => $q->produksi->jumlah_produksi ?? 0,
                'Keterangan' => $q->keterangan ?? '-',
                'Pemeriksa' => $pemeriksa,
                'Status' => $q->hasil == 'Layak' ? 'Lolos' : 'Reject',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Produk',
            'Jumlah Produksi',
            'Keterangan',
            'Pemeriksa',
            'Status',
        ];
    }
}