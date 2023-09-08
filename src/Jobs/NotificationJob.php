<?php
namespace App\GenericNotification\Notification\Jobs;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Interfaces\GenericNotifiableInterface;
use App\GenericNotification\Notification\Services\NotificationPersist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info("GenericNotification Notification Job Dispatch: {$this->getNotificationBody()->getUniqueIdentifier()}");
            $notificationPersist = NotificationPersist::persist($this->getNotificationBody());
            Log::info("GenericNotification Notification Job Status: ". $notificationPersist->status);
            $this->sendToQueue($notificationPersist);
        } catch (Throwable $ex) {
            throw $ex;
        }
    }

    /**
     * @return ?string
     */
    public function getUuid(): ?string
    {
        return !empty($this->job) ? $this->job->uuid() : null;
    }

    /**
     * sendToQueue
     *
     * @return void
     */
    abstract protected function sendToQueue(GenericNotification $genericNotification);


    /**
     * getNotificationBody
     *
     * @return GenericNotifiableInterface
     */
    abstract protected function getNotificationBody():GenericNotifiableInterface;
}
