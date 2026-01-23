<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'invoice_date',
        'total_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                // Generate unique invoice number: INV-YYYYMMDD-XXXX
                $prefix = 'INV-' . date('Ymd') . '-';
                $latest = self::where('invoice_number', 'like', $prefix . '%')
                    ->orderBy('invoice_number', 'desc')
                    ->first();

                if ($latest) {
                    $lastNumber = intval(substr($latest->invoice_number, -4));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $invoice->invoice_number = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
