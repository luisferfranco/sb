<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prop extends Model
{
    protected $fillable = [
        'event_id',
        'description',
        'line',
        'is_over',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
