<?php

namespace App\GenericNotification\Notification\Services;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Interfaces\GenericNotifiableInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationPersist
{

    /**
     * persist
     *
     * @param  GenericNotifiableInterface $genericNotifiable
     * @return GenericNotification
     */
    public static function persist(GenericNotifiableInterface $genericNotifiable): GenericNotification
    {
        try {
            $genericNotificationData = static::prepareData($genericNotifiable);
            return GenericNotification::persistCreateGenericNotification($genericNotificationData);
        } catch (Exception $ex) {
            Log::error($ex->getTraceAsString());
            throw $ex;
        }
    }

    /**
     * prepareData
     *
     * @param  GenericNotifiableInterface $genericNotifiable
     * @return array<string,mixed>
     */
    private static function prepareData(GenericNotifiableInterface $genericNotifiable): array
    {
        return [
            'identifier' => $genericNotifiable->getUniqueIdentifier(),
            'type' => $genericNotifiable->getType(),
            'medium' => $genericNotifiable->getMedium(),
            'data' => $genericNotifiable->getData(),
            'sent_at' => Carbon::now(),
            'status' => $genericNotifiable->getStatus(),
            'created_by' => (Auth::check() == true && !empty(Auth::user())) ? Auth::user()->id : null
        ];
    }
}
