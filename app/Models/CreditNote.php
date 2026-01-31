<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_note_number',
        'invoice_id',
        'client_id',
        'credit_date',
        'total_amount',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'credit_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($creditNote) {
            if (!$creditNote->credit_note_number) {
                // Generate unique credit note number: AV-YYYYMMDD-XXXX
                $prefix = 'AV-' . date('Ymd') . '-';
                $latest = self::where('credit_note_number', 'like', $prefix . '%')
                    ->orderBy('credit_note_number', 'desc')
                    ->first();

                if ($latest) {
                    $lastNumber = intval(substr($latest->credit_note_number, -4));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $creditNote->credit_note_number = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(CreditNoteItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
