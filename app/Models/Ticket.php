<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'name',
        'group_id',
        'user_id',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }

    public function props()
    {
        return $this->group->event->props();
    }
}
