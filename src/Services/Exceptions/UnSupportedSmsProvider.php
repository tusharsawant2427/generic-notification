<?php
namespace App\GenericNotification\Notification\Services\Exceptions;

use Exception;

class UnSupportedSmsProvider extends Exception
{
    protected $message = "Unsupported SMS provider...";
}
