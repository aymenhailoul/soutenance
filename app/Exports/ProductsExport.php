<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::query();

        if (!empty($this->filters['search'])) {
            $query->where('name', 'like', '%' . $this->filters['search'] . '%');
        }

        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        return $query->get()->map(function ($product) {
            return [
                'Name' => $product->name,
                'Type' => $product->type,
                'Prix Achat' => $product->prix_achat,
                'Prix Vente' => $product->prix_vente,
                'Code' => $product->serial_code,
                'Created At' => $product->created_at
                    ? $product->created_at->format('Y-m-d')
                    : null,
                'Stock' => $product->type === 'Produit' ? (string) $product->stock : '-',
            ];
        });

    }

    public function headings(): array
    {
        return [
            'Name',
            'Type',
            'Prix Achat',
            'Prix Vente',
            'Code',
            'Created At',
            'Stock',
        ];
    }
}
