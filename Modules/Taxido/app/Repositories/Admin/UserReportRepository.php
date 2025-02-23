<?php

namespace Modules\Taxido\Repositories\Admin;

use App\Models\User;
use Prettus\Repository\Eloquent\BaseRepository;

class UserReportRepository extends BaseRepository
{
    function model()
    {
        return User::class;
    }
}