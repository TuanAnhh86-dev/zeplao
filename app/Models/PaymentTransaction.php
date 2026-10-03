<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id', 'provider', 'callback_type', 'transaction_no', 'txn_ref', 'amount',
        'response_code', 'transaction_status', 'bank_code', 'pay_date', 'signature_valid', 'payload',
    ];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'signature_valid' => 'boolean', 'payload' => 'array'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
