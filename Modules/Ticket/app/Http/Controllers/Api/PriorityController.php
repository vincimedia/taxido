<?php

namespace Modules\Ticket\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Modules\Ticket\Models\Priority;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use Modules\Ticket\Repositories\Api\PriorityRepository;

class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public $repository;

    public function __construct(PriorityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {

            $priority = $this->filter($this->repository, $request);
            return $priority->paginate($request->paginate ?? $priority->count());
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
    public function show(Priority $priority)
    {
        return $this->repository->show($priority->id);
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

    public function filter($priority, $request)
    {
        if ($request->field && $request->sort) {
            $priority = $priority->orderBy($request->field, $request->sort);
        }

        if (isset($request->status)) {
            $priority = $priority->where('status', $request->status);
        }

        return $priority;
    }

}
