<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use Carbon\Carbon;
use App\Enums\RoleEnum;
use Modules\Taxido\Models\Rider;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\RideRequest;
use Modules\Taxido\Models\VehicleType;
use Modules\Taxido\Http\Traits\RideTrait;
use Modules\Taxido\Events\RideRequestEvent;
use Modules\Taxido\Http\Traits\BiddingTrait;
use Modules\Taxido\Enums\ServiceCategoryEnum;
use Prettus\Repository\Eloquent\BaseRepository;
use Modules\Taxido\Enums\RoleEnum as EnumsRoleEnum;

class RideRequestRepository extends BaseRepository
{
    use BiddingTrait, RideTrait;

    public function model()
    {
        return RideRequest::class;
    }

    public function verifyRideWalletBalance($rider_id)
    {
        $rider = Rider::findOrFail($rider_id);
        if ($rider?->wallet?->balance < 0) {
            throw new Exception(__('taxido::static.rides.negative_wallet_balance'), 400);
        }

        return true;
    }

    public function verifyVehicleType($request)
    {
        $vehicleType = VehicleType::where('id',$request->vehicle_type_id)?->whereNull('deleted_at')?->first();
        $selectedVehicleServiceIds = $vehicleType?->services()->pluck('service_id')?->toArray();
        if(!in_array($request?->service_id, $selectedVehicleServiceIds ?? [])) {
            throw new Exception(__('taxido::static.rides.service_not_allow_for_vehicle', ['vehicleType' => $vehicleType?->name]), 400);
        }

        $selectedVehicleServiceCategoryIds = $vehicleType?->service_categories()->pluck('service_category_id')?->toArray();
        if(!in_array($request?->service_id, $selectedVehicleServiceCategoryIds ?? [])) {
            throw new Exception(__('taxido::static.rides.category_not_allow_for_vehicle', ['vehicleType' => $vehicleType?->name]), 400);
        }

        return true;
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $no_of_days = $this->getNoOfDaysAttribute($request?->start_time,$request?->end_time);
            if($this->verifyVehicleType($request)) {
                if ($this->verifyBiddingFairAmount($request)) {
                    $rider_id = $request->rider_id ?? getCurrentUserId();
                    if ($this->verifyRideWalletBalance($rider_id)) {
                        $formattedLocations = $request->locations;
                        $rideRequest = $this->model->create([
                            'rider_id' => $rider_id,
                            'payment_method' => $request->payment_method,
                            'vehicle_type_id' => $request->vehicle_type_id,
                            'service_id' => $request->service_id,
                            'service_category_id' => $request->service_category_id,
                            'rider' => $request->new_rider ?? getCurrentRider(),
                            'ride_fare' => $this->calRideFairAmount($request),
                            'description' => $request->description,
                            'distance' => $request->distance,
                            'distance_unit' => $request->distance_unit,
                            'locations' => $formattedLocations,
                            'location_coordinates' => $request->location_coordinates,
                            'hourly_package_id' => $request->hourly_package_id,
                            'weight' => $request->weight,
                            'parcel_receiver' => $request->parcel_receiver,
                            'parcel_delivered_otp'=> rand(1000, 9999),
                            'start_time' => $request->start_time,
                            'no_of_days' => $no_of_days
                        ]);

                        if ($request->hasFile('cargo_image')) {
                            $attachment = createAttachment();
                            $attachment_id = addMedia($attachment, $request->file('cargo_image'))?->id;
                            $rideRequest->cargo_image_id = $attachment_id;
                            $rideRequest->save();
                            $rideRequest->cargo_image;
                        }

                        $zones = [];
                        if (!is_array($request->location_coordinates)) {
                            throw new Exception(__('taxido::static.rides.location_coordinates_not_array'), 400);
                        }

                        if (count($request->location_coordinates ?? [])) {
                            if($rideRequest?->service_category?->slug != ServiceCategoryEnum::RENTAL) {
                                $coordinate = head($request->location_coordinates);
                                $zones = getZoneByPoint($coordinate['lat'], $coordinate['lng'])?->pluck('id')?->toArray();
                                if (!count($zones)) {
                                    throw new Exception(__('taxido::static.rides.ride_requests_not_accepted'), 400);
                                }

                                $rideRequest?->zones()?->attach($zones);
                                $drivers = getNearestDriversByZoneIds($zones, $request->location_coordinates, $request->vehicle_type_id);

                                if (!count(value: $drivers?->toArray())) {
                                    throw new Exception(__('taxido::static.rides.no_driver_available'), 400);
                                }

                                $rideRequest?->drivers()?->attach($drivers);
                            }
                        }

                        DB::commit();
                        $rideRequest->driver;
                        $rideRequest->vehicle_type;

                        event(new RideRequestEvent($rideRequest));

                        return $rideRequest;
                    }
                }
            }

        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {

            $roleName = getCurrentRoleName();
            $rideRequest = $this->model->FindOrFail($id);
            if ($roleName != RoleEnum::ADMIN && $roleName != EnumsRoleEnum::DRIVER) {
                if ($rideRequest?->created_by_id != getCurrentUserId()) {
                    throw new Exception(__('taxido::static.rides.update_permission'), 400);
                }
            }

            if (isset($request['drivers'])) {
                $rideRequest->drivers()->sync($request['drivers']);
                $rideRequest->categories;
            }

            DB::commit();
            return $rideRequest;
        } catch (Exception $e) {

            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {

            return $this->model->findOrFail($id)->destroy($id);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function accept($request)
    {
        try {

            $driver = getCurrentDriver();
            if($driver) {
                $ride = $this->createRide($request);
                if(!$ride) {
                    throw new Exception(__('taxido::static.bids.failed_to_create_ride'), 500);
                }

                return $ride;
            }

            throw new Exception(__('taxido::static.rides.only_driver_can_accept_ride_request_directly'), 400);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function rental($request)
    {
        DB::beginTransaction();
        try {

            if($this->verifyVehicleType($request)) {
                if ($this->verifyBiddingFairAmount($request)) {
                    $rider_id = $request->rider_id ?? getCurrentUserId();
                    if ($this->verifyRideWalletBalance($rider_id)) {
                        $formattedLocations = $request->locations;
                        $no_of_days = $this->getNoOfDaysAttribute($request->start_time,$request->end_time);
                        $rideRequest = $this->model->create([
                            'rider_id' => $rider_id,
                            'payment_method' => $request->payment_method,
                            'vehicle_type_id' => $request->vehicle_type_id,
                            'service_id' => $request->service_id,
                            'service_category_id' => $request->service_category_id,
                            'rider' => $request->new_rider ?? getCurrentRider(),
                            'ride_fare' => $this->calRideFairAmount($request),
                            'description' => $request->description,
                            'locations' => $formattedLocations,
                            'location_coordinates' => $request->location_coordinates,
                            'is_with_driver' => $request?->is_with_driver,
                            'start_time' => $request->start_time,
                            'end_time' => $request->end_time,
                            'rental_vehicle_id' => $request->rental_vehicle_id,
                            'no_of_days' => $no_of_days,
                        ]);

                        $zones = [];
                        if (!is_array($request->location_coordinates)) {
                            throw new Exception(__('taxido::static.rides.location_coordinates_not_array'), 400);
                        }

                        if (count($request->location_coordinates ?? [])) {
                            $coordinate = head($request->location_coordinates);
                            $zones = getZoneByPoint($coordinate['lat'], $coordinate['lng'])?->pluck('id')?->toArray();
                            if (!count($zones)) {
                                throw new Exception(__('taxido::static.rides.ride_requests_not_accepted'), 400);
                            }
                            $rideRequest?->zones()?->attach($zones);
                        }

                        $driver_id = $rideRequest?->rental_vehicle?->driver_id;
                        $rideRequest?->drivers()?->attach([$driver_id]);

                        DB::commit();
                        event(new RideRequestEvent($rideRequest));

                        return $rideRequest;
                    }
                }
            }

        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function getNoOfDaysAttribute($start_date , $end_date)
    {
        if ($start_date && $end_date) {
            $start = Carbon::parse($start_date);
            $end = Carbon::parse($end_date);
            return $start->diffInDays($end);
        }
        return 0;
    }
}
