<?php

namespace App\Console\Commands;

use App\Models\Shopper\Shopper;
use App\Models\Shopper\Status;
use App\Processes\Store\Location\ShopperQueue\ProcessQueue\ShopperQueueProcess;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ShopperAutoComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopper-auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'shopper completed after a period of time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** get date by Model instead of Repository */
        $statusActive = Status::where('name', Status::Active)->first()->id;

        $statusCompleted = Status::where('name', Status::Completed)->first()->id;

        $shoppers = Shopper::where('status_id', $statusActive)
            ->where('check_in', '<',  now()->subHour(2))->get();


        if(!empty($shoppers)){
            try{
                foreach ( $shoppers as $shopper){
                    $shopper->check_out = date('Y-m-d H:i:s');
                    $shopper->status_id = $statusCompleted;
                    $shopper->save();

                    Log::info('Shopper Completed:'.$shopper['id']);
                    /**  invoke queue process */
                    $process = app()->make(ShopperQueueProcess::class);
                    $process->run([ 'location_id' => $shopper['location_id'] ]);
                }
            }catch(\Exception $e){

                Log::error(__METHOD__ . $e->getMessage());
            }
        }
    }
}
