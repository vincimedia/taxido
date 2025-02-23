<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use Modules\Taxido\Models\Bid;
use Modules\Taxido\Models\Ride;
use Illuminate\Support\Facades\DB;
use Modules\Taxido\Enums\RoleEnum;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\RideRequest;
use Modules\Taxido\Enums\BidStatusEnum;
use Modules\Taxido\Http\Traits\RideTrait;
use Modules\Taxido\Http\Traits\BiddingTrait;
use Modules\Taxido\Events\RejectBiddingEvent;
use Modules\Taxido\Events\AcceptBiddingEvent;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class BidRepository extends BaseRepository
{
    use BiddingTrait, RideTrait;

    protected $ride;
    protected $rideRequest;

    function model()
    {
        $this->ride = new Ride();
        $this->rideRequest = new RideRequest();
        return Bid::class;
    }

    public function  boot()
    {
        try {

            $this->pushCriteria(app(RequestCriteria::class));

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        try {

            return $this->model->findOrFail($id);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyBidingAmount($request)
    {
        $rideRequest = RideRequest::where('id', $request?->ride_request_id)?->whereNull('deleted_at')?->first();
        if ($rideRequest) {
            return $this->verifyBiddingFairAmount($rideRequest, $request->amount);
        }

        return false;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {

            if (!(getCurrentRoleName() == RoleEnum::DRIVER)) {
                throw new Exception(__('taxido::static.bids.only_drivers_can_place_bids'), 400);
            }

            if ($this->verifyBidingAmount($request)) {
                $driver_id = $request->driver_id ?? getCurrentUserId();
                if (!$this->isExistsBidAtTime($driver_id, $request->ride_request_id)) {
                    $bid = $this->model->create([
                        'ride_request_id' => $request->ride_request_id,
                        'amount' => $request->amount,
                        'driver_id' => $driver_id
                    ]);

                    DB::commit();
                    return $bid;
                }

                throw new Exception(__('taxido::static.bids.create_next_bid'), 400);
            }

            throw new Exception(__('taxido::static.bids.invalid_bidding_amount'), 400);

        } catch (Exception $e) {

            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $bid = $this->model->findOrFail($id);
            $bid->update(['status' => $request['status']]);
            if (!is_null($bid->status)) {
                DB::commit();
                $bid = $bid->fresh();
                event(new RejectBiddingEvent($bid));
                if ($bid->status == BidStatusEnum::ACCEPTED) {
                    $ride = $this->createRide($request, $bid);
                    event(new AcceptBiddingEvent($ride));
                    if(!$ride) {
                        throw new Exception(__('taxido::static.bids.failed_to_create_ride'), 500);
                    }
                    return $ride;
                }

                return $bid;
            }

            throw new Exception(__('taxido::static.bids.bid_status_already_changed', ['status' => $bid->status]), 403);

        } catch (Exception $e) {

            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

}
