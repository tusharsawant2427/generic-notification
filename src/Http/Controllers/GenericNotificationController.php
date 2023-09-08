<?php

namespace App\GenericNotification\Notification\Http\Controllers;

use App\GenericNotification\Notification\Services\MarkMailRead;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GenericNotificationController extends Controller
{
    /**
     * trackEmail
     *
     * @param  string $identifier
     * @throws ModelNotFoundException
     * @return BinaryFileResponse
     */
    public function trackEmail(string $identifier): BinaryFileResponse
    {
        MarkMailRead::handle($identifier);
         //Return a transparent pixel image in the email.
        return response()->file(public_path('generic-notification/pixel.png'));
    }
}
