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

    /**
     * notify
     *
     * @param GenericNotifiableInterface $genericNotifiableInterface
     * @throws NotificationBodyNotFoundException
     */
    public static function notify(GenericNotifiableInterface $genericNotifiableInterface)
    {
        if ($genericNotifiableInterface instanceof MailBodyInterface) {
            static::sendMail($genericNotifiableInterface);
        } elseif ($genericNotifiableInterface instanceof SmsBodyInterface) {
            static::sendSms($genericNotifiableInterface);
        } else {
            throw new NotificationBodyNotFoundException();
        }
    }

    /**
     * sendMail.
     *
     * @param MailBodyInterface $mailBody
     * @throws Exception
     */
    private static function sendMail(MailBodyInterface $mailBody)
    {
        try {
            MailJob::dispatch(new GenericMail($mailBody));
        } catch (Throwable $exception) {
            Log::error($exception->getTraceAsString());
            throw $exception;
        }
    }

    /**
     * sendSms
     *
     * @param SmsBodyInterface $smsBody
     * @throws Throwable
     */
    private static function sendSms(SmsBodyInterface $smsBody)
    {
        try {
            SmsJob::dispatch(new GenericSms($smsBody));
        } catch (Throwable $exception) {
            Log::error($exception->getTraceAsString());
            throw $exception;
        }
    }
}
