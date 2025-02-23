<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ExceptionHandler;
use App\Repositories\Api\LanguageRepository;

class LanguageController extends Controller
{
    public $repository;

    public function __construct(LanguageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        try {

            $languages = $this->filter($this->repository, $request);
            return $languages->paginate($request->paginate ?? $languages->count());
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
    public function show(Language $language)
    {

        return $this->repository->show($language?->id);
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
    public function filter($language, $request)
    {
        if ($request->field && $request->sort) {
            $language = $language->orderBy($request->field, $request->sort);
        }

        if (isset($request->status)) {
            $language = $language->where('status', $request->status);
        }

        return $language;
    }
}
