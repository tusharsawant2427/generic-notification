<?php

namespace App\GenericNotification\Notification\Services\Exceptions;

use App\Exceptions\Api\NotFoundException;

class NotificationBodyNotFoundException extends NotFoundException
{
    protected $message = "Notification Body Not Found...";
}
