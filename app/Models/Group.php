<?php

namespace App\Models;

use App\Enums\GroupStatus;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'slug',
        'event_id',
        'status',
        'published',
        'accepting',
    ];

    protected $casts = [
        'status'    => GroupStatus::class,
        'published' => 'boolean',
        'accepting' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($group) {
            $group->slug = Str::slug($group->name);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->using(GroupUser::class)
            ->withTimestamps()
            ->withPivot('status');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
