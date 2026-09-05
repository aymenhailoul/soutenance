<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'ice',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'notes',
    ];

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function assignments()
    {
        return $this->hasMany(EquipmentAssignment::class);
    }
}
