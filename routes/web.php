<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['localization' , 'maintenance'], 'namespace' => 'Front'], function () {

    // Home
    Route::get('/', 'HomeController@index')->name('home');

    // Blog
    Route::get('blog/{slug}', 'BlogController@getBlogBySlug')->name('blog.slug');
    Route::get('blogs', 'BlogController@index')->name('blog.index');
    Route::get('page/{slug}', 'PageController@getPageBySlug')->name('page.slug');
    Route::get('/sitemap.xml', 'SitemapController@generate');

    // Languages
    Route::get('language/{locale}', function ($locale) {
        app()->setLocale($locale);
        session()?->put('front-locale', $locale);
        return redirect()->back();
    })->name('lang');
    Route::post('/newsletter','SubscribesController@store')->name('newsletter');
    Route::post('/set-theme', 'HomeController@setTheme')->name('set-theme');
});

