<?php

namespace App\GenericNotification\Notification\Services\Interfaces;

use App\GenericNotification\Notification\Services\Sms\SmsBody;

interface SmsServiceInterface
{
    public function send(): bool;

    public function getSmsBody(): SmsBody;

    public function getMessageUrl(): string;

    public function getServiceName(): string;

}
