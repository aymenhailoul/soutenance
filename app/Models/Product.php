<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'prix_achat',
        'prix_vente',
        'serial_code',
    ];
    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Calculate current stock from movements (Entrée - Sortie)
    public function getStockAttribute(): int
    {
        $entries = (int) $this->stockMovements()->where('movement', 'Entrée')->sum('quantity');
        $exits = (int) $this->stockMovements()->where('movement', 'Sortie')->sum('quantity');
        return $entries - $exits;
    }
}
