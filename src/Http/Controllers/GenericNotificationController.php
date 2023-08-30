<?php

namespace App\GenericNotification\Notification\Http\Controllers;

use App\GenericNotification\Notification\Models\GenericNotification;
use App\GenericNotification\Notification\Services\GenericNotificationService;
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
        $genericNotification = GenericNotification::where('identifier', $identifier)->firstOrFail();

        $genericNotificationService = new GenericNotificationService();
        $genericNotificationService->setOpenAtAndOpenStatus($genericNotification);
        $genericNotificationService->updateOpenCount($genericNotification);
         //Return a transparent pixel image in the email.
         return response()->file(public_path('generic-notification/pixel.png'));
    }
}
