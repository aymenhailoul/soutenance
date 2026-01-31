<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'credit_note_id',
        'invoice_item_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute()
    {
        return ($this->unit_price * $this->quantity) - $this->discount;
    }
}
