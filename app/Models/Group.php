<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'slug',
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
}
