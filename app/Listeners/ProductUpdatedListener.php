<?php

namespace App\Listeners;

use App\Events\ProductUpdatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class ProductUpdatedListener
{
    /**
     * Create the event listener.
     */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Handle the event.
     */
    public function handle(ProductUpdatedEvent $event): void
    {
       Cache::forget('products_frontend');
       Cache::forget('products_backend');

    }
}
