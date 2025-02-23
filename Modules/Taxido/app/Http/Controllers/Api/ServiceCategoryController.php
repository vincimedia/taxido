<?php

namespace Modules\Taxido\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use Modules\Taxido\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Builder;
use Modules\Taxido\Repositories\Api\ServiceCategoryRepository;

class ServiceCategoryController extends Controller
{
    public $repository;

    public function  __construct(ServiceCategoryRepository $repository)
    {
        $this->authorizeResource(ServiceCategory::class, 'serviceCategory', [
            'except' => [ 'index', 'show' ],
        ]);
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $serviceCategory = $this->filter($this->repository, $request);
            return $serviceCategory->paginate($request->paginate ?? $serviceCategory->count());
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceCategory $serviceCategory)
    {

        return $this->repository->show($serviceCategory?->id);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function filter($serviceCategories, $request)
    {
        if ($request->field && $request->sort) {
            $serviceCategories = $serviceCategories->orderBy($request->field, $request->sort);
        }

        if (isset($request->status)) {
            $serviceCategories = $serviceCategories->where('status', $request->status);
        }

        if ($request->service_id) {
            $service_id = $request->service_id;
            $serviceCategories = $serviceCategories->whereHas('services', function (Builder $services) use ($service_id) {
                $services->where('services.id', $service_id);
            });
        }

        return $serviceCategories;
    }
}
