<?php

namespace Modules\Taxido\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Taxido\Models\Ride;
use Modules\Taxido\Tables\RideTable;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Modules\Taxido\Enums\RideStatusEnum;

use Modules\Taxido\Repositories\Admin\RideRepository;

class RideController extends Controller
{
    private $repository;

    public function __construct(RideRepository $repository)
    {
        $this->authorizeResource(Ride::class, 'ride');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RideTable $rideTable)
    {
        return $this->repository->index($rideTable->generate());
    }

    public function getRequestedRide(RideTable $rideTable)
    {
        request()->merge(['status' => RideStatusEnum::REQUESTED]);
        return $this->repository->getRequestedRide($rideTable->generate());
    }

    public function getScheduledRide(RideTable $rideTable)
    {
        request()->merge(['status' => RideStatusEnum::SCHEDULED]);
        return $this->repository->getRequestedRide($rideTable->generate());
    }

    public function getArrivedRide(RideTable $rideTable)
    {
        request()->merge(['status' => RideStatusEnum::ARRIVED]);
        return $this->repository->getRequestedRide($rideTable->generate());
    }

    public function getAcceptedRide(RideTable $rideTable)
    {
        request()->merge(['status' => RideStatusEnum::ACCEPTED]);
        return $this->repository->getRequestedRide($rideTable->generate());
    }

    public function getStartedRide(RideTable $rideTable)
    {
        request()->merge(['status' => RideStatusEnum::STARTED]);
        return $this->repository->getRequestedRide($rideTable->generate());
    }

    public function getCancelledRide(RideTable $rideTable)
    {
        request()->merge(['status' => RideStatusEnum::CANCELLED]);
        return $this->repository->getRequestedRide($rideTable->generate());
    }

    public function getCompletedRide(RideTable $rideTable)
    {
        request()->merge(['status' => RideStatusEnum::COMPLETED]);
        return $this->repository->getRequestedRide($rideTable->generate());
    }

    public function details(Request $request)
    {
        return $this->repository->details($request->ride_number);
    }

    public function export(Request $request)
    {
        return $this->repository->export($request);
    }

}
