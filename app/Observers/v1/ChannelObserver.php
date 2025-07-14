<?php

namespace App\Observers\v1;

use App\Models\Channel;

class ChannelObserver
{
    /**
     * Handle the Channel "created" event.
     */
    public function creating(Channel $channel): void
    {
        $channel->user_id ?? auth()->id();
    }
}
