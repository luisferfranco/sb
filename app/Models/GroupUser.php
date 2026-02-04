<?php

namespace App\Models;

use App\Enums\GroupMemberStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    protected $table = 'group_user';

    protected $casts = [
        'status' => GroupMemberStatus::class,
    ];
}
