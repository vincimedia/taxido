<?php

namespace App\Repositories\Front;

use App\Models\Subscribes;
use DB;
use Exception;

use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;

class SubscribesRepository extends BaseRepository
{
    function model()
    {
        return Subscribes::class;
    }

    public function store($request)
    {
      DB::beginTransaction();
      try {

        $subscribes = $this->model->create([
          'email' => $request->email,
        ]);
        DB::commit();
        // return redirect()->route('home')->with('success', __('static.list'));
        return redirect()->back()->with('success', __('static.list'));
      } catch (Exception $e) {
        throw new ExceptionHandler($e);
      }
    }
}
