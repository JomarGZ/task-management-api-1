<?php

namespace App\Observers\v1;

use App\Enums\ChatTypeEnum;
use App\Models\Channel;
use App\Models\Tenant;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        $generalChannel = Channel::firstOrCreate(
            [
                'type' => ChatTypeEnum::GENERAL->value,
                'tenant_id' => $user->tenant_id
            ],
            [
                'name' => 'General',
                'description' => 'General channel for all users',
                'user_id' => $user->id
            ]
        );
        $generalChannel->participants()->attach($user->id, ['tenant_id' => $user->tenant_id]);
    }
}
