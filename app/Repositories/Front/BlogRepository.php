<?php

namespace App\Repositories\Front;

use Exception;
use App\Models\Blog;
use App\Exceptions\ExceptionHandler;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Support\Facades\Session;
use App\Models\LandingPage;
class BlogRepository extends BaseRepository
{
    function model()
    {
        return Blog::class;
    }

    public function getBlogBySlug($slug)
    {
        try {
            $locale = Session::get('front-locale', 'en');
            $content = LandingPage::first();
            $content = $content ? $content->toArray($locale) : [];

            $content = $content['content'];

            $blog = $this->model->where('slug',$slug)?->first()->toArray($locale);
     
            return view('front.blogs.details',['blog' => $blog , 'content' => $content]);

        } catch(Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
    public function index()
    {
        try {
            $locale  = Session::get('front-locale', 'en');
            $content = LandingPage::first();
            $content = $content ? $content->toArray($locale) : [];

            $content = $content['content'];

            $categorySlug = request('category');
            $tagSlug      = request('tag');

            $blogs = $this->model->where('status', 1)
                ->when($categorySlug, function ($query) use ($categorySlug) {
                    return $query->whereHas('categories', function ($query) use ($categorySlug) {
                        $query->where('slug', $categorySlug);
                    });
                })
                ->when($tagSlug, function ($query) use ($tagSlug) {
                    return $query->whereHas('tags', function ($query) use ($tagSlug) {
                        $query->where('slug', $tagSlug);
                    });
                })
                ->paginate(6);

            return view('front.blogs.index', ['blogs' => $blogs, 'content' => $content]);

        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
