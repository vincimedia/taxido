<?php

namespace App\Providers;

use App\Facades\WMenu;
use App\Models\Plugin;
use App\Observers\PluginObserver;
use App\Services\WidgetManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind('Menu', function () {
            return new WMenu();
        });

        $this->app->singleton(WidgetManager::class, function () {
            return new WidgetManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Paginator::useBootstrap();
        Plugin::observe(PluginObserver::class);
        Translatable::fallback(fallbackAny: true,);

    }
}
