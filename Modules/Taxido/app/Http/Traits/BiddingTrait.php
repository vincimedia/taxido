<?php

namespace Modules\Taxido\Http\Traits;

use Exception;
use Modules\Taxido\Models\Bid;
use Modules\Taxido\Models\VehicleType;
use Modules\Taxido\Enums\ServicesEnum;
use Modules\Taxido\Models\HourlyPackage;
use Modules\Taxido\Enums\ServiceCategoryEnum;

trait BiddingTrait
{
    public function isExistsBidAtTime($driver_id, $ride_request_id)
    {
        return Bid::whereNull('deleted_at')
            ->where('driver_id', $driver_id)
            ->where('ride_request_id', $ride_request_id)
            ->whereNull('status')
            ->exists();
    }

    public function convertKmToMiles($km)
    {
        return round($km * 0.621371, 2);
    }

    public function convertMilesToKm($miles)
    {
        return round($miles * 1.60934, 2);
    }

    public function convertWeightToKg($weight, $unit)
    {
        switch ($unit) {
            case 'gram':
                return $weight / 1000;
            case 'pound':
                return $weight * 0.453592;
            case 'kg':
                return $weight;
            default:
                throw new Exception(__('taxido::static.traits.invalid_weight_unit'), 400);
        }
    }

    public function isOptimumFairAmount($fair_amount, $min_charge, $max_charge)
    {
        return (max(min($fair_amount, $max_charge), $min_charge) == $fair_amount);
    }

    public function calDistanceMinMaxCharges($request, $vehicleType)
    {
        $distance = $request->distance;
        if ($request->distance_unit == 'mile') {
            $distance = $this->convertMilesToKm($distance);
        }

        $minDistanceCharge = round($distance * $vehicleType->min_per_unit_charge, 2);
        $maxDistanceCharge = round($distance * $vehicleType->max_per_unit_charge, 2);
        return ['min_distance_charge' => $minDistanceCharge, 'max_distance_charge' => $maxDistanceCharge, 'distanceInKm' => $distance];
    }

    public function calWeightMinMaxCharges($request, $vehicleType)
    {
        $weight = $request->weight;
        $unit = $request->weight_unit ?? 'kg';
        if ($request->weight_unit != 'kg') {
            $weight = $this->convertWeightToKg($weight, $unit);
        }

        $minWeightCharge = floor($weight * $vehicleType->min_per_weight_charge);
        $maxWeightCharge = floor($weight * $vehicleType->max_per_weight_charge);
        return ['min_weight_charge' => $minWeightCharge, 'max_weight_charge' => $maxWeightCharge, 'weightInKg' => $weight];
    }

    public function calHourMinMaxCharges($request, $vehicleType)
    {
        $hours          = $request->hours;
        $reqMinutes     = $hours * 60;
        $minHoursCharge = floor($reqMinutes * $vehicleType->min_per_min_charge);
        $maxHoursCharge = floor($reqMinutes * $vehicleType->max_per_min_charge);
        return ['min_hour_charge' => $minHoursCharge, 'max_hour_charge' => $maxHoursCharge, 'hours' => $hours];
    }

    public function verifyBiddingFairAmount($request, $amount = null)
    {
        $settings        = getTaxidoSettings();
        $serviceCategory = getServiceCategoryById($request->service_category_id);
        $vehicleType     = VehicleType::where('id', $request->vehicle_type_id)?->whereNull('deleted_at')?->first();
        $service         = getServiceById($request->service_id);
        if ($service?->slug == ServicesEnum::CAB || $service?->slug == ServicesEnum::FREIGHT) {
            if (($serviceCategory?->slug != ServiceCategoryEnum::RENTAL) &&
                ($serviceCategory?->slug != ServiceCategoryEnum::PACKAGE)) {
                if ((int) $settings['activation']['bidding']) {
                    $distance    = $request->distance;
                    $fairCharges = $this->calDistanceMinMaxCharges($request, $vehicleType);
                    if (! $this->isOptimumFairAmount($amount ?? $request->ride_fare, $fairCharges['min_distance_charge'], $fairCharges['max_distance_charge'])) {
                        throw new Exception("The fare amount must be between {$fairCharges['min_distance_charge']} and {$fairCharges['max_distance_charge']} for a {$distance} {$vehicleType->charge_unit} route.", 400);
                    }
                }

                return true;

            } elseif ($serviceCategory?->slug == ServiceCategoryEnum::PACKAGE) {
                if ($request->hourly_package_id) {
                    $serviceCategoriesIds = $vehicleType?->service_categories?->pluck('id')?->toArray() ?? [];
                    $reqServiceCategoryId = $request->service_category_id;
                    if (in_array($reqServiceCategoryId, $serviceCategoriesIds)) {
                        if ((int) $settings['activation']['bidding']) {
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
                                $maxPackageCharge    = $distanceFairCharges['max_distance_charge'] + $hourFairCharges['max_hour_charge'];
                                if (! $this->isOptimumFairAmount($amount ?? $request->ride_fare, $minPackageCharge, $maxPackageCharge)) {
                                    throw new Exception("The fare amount must be between {$minPackageCharge} and {$maxPackageCharge} for a selected package.", 400);
                                }

                                return true;
                            }

                            throw new Exception(__('taxido::static.traits.invalid_hourly_package'), 400);
                        }

                        return true;
                    }

                    throw new Exception(__('taxido::static.traits.invalid_vehicle_for_package'), 400);
                }

                throw new Exception(__('taxido::static.traits.hourly_package_required'), 400);
            } elseif ($serviceCategory?->slug == ServiceCategoryEnum::RENTAL) {

                return true;
            }

        } elseif ($service?->slug == ServicesEnum::PARCEL) {
            $serviceCategoriesIds = $vehicleType?->service_categories?->pluck('id')?->toArray() ?? [];
            $reqServiceCategoryId = $request->service_category_id;
            if (in_array($reqServiceCategoryId, $serviceCategoriesIds)) {
                if (isset($settings['activation']['bidding'])) {
                    if ((int) $settings['activation']['bidding']) {
                        $distanceCharges = $this->calDistanceMinMaxCharges($request, $vehicleType);
                        $weightCharges   = $this->calWeightMinMaxCharges($request, $vehicleType);
                        $minParcelCharge = $distanceCharges['min_distance_charge'] + $weightCharges['min_weight_charge'];
                        $maxParcelCharge = $distanceCharges['max_distance_charge'] + $weightCharges['max_weight_charge'];

                        if (! $this->isOptimumFairAmount($amount ?? $request->ride_fare, $minParcelCharge, $maxParcelCharge)) {
                            throw new Exception("The fare amount for the parcel must be between {$minParcelCharge} and {$maxParcelCharge} for a {$distanceCharges['distanceInKm']} Km and {$weightCharges['weightInKg']} Kg", 400);
                        }
                    }
                }

                return true;
            }

            throw new Exception(__('taxido::static.traits.invalid_vehicle_for_parcel'), 400);

        }

        throw new Exception(__('taxido::static.traits.invalid_service'), 400);
    }

    public function getHourlyPackageById($id)
    {
        return HourlyPackage::where('id', $id)?->whereNull('deleted_at')?->first();
    }

    private function calculateDistanceCharge($distance, $vehicleType)
    {
        $minCharge = $distance * $vehicleType->min_per_unit_charge;
        $maxCharge = $distance * $vehicleType->max_per_unit_charge;

        return [
            'min' => floor($minCharge),
            'max' => floor($maxCharge),
        ];
    }
}
