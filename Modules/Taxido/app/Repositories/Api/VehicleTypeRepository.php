<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\VehicleType;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class VehicleTypeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name' => 'like',
    ];

    function model()
    {
        return VehicleType::class;
    }

    public function boot()
    {
        try {

            $this->pushCriteria(app(RequestCriteria::class));

        } catch (ExceptionHandler $e) {

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

    public function getVehicleTypeByLocations($request)
    {
       try {

            $zoneIds = [];
            $locations = $request->locations;
            $serviceId = $request->service_id;
            $serviceCategoryId = $request->service_category_id;
            foreach ($locations as $location) {
                $zones = getZoneByPoint($location['lat'], $location['lng']);
                if (!$zones->isEmpty()) {
                    foreach ($zones as $zone) {
                        $zoneIds[] = $zone?->id;
                    }
                }
            }

            if (empty($zoneIds)) {
                throw new Exception(__('taxido::static.vehicleTypes.not_found_vehicles_by_points'), 400);
            }

            $vehicleTypes = $this->model->whereHas('zones', function (Builder $zones) use ($zoneIds) {
                $zones->whereIn('zones.id', $zoneIds);
            })->where('status', true);

            if ($serviceId) {
                $vehicleTypes = $vehicleTypes->whereHas('services', function (Builder $service) use ($serviceId) {
                    $service->where('services.id', $serviceId);
                });
            }

            if ($serviceCategoryId) {
                $vehicleTypes = $vehicleTypes->whereHas('service_categories', function (Builder $serviceCategory) use ($serviceCategoryId) {
                    $serviceCategory->where('service_categories.id', $serviceCategoryId);
                });
            }

            if ($request->service_category) {
                $service_category = $request->service_category;
                $vehicleTypes = $vehicleTypes->whereHas('service_categories', function (Builder $query) use ($service_category) {
                    $query->where('slug', '=' , $service_category);
                });
            }

            return $vehicleTypes?->get();

         } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
