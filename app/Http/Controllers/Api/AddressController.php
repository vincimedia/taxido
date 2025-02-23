<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use App\Repositories\Api\AddressRepository;

class AddressController extends Controller
{
    public $repository;

    public function __construct(AddressRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $addresses = $this->filter($this->repository, $request);
            return $addresses->latest('created_at')->paginate($request->paginate ?? $addresses->count());
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->repository->show($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return $this->repository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->repository->destroy($id);
    }

    public function isPrimary($id)
    {
        return $this->repository->isPrimary($id);
    }

    public function changeAddressStatus(Request $request, $id)
    {
        return $this->repository->changeAddressStatus($request, $id);
    }

    public function filter($addresses, $request)
    {
        if ($request->field && $request->sort) {
            $addresses = $addresses->orderBy($request->field, $request->sort);
        }

        if (isset($request->status)) {
            $addresses = $addresses->where('status', $request->status);
        }

        return $addresses;
    }
}
