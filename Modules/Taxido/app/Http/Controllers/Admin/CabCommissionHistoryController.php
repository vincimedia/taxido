<?php

namespace Modules\Taxido\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Taxido\Models\CabCommissionHistory;
use Modules\Taxido\Tables\CabCommissionHistoryTable;
use Modules\Taxido\Repositories\Admin\CabCommissionHistoryRepository;

class CabCommissionHistoryController extends Controller
{
    public $repository;

    public function __construct(CabCommissionHistoryRepository $repository)
    {
        $this->authorizeResource(CabCommissionHistory::class, 'cab_commission_history');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CabCommissionHistoryTable $cabCommissionHistoryTable)
    {
        return $this->repository->index($cabCommissionHistoryTable->generate());
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
     * Show the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
