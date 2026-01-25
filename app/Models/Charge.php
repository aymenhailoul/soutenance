<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    protected $fillable = [
        'amount',
        'motif',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
