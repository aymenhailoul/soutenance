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
        'type',
        'client_id',
        'vehicle_id',
        'kilometrage',
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
                // Generate unique invoice number based on type
                // FACT-SERV-XXXXXX for services
                // FACT-PROD-XXXXXX for products
                $typePrefix = $invoice->type === 'service' ? 'SERV' : 'PROD';
                $prefix = 'FACT-' . $typePrefix . '-';
                
                // Find the maximum numeric suffix for invoices of this type
                $maxNumber = self::where('invoice_number', 'like', $prefix . '%')
                    ->get()
                    ->map(function ($inv) {
                        $parts = explode('-', $inv->invoice_number);
                        return intval(end($parts));
                    })
                    ->max() ?? 0;

                $newNumber = $maxNumber + 1;

                // Use minimum 6 digits, but allow more if needed
                $invoice->invoice_number = $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
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

    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
