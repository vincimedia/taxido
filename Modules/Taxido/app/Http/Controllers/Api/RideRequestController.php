<?php

namespace Modules\Taxido\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Modules\Taxido\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\RideRequest;
use Illuminate\Database\Eloquent\Builder;
use Modules\Taxido\Http\Requests\Api\AcceptRideRequest;
use Modules\Taxido\Http\Requests\Api\CreateRideRequest;
use Modules\Taxido\Http\Requests\Api\UpdateRideRequest;
use Modules\Taxido\Repositories\Api\RideRequestRepository;
use Modules\Taxido\Http\Requests\Api\CreateRentalRideRequest;

class RideRequestController extends Controller
{
    public $repository;

    public function  __construct(RideRequestRepository $repository)
    {
        $this->authorizeResource(RideRequest::class, 'rideRequest');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $rideRequests = $this->repository->whereNull('deleted_at');
            $rideRequests = $this->filter($rideRequests, $request);
            return $rideRequests->latest('created_at')->paginate($request->paginate ?? $rideRequests->count());

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRideRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
       //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRideRequest $request, RideRequest $rideRequest)
    {
        return $this->repository->update($request->all(), $rideRequest->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }

    public function accept(AcceptRideRequest $request)
    {
        return $this->repository->accept($request);
    }

    public function filter($rideRequests, $request)
    {
        $roleName = getCurrentRoleName();
        if ($roleName == RoleEnum::RIDER) {
            $rideRequests = $rideRequests->where('rider_id', getCurrentRider()?->id);
        }

        if ($roleName == RoleEnum::DRIVER) {
            $rideRequests = $rideRequests->whereHas('drivers', function (Builder $query) {
                $query->where('driver_id', getCurrentUserId());
            });
        }

        return $rideRequests;
    }

    public function rental(CreateRentalRideRequest $request)
    {
        return $this->repository->rental($request);
    }

}
