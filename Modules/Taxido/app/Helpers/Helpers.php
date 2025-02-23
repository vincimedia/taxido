<?php

use Carbon\Carbon;
use App\Models\Tax;
use App\Enums\PaymentStatus;
use Modules\Taxido\Models\Zone;
use Modules\Taxido\Models\Ride;
use Modules\Taxido\Models\Rider;
use Modules\Taxido\Models\Coupon;
use Modules\Taxido\Models\Driver;
use Modules\Taxido\Models\Service;
use Illuminate\Support\Facades\DB;
use Modules\Taxido\Enums\RoleEnum;
use Illuminate\Support\Facades\Http;
use Modules\Taxido\Models\RideStatus;
use Modules\Taxido\Enums\RequestEnum;
use Modules\Taxido\Models\RiderReview;
use Modules\Taxido\Models\RideRequest;
use Modules\Taxido\Models\VehicleType;
use Modules\Taxido\Models\DriverReview;
use Modules\Taxido\Models\DriverWallet;
use Modules\Taxido\Enums\RideStatusEnum;
use Modules\Taxido\Models\TaxidoSetting;
use Modules\Taxido\Models\PaymentAccount;
use Modules\Taxido\Models\DriverDocument;
use Modules\Taxido\Models\ServiceCategory;
use Modules\Taxido\Models\WithdrawRequest;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Taxido\Models\CabCommissionHistory;

if (!function_exists('getCurrentRider')) {
  function getCurrentRider()
  {
    return Rider::where('id', getCurrentUserId())->first(['id', 'name', 'email', 'country_code', 'phone']);
  }
}

if (!function_exists('getRiderById')) {
  function getRiderById($rider_id)
  {
    return Rider::where('id', $rider_id)->first(['id', 'name', 'email', 'country_code', 'phone']);
  }
}

if (!function_exists('getAllRiders')) {
  function getAllRiders()
  {
    return Rider::where('status', true)?->get();
  }
}

if (!function_exists('getAllCouponCodes')) {
  function getAllCouponCodes()
  {
    return Coupon::where('status', true)?->get();
  }
}

if (!function_exists('getRideStatus')) {
  function getRideStatus()
  {
    return RideStatus::get();
  }
}

if (!function_exists('getRideStatusIdBySlug')) {
  function getRideStatusIdBySlug($slug)
  {
    return RideStatus::where('slug', $slug)?->value('id');
  }
}

if (!function_exists('getTotalRidesByStatus')) {
  function getTotalRidesByStatus($status, $start_date = null, $end_date = null)
  {
    $rides = Ride::where('ride_status_id', getRideStatusIdBySlug($status))
      ->whereNull('deleted_at');

    if (getCurrentRoleName() == RoleEnum::DRIVER) {
      $rides = $rides->where('driver_id', getCurrentUserId());
    }

    if ($start_date && $end_date) {
      $rides = $rides->whereBetween('created_at', [$start_date, $end_date]);
    }

    return $rides->count();
  }
}


if (!function_exists('getTotalDriverRidesByStatus')) {
  function getTotalDriverRidesByStatus($status, $driver_id)
  {
    return Ride::where('ride_status_id', getRideStatusIdBySlug($status))?->where('driver_id', $driver_id)->whereNull('deleted_at')?->count();
  }
}

if (!function_exists('getTotalRiders')) {
  function getTotalRiders($start_date = null, $end_date = null)
  {
    $query = Rider::where('status', true);

    if ($start_date && $end_date) {
      return $query->whereBetween('created_at', [$start_date, $end_date])->count();
    }

    return $query->whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->count();
  }
}

if (!function_exists('getTotalRidersPercentage')) {
  function getTotalRidersPercentage($start_date = null, $end_date = null)
  {
    $sort = request('sort') ?? null;
    $previousRange = getPreviousDateRange($sort, request('start'), request('end'));
    $previousCount = getTotalRiders($previousRange['start'], $previousRange['end']);
    $customRangeCount = getTotalRiders($start_date, $end_date);

    return calculatePercentage($customRangeCount, $previousCount);
  }
}


if (!function_exists('getTotalWithdrawRequestsPercentage')) {
  function getTotalWithdrawRequestsPercentage($start_date = null, $end_date = null)
  {
    $sort = request('sort') ?? null;
    $previousRange = getPreviousDateRange($sort, request('start'), request('end'));
    $previousAmount = getTotalWithdrawals($previousRange['start'], $previousRange['end']);
    $customRangeAmount = getTotalWithdrawals($start_date, $end_date);

    return calculatePercentage($customRangeAmount, $previousAmount);
  }
}

