<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prenom',
        'phone',
    ];

    /**
     * Get all vehicles for this client.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Get all invoices for this client.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
