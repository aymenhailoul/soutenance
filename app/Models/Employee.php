<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'cin',
        'salary',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'salary' => 'decimal:2',
    ];
}
