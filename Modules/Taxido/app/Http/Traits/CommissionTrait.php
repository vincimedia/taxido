<?php

namespace Modules\Taxido\Http\Traits;

use App\Enums\PaymentMode;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Modules\Taxido\Models\Ride;
use Modules\Taxido\Models\Plan;
use App\Enums\WalletPointsDetail;
use Modules\Taxido\Enums\RideStatusEnum;
use Modules\Taxido\Models\CabCommissionHistory;

trait CommissionTrait
{
    use WalletPointsTrait;

    public function calAdminDriverCommission($ride)
    {
        $settings = getTaxidoSettings();
        if ($settings['driver_commission']['status']) {
            if ($ride?->payment_status == PaymentStatus::COMPLETED &&
                $ride?->ride_status?->slug == RideStatusEnum::COMPLETED
            ) {
                $vehicleType = $ride?->vehicle_type;
                $subTotal = $ride?->sub_total;
                $adminCommission = $this->getAdminCommission($subTotal, $vehicleType);
                $driverCommission = $subTotal - $adminCommission;
                $driverId = $ride?->driver_id;
                $driver = getDriverById($driverId);
                if($driver) {
                    if($driver?->subscription) {
                        if($driver?->subscription?->is_active) {
                            if($driver?->subscription) {
                                $plan = Plan::with(['service_categories'])?->where('id', $driver?->subscription?->plan_id)
                                    ?->whereNull('deleted_at')
                                    ?->where('status', true)
                                    ?->first();
                                if($plan) {
                                    $categoryIds = $plan?->service_categories?->pluck('id')?->toArray();
                                    if(in_array($ride?->service_category_id, $categoryIds)) {
                                        $adminCommission = 0;
                                        $driverCommission = $subTotal - $adminCommission;
                                    }
                                }

                            }
                        }
                    }
                }

                if (!$this->isExistsCommissionHistory($ride)) {
                    if( $ride?->payment_method == PaymentMethod::CASH ||
                        $ride?->payment_mode == PaymentMode::OFFLINE) {
                        $this->debitDriverWallet($driverId, $adminCommission, __('taxido::static.messages.admin_debited_commission'));
                        if($ride?->tax > 0) {
                            $this->debitDriverWallet($driverId, $ride->tax, __('taxido::static.messages.admin_debited_tax'));
                        }

                        if($ride?->platform_fees > 0) {
                            $this->debitDriverWallet($driverId, $ride->tax, __('taxido::static.messages.admin_debited_platform_fee'));
                        }

                        $this->createCommissionHistory($ride, $adminCommission, $driverCommission);

                    } elseif ($ride?->payment_mode == PaymentMode::ONLINE) {
                        $this->creditDriverWallet($driverId, $driverCommission, WalletPointsDetail::COMMISSION);
                        $this->createCommissionHistory($ride, $adminCommission, $driverCommission);
                    }
                }
            }
        }

        return $ride;
    }

    public function createCommissionHistory($ride, $adminCommission, $driverCommission)
    {
        $ride->commission_history()->create([
            'admin_commission' => $adminCommission,
            'driver_commission' => $driverCommission,
            'driver_id' => $ride?->driver_id,
            'commission_rate' => $ride?->vehicle_type?->commission_rate,
            'commission_type' => $ride?->vehicle_type?->commission_type,
        ]);

        return $ride;
    }

    public function getAdminCommission($subTotal, $vehicleType)
    {
        $commissionRate = $vehicleType?->commission_rate;
        if ($vehicleType?->commission_type == 'percentage') {
            return ($subTotal * $commissionRate) / 100;
        }

        return $commissionRate;
    }

    public function isExistsCommissionHistory(Ride $ride)
    {
        return CabCommissionHistory::where('ride_id', $ride?->id)->exists();
    }
}
