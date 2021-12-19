<?php

namespace App\Http\Controllers\Store\Location;

use App\Events\LocationCheckOutEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\Location\LocationCreateRequest;
use App\Http\Requests\Store\Location\LocationQueueRequest;
use App\Http\Requests\Store\Location\LocationStoreRequest;
use App\Models\Shopper\Status;
use App\Models\Store\Location\Location;
use App\Services\Shopper\ShopperService;
use App\Services\Shopper\StatusService;
use App\Services\Store\Location\LocationService;

/**
 * Class LocationController
 * @package App\Http\Controllers\Store
 */
class LocationController extends Controller
{
    /**
     * @var LocationService
     */
    protected $location;

    /**
     * @var ShopperService
     */
    protected $shopper;

    /**
     * @var StatusService
     */
    protected $status;


    /**
     * LocationController constructor.
     * @param LocationService $location
     * @param ShopperService $shopper
     * @param StatusService $status
     */
    public function __construct(LocationService $location, ShopperService $shopper, StatusService $status)
    {
        $this->location = $location;
        $this->shopper = $shopper;
        $this->status = $status;
    }

    /**
     * @param Location $location
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function public(Location $location)
    {
        return view('stores.location.public')
            ->with('location', $location);
    }

    /**
     * @param LocationCreateRequest $request
     * @param string $storeUuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(LocationCreateRequest $request, string $storeUuid)
    {
        return view('stores.location.create')
            ->with('store', $storeUuid);
    }

    /**
     * @param LocationStoreRequest $request
     * @param string $storeUuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LocationStoreRequest $request, string $storeUuid): \Illuminate\Http\RedirectResponse
    {
        $this->location->create([
            'location_name' => $request->location_name,
            'shopper_limit' => $request->shopper_limit,
            'store_id' => $storeUuid
        ]);

        return redirect()->route('store.store', ['store' => $storeUuid]);
    }


    /**
     * @param LocationCreateRequest $request
     * @param string $storeUuid
     * @param string $locationUuid
     * @return $this
     */
    public function edit(LocationCreateRequest $request, string $storeUuid, string $locationUuid){


        $location = $this->location->show(
            [
                'uuid' => $locationUuid
            ]
        );

        return view('stores.location.edit')->with(
            [
                'store' => $storeUuid,
                'location' => $location
            ]
        );
    }

    /**
     * @param LocationStoreRequest $request
     * @param string $storeUuid
     * @param string $locationUuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(LocationStoreRequest $request, string $storeUuid, string $locationUuid): \Illuminate\Http\RedirectResponse{

        $location = $this->location->show(
            [
                'uuid' => $locationUuid
            ]
        );

        $this->location->update(
            $location['id'],
            [
                'shopper_limit' => $request->shopper_limit,
            ]
        );

        return redirect()->route('store.store', ['store' => $storeUuid]);
    }

    /**
     * @param LocationQueueRequest $request
     * @param string $storeUuid
     * @param string $locationUuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function queue(LocationQueueRequest $request, string $storeUuid, string $locationUuid)
    {
        $location = $this->location->show(
            [
                'uuid' => $locationUuid
            ],
            [
                'Shoppers',
                'Shoppers.Status'
            ]
        );

        $shoppers = null;

        if( isset($location['shoppers']) && count($location['shoppers']) >= 1 ){
            $shoppers = $this->location->getShoppers($location['shoppers']);
        }

        return view('stores.location.queue')
            ->with('location', $location)
            ->with('storeUuid', $storeUuid)
            ->with('shoppers', $shoppers);
    }

    /**
     * @param LocationQueueRequest $request
     * @param string $storeUuid
     * @param string $locationUuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkOut(LocationQueueRequest $request, string $storeUuid, string $locationUuid){

        $location = $this->location->show(
            [
                'uuid' => $locationUuid
            ]
        );

        $shopper = $this->shopper->show(
            [
                'uuid' => $request->shopper_id
            ]
        );

        $status = $this->status->show(
            [
                'name' => Status::Completed
            ]
        );


        $this->shopper->update(
            $shopper['id'],
            [
                'status_id' => $status['id'],
                'check_out' => date('Y-m-d H:i:s')
            ]
        );

        /** invoke event */
        LocationCheckOutEvent::dispatch($location);

        return back();
    }
}
