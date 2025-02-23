<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Repositories\Api\StateRepository;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public $repository;

    public function __construct(StateRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $states = $this->filter($this->repository, $request);
        return $states->latest('created_at')->get();
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
    public function show(State $state)
    {
        return $this->repository->show($state->id);
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

    public function getStates($country_id)
    {
        return $this->repository->getStates($country_id);
    }

    public function filter($states, $request)
    {
        if ($request->country_id) {
            $states = $states->where('country_id', $request->country_id);
        }

        return $states;
    }
}
