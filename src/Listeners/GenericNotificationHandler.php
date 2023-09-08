<?php

namespace App\GenericNotification\Notification\Listeners;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class GenericNotificationHandler
{
    protected function handleEvent($event, int $statusType)
    {
        $genericNotification = $this->getGenericNotification($event);

        if ($genericNotification) {
            $data = $genericNotification->data;
            if ($statusType === StatusType::FAILED) {
                $data['exception'] = $this->getExceptionBody($event->exception);
            }
            $genericNotification->updateStatus(status: $statusType, data: $data);

            Log::info("GenericNotification Notification Job Status: " . $genericNotification->status);
        }
    }

    /**
     * getGenericNotification
     *
     * @param  mixed $event
     * @return ?GenericNotification
     */
    public function getGenericNotification($event): ?GenericNotification
    {
        $uuid = $event->job->uuid();
        return GenericNotification::findByJobUuid($uuid);

    }

    /**
     * getExceptionBody
     *
     * @param  Throwable $exception
     * @return array<string,string>
     */
    private function getExceptionBody(Throwable $exception): array
    {
        return [
            'message' => $exception->getMessage(),
            'file_path' => $exception->getFile(),
            'line_no' => $exception->getLine(),
        ];
    }
}
