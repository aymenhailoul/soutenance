<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'plaque',
        'marque',
        'modele',
        'annee',
        'kilometrage',
        'carburant',
        'couleur',
    ];

    protected $casts = [
        'annee' => 'integer',
        'kilometrage' => 'integer',
    ];

    /**
     * Get the client that owns the vehicle.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get all invoices for this vehicle.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get finalized service history for this vehicle.
     */
    public function serviceHistory()
    {
        return $this->invoices()
            ->where('status', 'Finalized')
            ->orderBy('invoice_date', 'desc');
    }

    /**
     * Get the display name for the vehicle.
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->plaque;
        if ($this->marque) {
            $name .= ' - ' . $this->marque;
        }
        if ($this->modele) {
            $name .= ' ' . $this->modele;
        }
        return $name;
    }
}
