<?php

namespace Modules\Taxido\Providers;

use Exception;
use Illuminate\Support\Facades\Route;
use Modules\Taxido\Enums\RideStatusEnum;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'Modules\\Taxido\\Http\\Controllers';
    protected $apiNamespace = 'Modules\\Taxido\\Http\\Controllers\\Api';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->registerMenus();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')->group(module_path('Taxido', '/routes/web.php'));
        Route::middleware('web')->prefix('admin')->namespace($this->namespace)->group(module_path('Taxido', '/routes/admin.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware('api')->namespace($this->apiNamespace)->prefix('api')->group(module_path('Taxido', '/routes/api/api.php'));
        Route::middleware('api')->namespace($this->apiNamespace)->prefix('api')->group(module_path('Taxido', '/routes/api/admin.php'));
    }

    protected function registerMenus()
    {
        try {

            add_menu(label: 'taxido::static.riders.riders', module_slug: 'taxido', slug: 'tx_riders', icon: 'ri-group-line', position: 3, section: 'static.user_management', permission: 'rider.index');
            add_menu(label: 'taxido::static.riders.all', route: 'admin.rider.index', parent_slug: 'tx_riders', module_slug: 'taxido', slug: 'tx_all_riders', icon: 'ri-team-line', section: 'static.user_management', permission: 'rider.index');
            add_menu(label: 'taxido::static.riders.add', route: 'admin.rider.create', parent_slug: 'tx_riders', module_slug: 'taxido', slug: 'tx_rider_create', icon: 'ri-user-add-line', section: 'static.user_management', permission: 'rider.create');
            add_menu(label: 'taxido::static.wallets.wallet', route: 'admin.rider-wallet.index', parent_slug: 'tx_riders', module_slug: 'taxido', slug: 'tx_rider_wallet', icon: 'ri-wallet-line', section: 'static.user_management', permission: 'rider_wallet.index');
            add_menu(label: 'taxido::static.drivers.drivers', module_slug: 'taxido', slug: 'tx_drivers', icon: 'ri-user-2-line', position: 4, section: 'static.user_management', permission: '');
            add_menu(label: 'taxido::static.drivers.verified_drivers', route: 'admin.driver.index', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_all_drivers', icon: 'ri-check-line', section: 'static.user_management', permission: 'driver.index');
            add_menu(label: 'taxido::static.drivers.unverified_driver', route: 'admin.driver.unverified-drivers', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_unverified_drivers', icon: 'ri-alert-line', section: 'static.user_management', permission: 'unverified_driver.index', badge: getUnverifiedDriver(), badgeable: true);
            add_menu(label: 'taxido::static.drivers.add', route: 'admin.driver.create', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_driver_add', icon: 'ri-add-line', section: 'static.user_management', permission: 'driver.create');
            add_menu(label: 'taxido::static.driver_documents.driver_documents', route: 'admin.driver-document.index', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_driverDocument', icon: 'ri-document-line', section: 'static.user_management', permission: 'driver_document.index', badge: getAllDriverDocumentsCount(), badgeable: true);
            add_menu(label: 'taxido::static.driver_rules.driver_rules', route: 'admin.driver-rule.index', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_driverRule', icon: 'ri-gavel-line', section: 'static.user_management', permission: 'driver_rule.index');
            add_menu(label: 'taxido::static.subscriptions.driver_subscription', route: 'admin.driver-subscription.index', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_driverSubscription', icon: 'ri-file-blank-line', section: 'static.user_management', permission: 'subscription.index');
            add_menu(label: 'taxido::static.locations.driver_location', route: 'admin.driver-location.index', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_locations', icon: 'ri-road-map-line', section: 'static.user_management', permission: 'driver_location.index');
            add_menu(label: 'taxido::static.notices.notice', route: 'admin.notice.index', parent_slug: 'tx_drivers', module_slug: 'taxido', icon: 'ri-notice-line', section: 'static.user_management', permission: 'notice.index');
            add_menu(label: 'taxido::static.wallets.wallet', route: 'admin.driver-wallet.index', parent_slug: 'tx_drivers', module_slug: 'taxido', icon: 'ri-wallet-line', section: 'static.user_management', permission: 'driver_wallet.index');
            add_menu(label: 'taxido::static.withdraw_requests.withdraw_request', route: 'admin.withdraw-request.index', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_withdrawRequest', icon: 'ri-money-dollar-circle-line', section: 'static.user_management', permission: 'withdraw_request.index', badge: getPendingWithdrawRequests(), badgeable: true);
            add_menu(label: 'taxido::static.commission_histories.commission_histories', route: 'admin.cab-commission-history.index', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_commissionHistory', icon: 'ri-money-dollar-circle-line', section: 'static.user_management', permission: 'cab_commission_history.index');
            add_menu(label: 'taxido::static.rental_vehicle.rental_vehicles', route: 'admin.rental-vehicle.index', parent_slug: 'tx_drivers', module_slug: 'taxido', slug: 'tx_rental_vehicle', icon: 'ri-clock-line', section: 'static.user_management', permission: 'rental_vehicle.index');

            add_menu(label: 'taxido::static.zones.zones', module_slug: 'taxido', slug: 'zones', icon: 'ri-route-line', position: 6, section: 'taxido::static.cab_management', permission: 'zone.index');
            add_menu(label: 'taxido::static.zones.zones', route: 'admin.zone.index', parent_slug: 'zones', module_slug: 'taxido', slug: 'tx_zones', icon: 'ri-map-2-line', section: 'taxido::static.cab_management', permission: 'zone.index');
            add_menu(label: 'taxido::static.zones.add', route: 'admin.zone.create', parent_slug: 'zones', module_slug: 'taxido', slug: 'tx_zones_create', icon: 'ri-map-2-line', section: 'taxido::static.cab_management', permission: 'zone.create');

            add_menu(label: 'taxido::static.cab', module_slug: 'taxido', slug: 'taxido', icon: 'ri-taxi-line', position: 7, section: 'taxido::static.cab_management', permission:'');
            add_menu(label: 'taxido::static.services.services', route: 'admin.service.index', parent_slug: 'taxido', module_slug: 'taxido', slug: 'tx_service', icon: 'ri-tools-line', section: 'taxido::static.cab_management', permission: 'service.index');
            add_menu(label: 'taxido::static.service_categories.serviceCategory', route: 'admin.service-category.index', parent_slug: 'taxido', module_slug: 'taxido', slug: 'tx_serviceCategory', icon: 'ri-folder-line', section: 'taxido::static.cab_management', permission: 'service_category.index');
            add_menu(label: 'taxido::static.vehicle_types.vehicles', route: 'admin.vehicle-type.index', parent_slug: 'taxido', module_slug: 'taxido', slug: 'tx_vehicleType', icon: 'ri-car-line', section: 'taxido::static.cab_management', permission: 'vehicle_type.index');
            add_menu(label: 'taxido::static.hourly_package.hourly_packages', route: 'admin.hourly-package.index', parent_slug: 'taxido', module_slug: 'taxido', slug: 'tx_hourlyPackage', icon: 'ri-clock-line', section: 'taxido::static.cab_management', permission: 'hourly_package.index');
            add_menu(label: 'taxido::static.coupons.coupons', route: 'admin.coupon.index', module_slug: 'taxido', slug: 'tx_coupons', icon: 'ri-coupon-2-line', section: 'taxido::static.cab_management', permission: 'coupon.index');
            add_menu(label: 'taxido::static.documents.documents', route:'admin.document.index', parent_slug: 'taxido',module_slug: 'taxido', slug:'tx_documents', icon:'ri-file-line', section:'taxido::static.cab_management', permission:'document.index');
            add_menu(label: 'taxido::static.soses.soses', route:'admin.sos.index', parent_slug: 'taxido', module_slug: 'taxido', slug:'tx_sos', icon: 'ri-alert-line', section:'taxido::static.cab_management', permission:'sos.index');
            add_menu(label: 'taxido::static.plans.plans', route: 'admin.plan.index', parent_slug: 'taxido', module_slug: 'taxido', slug: 'tx_plan', icon: 'ri-gavel-line', section: 'taxido::static.cab_management', permission: 'plan.index');
            add_menu(label: 'taxido::static.cancellation-reasons.cancellation-reasons', route:'admin.cancellation-reason.index', parent_slug: 'taxido', module_slug: 'taxido', slug:'tx_cancellationReason', icon: 'ri-error-warning-line', section:'taxido::static.cab_management', permission:'cancellation_reason.index');
            add_menu(label: 'taxido::static.settings.app_settings', route:'admin.taxido-setting.index', parent_slug: 'taxido', module_slug: 'taxido', slug:'tx_setting', icon: 'ri-settings-line', section:'taxido::static.cab_management', permission:'taxido_setting.index');

            add_menu(label: 'taxido::static.reports.reports',module_slug: 'taxido', slug:'tx_reports', icon: 'ri-folder-chart-line', section:'taxido::static.cab_management', position:8,permission:'report.index');
            add_menu(label: 'taxido::static.reports.transaction_reports', route:'admin.transaction-report.index', parent_slug: 'tx_reports', module_slug: 'taxido', slug:'tx_transaction_reports', icon: 'ri-road-line', section:'taxido::static.cab_management',permission:'report.index');
            add_menu(label: 'taxido::static.reports.ride_reports', route:'admin.ride-report.index', parent_slug: 'tx_reports', module_slug: 'taxido', slug:'tx_ride_reports', icon: 'ri-traffic-line', section:'taxido::static.cab_management',permission:'report.index');
            add_menu(label: 'taxido::static.reports.driver_reports', route:'admin.driver-report.index', parent_slug: 'tx_reports', module_slug: 'taxido', slug:'tx_driver_reports', icon: 'ri-user-line', section:'taxido::static.cab_management',permission:'report.index');
            add_menu(label: 'taxido::static.reports.coupon_reports', route:'admin.coupon-report.index', parent_slug: 'tx_reports', module_slug: 'taxido', slug:'tx_coupon_reports', icon: 'ri-road-line', section:'taxido::static.cab_management',permission:'report.index');
            add_menu(label: 'taxido::static.reports.zone_reports', route:'admin.zone-report.index', parent_slug: 'tx_reports', module_slug: 'taxido', slug:'tx_zone_reports', icon: 'ri-bar-chart-2-line', section:'taxido::static.cab_management',permission:'report.index');

            add_menu(label: 'taxido::static.rides.rides', module_slug: 'taxido', slug:'tx_ride', icon:'ri-map-2-line', section:'static.home', position:10, permission:'ride.index');
            add_menu(label: 'taxido::static.rides.ride_requests', route:'admin.ride-request.index', parent_slug: 'tx_ride', module_slug: 'taxido', slug:'tx_all_ride_requests', icon: 'ri-traffic-light-line', section:'static.home', permission:'ride_request.index');
            add_menu(label: 'taxido::static.rides.all', route:'admin.ride.index', parent_slug: 'tx_ride', module_slug: 'taxido', slug:'tx_all_rides', icon: 'ri-traffic-light-line', section:'static.home', permission:'ride.index');
            add_menu(label: 'taxido::static.rides.requested', route:'admin.ride.requested-rides', parent_slug: 'tx_ride', module_slug: 'taxido', slug:'tx_requested_rides', icon: 'ri-traffic-light-line', section:'static.home', permission:'ride.index', badge: getTotalRidesByStatus(RideStatusEnum::REQUESTED), badgeable: true);
            add_menu(label: 'taxido::static.rides.scheduled', route:'admin.ride.scheduled-rides', parent_slug: 'tx_ride', module_slug: 'taxido', slug:'tx_scheduled_rides', icon: 'ri-traffic-light-line', section:'static.home', permission:'ride.index', badge: getTotalRidesByStatus(RideStatusEnum::SCHEDULED), badgeable: true);
            add_menu(label: 'taxido::static.rides.accepted', route:'admin.ride.accepted-rides', parent_slug: 'tx_ride', module_slug: 'taxido', slug:'tx_accepted_rides', icon: 'ri-traffic-light-line', section:'static.home', permission:'ride.index', badge: getTotalRidesByStatus(RideStatusEnum::ACCEPTED), badgeable: true);
            add_menu(label: 'taxido::static.rides.arrived', route: 'admin.ride.arrived-rides', parent_slug: 'tx_ride', module_slug: 'taxido', slug: 'tx_arrived_rides', icon: 'ri-traffic-light-line', section: 'static.home', permission: 'ride.index', badge: getTotalRidesByStatus(RideStatusEnum::ARRIVED), badgeable: true);
            add_menu(label: 'taxido::static.rides.started', route:'admin.ride.started-rides', parent_slug: 'tx_ride', module_slug: 'taxido', slug:'tx_started_rides', icon: 'ri-traffic-light-line', section:'static.home', permission:'ride.index', badge: getTotalRidesByStatus(RideStatusEnum::STARTED), badgeable: true);
            add_menu(label: 'taxido::static.rides.cancelled', route:'admin.ride.cancelled-rides', parent_slug: 'tx_ride', module_slug: 'taxido', slug:'tx_cancelled_rides', icon: 'ri-traffic-light-line', section:'static.home', permission:'ride.index', badge: getTotalRidesByStatus(RideStatusEnum::CANCELLED), badgeable: true);
            add_menu(label: 'taxido::static.rides.completed', route:'admin.ride.completed-rides', parent_slug: 'tx_ride', module_slug: 'taxido', slug:'tx_completed_rides', icon: 'ri-traffic-light-line', section:'static.home', permission:'ride.index', badge: getTotalRidesByStatus(RideStatusEnum::COMPLETED), badgeable: true);

            add_menu(label: 'taxido::static.reviews.reviews', module_slug: 'taxido', slug:'tx_reviews', icon:'ri-user-star-line', section:'taxido::static.cab_management', position:11, permission:'driver_review.index');
            add_menu(label: 'taxido::static.reviews.rider_reviews', route: 'admin.rider-review.index', parent_slug: 'tx_reviews', module_slug: 'taxido', slug: 'tx_rider_review', icon: 'ri-star-line', section: 'taxido::static.cab_management', permission: 'rider.create');
            add_menu(label: 'taxido::static.reviews.driver_reviews', route: 'admin.driver-review.index', parent_slug: 'tx_reviews', module_slug: 'taxido', icon: 'ri-star-line', section: 'taxido::static.cab_management', permission: 'driver_review.index');

            add_menu(label: 'taxido::static.banners.banners', route: 'admin.banner.index', module_slug: 'taxido', slug: 'tx_banners', icon: 'ri-todo-line', section: 'static.promotion_management', permission: 'banner.index');
            add_menu(label: 'taxido::static.push_notification.push_notification',   module_slug: 'taxido', slug: 'tx_pushNotification', icon: 'ri-send-plane-line', section: 'static.promotion_management', position: 11, permission: 'push_notification.index');
            add_menu(label: 'taxido::static.push_notification.all', route: 'admin.push-notification.index', parent_slug: 'tx_pushNotification', module_slug: 'taxido', slug: 'tx_all_pushNotification', icon: 'ri-notification-2-line', section: 'static.promotion_management', permission: 'push_notification.index');
            add_menu(label: 'taxido::static.push_notification.send', route: 'admin.push-notification.create', parent_slug: 'tx_pushNotification', module_slug: 'taxido', slug: 'tx_send_pushNotification', icon: 'ri-send-plane-line', section: 'static.promotion_management', permission: 'push_notification.create');
        } catch (Exception $e) {

            // throw $e;
        }
    }
}
