<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'disk',
        'size',
        'type',
        'status',
        'completed_at',
        'error_message',
        'user_id',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'size' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
