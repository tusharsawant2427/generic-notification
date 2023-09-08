<?php

namespace App\GenericNotification\Notification\Listeners;

use App\GenericNotification\Notification\Services\Constants\StatusType;
use Illuminate\Queue\Events\JobProcessed;

class JobProcessedListener extends GenericNotificationHandler
{
    public function handle(JobProcessed $event)
    {
        $this->handleEvent($event, StatusType::SENT);
    }

}
