<?php

namespace Modules\Taxido\Providers;
use Modules\Taxido\Events\RideRequestEvent;
use Modules\Taxido\Listeners\RideRequestListener;

use Modules\Taxido\Events\AcceptBiddingEvent;
use Modules\Taxido\Listeners\AcceptBiddingListener;

use Modules\Taxido\Events\CreateWithdrawRequestEvent;
use Modules\Taxido\Listeners\CreateWithdrawRequestListener;

use Modules\Taxido\Events\CreateBidEvent;
use Modules\Taxido\Listeners\CreateBidListener;

use Modules\Taxido\Events\RejectBiddingEvent;
use Modules\Taxido\Listeners\RejectBiddingListener;

use Modules\Taxido\Events\UpdateWithdrawRequestEvent;
use Modules\Taxido\Listeners\UpdateWithdrawRequestListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        RideRequestEvent::class => [
            RideRequestListener::class
        ],
        AcceptBiddingEvent::class => [
            AcceptBiddingListener::class
        ],
        CreateWithdrawRequestEvent::class => [
            CreateWithdrawRequestListener::class
        ],
        CreateBidEvent::class => [
            CreateBidListener::class
        ],
        RejectBiddingEvent::class => [
            RejectBiddingListener::class
        ],
        UpdateWithdrawRequestEvent::class => [
            UpdateWithdrawRequestListener::class
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     *
     * @return void
     */
    protected function configureEmailVerification(): void
    {

    }
}
