<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

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
    return $this->belongsToMany(User::class)->withTimestamps()->withPivot('status');
  }
}
