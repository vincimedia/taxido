<?php

namespace Modules\Taxido\Repositories\Admin;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\RideRequest;
use Modules\Taxido\Http\Traits\RideTrait;
use Modules\Taxido\Enums\ServiceCategoryEnum;
use Prettus\Repository\Eloquent\BaseRepository;

class RideRequestRepository extends BaseRepository
{
    use  RideTrait;

    protected $rideRequest;

    function model()
    {
        return RideRequest::class;
    }

    public function index($rideRequestTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.ride-request.index', ['tableConfig' => $rideRequestTable]);
    }

    public function details($id)
    {
        try {

            $rideRequest = $this->model->where('id', $id)?->first();
            if ($rideRequest) {
                return view('taxido::admin.ride-request.details', ['rideRequest' => $rideRequest]);
            }

            throw new Exception("Ride not exists", 404);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {

            $packageId = getServiceCategoryIdBySlug(ServiceCategoryEnum::PACKAGE);
            $rentalId = getServiceCategoryIdBySlug(ServiceCategoryEnum::RENTAL);
            $locations = $request->service_category_id == $rentalId || $request->service_category_id == $packageId
                ? $request->rental_locations
                : $request->locations;

            if (empty($locations)) {
                throw new Exception("taxido::static.rides.locations_empty", 404);
            }

            $location_coordinates = $request->service_category_id == $rentalId || $request->service_category_id == $packageId
                ? $request->rental_location_coordinates
                : $request->location_coordinates;

            if (empty($location_coordinates)) {
                throw new Exception("taxido::static.rides.location_coordinates_empty", 404);
            }

            $origin = "{$location_coordinates[0]['lat']},{$location_coordinates[0]['lng']}";
            $lastIndex = count($location_coordinates) - 1;
            $destination = "{$location_coordinates[$lastIndex]['lat']},{$location_coordinates[$lastIndex]['lng']}";
            $distance = calculateRideDistance($origin, $destination);
            $request?->merge([
                'distance' => $distance['distance_value'],
                'distance_unit' => $distance['distance_unit']
            ]);
            $rider = getRiderById($request?->rider_id);
            $rideRequest = $this->model->create([
                'rider_id' => $request?->rider_id,
                'rider' => $rider,
                'payment_method' => $request?->payment_method,
                'vehicle_type_id' => $request?->vehicle_type_id,
                'service_id' => $request?->service_id,
                'service_category_id' => $request?->service_category_id,
                'ride_fare' => $this->calRideFairAmount($request),
                'description' => $request?->description,
                'distance' => $request?->distance,
                'distance_unit' => $request?->distance_unit,
                'locations' => $locations,
                'location_coordinates' => $location_coordinates,
                'hourly_package_id' => $request?->hourly_package_id,
                'weight' => $request?->weight,
                'parcel_delivered_otp' => rand(1000, 9999),
                'cargo_image_id' => $request?->cargo_image_id,
                'rental_vehicle_id' => $request?->rental_vehicle_id,
                'start_time' => $request?->start_time,
                'end_time' => $request?->end_time,
                'parcel_receiver' => $request->parcel_receiver,
            ]);
            $rideRequest?->cargo_image;
            $zones = [];
            if (!is_array($location_coordinates)) {
                throw new Exception(__('taxido::static.rides.location_coordinates_not_array'), 400);
            }

            if (count($location_coordinates ?? [])) {
                $coordinate = head($location_coordinates);
                $zones = getZoneByPoint($coordinate['lat'], $coordinate['lng'])?->pluck('id')?->toArray();
                if (!count($zones)) {
                    throw new Exception(__('taxido::static.rides.ride_requests_not_accepted'), 400);
                }
                $rideRequest?->zones()?->attach($zones);
                $rideRequest?->drivers()?->attach($request?->drivers);
            }

            DB::commit();

            $rideRequest->driver;
            $rideRequest->vehicle_type;

            return to_route('admin.ride-request.index')->with('success', __('taxido::static.rides.ride_request_create_successfully'));

        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