if (!function_exists('getTotalWalletsPercentage')) {
  function getTotalWalletsPercentage($start_date = null, $end_date = null)
  {
    $sort = request('sort') ?? null;
    $previousRange = getPreviousDateRange($sort, request('start'), request('end'));
    $previousBalance = getDriverWalletBalance(getCurrentUserId(), $previousRange['start'], $previousRange['end']);
    $customRangeBalance = getDriverWalletBalance(getCurrentUserId(), $start_date, $end_date);

    return calculatePercentage($customRangeBalance, $previousBalance);
  }
}

if (!function_exists('getTotalDriversPercentage')) {
  function getTotalDriversPercentage($start_date = null, $end_date = null, $is_verified = null)
  {
    $sort = request('sort') ?? null;
    $previousRange = getPreviousDateRange($sort, request('start'), request('end'));
    $previousCount = getTotalDrivers($previousRange['start'], $previousRange['end'], $is_verified);
    $customRangeCount = getTotalDrivers($start_date, $end_date, $is_verified);

    return calculatePercentage($customRangeCount, $previousCount);
  }
}

if (!function_exists('getTotalReviewsPercentage')) {
  function getTotalReviewsPercentage($start_date = null, $end_date = null)
  {
    $sort = request('sort') ?? null;
    $previousRange = getPreviousDateRange($sort, request('start'), request('end'));
    $previousCount = getDriverReviewsCount(getCurrentUserId(), $previousRange['start'], $previousRange['end']);
    $customRangeCount = getDriverReviewsCount(getCurrentUserId(), $start_date, $end_date);

    return calculatePercentage($customRangeCount, $previousCount);
  }
}

if (!function_exists('getTotalDocumentsPercentage')) {
  function getTotalDocumentsPercentage($start_date = null, $end_date = null)
  {
    $sort = request('sort') ?? null;
    $previousRange = getPreviousDateRange($sort, request('start'), request('end'));
    $previousCount = getDriverDocumentsCount(getCurrentUserId(), $previousRange['start'], $previousRange['end']);
    $customRangeCount = getDriverDocumentsCount(getCurrentUserId(), $start_date, $end_date);

    return calculatePercentage($customRangeCount, $previousCount);
  }
}

if (!function_exists('getTotalRidesPercentage')) {
  function getTotalRidesPercentage($start_date = null, $end_date = null)
  {
    $sort = request('sort') ?? null;
    $previousRange = getPreviousDateRange($sort, request('start'), request('end'));
    $previousCount = getTotalRides($previousRange['start'], $previousRange['end']);
    $customRangeCount = getTotalRides($start_date, $end_date);

    return calculatePercentage($customRangeCount, $previousCount);
  }
}

if (!function_exists('getTotalRidesEarningsPercentage')) {
  function getTotalRidesEarningsPercentage($start_date = null, $end_date = null, $paymentMethod = null)
  {
    $sort = request('sort') ?? null;
    $previousRange = getPreviousDateRange($sort, request('start'), request('end'));
    $previousEarnings = getTotalRidesEarnings($previousRange['start'], $previousRange['end'], $paymentMethod);
    $customRangeEarnings = getTotalRidesEarnings($start_date, $end_date, $paymentMethod);

    return calculatePercentage($customRangeEarnings, $previousEarnings);
  }
}


if (!function_exists('calculatePercentage')) {
  function calculatePercentage($customRangeCount, $todayCount)
  {

    if ($todayCount == 0) {
      $todayCount = 1;
      $difference = 1;
      $percentage = ($customRangeCount / $todayCount) * 100;
    } else {
      $difference = $customRangeCount - $todayCount;
      $percentage = ($difference / $todayCount) * 100;
    }

    return [
      'status' => $difference > 0 ? 'increase' : ($difference < 0 ? 'decrease' : 'no_change'),
      'percentage' => number_format(($percentage), 2),
    ];
  }
}

if (!function_exists('getTotalDrivers')) {
  function getTotalDrivers($start_date = null, $end_date = null, $is_verified = null)
  {
    $query = Driver::where('status', true);

    if ($is_verified !== null) {
      $query->where('is_verified', $is_verified);
    }

    if ($start_date && $end_date) {
      return $query->whereBetween('created_at', [$start_date, $end_date])->count();
    }

    return $query->whereYear('created_at', date('Y'))
      ->whereMonth('created_at', date('m'))
      ->count();
  }
}

