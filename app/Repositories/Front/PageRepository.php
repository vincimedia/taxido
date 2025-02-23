<?php

namespace App\Repositories\Front;

use Exception;
use App\Models\Page;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;

class PageRepository extends BaseRepository
{
    function model()
    {
        return Page::class;
    }

    public function getPageBySlug($slug)
    {
        try {

            $page = $this->model->where('slug',$slug)?->first();
            return view('front.pages.details',['page' => $page]);
        } catch(Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
