<?php
namespace Modules\Taxido\Repositories\Admin;

use App\Events\NewUserEvent;
use App\Exceptions\ExceptionHandler;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Taxido\Enums\RoleEnum;
use Modules\Taxido\Exports\RidersExport;
use Modules\Taxido\Imports\RiderImport;
use Modules\Taxido\Models\Rider;
use Prettus\Repository\Eloquent\BaseRepository;
use Spatie\Permission\Models\Role;

class RiderRepository extends BaseRepository
{
    protected $role;

    public function model()
    {
        $this->role = new Role();
        return Rider::class;
    }

    public function index($riderTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.rider.index', ['tableConfig' => $riderTable]);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $rider = $this->model->create([
                'name'         => $request->name,
                'email'        => $request->email,
                'country_code' => $request->country_code,
                'phone'        => (string) $request->phone,
                'status'       => $request->status,
                'password'     => Hash::make($request->password),
            ]);

            $role = $this->role->findOrCreate(RoleEnum::RIDER, 'web');
            $rider->assignRole($role);

            if ($request->notify) {
                event(new NewUserEvent($rider, $request->password));
            }

            DB::commit();
            return to_route('admin.rider.index')->with('success', __('taxido::static.riders.create_successfully'));

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

            $rider = $this->model->findOrFail($id);

            if ($rider->system_reserve) {
                return redirect()->route('admin.rider.index')->with('error', __('This rider cannot be update, It is system reserved.'));
            }

            $rider->update($request);
            $rider->address;

            if (isset($request['role_id'])) {
                $role = $this->role->find($request['role_id']);
                $rider->syncRoles($role);
            }

            DB::commit();
            return to_route('admin.rider.index')->with('success', __('taxido::static.riders.update_successfully'));

        } catch (Exception $e) {

            DB::rollback();

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function status($id, $status)
    {
        try {

            $rider = $this->model->findOrFail($id);
            $rider->update(['status' => $status]);

            return json_encode(["resp" => $rider]);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {

            $rider = $this->model->findOrFail($id);
            $rider->destroy($id);
            return redirect()->back()->with('success', __('taxido::static.riders.delete_successfully'));

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function restore($id)
    {
        try {

            $rider = $this->model->onlyTrashed()->findOrFail($id);
            $rider->restore();

            return redirect()->back()->with('success', __('taxido::static.riders.restore_successfully'));

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function forceDelete($id)
    {
        try {

            $rider = $this->model->onlyTrashed()->findOrFail($id);
            $rider->forceDelete();

            return redirect()->back()->with('success', __('taxido::static.riders.permanent_delete_successfully'));

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function export($request)
    {
        try {
            $format = $request->input('format', 'xlsx');

            if ($format == 'csv') {
                return Excel::download(new RidersExport, 'riders.csv');
            }
            return Excel::download(new RidersExport, 'riders.xlsx');
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

                if (! $googleSheetUrl) {
                    throw new Exception(__('static.import.no_url_provided'));
                }

                if (! filter_var($googleSheetUrl, FILTER_VALIDATE_URL)) {
                    throw new Exception(__('static.import.invalid_url'));
                }

                $parsedUrl = parse_url($googleSheetUrl);
                preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $parsedUrl['path'], $matches);
                $sheetId = $matches[1] ?? null;
                parse_str($parsedUrl['query'] ?? '', $queryParams);
                $gid = $queryParams['gid'] ?? 0;

                if (! $sheetId) {
                    throw new Exception(__('static.import.invalid_sheet_id'));
                }

                $csvUrl = "https://docs.google.com/spreadsheets/d/{$sheetId}/export?format=csv&gid={$gid}";

                $response = Http::get($csvUrl);

                if (! $response->ok()) {
                    throw new Exception(__('static.import.failed_to_fetch_csv'));
                }

                $tempFile = tempnam(sys_get_temp_dir(), 'google_sheet_') . '.csv';
                file_put_contents($tempFile, $response->body());
            } elseif ($activeTab === 'local-file') {
                $file = $request->file('fileImport');
                if (! $file) {
                    throw new Exception(__('static.import.no_file_uploaded'));
                }

                if ($file->getClientOriginalExtension() != 'csv') {
                    throw new Exception(__('static.import.csv_file_allow'));
                }

                $tempFile = $file->getPathname();
            } else {
                throw new Exception(__('static.import.no_valid_input'));
            }

            Excel::import(new RiderImport(), $tempFile);

            if ($activeTab === 'google_sheet' && file_exists($tempFile)) {
                unlink($tempFile);
            }

            return redirect()->back()->with('success', __('static.import.csv_file_import'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