if (!function_exists('getTotalRides')) {
  function getTotalRides($start_date = null, $end_date = null)
  {
    $query = Ride::query();

    if ($start_date && $end_date) {
      $query->whereBetween('created_at', [$start_date, $end_date]);
    } else {
      $query->whereYear('created_at', date('Y'))
        ->whereMonth('created_at', date('m'));
    }

    if (getCurrentRoleName() == RoleEnum::DRIVER) {
      $query->where('driver_id', getCurrentUserId());
    }

    return $query->count();
  }
}

if (!function_exists('getTotalRidesEarnings')) {
  function getTotalRidesEarnings($start_date = null, $end_date = null, $paymentMethod = null,)
  {
    $query = Ride::query();

    if ($start_date && $end_date) {
      $query->whereBetween('created_at', [$start_date, $end_date]);
    } else {
      $query->whereYear('created_at', date('Y'))
        ->whereMonth('created_at', date('m'));
    }

    if (getCurrentRoleName() == RoleEnum::DRIVER) {
      $query->where('driver_id', getCurrentUserId());
    }

    if ($paymentMethod === 'cash') {
      $query->where('payment_method', 'cash');
    } elseif ($paymentMethod === 'online') {
      $query->whereNot('payment_method', 'cash');
    }

    return $query->sum('sub_total');
  }
}

if (!function_exists('getAllZones')) {
  function getAllZones()
  {
    return Zone::where('status', true)?->get(['id', 'name']);
  }
}

if (!function_exists('getAllVehicleTypes')) {
  function getAllVehicleTypes()
  {
    return VehicleType::where('status', true)->get();
  }
}

if (!function_exists('getAllServiceCategories')) {
  function getAllServiceCategories()
  {
    return ServiceCategory::where('status', true)->get();
  }
}

if (!function_exists('getAllVerifiedDrivers')) {
  function getAllVerifiedDrivers()
  {
    return Driver::where('is_verified', true)->where('status', true)->get();
  }
}

if (!function_exists('getAllDrivers')) {
  function getAllDrivers()
  {
    return Driver::where('status', true)->get();
  }
}

if (!function_exists('getAllServices')) {
  function getAllServices()
  {
    return Service::where('status', true)->get();
  }
}

if (!function_exists('getCurrentDriver')) {
  function getCurrentDriver()
  {
    return Driver::where('id', getCurrentUserId())->first();
  }
}

if (!function_exists('getZoneByPoint')) {
  function getZoneByPoint($latitude, $longitude)
  {
    $lat = (float) $latitude;
    $lng = (float) $longitude;
    $point = new Point($lat, $lng);
    return Zone::whereContains('place_points', $point)->get(['id', 'name', 'locations', 'amount']);
  }
}

if (!function_exists('getTaxidoSettings')) {
  function getTaxidoSettings()
  {
    return TaxidoSetting::pluck('taxido_values')?->first();
  }
}

if (!function_exists('getRideStatusIdByName')) {
  function getRideStatusIdByName($name)
  {
    return RideStatus::where('name', ucfirst($name))->value('id');
  }
}

if (!function_exists('getRideStatusIdBySlug')) {
  function getRideStatusIdBySlug($slug)
  {
    return RideStatus::where('slug', $slug)->value('id');
  }
}

if (!function_exists('couponIsEnable')) {
  function couponIsEnable()
  {
    $taxidoSettings = getTaxidoSettings();
    return $taxidoSettings['activation']['coupon_enable'];
  }
}

if (!function_exists('driverTipIsEnable')) {
  function driverTipIsEnable()
  {
    $taxidoSettings = getTaxidoSettings();
    return $taxidoSettings['activation']['driver_tips'];
  }
}

if (!function_exists('getDriversByZoneIds')) {
  function getDriversByZoneIds($zoneIds)
  {
    return Driver::whereRelation('zones', function ($zones) use ($zoneIds) {
      $zones->WhereIn('zone_id', $zoneIds);
    })->where('is_online', true)?->whereNull('deleted_at');
  }
}

