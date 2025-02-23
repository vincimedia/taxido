<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\AccountRepository;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;

class AccountController extends Controller
{
    protected $repository;

    public function __construct(AccountRepository $repository){
        $this->repository = $repository;
    }

    public function profile()
    {
        return view('admin.account.index');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        return $this->repository->updatePassword($request);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        return $this->repository->updateProfile($request);
    }

}
