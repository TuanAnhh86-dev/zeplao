<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketType extends Model
{
    protected $fillable = [
        'name',
        'price',
        'quantity',
        'sold',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'quantity' => 'integer',
            'sold' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
