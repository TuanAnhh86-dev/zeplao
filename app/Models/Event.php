<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'city',
        'city_key',
        'venue',
        'starts_at',
        'cover_image',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }
}
