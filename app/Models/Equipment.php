<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'category_id',
        'brand',
        'model',
        'serial_number',
        'asset_tag',
        'purchase_date',
        'purchase_price',
        'warranty_end_date',
        'status',
        'condition',
        'site_id',
        'notes',
        'quantity',
        'min_stock',
        'is_consumable',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_end_date' => 'date',
        'purchase_price' => 'decimal:2',
        'quantity' => 'integer',
        'min_stock' => 'integer',
        'is_consumable' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'equipment_id');
    }

    public function assignments()
    {
        return $this->hasMany(EquipmentAssignment::class, 'equipment_id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'equipment_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function getStockAttribute(): int
    {
        if (!$this->is_consumable) {
            return $this->quantity;
        }
        $entries = (int) $this->stockMovements()->where('movement', 'Entrée')->sum('quantity');
        $exits = (int) $this->stockMovements()->where('movement', 'Sortie')->sum('quantity');
        return $entries - $exits;
    }
}
