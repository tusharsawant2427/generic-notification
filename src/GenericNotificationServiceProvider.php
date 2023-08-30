<?php

namespace App\GenericNotification\Notification;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class GenericNotificationServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\GenericNotification\Notification';

    public function boot()
    {
        // Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        Route::middleware('web')
        ->namespace($this->namespace)
        ->group(__DIR__.'/Routes/web.php');

        $this->publishes([
            __DIR__.'/Config/gn24x7sms.php' => config_path('gn24x7sms.php'),
            __DIR__.'/public/pixel.png' => public_path('generic-notification/pixel.png'),
        ], 'generic-notification-config');
    }

    public function register()
    {
        // ...
    }
}
