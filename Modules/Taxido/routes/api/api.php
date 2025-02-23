<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "api" middleware group. Now create something great!
|
*/

Route::get('ride/invoice/{ride_number}', 'RideController@getInvoice')->name('ride.invoice');

// Rider Authentication
Route::post('/login-email', 'AuthController@login_with_email');
Route::post('/rider/register', 'AuthController@riderRegister');
Route::post('rider/forgot-password', 'AuthController@forgotPassword');
Route::post('/rider/login', 'AuthController@login_with_numb');
Route::post('rider/verifyOtp', 'AuthController@verifyOtp');
Route::post('rider/update-password', 'AuthController@updatePassword');
Route::post('/rider/verify-token', 'AuthController@verifyRiderToken');
Route::post('rider/social/login', 'AuthController@socialLogin');
Route::post('login/facebook', 'AuthController@facebookLogin');

// Driver Authentication
Route::post('/driver/login' , 'DriverAuthController@driverLogin');
Route::post('/driver/verify-token', 'DriverAuthController@verifyDriverToken');
Route::post('driver/update-password', 'DriverAuthController@updatePassword');
Route::post('driver/forgot-password', 'DriverAuthController@forgotPassword');
Route::post('/driver/register', 'DriverAuthController@driverRegister');

// Settings
Route::get('taxido/settings','SettingController@index');

// Services
Route::apiResource('service', 'ServiceController', ['only' => ['index', 'show']]);

// Service Categories
Route::apiResource('serviceCategory', 'ServiceCategoryController', ['only' => ['index', 'show']]);

// Driver Rules
Route::apiResource('driverRule', 'DriverRuleController', ['only' => ['index', 'show']]);

// Vehicle Types
Route::apiResource('vehicleType', 'VehicleTypeController', ['only' => ['index', 'show']]);

// Documents
Route::apiResource('document', 'DocumentController', ['only' => ['index', 'show']]);

// Zones
Route::apiResource('zone','ZoneController',['only' => ['index' ,'show']]);
Route::get('zone-by-point','ZoneController@getZoneIds')->name('get.zoneId');

// Coupons
Route::apiResource('coupon', 'CouponController', ['only' => ['show', 'index']]);

 // Banners
 Route::apiResource('banner','BannerController',['only' => ['index' ,'show']]);

Route::group(['middleware' => ['auth:sanctum', 'localization'], 'as' => 'api.'], function () {

  // Driver
  Route::get('driver/self' , 'DriverAuthController@self');
  Route::apiResource('driver','DriverController',['only' => ['index']]);
  Route::post('driver/zone-update', 'DriverController@driverZone')->name('driver.zone.update');

  // Vehicle Types
  Route::post('vehicleType/locations', 'VehicleTypeController@getVehicleTypeByLocations');

  // Hourly Packages
  Route::apiResource('hourlyPackage', 'HourlyPackageController', ['only' => ['index', 'show']]);

  // Cancellation Reason
  Route::apiResource('cancellationReason', 'CancellationReasonController', ['only' => ['index', 'show']]);

  // Notice
  Route::apiResource('notice', 'NoticeController', ['only' => ['index', 'show']]);

  // Coupons
  Route::apiResource('coupon', 'CouponController', ['except' => ['show', 'index']]);
  Route::apiResource('rental-vehicle', 'RentalVehicleController');
  Route::put('rental-vehicle/{id}/{status}', 'RentalVehicleController@status')->middleware('can:rental_vehicle.edit');

  // zones
  Route::apiResource('zone','ZoneController', ['except' => ['show', 'index']]);

  // Ride Requests
  Route::apiResource('rideRequest','RideRequestController',['except' => ['show']]);
  Route::post('accept-ride-request','RideRequestController@accept');
  Route::post('rental/rideRequest','RideRequestController@rental');

  // Soses
  Route::apiResource('sos', 'SOSController', ['except' => ['show','edit','update']]);
  Route::get('sos/{sos}', 'SOSController@show')->name('sos.show');

   // Plan
  Route::apiResource('plan', 'PlanController', ['only' => ['index', 'show']]);
  Route::post('plan-purchase', 'PlanController@purchase')->name('plan.purchase');

  // Rides
  Route::apiResource('ride','RideController');
  Route::post('ride/start-ride','RideController@startRide')->name('ride.start')->middleware('can:ride.edit');
  Route::post('ride/payment','RideController@payment')->name('ride.payment')->middleware('can:ride.create');
  Route::post('ride/verify-payment','RideController@verifyPayment')->name('ride.verify.payment');
  Route::post('ride/verify-coupon','RideController@verifyCoupon')->name('ride.verify.coupon')->middleware('can:ride.create');
  Route::post('ride/verify-otp', 'RideController@verifyOtp')->name('ride.verify-otp');

  // Bids
  Route::apiResource('bid','BidController');

  // Rider Wallet
  Route::get('riderWallet/history', 'RiderWalletController@index')->middleware('can:rider_wallet.index');
  Route::post('rider/top-up', 'RiderWalletController@topUp');

  // Driver Wallet
  Route::get('driverWallet/history', 'DriverWalletController@index')->middleware('can:driver_wallet.index');
  Route::post('driver/withdraw-request', 'DriverWalletController@withdrawRequest')->middleware('can:withdraw_request.create');
  Route::get('driverWallet/withdraw-request', 'DriverWalletController@getWithdrawRequest')->middleware('can:withdraw_request.index');

  // Rider Reviews
  Route::apiResource('riderReview', 'RiderReviewController');

  // Driver Reviews
  Route::apiResource('driverReview' , 'DriverReviewController');

  // Payment Account
  Route::apiResource('paymentAccount', 'PaymentAccountController',['only' => ['index','update','store']]);

  // Locations
  Route::apiResource('location','LocationController', ['except' => ['show']]);
});

