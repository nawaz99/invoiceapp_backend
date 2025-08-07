<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'client_id',
        'invoice_number',
        'user_invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax',
        'total',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function company()
    {
        return $this->belongsTo(CompanySetting::class);
    }
}
