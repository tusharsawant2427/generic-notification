<?php

namespace App\GenericNotification\Notification\Services;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Interfaces\GenericNotifiableInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GenericNotificationService
{
    /**
     * store
     *
     * @param  GenericNotifiableInterface $genericNotifiable
     * @return GenericNotification
     */
    public function store(GenericNotifiableInterface $genericNotifiable): GenericNotification
    {
        try {
            $genericNotificationData = $this->prepareData($genericNotifiable);
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
    private function prepareData(GenericNotifiableInterface $genericNotifiable): array
    {
        return [
            'identifier' => $genericNotifiable->getUniqueIdentifier(),
            'type' => $genericNotifiable->getType(),
            'medium' => $genericNotifiable->getMedium(),
            'data' => json_encode($genericNotifiable->getData()),
            'sent_at' => Carbon::now(),
            'status' => $genericNotifiable->getStatus(),
            'created_by' => (Auth::check()==true && !empty(Auth::user())) ? Auth::user()->id : null
        ];
    }

    /**
     * setOpenAtAndOpenStatus
     *
     * @param  GenericNotification $genericNotification
     * @return bool
     */
    public function setOpenAtAndOpenStatus(GenericNotification $genericNotification): bool
    {
        return $genericNotification->updateOpenAtAndOpenStatus();
    }


    /**
     * setOpenAt
     *
     * @param  GenericNotification $genericNotification
     * @return bool
     */
    public function updateOpenCount(GenericNotification $genericNotification): bool
    {
        return $genericNotification->incrementOpenCount();
    }

     /**
     * setOpenAt
     *
     * @param  GenericNotification $genericNotification
     * @return bool
     */
    public function updateStatusEmailFailed(GenericNotification $genericNotification): bool
    {
        return $genericNotification->incrementOpenCount();
    }
}
