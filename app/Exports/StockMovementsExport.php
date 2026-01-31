<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockMovementsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc');

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->get()
            ->map(function ($movement) {
                return [
                    'Type'     => $movement->movement,
                    'Produit'  => $movement->product->name ?? 'N/A',
                    'Quantité' => $movement->quantity,
                    'Date'     => $movement->created_at->format('Y-m-d'),
                    'Heure'    => $movement->created_at->format('H:i'),
                    'User'     => $movement->user->name ?? 'N/A',
                    'Motif'    => $movement->comment ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Type',
            'Produit',
            'Quantité',
            'Date',
            'Heure',
            'User',
            'Motif',
        ];
    }
}
