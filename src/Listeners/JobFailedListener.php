<?php

namespace App\GenericNotification\Notification\Listeners;

use App\GenericNotification\Notification\Services\Constants\StatusType;
use Illuminate\Queue\Events\JobFailed;

class JobFailedListener extends GenericNotificationHandler
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(JobFailed $event)
    {
        $this->handleEvent($event, StatusType::FAILED);
    }
}
