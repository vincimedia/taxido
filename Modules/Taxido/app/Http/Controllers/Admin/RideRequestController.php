<?php

namespace Modules\Taxido\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Taxido\Http\Requests\Admin\CreateRideRequest;
use Modules\Taxido\Models\RideRequest;
use Modules\Taxido\Tables\RideRequestTable;
use App\Http\Controllers\Controller;
use Modules\Taxido\Enums\RideStatusEnum;
use Modules\Taxido\Repositories\Admin\RideRequestRepository;


class RideRequestController extends Controller
{
    private $repository;

    public function __construct(RideRequestRepository $repository)
    {
        // $this->authorizeResource(RideRequest::class, 'ride');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RideRequestTable $rideRequestTable)
    {
        return $this->repository->index($rideRequestTable->generate());
    }

    public function create()
    {
        return view('taxido::admin.ride.create');
    }

    public function store(CreateRideRequest $request)
    {
        return $this->repository->store($request);
    }

    public function details(Request $request)
    {
        return $this->repository->details($request->id);
    }

}
