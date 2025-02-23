<?php

namespace Modules\Taxido\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Taxido\Models\VehicleType;
use Modules\Taxido\Tables\VehicleTypeTable;
use Modules\Taxido\Repositories\Admin\VehicleTypeRepository;
use Modules\Taxido\Http\Requests\Admin\CreateVehicleTypeRequest;
use Modules\Taxido\Http\Requests\Admin\UpdateVehicleTypeRequest;

class VehicleTypeController extends Controller
{
    public $repository;

    public function __construct(VehicleTypeRepository $repository)
    {
        $this->authorizeResource(VehicleType::class, 'vehicle_type');
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(VehicleTypeTable $vehicleTypeTable)
    {
        return $this->repository->index($vehicleTypeTable->generate());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('taxido::admin.vehicle-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateVehicleTypeRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleType $vehicleType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehicleType $vehicleType)
    {
        return view('taxido::admin.vehicle-type.edit', ['vehicleType' => $vehicleType]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleTypeRequest $request, VehicleType $vehicleType)
    {
        return $this->repository->update($request->all(), $vehicleType->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleType $vehicleType)
    {
        return $this->repository->destroy($vehicleType->id);
    }

    public function status(Request $request, $id)
    {
        return $this->repository->status($id, $request->status);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->repository->restore($id);
    }

    /**
     * Permanent delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        return $this->repository->forceDelete($id);
    }

    public function export(Request $request)
    {
        return $this->repository->export($request);
    }


    public function import(Request $request)
    {
        return $this->repository->import($request);
    }
}
