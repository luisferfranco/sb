<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $fillable = [
        'user_id',
        'prop_id',
        'group_id',
        'option',
        'points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
