<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\PayrollStatusUpdated;
use App\Listeners\SendPayrollStatusNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event-to-listener mappings for the application.
     */
    protected $listen = [
        PayrollStatusUpdated::class => [
        SendPayrollStatusNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
