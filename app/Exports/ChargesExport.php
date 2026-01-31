<?php

namespace App\Exports;

use App\Models\Charge;
use App\Models\StockMovement;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChargesExport implements FromCollection, WithHeadings
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct(string $dateFrom, string $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        $dateFromParsed = Carbon::parse($this->dateFrom)->startOfDay();
        $dateToParsed = Carbon::parse($this->dateTo)->endOfDay();

        // Get stock entrées (exclude those from cancelled invoices)
        $entrees = StockMovement::with('product')
            ->where('movement', 'Entrée')
            ->where(function ($q) {
                $q->whereNull('comment')
                    ->orWhere(function ($subQ) {
                        $subQ->where('comment', 'not like', '%Annulation%')
                             ->where('comment', 'not like', '%Retour%');
                    });
            })
            ->whereHas('product', function ($q) {
                $q->where('type', 'Produit');
            })
            ->whereBetween('created_at', [$dateFromParsed, $dateToParsed])
            ->get()
            ->map(function ($movement) {
                return [
                    'Type' => 'Entrée',
                    'Montant' => $movement->product->prix_achat * $movement->quantity,
                    'Motif' => 'Entrée - ' . $movement->product->name . ' (x' . $movement->quantity . ')',
                    'Date' => $movement->created_at->format('Y-m-d H:i'),
                ];
            });

        // Get custom charges
        $customCharges = Charge::whereBetween('created_at', [$dateFromParsed, $dateToParsed])
            ->get()
            ->map(function ($charge) {
                return [
                    'Type' => 'Supplémentaire',
                    'Montant' => $charge->amount,
                    'Motif' => $charge->motif,
                    'Date' => $charge->created_at->format('Y-m-d H:i'),
                ];
            });

        return $entrees->concat($customCharges)->sortByDesc('Date')->values();
    }

    public function headings(): array
    {
        return [
            'Type',
            'Montant',
            'Motif',
            'Date',
        ];
    }
}