if (!function_exists('getNearestDriversByZoneIds')) {
  function getNearestDriversByZoneIds($zoneIds, $coordinates, $vehicleTypeId = null)
  {
    $leastParts = [];
    $settings = getTaxidoSettings();
    $radius = $settings['location']['radius_meter'];
    $drivers = getDriversByZoneIds($zoneIds);

    if ($vehicleTypeId) {
      $drivers = $drivers->whereHas('vehicle_info', function ($query) use ($vehicleTypeId) {
        $query->where('vehicle_type_id', $vehicleTypeId);
      });
    }

    foreach ($coordinates as $coordinate) {
      $lat = $coordinate['lat'];
      $lng = $coordinate['lng'];
      $leastParts[] = "
      6371000 * acos(
        cos(radians(?)) * cos(radians(JSON_UNQUOTE(JSON_EXTRACT(location, '$[0].lat')))) *
        cos(radians(JSON_UNQUOTE(JSON_EXTRACT(location, '$[0].lng'))) - radians(?)) +
        sin(radians(?)) * sin(radians(JSON_UNQUOTE(JSON_EXTRACT(location, '$[0].lat'))))
        )
        ";
      $bindings[] = $lat;
      $bindings[] = $lng;
      $bindings[] = $lat;
    }

    $selectRaw = count($leastParts) > 1 ? 'LEAST(' . implode(',', $leastParts) . ') AS radius'
      : head($leastParts) . ' AS radius';
    return $drivers->selectRaw("users.*, $selectRaw", $bindings)
      ->having('radius', '<', $radius)
      ->orderBy('radius')
      ->get();
  }
}

if (!function_exists('getDriverWalletId')) {
  function getDriverWalletId($driver_id)
  {
    return Driver::findOrFail($driver_id)->wallet()->pluck('id')->first();
  }
}

if (!function_exists('getRiderWalletId')) {
  function getRiderWalletId($rider_id)
  {
    return Rider::findOrFail($rider_id)->wallet()?->pluck('id')?->first();
  }
}

if (!function_exists('riderWalletIsEnable')) {
  function riderWalletIsEnable()
  {
    $taxidoSettings = getTaxidoSettings();
    return $taxidoSettings['activation']['rider_wallet'];
  }
}

if (!function_exists('driverWalletIsEnable')) {
  function driverWalletIsEnable()
  {
    $taxidoSettings = getTaxidoSettings();
    return $taxidoSettings['activation']['driver_wallet'];
  }
}

if (!function_exists('isRideOtpRequired')) {
  function isRideOtpRequired()
  {
    $taxidoSettings = getTaxidoSettings();
    return $taxidoSettings['activation']['ride_otp'];
  }
}

if (!function_exists('getRoleNameByUserId')) {
  function getRoleNameByUserId($user_id)
  {
    return Driver::find($user_id)?->role?->name;
  }
}

if (!function_exists('getRolesNameByUserId')) {
  function getRolesNameByUserId($user_id)
  {
    return Rider::find($user_id)?->role?->name;
  }
}


if (!function_exists('getVehicleTaxRate')) {
  function getVehicleTaxRate($vehicle_type_id)
  {
    $tax_id = VehicleType::findOrFail($vehicle_type_id)?->value('tax_id');
    if ($tax_id) {
      return Tax::where([['id', $tax_id], ['status', true]])?->value('rate');
    }
  }
}

if (!function_exists('getRideStatusColorClasses')) {
  function getRideStatusColorClasses()
  {
    return [
      ucfirst(RideStatusEnum::REQUESTED) => 'requested',
      ucfirst(RideStatusEnum::SCHEDULED) => 'scheduled',
      ucfirst(RideStatusEnum::ACCEPTED) => 'success',
      ucfirst(RideStatusEnum::REJECTED) => 'rejected',
      ucfirst(RideStatusEnum::ARRIVED) => 'arrived',
      ucfirst(RideStatusEnum::STARTED) => 'started',
      ucfirst(RideStatusEnum::CANCELLED) => 'cancelled',
      ucfirst(RideStatusEnum::COMPLETED) => 'completed',
    ];
  }
}

if (!function_exists('getPaymentStatusColorClasses')) {
  function getPaymentStatusColorClasses()
  {
    return [
      ucfirst(PaymentStatus::COMPLETED) => 'completed',
      ucfirst(PaymentStatus::PENDING) => 'pending',
      ucfirst(PaymentStatus::PROCESSING) => 'positive',
      ucfirst(PaymentStatus::FAILED) => 'failed',
      ucfirst(PaymentStatus::EXPIRED) => 'expired',
      ucfirst(PaymentStatus::REFUNDED) => 'progress',
      ucfirst(PaymentStatus::CANCELLED) => 'critical',
    ];
  }
}

