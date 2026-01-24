<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Invoice::with(['client', 'items.product'])->orderBy('invoice_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Numéro de Facture',
            'Date',
            'Client',
            'Total (MAD)',
            'Statut',
        ];
    }

    public function map($invoice): array
    {
        $clientFullName = trim(($invoice->client->prenom ?? '') . ' ' . ($invoice->client->name ?? 'N/A'));

        return [
            $invoice->invoice_number,
            $invoice->invoice_date->format('Y-m-d'),
            $clientFullName,
            number_format($invoice->total_amount, 2, '.', ''),
            $invoice->status ?? 'Payée',
        ];
    }
}
