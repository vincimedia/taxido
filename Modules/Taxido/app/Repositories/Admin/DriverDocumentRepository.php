<?php

namespace Modules\Taxido\Repositories\Admin;

use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\DriverDocument;
use Prettus\Repository\Eloquent\BaseRepository;
use Modules\Taxido\Imports\DriverDocumentImport;
use Modules\Taxido\Exports\DriverDocumentsExport;
use Illuminate\Support\Facades\Http;

class DriverDocumentRepository extends BaseRepository
{
    function model()
    {
        return DriverDocument::class;
    }

    public function index($driverDocumentTable)
    {
        if (request()['action']) {
            return redirect()->back();
        }

        return view('taxido::admin.driver-document.index', ['tableConfig' => $driverDocumentTable]);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $this->model->create([
                'driver_id' => $request->driver_id,
                'document_id' => $request->document_id,
                'document_no' => $request->document_no,
                'document_image_id' => $request->document_image_id,
                'status' => $request->status,
            ]);

            DB::commit();
            return to_route('admin.driver-document.index')->with('success', __('taxido::static.driver_documents.create_successfully'));
        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $driverDocument = $this->model->FindOrFail($id);
            $driverDocument->update($request);

            DB::commit();
            return to_route('admin.driver-document.index')->with('success', __('taxido::static.driver_documents.update_successfully'));
        } catch (Exception $e) {

            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            $driverDocument = $this->model->findOrFail($id);
            $driverDocument->destroy($id);

            return redirect()->route('admin.driver-document.index')->with('success', __('taxido::static.driver_documents.delete_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function status($id, $status)
    {
        try {

            $driverDocument = $this->model->findOrFail($id);
            $driverDocument->update(['status' => $status]);

            return json_encode(["resp" => $driverDocument]);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function restore($id)
    {
        try {

            $driverDocument = $this->model->onlyTrashed()->findOrFail($id);
            $driverDocument->restore();

            return redirect()->back()->with('success', __('taxido::static.driver_documents.restore_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function forceDelete($id)
    {
        try {

            $driverDocument = $this->model->onlyTrashed()->findOrFail($id);
            $driverDocument->forceDelete();

            return redirect()->back()->with('success', __('taxido::static.driver_documents.permanent_delete_successfully'));
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
        return Excel::download(new DriverDocumentsExport, 'driver_documents.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new DriverDocumentsExport, 'driver_documents.csv');
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

            Excel::import(new DriverDocumentImport(), $tempFile);

            if ($activeTab === 'google_sheet' && file_exists($tempFile)) {
                unlink($tempFile);
            }

            return redirect()->back()->with('success', __('static.import.csv_file_import'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}


