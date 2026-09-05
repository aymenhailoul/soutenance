<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'code',
        'address',
        'city',
        'contact_name',
        'contact_phone',
        'notes',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    public function assignments()
    {
        return $this->hasMany(EquipmentAssignment::class);
    }
}
