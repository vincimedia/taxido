<?php

namespace Modules\Taxido\Repositories\Admin;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Taxido\Models\VehicleType;
use Modules\Taxido\Imports\VehicleTypeImport;
use Modules\Taxido\Exports\VehicleTypesExport;
use Modules\Taxido\Models\Zone;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Facades\Http;

class VehicleTypeRepository extends BaseRepository
{
    function model()
    {
        Zone::class;
        return VehicleType::class;
    }

    public function index($vehicleTypeTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.vehicle-type.index', ['tableConfig' => $vehicleTypeTable]);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {

            $vehicleType = $this->model->create([
                'name' => $request->name,
                'base_amount' => $request->base_amount,
                'vehicle_image_id' => $request->vehicle_image_id,
                'vehicle_map_icon_id' => $request->vehicle_map_icon_id,
                'min_per_unit_charge' => $request->min_per_unit_charge,
                'max_per_unit_charge' => $request-> max_per_unit_charge,
                'cancellation_charge' => $request->cancellation_charge,
                'waiting_time_charge' => $request->waiting_time_charge,
                'commission_type' => $request->commission_type,
                'min_per_min_charge' => $request->min_per_min_charge,
                'max_per_min_charge' => $request->max_per_min_charge,
                'commission_rate' => $request->commission_rate,
                'tax_id' => $request->tax_id,
                'status' => $request->status,
                'min_per_weight_charge' => $request->min_per_weight_charge,
                'max_per_weight_charge' => $request->max_per_weight_charge
            ]);

            if ($request?->is_all_zones)
            {
                $zones = Zone::pluck('id')->toArray();
                $vehicleType->zones()->attach($zones);
            }
            else if (!empty($request->zones)) {
                $vehicleType->zones()->attach($request->zones);
            }

            if (!empty($request->services)) {
                $vehicleType->services()->attach($request->services);
            }

            if (!empty($request->serviceCategories)) {
                $vehicleType->service_categories()->attach($request->serviceCategories);
            }

            $locale = $request['locale'] ?? app()->getLocale();
            $vehicleType->setTranslation('name', $locale, $request['name']);

            DB::commit();
            return to_route('admin.vehicle-type.index')->with('success', __('taxido::static.vehicle_types.create_successfully'));

        } catch (Exception $e) {

            DB::rollBack();

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {

            $vehicleType = $this->model->FindOrFail($id);
            $locale = $request['locale'] ?? app()->getLocale();
            $vehicleType->setTranslation('name', $locale, $request['name']);
            $data = array_diff_key($request, array_flip(['name', 'locale']));
            $vehicleType->update($data);

            if (isset($request['vehicle_image_id'])) {
                $vehicleType->vehicle_image()->associate($request['vehicle_image_id']);
                $vehicleType->vehicle_image;
            }

            if (isset($request['vehicle_map_icon_id'])) {
                $vehicleType->vehicle_map_icon()->associate($request['vehicle_map_icon_id']);
                $vehicleType->vehicle_map_icon;
            }

            if (!empty($request['zones'])) {
                $vehicleType->zones()->sync($request['zones']);
            }

            if ($request['is_all_zones'])
            {
                $zones = Zone::pluck('id')->toArray();
                $vehicleType->zones()->sync($zones);
            }
            else if (!empty($request['zones'])) {
                $vehicleType->zones()->sync($request['zones']);
            }

            if (!empty($request['services'])) {
                $vehicleType->services()->sync($request['services']);
            }
            if (!empty($request['serviceCategories'])) {
                $vehicleType->service_categories()->sync($request['serviceCategories']);
            }

            DB::commit();
            $vehicleType = $vehicleType->fresh();
            return to_route('admin.vehicle-type.index')->with('success', __('taxido::static.vehicle_types.update_successfully'));

        } catch (Exception $e) {
            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());

        }
    }

    public function destroy($id)
    {
        try {

            $vehicleType = $this->model->findOrFail($id);
            $vehicleType->destroy($id);

            return to_route('admin.vehicle-type.index')->with('success', __('taxido::static.vehicle_types.delete_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());

        }
    }

    public function status($id, $status)
    {
        try {

            $vehicleType = $this->model->findOrFail($id);
            $vehicleType->update(['status' => $status]);

            return json_encode(["resp" => $vehicleType]);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());

        }
    }

    public function restore($id)
    {
        try {

            $vehicleType = $this->model->onlyTrashed()->findOrFail($id);
            $vehicleType->restore();

            return redirect()->back()->with('success', __('taxido::static.vehicle_types.restore_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());

        }
    }

    public function forceDelete($id)
    {
        try {

            $vehicleType = $this->model->onlyTrashed()->findOrFail($id);
            $vehicleType->forceDelete();

            return redirect()->back()->with('success', __('taxido::static.vehicle_types.permanent_delete_successfully'));
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
        return Excel::download(new VehicleTypesExport, 'vehicle_types.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new VehicleTypesExport, 'vehicle_types.csv');
    }

    public function import($request)
    {
        try {
            $activeTab = $request->input('active_tab');

            $tempFile = null;

            if ($activeTab === 'direct-link') {

                $googleSheetUrl = $request->input('google_sheet_url');

                if (!$googleSheetUrl) {
                    throw new Exception(__('static.import.no_url_provided'));
                }

                if (!filter_var($googleSheetUrl, FILTER_VALIDATE_URL)) {
                    throw new Exception(__('static.import.invalid_url'));
                }

                $parsedUrl = parse_url($googleSheetUrl);
                preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $parsedUrl['path'], $matches);
                $sheetId = $matches[1] ?? null;
                parse_str($parsedUrl['query'] ?? '', $queryParams);
                $gid = $queryParams['gid'] ?? 0;

                if (!$sheetId) {
                    throw new Exception(__('static.import.invalid_sheet_id'));
                }

                $csvUrl = "https://docs.google.com/spreadsheets/d/{$sheetId}/export?format=csv&gid={$gid}";

                $response = Http::get($csvUrl);

                if (!$response->ok()) {
                    throw new Exception(__('static.import.failed_to_fetch_csv'));
                }

                $tempFile = tempnam(sys_get_temp_dir(), 'google_sheet_') . '.csv';
                file_put_contents($tempFile, $response->body());
            } elseif ($activeTab === 'local-file') {
                $file = $request->file('fileImport');
                if (!$file) {
                    throw new Exception(__('static.import.no_file_uploaded'));
                }

                if ($file->getClientOriginalExtension() != 'csv') {
                    throw new Exception(__('static.import.csv_file_allow'));
                }

                $tempFile = $file->getPathname();
            } else {
                throw new Exception(__('static.import.no_valid_input'));
            }

            Excel::import(new VehicleTypeImport(), $tempFile);

            if ($activeTab === 'google_sheet' && file_exists($tempFile)) {
                unlink($tempFile);
            }

            return redirect()->back()->with('success', __('static.import.csv_file_import'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
