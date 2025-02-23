<?php

namespace Modules\Taxido\Repositories\Admin;

use Exception;
use Modules\Taxido\Models\Zone;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Prettus\Repository\Eloquent\BaseRepository;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;

class ZoneRepository extends BaseRepository
{
    public function model()
    {
        return Zone::class;
    }

    public function index($zoneTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.zone.index', ['tableConfig' => $zoneTable]);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {

            $coordinates = json_decode($request?->place_points ?? '', true);
            $points = array_map(function ($coordinate) {
                return new Point($coordinate['lat'], $coordinate['lng']);
            }, $coordinates);

            if (head($points) != $points[count($points) - 1]) {
                $points[] = head($points);
            }

            $lineString = new LineString($points);
            $place_points = new Polygon([$lineString]);
            $zone = $this->model->create([
                'name' => $request->name,
                'amount' => $request->amount,
                'distance_type' => $request->distance_type,
                'currency_id' => $request->currency_id,
                'place_points' => $place_points,
                'locations' => $coordinates,
                'status' => $request->status,
            ]);

            $locale = $request['locale'] ?? app()->getLocale();
            $zone->setTranslation('name', $locale, $request['name']);

            DB::commit();
            return to_route('admin.zone.index')->with('success', __('taxido::static.zones.created'));

        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {

            $zone = $this->model->findOrFail($id);
            
            $locale = $request['locale'] ?? app()->getLocale();
            $zone->setTranslation('name', $locale, $request['name']);

            if (isset($request['place_points']) && ! empty($request['place_points'])) {
                $coordinates = json_decode($request['place_points'] ?? '', true);
                $points = array_map(function ($coordinate) {
                    return new Point($coordinate['lat'], $coordinate['lng']);
                }, $coordinates);

                if (head($points) != $points[count($points) - 1]) {
                    $points[] = head($points);
                }

                $lineString = new LineString($points);
                $place_points = new Polygon([$lineString]);
                unset($request['place_points']);

                $zone->place_points = $place_points;
                $zone->locations = $coordinates;
                $zone->save();
            }
            
            $data = array_diff_key($request, array_flip(['name', 'locale']));
            $zone->update($data);

            DB::commit();

            return to_route('admin.zone.index')->with('success', __('taxido::static.zones.updated'));

        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $zone = $this->model->findOrFail($id);
            $zone->destroy($id);

            DB::commit();
            return redirect()->back()->with('success',__('taxido::static.zones.deleted'));

        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function status($id, $status)
    {
        try {

            $zone = $this->model->findOrFail($id);
            $zone->update(['status' => $status]);

            return json_encode(['resp' => $zone]);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function restore($id)
    {
        try {

            $zone = $this->model->onlyTrashed()->findOrFail($id);
            $zone->restore();
            return redirect()->back()->with('success', __('taxido::static.zones.restore_successfully'));

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function forceDelete($id)
    {
        try {

            $zone = $this->model->onlyTrashed()->findOrFail($id);
            $zone->forceDelete();

            return redirect()->back()->with('success', __('taxido::static.zones.permanent_delete_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function deleteAll($ids)
    {
        DB::beginTransaction();
        try {

            $this->model->whereNot('system_reserve', true)->whereIn('id', $ids)?->delete();

            DB::commit();
            return back()->with('success', __('taxido::static.zones.deleted'));

        } catch (Exception $e) {

            DB::rollback();

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
