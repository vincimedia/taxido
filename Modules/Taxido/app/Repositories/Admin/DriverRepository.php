<?php

namespace Modules\Taxido\Repositories\Admin;

use Exception;
use Illuminate\Support\Arr;
use Modules\Taxido\Models\Zone;
use Modules\Taxido\Models\Driver;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Modules\Taxido\Enums\RoleEnum;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Modules\Taxido\Imports\DriverImport;
use Modules\Taxido\Models\DriverDocument;
use Modules\Taxido\Exports\DriversExport;
use Prettus\Repository\Eloquent\BaseRepository;
class DriverRepository extends BaseRepository
{
    protected $role;

    function model()
    {
        $this->role = new Role();
        return Driver::class;
    }

    public function index($driverTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }
        $title = request()->has('is_verified')
        ? __('taxido::static.drivers.verified_drivers')
        : __('taxido::static.drivers.unverified_drivers');

        return view('taxido::admin.driver.index', ['tableConfig' => $driverTable, 'title' => $title]);
    }

    public function getUnverifiedDrivers($driverTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }
        $title = __('taxido::static.drivers.unverified_drivers');
        return view('taxido::admin.driver.index', ['tableConfig' => $driverTable, 'title' => $title]);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {

            $driver = $this->model->create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone' => (string) $request->phone,
                'status' => $request->status,
                'password'     => Hash::make($request->password),
                'profile_image_id' => $request->profile_image_id,
            ]);

            $role = $this->role->findOrCreate(RoleEnum::DRIVER, 'web');
            $driver->assignRole($role);
            if (!empty($request->address)) {
                $driver->addresses()->create($request->address);
            }

            if (!empty($request->vehicle_info)) {
                $driver->vehicle_info()->create($request->vehicle_info);
            }

            if (!empty($request->payment_account)) {
                $driver->payment_account()->create($request->payment_account);
            }

            if (!empty($request->zones)) {
                $driver->zones()->attach($request->zones);
            }

            // if ($request->notify) {
            //     event(new NewUserEvent($driver, $request->password));
            // }

            $driver->profile_image;
            DB::commit();

            return to_route('admin.driver.index')->with('success', __('taxido::static.drivers.create_successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {

            $request = Arr::except($request, ['password']);
            if (isset($request['phone'])) {
                $request['phone'] = (string) $request['phone'];
            }

            $driver = $this->model->findOrFail($id);
            $driver->update($request);

            if (isset($request['profile_image_id'])) {
                $driver->profile_image()->associate($request['profile_image_id']);
            }

            if (isset($request['vehicle_info'])) {
                $driver->vehicle_info()->updateOrCreate([], $request['vehicle_info'] ?? []);
            }

            if (isset($request['address'])) {
                $driver->address()->updateOrCreate([], $request['address'] ?? []);
            }

            if (isset($request['payment_account'])) {
                $driver->payment_account()->updateOrCreate([], $request['payment_account'] ?? []);
            }

            if (!empty($request['zones'])) {
                $driver->zones()->sync($request['zones']);
            }

            DB::commit();
            return to_route('admin.driver.index')->with('success', __('taxido::static.drivers.update_successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function status($id, $status)
    {
        try {

            $driver = $this->model->findOrFail($id);
            $driver->update(['status' => $status]);

            return json_encode(["resp" => $driver]);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {

            $driver = $this->model->findOrFail($id);
            $driver->destroy($id);

            return redirect()->back()->with('success', __('taxido::static.drivers.delete_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function restore($id)
    {
        try {

            $driver = $this->model->onlyTrashed()->findOrFail($id);
            $driver->restore();

            return redirect()->back()->with('success', __('taxido::static.drivers.restore_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function forceDelete($id)
    {
        try {

            $driver = $this->model->onlyTrashed()->findOrFail($id);
            $driver->forceDelete();

            return redirect()->back()->with('success', __('taxido::static.drivers.permanent_delete_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
    public function verify($id, $status)
    {
        DB::beginTransaction();

        try {

            $driver = $this->model->findOrFail($id);
            $driver->update(['is_verified' => $status]);
            if ($status) {
                DriverDocument::where('driver_id', $id)->update(['status' => 'approved']);
            } else {
                DriverDocument::where('driver_id', $id)->update(['status' => 'pending']);
            }

            DB::commit();
            return json_encode(["resp" => $driver]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function driverLocation()
    {
        try {
            $drivers = $this->model->with('vehicle_info.vehicle.vehicle_map_icon')?->get();
            $locations = $drivers->flatMap(function ($driver) {
                $locationData = $driver->location;
                return collect($locationData)->map(function ($loc) use ($driver) {
                    $vehicleImage = $driver->vehicle_info->vehicle?->vehicle_map_icon?->original_url ?? asset('images/user.png');
                    return [
                        'lat' => $loc['lat'],
                        'lng' => $loc['lng'],
                        'id' => $driver->id,
                        'image' => $driver->profile_image?->original_url,
                        'name' => $driver->name,
                        'phone' => $driver->phone,
                        'vehicle_name' => $driver->vehicle_info?->vehicle?->name,
                        'vehicle_model' => $driver->vehicle_info?->model,
                        'plate_number' => $driver->vehicle_info?->plate_number,
                        'vehicle_image' => $vehicleImage,
                    ];
                });
            });

            return view('taxido::admin.driver-location.index', [
                'locations' => $locations,
                'drivers' => $drivers
            ]);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function driverCoordinates($request)
    {
        try {
            $zoneId = $request->input('zone_id');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            $zones = Zone::where('status', true)->get();

            $driversQuery = Driver::with(['vehicle_info.vehicle.vehicle_map_icon', 'zones']);

            if ($zoneId) {
                $driversQuery->whereHas('zones', function ($query) use ($zoneId) {
                    $query->where('zones.id', $zoneId);
                });
            }

            $drivers = $driversQuery->get();

            $driversInZone = $drivers->filter(function ($driver) use ($latitude, $longitude) {
                return $driver->isInsideZone($latitude, $longitude);
            });

            $locations = $driversInZone->flatMap(function ($driver) {
                $locationData = $driver->location;
                return collect($locationData)->map(function ($loc) use ($driver) {
                    $vehicleImage = $driver->vehicle_info->vehicle?->vehicle_map_icon?->original_url ?? asset('images/user.png');
                    return [
                        'lat' => $loc['lat'],
                        'lng' => $loc['lng'],
                        'id' => $driver->id,
                        'image' => $driver->profile_image?->original_url,
                        'name' => $driver->name,
                        'phone' => $driver->phone,
                        'vehicle_name' => $driver->vehicle_info?->vehicle?->name,
                        'vehicle_model' => $driver->vehicle_info?->model,
                        'plate_number' => $driver->vehicle_info?->plate_number,
                        'vehicle_image' => $vehicleImage,
                        'zone_id' => $driver->zones->first()->id ?? null
                    ];
                });
            });

            if (request()->ajax()) {
                return response()->json(['locations' => $locations]);
            }
            
            return view('taxido::admin.driver-location.index', [
                'locations' => $locations,
                'drivers' => $driversInZone,
                'zones' => $zones,
            ]);
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }


    public function export($request)
    {
        try {
            $format = $request->input('format', 'xlsx');

            if ($format == 'csv') {
                return Excel::download(new DriversExport, 'drivers.csv');
            }
            return Excel::download(new DriversExport, 'drivers.xlsx');
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
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

            Excel::import(new DriverImport(), $tempFile);

            if ($activeTab === 'google_sheet' && file_exists($tempFile)) {
                unlink($tempFile);
            }

            return redirect()->back()->with('success', __('static.import.csv_file_import'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
