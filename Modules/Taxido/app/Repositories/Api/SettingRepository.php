<?php

namespace Modules\Taxido\Repositories\Api;

use Modules\Taxido\Models\TaxidoSetting;
use Prettus\Repository\Eloquent\BaseRepository;

class SettingRepository extends BaseRepository
{   

    function model()
    {
        return TaxidoSetting::class;
    }

    public function index()
    {
        return $this->model->latest('created_at')->first();
    }
}