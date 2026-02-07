<?php

namespace Database\Seeders;

use App\Enums\GroupMemberStatus;
use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group = Group::create([
            'name' => 'La Quiniela',
            'slug' => 'la-quiniela',
            'description' => 'El grupo de siempre, para los props del SB',
            'owner_id' => 1,
        ]);
        $group->members()->attach(1, ['status' => GroupMemberStatus::APPROVED->value]);
    }
}
