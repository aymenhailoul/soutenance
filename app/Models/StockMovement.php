<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'equipment_id',
        'movement',
        'quantity',
        'source_site_id',
        'destination_site_id',
        'prix_achat',
        'montant',
        'user_id',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'prix_achat' => 'decimal:2',
            'montant' => 'decimal:2',
        ];
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sourceSite()
    {
        return $this->belongsTo(Site::class, 'source_site_id');
    }

    public function destinationSite()
    {
        return $this->belongsTo(Site::class, 'destination_site_id');
    }
}