if (!function_exists('getRideStatusClassByStatus')) {
  function getRideStatusClassByStatus($status)
  {
    return getRideStatusColorClasses()[ucfirst($status)] ?? '';
  }
}

if (!function_exists('getServiceIdsBySlugs')) {
  function getServiceIdsBySlugs($slugs)
  {
    return Service::whereIn('slug', $slugs)?->pluck('id');
  }
}

if (!function_exists('getRideStatusIdsBySlugs')) {
  function getRideStatusIdsBySlugs($slugs)
  {
    return RideStatus::whereIn('slug', $slugs)?->pluck('id');
  }
}

if (!function_exists('getServiceById')) {
  function getServiceById($id)
  {
    return Service::where('id', $id)?->first();
  }
}

if (!function_exists('getServiceCategoryById')) {
  function getServiceCategoryById($id)
  {
    return ServiceCategory::where('id', $id)?->whereNull('deleted_at')->first();
  }
}

if (!function_exists('getServiceCategoryIdsBySlugs')) {
  function getServiceCategoryIdsBySlugs($slugs)
  {
    return ServiceCategory::whereIn('slug', $slugs)?->pluck('id');
  }
}

if (!function_exists('getServiceCategoryIdBySlug')) {
  function getServiceCategoryIdBySlug($slug)
  {
    return ServiceCategory::where('slug', $slug)->value('id');
  }
}

if (!function_exists('getUnverifiedDriver')) {
  function getUnverifiedDriver()
  {
    return Driver::where('is_verified', false)->where('system_reserve', false)->count();
  }
}

if (!function_exists('getAllDriverDocumentsCount')) {
  function getAllDriverDocumentsCount()
  {
    return DriverDocument::whereNull('deleted_at')?->count();
  }
}

if (!function_exists('getPendingWithdrawRequests')) {
  function getPendingWithdrawRequests()
  {
    return WithdrawRequest::where('status', RequestEnum::PENDING)?->count();
  }
}

if (!function_exists('isRideCompleted')) {
  function isRideCompleted($ride)
  {
    $completedStatusId = RideStatus::where('name', RideStatusEnum::COMPLETED)->value('id');
    return ($ride?->payment_status == PaymentStatus::COMPLETED
      && $ride?->ride_status?->id == $completedStatusId);
  }
}

if (!function_exists('isAlreadyReviewed')) {
  function isAlreadyReviewed($user_id, $ride_id, $type = 'driver')
  {
    return $type === 'driver'
      ? DriverReview::where('driver_id', $user_id)->where('ride_id', $ride_id)->exists()
      : RiderReview::where('rider_id', $user_id)->where('ride_id', $ride_id)->exists();
  }
}

if (!function_exists('getReviewRatings')) {
  function getReviewRatings($ride_id, $type = 'rider')
  {
    $reviewClass = $type === 'rider' ? RiderReview::class : DriverReview::class;
    $review = $reviewClass::where('ride_id', $ride_id)->get();
    return [
      $review->where('rating', 1)->count(),
      $review->where('rating', 2)->count(),
      $review->where('rating', 3)->count(),
      $review->where('rating', 4)->count(),
      $review->where('rating', 5)->count(),
    ];
  }
}

if (!function_exists('getCoupon')) { {
    function getCoupon($data)
    {
      return Coupon::where([['code', 'LIKE', '%' . $data . '%'], ['status', true]])
        ->orWhere('id', 'LIKE', '%' . $data . '%')
        ->whereNull('deleted_at')
        ->first();
    }
  }
}

if (!function_exists('getSubTotal')) {
  function getSubTotal($price, $quantity = 1)
  {
    return $price * $quantity;
  }
}

if (!function_exists('getRiderId')) {
  function getRiderId($request)
  {
    return $request->rider_id ?? getCurrentUserId();
  }
}

if (!function_exists('getTotalAmount')) {
  function getTotalAmount($rides)
  {
    $subtotal = [];
    foreach ($rides as $ride) {
      $subtotal[] = getSubTotal($ride);
    }

    return array_sum($subtotal);
  }
}

if (!function_exists('pushNotification')) {
  function pushNotification($pushNotification)
  {
    try {
      $projectId = null;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pushNotification));
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . getFCMAccessToken()]);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $result = curl_exec($ch);
      curl_close($ch);
    } catch (Exception $e) {
    }
  }
}

if (!function_exists('getFCMAccessToken')) {
  function getFCMAccessToken()
  {
    $client = new Google_Client();
    $client->setAuthConfig(public_path('admin/assets/firebase.json'));
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $client->refreshTokenWithAssertion();
    $token = $client->getAccessToken();

    return $token['access_token'];
  }
}

