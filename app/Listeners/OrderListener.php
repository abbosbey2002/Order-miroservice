<?php

namespace App\Listeners;

use App\Events\OrderEvent;
use App\Jobs\CreateOrderEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderEvent $event): void
    {
        CreateOrderEmailNotification::dispatch($event->order);
    }
}
