<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $clientId;
    protected $dateFrom;
    protected $dateTo;
    protected $invoiceNumber;

    public function __construct($clientId = null, $dateFrom = null, $dateTo = null, $invoiceNumber = null)
    {
        $this->clientId = $clientId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->invoiceNumber = $invoiceNumber;
    }

    public function collection()
    {
        $query = Invoice::with(['client', 'items.product'])->orderBy('invoice_date', 'desc');

        if ($this->clientId) {
            $query->where('client_id', $this->clientId);
        }
        if ($this->dateFrom) {
            $query->whereDate('invoice_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('invoice_date', '<=', $this->dateTo);
        }
        if ($this->invoiceNumber) {
            $query->where('invoice_number', 'like', '%' . $this->invoiceNumber . '%');
        }

        return $query->get();
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
