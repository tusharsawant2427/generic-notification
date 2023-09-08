<?php

namespace App\GenericNotification\Notification\Jobs;

use App\GenericNotification\Notification\Jobs\NotificationJob;
use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\Constants\StatusType;
use App\GenericNotification\Notification\Services\Interfaces\GenericNotifiableInterface;
use App\GenericNotification\Notification\Services\Mail\GenericMail;
use App\GenericNotification\Notification\Services\Mail\MailBody;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailJob extends NotificationJob
{
    const JOB_UUID_KEY = "job_uuid";

    protected Mailable $mailable;

    /**
     * @param Mailable $mailable
     */
    public function __construct(Mailable $mailable)
    {
        $this->mailable = $mailable;
    }


    /**
     * sendToQueue
     *
     * @return void
     */
    protected function sendToQueue(GenericNotification $genericNotification)
    {
         /**
         * @var GenericMail $mailable
         */
        $mailable = $this->mailable;

        Mail::to($mailable->getMailBody()->getEmails())
            ->cc($mailable->getMailBody()->getCcEmails())
            ->queue($mailable);

        $genericNotification->updateStatus(StatusType::IN_QUEUE);

        Log::info("GenericNotification Notification Job Status: ". $genericNotification->status);

    }

    /**
     * getNotificationBody
     *
     * @return GenericNotifiableInterface
     */
    protected function getNotificationBody(): GenericNotifiableInterface
    {
        /**
         * @var  MailBody $mailBody
         */
        $mailBody = $this->mailable->getMailBody();
        $mailBody->setHtmlContent($this->mailable->render());
        $mailBody->setData(self::JOB_UUID_KEY, $this->getUuid());
        return $mailBody;
    }

}
