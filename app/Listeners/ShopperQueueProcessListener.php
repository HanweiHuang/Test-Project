<?php

namespace App\Listeners;

use App\Events\LocationCheckOutEvent;
use App\Processes\Store\Location\ShopperQueue\ProcessQueue\ShopperQueueProcess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ShopperQueueProcessListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LocationCheckOutEvent  $event
     * @return void
     */
    public function handle(LocationCheckOutEvent $event)
    {
        $location = $event->location;

        try{

            $process = app()->make(ShopperQueueProcess::class);
            $process->run([ 'location_id' => $location['id'] ]);

        }catch(\Exception $e){
            \Log::error(__METHOD__ .':'. $e->getMessage());
        }
    }
}
