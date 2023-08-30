<?php

use App\GenericNotification\Notification\Http\Controllers\GenericNotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/track/email/{unique_identifier}/pixel.png', [GenericNotificationController::class, 'trackEmail'])->name("track.email");
