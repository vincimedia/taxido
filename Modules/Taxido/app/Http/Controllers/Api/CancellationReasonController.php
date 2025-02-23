<?php

namespace Modules\Taxido\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use Modules\Taxido\Models\CancellationReason;
use Modules\Taxido\Repositories\Api\CancellationReasonRepository;

class CancellationReasonController extends Controller
{
    public $repository;
    public function  __construct(CancellationReasonRepository $repository)
    {
        $this->authorizeResource(CancellationReason::class, 'cancellationReason', [
            'except' => [ 'index', 'show' ],
        ]);
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $cancellationReason = $this->filter($this->repository, $request);
            return $cancellationReason->latest('created_at')->paginate($request->paginate ?? $cancellationReason->count());
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
    public function show(CancellationReason $cancellationReason)
    {
        return $this->repository->show($cancellationReason?->id);
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

    public function filter($cancellationReason, $request)
    {
        if ($request->field && $request->sort) {
            $cancellationReason = $cancellationReason->orderBy($request->field, $request->sort);
        }

        if (isset($request->status)) {
            $cancellationReason = $cancellationReason->where('status', $request->status);
        }

        return $cancellationReason;
    }
}
