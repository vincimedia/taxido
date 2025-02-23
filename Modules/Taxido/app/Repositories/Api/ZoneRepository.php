<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use Modules\Taxido\Models\Zone;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;

class ZoneRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name' => 'like',
    ];

    function model()
    {
        return Zone::class;
    }

    public function getZoneIds($request)
    {
        try {

            if ($request->lat && $request->lng) {
                $zones = getZoneByPoint($request->lat, $request->lng);
                if (count($zones)) {
                    return [
                        'success' => true,
                        'data' => $zones
                    ];
                }
            }

            return [
                'success' => false,
                'data' => []
            ];

        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }


}
