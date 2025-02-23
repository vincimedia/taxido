<?php

namespace Modules\Taxido\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Modules\Taxido\Models\Coupon;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Repositories\Api\CouponRepository;
use Modules\Taxido\Http\Requests\Api\CreateCouponRequest;

class CouponController extends Controller
{
    public $repository;

    public function  __construct(CouponRepository $repository)
    {
        $this->authorizeResource(Coupon::class, 'coupon', ['except' => 'index']);
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $coupons = $this->filter($this->repository, $request);
            return $coupons->latest('created_at')->paginate($request->paginate ?? $coupons->count());

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
    public function store(CreateCouponRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->repository->findOrFail($id);
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

    public function filter($coupons, $request)
    {
        if ($request->field && $request->sort) {
            $coupons = $coupons->orderBy($request->field, $request->sort);
        }

        if (isset($request->status)) {
            $coupons = $coupons->where('status', $request->status);
        }

        return $coupons;
    }
}
