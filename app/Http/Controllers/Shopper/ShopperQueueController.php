<?php

namespace App\Http\Controllers\Shopper;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shopper\ShopperQueueEnterRequest;
use App\Models\Shopper\Status;
use App\Services\Shopper\ShopperService;
use App\Services\Shopper\StatusService;
use App\Services\Store\Location\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShopperQueueController extends Controller
{
    /**
     * @var LocationService
     */
    protected $locationService;

    /**
     * @var ShopperService
     */
    protected $shopperService;

    /**
     * @var StatusService
     */
    protected $statesService;


    /**
     * ShopperQueueController constructor.
     * @param LocationService $locationService
     * @param ShopperService $shopperService
     * @param StatusService $statusService
     */
    public function __construct(
        LocationService $locationService,
        ShopperService $shopperService,
        StatusService $statusService
    )
    {
        $this->locationService = $locationService;
        $this->shopperService = $shopperService;
        $this->statesService = $statusService;
    }

    /**
     * @param ShopperQueueEnterRequest $request
     * @param string $locationUuId
     * @return $this
     */
    public function store(ShopperQueueEnterRequest $request, string $locationUuId){

        $location = $this->locationService->show(
            [
                'uuid' => $locationUuId
            ],
            [
                'Shoppers',
                'Shoppers.Status'
            ]
        );

        try{
            $currentShoppers = collect($location['shoppers'])->where('status.name', 'Active')->count();

            /** If > X people are currently shopping, they should enter the shopping queue as a "pending" shopper. */

            $statusName = Status::Pending;

            /** If < X shoppers are currently actively shopping, the shopper should automatically become active upon check-in. */
            if($location['shopper_limit'] > $currentShoppers){
                $statusName = Status::Active;
            }
            $statusArr = $this->statesService->show(['name' => $statusName]);

            /** entry a shopper */
            $this->shopperService->create(
                [
                    'location_id' => $location['id'],
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'status_id' => $statusArr['id'],
                    'check_in' => date('Y-m-d H:i:s'),
                ]
            );

            return redirect()
                ->route('public.location', ['location' => $locationUuId])
                ->with('status', 'You status in the queue is: ' . $statusArr['name']);
        }
        catch(\Exception $e) {
            Log::error(__METHOD__ .':'. $e->getMessage());

            return redirect()
                ->route('public.location', ['location' => $locationUuId])
                ->withErrors("Something is wrong, please try it again later!");
        }
    }
}
