<?php

namespace App\GenericNotification\Notification\Services;

use App\GenericNotification\Notification\Jobs\MailJob;
use App\GenericNotification\Notification\Jobs\SmsJob;
use App\GenericNotification\Notification\Services\Exceptions\NotificationBodyNotFoundException;
use App\GenericNotification\Notification\Services\Interfaces\GenericNotifiableInterface;
use App\GenericNotification\Notification\Services\Interfaces\MailBodyInterface;
use App\GenericNotification\Notification\Services\Interfaces\SmsBodyInterface;
use App\GenericNotification\Notification\Services\Mail\GenericMail;
use App\GenericNotification\Notification\Services\Sms\GenericSms;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationService
{

    public GenericNotifiableInterface $genericNotifiableInterface;

    public function __construct(GenericNotifiableInterface $genericNotifiableInterface)
    {
        $this->genericNotifiableInterface = $genericNotifiableInterface;
    }

    /**
     * notify
     *
     * @return bool
     * @throws NotificationBodyNotFoundException
     */
    public function notify(): bool
    {
        if ($this->genericNotifiableInterface instanceof MailBodyInterface) {
            return $this->sendMail($this->genericNotifiableInterface);
        } elseif ($this->genericNotifiableInterface instanceof SmsBodyInterface) {
            return $this->sendSms($this->genericNotifiableInterface);
        } else {
            throw new NotificationBodyNotFoundException();
        }
    }

    /**
     * sendMail
     *
     * @param  MailBodyInterface $mailBody
     * @return bool
     * @throws Exception
     */
    public function sendMail(MailBodyInterface $mailBody): bool
    {
        try {
            MailJob::dispatch(new GenericMail($mailBody));
        } catch (Throwable $exception) {
            Log::error($exception->getTraceAsString());
            throw $exception;
        }
        return true;
    }

    /**
     * sendSms
     *
     * @param  SmsBodyInterface $smsBody
     * @return bool
     * @throws Throwable
     */
    public function sendSms(SmsBodyInterface $smsBody): bool
    {
        try {
            SmsJob::dispatch(new GenericSms($smsBody));
        } catch (Throwable $exception) {
            Log::error($exception->getTraceAsString());
            throw $exception;
        }
        return true;
    }
}
