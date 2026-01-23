<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockMovementsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
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
