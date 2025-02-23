<?php

namespace Modules\Taxido\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Modules\Taxido\Repositories\Api\DriverRepository;

class DriverController extends Controller
{
    public $repository;

    public function __construct(DriverRepository $repository)
    {
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

            $drivers = $this->repository->whereNull('deleted_at');
            $drivers = $this->filter($drivers, $request);
            return $drivers->latest('created_at')->paginate($request->paginate ?? $drivers->count());

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function driverZone(Request $request)
    {
        return $this->repository->driverZone($request);
    }

    public function getDrivers(Request $request)
    {
        return $this->repository->getDrivers($request);
    }

    public function filter($drivers,$request)
    {
        if ($request->zones) {
            $zoneIds = explode(',', $request->zones);
            $drivers  = $drivers->whereHas('zones', function (Builder $query) use ($zoneIds) {
                    $query->whereIn('zone_id', $zoneIds);
                });
        }

        if ($request->is_online) {
            $drivers  = $drivers->where('is_online', $request->is_online);
        }

        if ($request->is_on_ride) {
            $drivers  = $drivers->where('is_on_ride', $request->is_on_ride);
        }

        return $drivers;
    }

}
