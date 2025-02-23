<?php

namespace Modules\Taxido\Repositories\Admin;

use Exception;
use Modules\Taxido\Models\Ride;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Taxido\Exports\RidesExport;
use Prettus\Repository\Eloquent\BaseRepository;
use Modules\Taxido\Models\RideRequest;
use Modules\Taxido\Enums\ServiceCategoryEnum;


class RideRepository extends BaseRepository
{
    protected $rideRequest;

    function model()
    {
        $this->rideRequest = new RideRequest();
        return Ride::class;
    }

    public function index($rideTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride.index', ['tableConfig' => $rideTable]);
    }

    public function getRequestedRide($rideTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride.index', ['tableConfig' => $rideTable]);
    }

    public function getScheduledRide($rideTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride.index', ['tableConfig' => $rideTable]);
    }

    public function getAcceptedRide($rideTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride.index', ['tableConfig' => $rideTable]);
    }

    public function getRejectedRide($rideTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride.index', ['tableConfig' => $rideTable]);
    }

    public function getArrivedRide($rideTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride.index', ['tableConfig' => $rideTable]);
    }

    public function getStartedRide($rideTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride.index', ['tableConfig' => $rideTable]);
    }

    public function getCancelledRide($rideTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride.index', ['tableConfig' => $rideTable]);
    }

    public function getCompletedRide($rideTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride.index', ['tableConfig' => $rideTable]);
    }

    public function details($ride_number)
    {
        try {

            $ride = $this->model->with(['commission_history','coupon'])->where('ride_number', $ride_number)?->first();
            if ($ride) {
                return view('taxido::admin.ride.details', ['ride' => $ride]);
            }

            throw new Exception("Ride not exists", 404);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function export($request)
    {
        try {

            $format = $request->get('format', 'csv');
            switch ($format) {
                case 'excel':
                    return $this->exportExcel();
                case 'csv':
                default:
                    return $this->exportCsv();
            }
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public  function exportExcel()
    {
        return Excel::download(new RidesExport, 'rides.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new RidesExport, 'rides.csv');
    }
}
