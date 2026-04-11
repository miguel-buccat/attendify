<?php

namespace App\Jobs;

use App\Models\Invitation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpireStaleInvitations implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Invitation::whereNull('accepted_at')
            ->where('expires_at', '<', now())
            ->delete();
    }
}
