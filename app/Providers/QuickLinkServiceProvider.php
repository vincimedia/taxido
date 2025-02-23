<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;

class QuickLinkServiceProvider extends ServiceProvider
{
    protected $quickLinks = [];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('quickLinks', function () {
            return [];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadQuickLinks();
        $this->registerQuickLinks();
    }

    protected function loadQuickLinks()
    {
        $this->quickLinks = collect(config('quick_links', []));
        $this->app->instance('quickLinks', $this->quickLinks);
    }

    public function registerQuickLinks()
    {
        try {

            add_quick_link(__('static.landing_pages.landing_page_title'), 'admin.landing-page.index', 'ri-pages-line');
            add_quick_link(__('static.settings.settings'), 'admin.setting.index', 'ri-settings-4-line');

        } catch (Exception $e) {

            // throw $e;
        }
    }
}
