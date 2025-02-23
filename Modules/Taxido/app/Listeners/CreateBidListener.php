<?php

namespace Modules\Taxido\Listeners;

use Exception;
use App\Models\User;
use Modules\Taxido\Events\CreateBidEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Taxido\Notifications\CreateBidNotification;

class CreateBidListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param CreateBidEvent $event
     */
    public function handle(CreateBidEvent $event): void
    {
        try {

            $user = User::find($event->ride->user_id);
            if ($user) {
                $user->notify(new CreateBidNotification($event->ride, $event->bidAmount, $event->driver));
            }

        } catch (Exception $e) {

            //
        }
    }
}
