<?php

namespace App\GenericNotification\Notification\Services;

use App\GenericNotification\Notification\Models\GenericNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MarkMailRead
{
    /**
     * handle
     *
     * @param  string $identifier
     * @throws ModelNotFoundException
     * @return void
     */
    public static function handle(string $identifier)
    {
        $genericNotification = GenericNotification::where('identifier', $identifier)->firstOrFail();
        $genericNotification->updateNotificationOpenCount($genericNotification);
    }
}
