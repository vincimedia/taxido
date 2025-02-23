<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use Modules\Taxido\Models\Driver;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;

class DriverRepository extends BaseRepository
{
    public function model()
    {
        return Driver::class;
    }

    public function driverZone($request)
    {
        DB::beginTransaction();
        try {

            $zoneIds = [];
            $locations = $request->locations;
            $driver = getCurrentDriver();
            if ($driver) {
                $driver->update([
                    'is_online' => $request->is_online,
                    'location' => $request->locations,
                ]);

                foreach ($locations as $location) {
                    $zones = getZoneByPoint($location['lat'], $location['lng']);
                    if (!$zones->isEmpty()) {
                        foreach ($zones as $zone) {
                            $zoneIds[] = $zone?->id;
                        }
                    }
                }

                if (!empty($zoneIds)) {
                    $driver->zones()->sync([]);
                    $driver->zones()->sync(array_unique($zoneIds));
                }

                DB::commit();
                $driver = $driver->fresh();
                return [
                    'success' => true,
                    'data' => $driver
                ];
            }

            throw new Exception(__('taxido::static.drivers.current_driver_not_found'), 400);
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
