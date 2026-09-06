<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

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

    public function activeAssignment()
    {
        return $this->hasOne(EquipmentAssignment::class, 'equipment_id')->where('status', 'Active');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'equipment_id');
    }

    public function activeMaintenances()
    {
        return $this->hasMany(Maintenance::class, 'equipment_id')->whereIn('status', ['Scheduled', 'In Progress']);
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function isConsumable(): bool
    {
        return (bool) $this->is_consumable;
    }

    public function isAssignable(): bool
    {
        if ($this->is_consumable) {
            return false;
        }

        if ($this->status !== 'Available') {
            return false;
        }

        if ($this->activeAssignment()->exists()) {
            return false;
        }

        if ($this->activeMaintenances()->exists()) {
            return false;
        }

        return true;
    }

    public function getStockAttribute(): int
    {
        return $this->quantity;
    }
}

