<?php

namespace App\Repositories\Front;

use Exception;
use App\Models\Setting;
use App\Models\LandingPage;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\Session;
use Prettus\Repository\Eloquent\BaseRepository;

class HomeRepository extends BaseRepository
{
    function model()
    {
        return Setting::class;
    }

    public function index()
    {
        try {

            $locale = Session::get('front-locale', 'en');
            $content = LandingPage::first();
            $content = $content ? $content->toArray($locale) : [];
            $settings = getSettings();
            $content = $content['content'];
       ;
            return view('front.home.index', ['content' => $content, 'settings' => $settings]);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

}
