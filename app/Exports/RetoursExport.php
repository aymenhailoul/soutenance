<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RetoursExport implements FromCollection, WithHeadings
{
    protected $creditNotes;

    public function __construct(Collection $creditNotes)
    {
        $this->creditNotes = $creditNotes;
    }

    public function collection()
    {
        return $this->creditNotes->map(function ($creditNote) {
            return [
                'Date' => $creditNote->credit_date->format('Y-m-d'),
                'N° Avoir' => $creditNote->credit_note_number,
                'N° Facture' => $creditNote->invoice->invoice_number ?? 'N/A',
                'Client' => ($creditNote->client->prenom ?? '') . ' ' . ($creditNote->client->name ?? 'N/A'),
                'Motif' => $creditNote->reason ?? '',
                'Total' => $creditNote->total_amount,
                'Créé par' => $creditNote->creator->name ?? 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'N° Avoir',
            'N° Facture',
            'Client',
            'Motif',
            'Total (MAD)',
            'Créé par',
        ];
    }
}
