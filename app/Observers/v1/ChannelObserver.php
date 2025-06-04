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

    /**
     * Handle the Channel "updated" event.
     */
    public function updated(Channel $channel): void
    {
        //
    }

    /**
     * Handle the Channel "deleted" event.
     */
    public function deleted(Channel $channel): void
    {
        //
    }

    /**
     * Handle the Channel "restored" event.
     */
    public function restored(Channel $channel): void
    {
        //
    }

    /**
     * Handle the Channel "force deleted" event.
     */
    public function forceDeleted(Channel $channel): void
    {
        //
    }
}
