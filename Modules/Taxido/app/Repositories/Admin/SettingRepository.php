<?php

namespace Modules\Taxido\Repositories\Admin;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\TaxidoSetting;
use Prettus\Repository\Eloquent\BaseRepository;

class SettingRepository extends BaseRepository
{
    public function model()
    {
        return TaxidoSetting::class;
    }

    public function index()
    {
        return view('taxido::admin.taxido-setting.index', [
            'taxidosettings' => getTaxidoSettings(),
            'id' => $this->model->pluck('id')->first(),
        ]);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {

            $taxidosettings = $this->model->findOrFail($id);
            $request = array_diff_key($request, array_flip(['_token', '_method']));
            $taxidosettings->update([
                'taxido_values' => $request,
            ]);

            DB::commit();
            return to_route('admin.taxido-setting.index')->with('success', __('static.settings.update_successfully'));

        } catch (Exception $e) {
            DB::rollback();

            throw new ExceptionHandler($e->getMessage(), $e->getCode());

        }
    }
}