if (!function_exists('getVehicleType')) {
  function getVehicleType()
  {
    return VehicleType::all()->map(function ($vehicleType) {
      return [
        'id' => $vehicleType->id,
        'name' => $vehicleType->name,
        'image' => $vehicleType->vehicle_image?->original_url ?? asset('images/default.png'),
      ];
    });
  }
}

if (!function_exists('getPaymentAccount')) {
  function getPaymentAccount($user_id)
  {
    return PaymentAccount::where('user_id', $user_id)->first();
  }
}


  if (!function_exists('parcelOtpEnabled')) {
  function parcelOtpEnabled()
  {
    $taxidoSettings = getTaxidoSettings();
    return $taxidoSettings['activation']['parcel_otp'];
  }


  if (! function_exists('getTotalRideRequestsByServiceCategory')) {
      function getTotalRideRequestsByServiceCategory($serviceCategory = null, $start_date = null, $end_date = null)
      {
          $query = RideRequest::where('service_category_id', getServiceCategoryIdBySlug($serviceCategory))
              ->whereNull('deleted_at');

          if (getCurrentRoleName() == RoleEnum::DRIVER) {
              $query = $query->where('driver_id', getCurrentUserId());
          }

          if ($start_date && $end_date) {
              $query->whereBetween('created_at', [$start_date, $end_date]);
          }

          if (request()?->status) {
              $query->where('ride_status_id', getRideStatusIdByName(request()?->status));
          }

          return $query->count();
      }
  }


  if (!function_exists('getTotalRidesByServiceCategory')) {
    function getTotalRidesByServiceCategory($serviceCategory = null, $start_date = null, $end_date = null)
    {
      $query = Ride::where('service_category_id', getServiceCategoryIdBySlug($serviceCategory))
        ->whereNull('deleted_at');

      if (getCurrentRoleName() == RoleEnum::DRIVER) {
        $query = $query->where('driver_id', getCurrentUserId());
      }

      if ($start_date && $end_date) {
        $query->whereBetween('created_at', [$start_date, $end_date]);
      }
      if (request()?->status) {
      $query->where('rides.ride_status_id', getRideStatusIdByName(request()?->status));
      }
      return $query->count();
    }
  }

  if (!function_exists('getTotalDriverReviewsByServiceCategory')) {
    function getTotalDriverReviewsByServiceCategory($serviceCategory)
    {
      $query = DriverReview::where('service_category_id', getServiceCategoryIdBySlug($serviceCategory))
        ->whereNull('deleted_at');

      if (getCurrentRoleName() == RoleEnum::DRIVER) {
        $query = $query->where('driver_id', getCurrentUserId());
      }

      return $query->count();
    }
  }

  if (!function_exists('getTotalRiderReviewsByServiceCategory')) {
    function getTotalRiderReviewsByServiceCategory($serviceCategory)
    {
      $query = DriverReview::where('service_category_id', getServiceCategoryIdBySlug($serviceCategory))
        ->whereNull('deleted_at');

      if (getCurrentRoleName() == RoleEnum::RIDER) {
        $query = $query->where('rider_id', getCurrentUserId());
      }

      return $query->count();
    }
  }


  if (!function_exists('getTotalRidesByServices')) {
    function getTotalRidesByServices($service)
    {
      $serviceId = getServiceIdBySlug($service);

      $rides = Ride::where('service_id', $serviceId)
        ->whereYear('created_at', now()->year)
        ->whereNull('deleted_at')
        ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_rides')
        ->groupBy(DB::raw('MONTH(created_at)'));

      if (getCurrentRoleName() == RoleEnum::DRIVER) {
        $rides = $rides->where('driver_id', getCurrentUserId());
      }

      $data = $rides->pluck('total_rides', 'month')->toArray();

      $count = array_fill(1, 12, 0);

      foreach ($data as $month => $total) {
        $count[$month] = $total;
      }

      return $count;
    }
  }

  if (!function_exists('getMonthlyCommissions')) {
    function getMonthlyCommissions()
    {

      $query = CabCommissionHistory::whereYear('created_at', now()->year)
        ->whereNull('deleted_at')
        ->selectRaw('
                MONTH(created_at) as month,
                SUM(admin_commission) as total_admin_commission,
                SUM(driver_commission) as total_driver_commission
            ')
        ->groupBy('month');


      if (getCurrentRoleName() == RoleEnum::DRIVER) {
        $query = $query->where('driver_id', getCurrentUserId());
      }


      $commissions = $query->get();

      $adminCommission = array_fill(1, 12, 0);
      $driverCommission = array_fill(1, 12, 0);

      foreach ($commissions as $data) {
        $adminCommission[$data->month] = $data->total_admin_commission;
        $driverCommission[$data->month] = $data->total_driver_commission;
      }

      return [
        'admin_commission' => $adminCommission,
        'driver_commission' => $driverCommission,
      ];
    }
  }

  if (!function_exists('getServiceIdBySlug')) {
    function getServiceIdBySlug($slug)
    {
      return Service::where('slug', $slug)?->value('id');
    }
  }

  if (! function_exists('getTopDrivers')) {
      function getTopDrivers($start_date = null, $end_date = null)
      {
          $drivers = Driver::where('status', true)
              ->where('is_verified', true)
              ->whereBetween('created_at', [$start_date, $end_date])
              ->get()
              ->filter(function ($driver) {
                  return getTotalDriverRides($driver->id) > 0;
              });

          $drivers = $drivers->sortByDesc(function ($driver) {
              return getTotalDriverRides($driver->id);
          })->values();

          $drivers = $drivers->take(5);

          return $drivers;
      }
  }


  if (!function_exists('getTotalDriverRides')) {
    function getTotalDriverRides($driver_id)
    {
      return Ride::where('driver_id', $driver_id)->whereNull('deleted_at')?->count();
    }
  }

  if (!function_exists('getDriverWallet')) {
    function getDriverWallet($driver_id)
    {
      $driverWallet = DriverWallet::where('driver_id', $driver_id)->whereNull('deleted_at')?->first();
      return $driverWallet?->balance;
    }
  }

  if (!function_exists('getRiderAvgReviewsById')) {
    function getRiderAvgReviewsById($rider_id)
    {
      if($rider_id) {
        $rider = Rider::where('id', $rider_id)->with(['reviews'])->whereNull('deleted_at')?->first();
        if($rider) {
          return  (int) $rider?->reviews?->avg('rating');
        }
      }
      return 0;
    }
  }

  if (!function_exists('getRiderTotalReviewsById')) {
    function getRiderTotalReviewsById($rider_id)
    {
      if($rider_id) {
        $rider = Rider::where('id', $rider_id)->with(['reviews'])->whereNull('deleted_at')?->first();
        if($rider) {
          return  (int) count($rider?->reviews->toArray());
        }
      }
      return 0;
    }
  }


  if (!function_exists('getRecentRides')) {
    function getRecentRides($start_date, $end_date, $serviceCategory)
    {
      $query = Ride::where('service_category_id', $serviceCategory)->orderBy('created_at', 'desc')->limit(5);

      if (getCurrentRoleName() == RoleEnum::DRIVER) {
        $query = $query->where('driver_id', getCurrentUserId());
      }
      if ($start_date && $end_date) {
        $query->whereBetween('created_at', [$start_date, $end_date]);
      }


      return $query->get();
    }
  }

  function getStartAndEndDate($sort, $startDate = null, $endDate = null)
  {
    $startCurrentDate = Carbon::now();
    $endCurrentDate = Carbon::now();

    switch ($sort) {
      case 'today':
        return [
          'start' => $startCurrentDate->startOfDay(),
          'end' => $endCurrentDate->endOfDay(),
        ];

      case 'this_week':
        return [
          'start' => $startCurrentDate->startOfWeek(),
          'end' => $endCurrentDate->endOfWeek(),
        ];

      case 'this_month':
        return [
          'start' => $startCurrentDate->startOfMonth(),
          'end' => $endCurrentDate->endOfMonth(),
        ];

      case 'this_year':
        return [
          'start' => $startCurrentDate->startOfYear(),
          'end' => $endCurrentDate->endOfYear(),
        ];

      case 'custom':
        if ($startDate && $endDate) {
          return [
            'start' => Carbon::createFromFormat('m-d-Y', $startDate)->startOfDay(),
            'end' => Carbon::createFromFormat('m-d-Y', $endDate)->endOfDay(),
          ];
        }
        break;
      default:
        return [
          'start' => $startCurrentDate->startOfYear(),
          'end' => $endCurrentDate->endOfYear(),
        ];
    }
  }

  if (!function_exists('getTotalWithdrawals')) {
    function getTotalWithdrawals($start_date = null, $end_date = null)
    {
      $query = WithdrawRequest::query();

      if (getCurrentRoleName() == RoleEnum::DRIVER) {
        $query->where('driver_id', getCurrentUserId());
      }

      if ($start_date && $end_date) {
        $query->whereBetween('created_at', [$start_date, $end_date]);
      } else {
        $query->whereYear('created_at', date('Y'))
          ->whereMonth('created_at', date('m'));
      }

      return $query->sum('amount');
    }
  }

  if (!function_exists('getDriverDocumentsCount')) {
    function getDriverDocumentsCount($driverId, $start_date = null, $end_date = null)
    {
      $query = DriverDocument::where('driver_id', $driverId);

      if ($start_date && $end_date) {
        $query->whereBetween('created_at', [$start_date, $end_date]);
      } else {
        $query->whereYear('created_at', date('Y'))
          ->whereMonth('created_at', date('m'));
      }

      return $query->count();
    }
  }

  if (!function_exists('getDriverReviewsCount')) {
    function getDriverReviewsCount($driverId, $start_date = null, $end_date = null)
    {
      $query = DriverReview::where('driver_id', $driverId);

      if ($start_date && $end_date) {
        $query->whereBetween('created_at', [$start_date, $end_date]);
      } else {
        $query->whereYear('created_at', date('Y'))
          ->whereMonth('created_at', date('m'));
      }

      return $query->count();
    }
  }

  if (!function_exists('getDriverWalletBalance')) {
    function getDriverWalletBalance($driverId, $start_date = null, $end_date = null)
    {
      $query = DriverWallet::where('driver_id', $driverId);

      if ($start_date && $end_date) {
        $query->whereBetween('created_at', [$start_date, $end_date]);
      } else {
        $query->whereYear('created_at', date('Y'))
          ->whereMonth('created_at', date('m'));
      }

      $balance = $query->sum('balance');

      return $balance;
    }
  }

  if (!function_exists('getPreviousDateRange')) {
    function getPreviousDateRange($sort, $start_date = null, $end_date = null)
    {
      switch ($sort) {
        case 'today':
          return [
            'start' => Carbon::yesterday()->startOfDay(),
            'end' => Carbon::yesterday()->endOfDay(),
          ];

        case 'this_week':
          return [
            'start' => Carbon::now()->subWeek()->startOfWeek(),
            'end' => Carbon::now()->subWeek()->endOfWeek(),
          ];

        case 'this_month':
          return [
            'start' => Carbon::now()->startOfMonth()->subMonthsNoOverflow(),
            'end' => Carbon::now()->subMonthsNoOverflow()->endOfMonth(),
          ];

        case 'this_year':
          return [
            'start' => Carbon::now()->subYear()->startOfYear(),
            'end' => Carbon::now()->subYear()->endOfYear(),
          ];

        case 'custom':
          if ($start_date && $end_date) {
            return [
              'start' => Carbon::createFromFormat('m-d-Y', $start_date)->subYear()->startOfDay(),
              'end' => Carbon::createFromFormat('m-d-Y', $end_date)->subYear()->endOfDay(),
            ];
          }
          break;

        default:
          return [
            'start' => Carbon::now()->subMonth()->startOfMonth(),
            'end' => Carbon::now()->subMonth()->endOfMonth(),
          ];
      }
    }
  }

  if (!function_exists('getDriverById')) {
    function getDriverById($id)
    {
      return Driver::where('id', $id)?->whereNull('deleted_at')?->first();
    }
  }

  if (!function_exists('calculateRideDistance')) {
    function calculateRideDistance($origin, $destination)
    {

      $apiKey = env('GOOGLE_MAP_API_KEY');
      $url = "https://maps.googleapis.com/maps/api/distancematrix/json";

      $response = Http::get($url, [
        'origins' => $origin,
        'destinations' => $destination,
        'key' => $apiKey,
      ]);

      if ($response->ok()) {
        $data = $response->json();

        if ($data['status'] === 'OK') {
          $element = $data['rows'][0]['elements'][0];
          if ($element['status'] === 'OK') {
            $distanceText = $element['distance']['text'] ?? null;
            $duration = $element['duration']['text'] ?? null;

            if ($distanceText) {

              [$distanceValue, $distanceUnit] = explode(' ', $distanceText);

              return [
                'distance_value' => (float) $distanceValue,
                'distance_unit' => $distanceUnit,
                'duration' => $duration,
              ];
            }
          }
        }
        return null;
      }

      return null;
    }
  }
}