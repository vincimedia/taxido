<?php

namespace Modules\Taxido\Http\Traits;

use Exception;
use Carbon\Carbon;
use Modules\Taxido\Models\Ride;
use Modules\Taxido\Models\Coupon;

trait CouponTrait
{
  public function getCoupon($code)
  {
    return Coupon::where([['code', 'LIKE', '%' . $code . '%'], ['status', true]])
      ->orWhere('id', 'LIKE', '%' . $code . '%')
      ->whereNull('deleted_at')
      ->first();
  }

  public function updateCouponUsage($coupon_id)
  {
    return Coupon::findOrFail($coupon_id)->decrement('usage_per_coupon');
  }

  public function isApplicable($coupon, $ride)
  {
    $selectedServiceIds = $coupon?->services()?->pluck('service_id')?->toArray();
    if(!in_array($ride?->service_id, $selectedServiceIds ?? [])) {
      throw new Exception(__("#{$coupon?->code} code not applicable for {$ride?->service?->name} service."), 422);
    }

    $selectedServiceCategoryIds = $coupon?->service_categories()?->pluck('service_category_id')?->toArray();
    if(!in_array($ride?->service_category_id, $selectedServiceCategoryIds ?? [])) {
      throw new Exception(__("#{$coupon?->code} code not applicable for {$ride?->service_category?->name} service category."), 422);
    }

    $selectedVehicleTypeIds = $coupon?->vehicle_types()?->pluck('vehicle_type_id')?->toArray();
    if(!in_array($ride?->vehicle_type_id, $selectedVehicleTypeIds ?? [])) {
      throw new Exception(__("#{$coupon?->code} code not applicable for {$ride?->vehicle_type?->name} vehicle type."), 422);
    }

    return true;
  }

  public function isValidCoupon($coupon, $ride)
  {
    $rider_id = getCurrentUserId();
    $ride_fare = $ride?->ride_fare;
    if (couponIsEnable()) {
      if ($coupon && $this->isValidSpend($coupon, $ride_fare)) {
        if ($this->isCouponUsable($coupon, $rider_id) && $this->isNotExpired($coupon)) {
          if(!$coupon?->is_apply_all) {
            if($this->isApplicable($coupon, $ride)) {
              return true;
            }
          }

          return true;
        }
      }

      throw new Exception(__(
        'taxido::static.coupons.to_apply_coupon', [
          'code' => $coupon->code,
          'min_spend' => $coupon->min_spend
        ]), 422);
    }

    throw new Exception(__('taxido::static.coupons.coupon_feature_disabled'), 422);
  }

  public function isCouponUsable($coupon, $rider)
  {
    if (!$coupon->is_unlimited) {
      if ($coupon->usage_per_customer) {
        if (!$rider) {
          throw new Exception(__('taxido::static.coupons.login_required', [
            'code' => $coupon->code
          ]), 422);
        }

        $getCountUsedPerRider = $this->getCountUsedPerRider($coupon->id, $rider);
        if ($coupon->usage_per_customer <= $getCountUsedPerRider) {
          throw new Exception(__('taxido::static.coupons.coupon_max_usage_reached', [
            'couponCode' => $coupon->code,
            'usagePerCustomer' => $coupon->usage_per_customer
          ]), 422);
        }
      }

      if ($coupon->usage_per_coupon <= 0) {
        throw new Exception(__('taxido::static.coupons.usage_limit_reached', ['code' => $coupon->code, 'usage' => $coupon->usage_per_coupon]), 422);
      }
    }
    return true;
  }

  public function getCountUsedPerRider($coupon_id, $rider_id)
  {
    return Ride::where([['rider_id', $rider_id], ['coupon_id', $coupon_id]])?->count();
  }

  public function isValidSpend($coupon, $ride_fare)
  {
    return $ride_fare >= $coupon->min_spend;
  }

  public function isNotExpired($coupon)
  {
    if ($coupon->is_expired) {
      if (!$this->isOptimumDate($coupon)) {
        throw new Exception(__('taxido::static.coupons.date_range',
          ['code' => $coupon->code, 'start_date' => $coupon->start_date, 'end_date' => $coupon->end_date]
        ), 422);
      }
    }

    return true;
  }

  public function isOptimumDate($coupon)
  {
    $currentDate = Carbon::now()->format('Y-m-d');
    if (max(min($currentDate, $coupon->end_date), $coupon->start_date) == $currentDate) {
      return true;
    }

    return false;
  }

  public function fixedDiscount($subtotal, $couponAmount)
  {
    if ($subtotal >= $couponAmount && $subtotal > 0) {
      return $couponAmount;
    }

    return 0;
  }

  public function percentageDiscount($subtotal, $couponAmount)
  {
    if ($subtotal >= $couponAmount && $subtotal > 0) {
      return ($subtotal * $couponAmount) / 100;
    }

    return 0;
  }
}
