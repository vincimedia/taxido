<?php

namespace Modules\Taxido\Http\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Taxido\Models\Bid;
use Modules\Taxido\Models\Ride;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\RideRequest;
use Modules\Taxido\Enums\ServicesEnum;
use Modules\Taxido\Models\RideStatus;
use Modules\Taxido\Models\VehicleType;
use Modules\Taxido\Enums\BidStatusEnum;
use Modules\Taxido\Enums\RideStatusEnum;
use Modules\Taxido\Models\HourlyPackage;
use Modules\Taxido\Models\RentalVehicle;
use Modules\Taxido\Enums\ServiceCategoryEnum;

trait RideTrait
{
    use BiddingTrait;

    public function calRideFairAmount($request)
    {
        $settings        = getTaxidoSettings();
        $service         = getServiceById($request->service_id);
        $serviceCategory = getServiceCategoryById($request->service_category_id);
        $vehicleType     = VehicleType::where('id', $request->vehicle_type_id)?->whereNull('deleted_at')?->first();

        if ($service?->slug == ServicesEnum::CAB || $service?->slug == ServicesEnum::FREIGHT) {
            if (in_array($serviceCategory?->slug, [
                ServiceCategoryEnum::RIDE,
                ServiceCategoryEnum::SCHEDULE,
                ServiceCategoryEnum::INTERCITY,
            ]) || $service?->slug == ServicesEnum::FREIGHT) {
                if (! ((int) $settings['activation']['bidding']) || ! ($request->expectsJson())) {
                    $distanceCharges = $this->calDistanceMinMaxCharges($request, $vehicleType);
                    if ($vehicleType?->base_amount >= $distanceCharges['min_distance_charge']) {
                        return $vehicleType?->base_amount;
                    }

                    return $distanceCharges['min_distance_charge'];
                }
            }

            if ($serviceCategory?->slug == ServiceCategoryEnum::PACKAGE) {
                if (! (int) $settings['activation']['bidding'] || ! ($request->expectsJson())) {
                    $hourlyPackage = HourlyPackage::where('id', $request->hourly_package_id)?->where('status', true)?->first();
                    if ($hourlyPackage) {
                        $request?->merge([
                            'distance'      => $hourlyPackage->distance,
                            'distance_unit' => $hourlyPackage->distance_type,
                            'hours'         => $hourlyPackage->hour,
                        ]);

                        $distanceFairCharges = $this->calDistanceMinMaxCharges($request, $vehicleType);
                        $hourFairCharges     = $this->calHourMinMaxCharges($request, $vehicleType);
                        $minPackageCharge    = $distanceFairCharges['min_distance_charge'] + $hourFairCharges['min_hour_charge'];
                        if ($vehicleType?->base_amount >= $minPackageCharge) {
                            return $vehicleType?->base_amount;
                        }

                        return $minPackageCharge;
                    }
                }
            }

            if ($serviceCategory?->slug == ServiceCategoryEnum::RENTAL) {
                $rentalVehicle = RentalVehicle::where('id', $request->rental_vehicle_id)?->whereNull('deleted_at')?->first();
                if ($rentalVehicle) {
                    $start_date = Carbon::parse($request->start_time);
                    $end_date   = Carbon::parse($request->end_time);
                    $no_of_days = ceil($start_date->diffInDays($end_date));
                    if ($request->is_with_driver) {
                        return $rentalVehicle->vehicle_per_day_price * $no_of_days + $rentalVehicle->driver_per_day_charge * $no_of_days;
                    }
                    return $rentalVehicle->vehicle_per_day_price * $no_of_days;
                }
            }
        } elseif ($service?->slug == ServicesEnum::PARCEL) {
            if (! ((int) $settings['activation']['bidding']) || ! ($request->expectsJson())) {
                $distanceCharges = $this->calDistanceMinMaxCharges($request, $vehicleType);
                $weightCharges   = $this->calWeightMinMaxCharges($request, $vehicleType);
                $charge          = $distanceCharges['min_distance_charge'] + $weightCharges['min_weight_charge'];
                if ($vehicleType?->base_amount >= $charge) {
                    return $vehicleType?->base_amount;
                }
                return $charge;
            }
        }

        return $request->ride_fare ?? $vehicleType?->base_amount;
    }

    public function getRideNumber($digits)
    {
        $i = 0;
        do {

            $ride_number = pow(10, $digits) + $i++;
        } while (Ride::where("ride_number", "=", $ride_number)->exists());

        return $ride_number;
    }

    public function getPlatformFees()
    {
        $platform_fees = 0;
        $settings      = getSettings();
        if (isset($settings['activation']['platform_fees'])) {
            if ($settings['activation']['platform_fees']) {
                $platform_fees = $settings['general']['platform_fees'] ?? 0;
            }
        }
        return $platform_fees;
    }

    public function updateRideStatusActivities($ride, $status, $changed_at = null)
    {
        $sequence           = RideStatus::getSequenceByName($status);
        $cancelRideSequence = RideStatus::getCancelSequence();
        $ride_sequences     = collect(range(1, $sequence))->reject(fn($item) => ($sequence > $cancelRideSequence && $item === $cancelRideSequence))->values()->all();
        if ($ride_sequences && is_array($ride_sequences)) {
            foreach ($ride_sequences as $ride_sequence) {
                $status = RideStatus::getNameBySequence($ride_sequence);
                if ($status) {
                    $changed_at = $changed_at ?? Carbon::now()->toDateTimeString();
                    $ride->ride_status_activities()->updateOrCreate(['status' => $status], [
                        'status'     => $status,
                        'changed_at' => $changed_at,
                    ]);
                }
            }
        }
    }

