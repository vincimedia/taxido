<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use App\Repositories\Api\CurrencyRepository;

class CurrencyController extends Controller
{
    public $repository;

    public function __construct(CurrencyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $currencies = $this->filter($this->repository, $request);
            return $currencies->paginate($request->paginate ?? $currencies->count());
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
    public function show(string $id)
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

    public function filter($currencies, $request)
    {

        if ($request->field && $request->sort) {
            $currencies = $currencies->orderBy($request->field, $request->sort);
        }
        if (isset($request->status)) {
            $currencies = $currencies->where('status', $request->status);
        }
        return $currencies;
    }
    
}
