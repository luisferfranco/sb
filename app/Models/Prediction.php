<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $fillable = [
        'ticket_id',
        'prop_id',
        'group_id',
        'option',
        'points',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function prop()
    {
        return $this->belongsTo(Prop::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