    public function createRide($request, $bid = null)
    {
        DB::beginTransaction();
        try {

            $ride_request_id = null;
            if (isset($request['ride_request_id'])) {
                $ride_request_id = $request['ride_request_id'];
            }

            if (! is_array($request) && ! is_null($request)) {
                $ride_request_id = $request->ride_request_id;
            }

            if ($bid) {
                $ride_request_id = $bid?->ride_request_id;
            }

            $rideRequest        = RideRequest::findOrFail($ride_request_id);
            $formattedLocations = $rideRequest->locations;
            if ($rideRequest) {
                $settings  = getTaxidoSettings();
                $bid       = $rideRequest?->getAcceptedBid();
                $rideFair  = $bid?->amount ?? $rideRequest?->ride_fare;
                $driver_id = $bid?->driver_id;

                if (! ((int) $settings['activation']['bidding']) || $rideRequest->service_category->slug === ServiceCategoryEnum::RENTAL) {
                    $rideFair = $rideRequest?->ride_fare;
                    $driver   = getCurrentDriver();
                    if (! $driver) {
                        throw new Exception(__('taxido::static.rides.only_driver_can_accept_ride_request_directly'), 400);
                    }
                    $driver_id = $driver?->id;
                }

                $tax           = getVehicleTaxRate($rideRequest?->vehicle_type_id);
                $platform_fees = $this->getPlatformFees();
                $ride          = Ride::create([
                    'ride_number'           => $this->getRideNumber(5),
                    'rider_id'              => $rideRequest?->rider_id,
                    'service_id'            => $rideRequest?->service_id,
                    'service_category_id'   => $rideRequest?->service_category_id,
                    'hourly_package_id'     => $rideRequest?->hourly_package_id,
                    'rental_vehicle_id'     => $rideRequest?->rental_vehicle_id,
                    'vehicle_type_id'       => $rideRequest?->vehicle_type_id,
                    'start_time'            => $rideRequest?->start_time,
                    'end_time'              => $rideRequest?->end_time,
                    'no_of_days'            => $rideRequest?->no_of_days,
                    'is_with_driver'        => $rideRequest?->is_with_driver,
                    'assigned_driver'       => $request?->assigned_driver ?? null,
                    'vehicle_per_day_price' => $rideRequest?->rental_vehicle?->vehicle_per_day_price,
                    'driver_per_day_charge' => $rideRequest?->rental_vehicle?->driver_per_day_charge,
                    'driver_id'             => $driver_id,
                    'rider'                 => $rideRequest?->rider,
                    'otp'                   => rand(1000, 9999),
                    'locations'             => $formattedLocations,
                    'location_coordinates'  => $rideRequest?->location_coordinates,
                    'duration'              => $rideRequest?->duration,
                    'distance'              => $rideRequest?->distance,
                    'distance_unit'         => $rideRequest?->distance_unit,
                    'payment_method'        => $rideRequest?->payment_method,
                    'description'           => $rideRequest?->description,
                    'cargo_image_id'        => $rideRequest?->cargo_image_id,
                    'ride_status_id'        => getRideStatusIdByName(RideStatusEnum::ACCEPTED),
                    'ride_fare'             => $rideFair,
                    'tax'                   => $tax,
                    'weight'                => $rideRequest?->weight,
                    'parcel_delivered_otp'  => $rideRequest?->parcel_delivered_otp,
                    'platform_fees'         => $platform_fees,
                    'sub_total'             => $rideFair,
                    'total'                 => $rideFair + $platform_fees + $tax,
                ]);

                $this->updateRideStatusActivities($ride, $ride->ride_status?->name, $ride->created_at);
                $zoneIds = $rideRequest?->zones()?->pluck('zone_id')->toArray();
                $ride->zones()->attach($zoneIds);
                $ride->zones;
                $ride->vehicle_type;
                $ride->service;
                $ride->service_category;
                $ride->driver;
                $bids = Bid::where('ride_request_id', $rideRequest->id)->whereNull('deleted_at')->pluck('id')->toArray();
                $ride->bids()->attach($bids);
                $ride->bids;

                DB::commit();
                if (!isRideOtpRequired()) {
                    $ride->update([
                        'is_otp_verified' => true,
                        'ride_status_id'  => getRideStatusIdByName(RideStatusEnum::STARTED),
                        'start_time'      => $request['start_time'] ?? null,
                    ]);
                    $this->updateRideStatusActivities($ride, RideStatusEnum::STARTED, $ride->updated_at);
                    $ride->ride_status;
                    $driver = getDriverById($ride?->driver_id);
                    $driver?->update([
                        'is_on_ride' => true,
                    ]);
                }

                if ($bid) {
                    $bid?->update([
                        'ride_id' => $ride?->id,
                    ]);

                    Bid::where('ride_request_id', $bid?->ride_request_id)
                        ->whereNot('id', $bid?->id)
                        ->update(['status' => BidStatusEnum::REJECTED]);
                }

                $rideRequest?->delete();
                return $ride;
            }

            throw new Exception(__('taxido::static.rides.ride_request'), 400);
        } catch (Exception $e) {

            DB::rollBack();

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
