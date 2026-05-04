<?php

namespace App\Providers;

use App\Events\NewDeviceLogin;
use App\Listeners\SendNewDeviceNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewDeviceLogin::class => [
            SendNewDeviceNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
