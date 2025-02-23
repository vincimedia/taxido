<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use Carbon\Carbon;
use App\Enums\PaymentMode;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Taxido\Models\Ride;
use App\Http\Traits\PaymentTrait;
use Illuminate\Support\Facades\DB;
use Modules\Taxido\Enums\RoleEnum;
use Nwidart\Modules\Facades\Module;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Enums\ServicesEnum;
use Modules\Taxido\Enums\WalletDetail;
use Modules\Taxido\Models\RiderWallet;
use Modules\Taxido\Models\DriverWallet;
use Modules\Taxido\Enums\RideStatusEnum;
use Modules\Taxido\Http\Traits\RideTrait;
use Modules\Taxido\Http\Traits\CouponTrait;
use Modules\Taxido\Models\DriverSubscription;
use Modules\Taxido\Http\Traits\CommissionTrait;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class RideRepository extends BaseRepository
{
    use RideTrait, CouponTrait, PaymentTrait, CommissionTrait;

    protected $fieldSearchable = [
        'ride_number' => 'like',
    ];

    public function model()
    {
        return Ride::class;
    }

    public function boot()
    {
        try {

            $this->pushCriteria(app(RequestCriteria::class));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        return $this->model?->findOrFail($id);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $roleName       = getCurrentRoleName();
            $ride           = $this->model->findOrFail($id);
            $ride_status_id = getRideStatusIdBySlug($request['status']);
            if ($request['status'] == RideStatusEnum::CANCELLED) {
                if ($ride->ride_status?->slug == RideStatusEnum::CANCELLED) {
                    throw new Exception(__('taxido::static.rides.ride_already_cancelled'), 400);
                }

                if (in_array($ride->ride_status?->slug, [RideStatusEnum::STARTED, RideStatusEnum::ARRIVED, RideStatusEnum::COMPLETED])) {
                    throw new Exception(__('taxido::static.rides.ride_cannot_cancel'), 400);
                }

                $platform_fees = $this->getPlatformFees();
                $ride_fare  = $ride?->vehicle_type?->cancellation_charge ?? 0;
                $tax = $ride?->tax ?? 0;
                $subTotal = $ride_fare + $platform_fees;
                $ride->update([
                    'ride_status_id' => $ride_status_id,
                    'platform_fees' => $platform_fees,
                    'ride_fare' => $ride_fare,
                    'tax' => $tax,
                    'sub_total' => $subTotal,
                    'total' => $subTotal + $tax,
                    'cancellation_reason' => $request['cancellation_reason'] ?? null,
                ]);

                $driver = getDriverById($ride?->driver_id);
                $driver?->update([
                    'is_on_ride' => false,
                ]);
            }

            if ($roleName == RoleEnum::DRIVER && $request['status'] == RideStatusEnum::COMPLETED) {
                $ride_status_id = getRideStatusIdBySlug(RideStatusEnum::COMPLETED);
                if ($ride->driver_id != getCurrentUserId()) {
                    throw new Exception(__('taxido::static.rides.only_assigned_driver'), 400);
                }

                if ($ride?->service?->slug == ServicesEnum::PARCEL) {
                    if (parcelOtpEnabled() && ! $ride?->is_otp_verified) {
                        if (isset($request['parcel_delivered_otp'])) {
                            if (! is_null($request['parcel_delivered_otp'])) {
                                if ($ride?->parcel_delivered_otp == $request['parcel_delivered_otp']) {
                                    $ride?->update([
                                        'is_otp_verified'        => true,
                                        'parcel_otp_verified_at' => now(),
                                    ]);

                                    $ride = $ride?->refresh();
                                }
                            }
                        }
                        if (! $ride?->parcel_otp_verified_at) {
                            throw new Exception(__('taxido::static.rides.otp_not_verified_for_parcel'), 400);
                        }
                    }
                }

                $ride->update(['ride_status_id' => $ride_status_id]);
                $driver = getDriverById($ride?->driver_id);
                $driver?->update([
                    'is_on_ride' => false,
                ]);
            }

            DB::commit();
            $this->updateRideStatusActivities($ride, $request['status'], $ride->updated_at);
            $ride = $ride?->refresh();
            return json_decode($ride?->toJson());
        } catch (Exception $e) {
            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function startRide($request)
    {
        DB::beginTransaction();
        try {

            $roleName = getCurrentRoleName();
            if ($roleName == RoleEnum::DRIVER) {
                $ride = $this->model->findOrFail($request?->ride_id);
                $service = getServiceById($ride->service_id);
                if ($ride->otp != $request?->otp) {
                    throw new Exception(__('taxido::static.rides.invalid_otp'), 400);
                }

                if ($ride->driver_id != getCurrentUserId()) {
                    throw new Exception(__('taxido::static.rides.only_assigned_driver'), 400);
                }

                if ($service?->slug != ServicesEnum::PARCEL) {
                    $ride->update([
                        'ride_status_id'       => getRideStatusIdBySlug(RideStatusEnum::STARTED),
                        'parcel_delivered_otp' => rand(1000, 9999),
                    ]);
                } else {
                    $ride?->update([
                        'is_otp_verified' => true,
                        'start_time'      => $request?->start_time,
                        'ride_status_id'  => getRideStatusIdBySlug(RideStatusEnum::STARTED),
                    ]);
                }

                $ride->save();
                DB::commit();

                $ride = $ride->fresh();
                $driver = getDriverById($ride?->driver_id);
                $driver?->update([
                    'is_on_ride' => true,
                ]);

                $this->updateRideStatusActivities($ride, RideStatusEnum::STARTED, $ride->updated_at);
                return $ride;
            }

            throw new Exception(__('taxido::static.rides.user_must_be_driver'), 400);
        } catch (Exception $e) {

            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyOtp($request)
    {
        DB::beginTransaction();
        try {

            if (parcelOtpEnabled()) {
                $roleName = getCurrentRoleName();
                if ($roleName != RoleEnum::DRIVER) {
                    throw new Exception(__('taxido::static.rides.user_must_be_driver'), 400);
                }

                $ride = $this->model->findOrFail($request->ride_id);
                if ($ride->service_id != getServiceIdBySlug(ServicesEnum::PARCEL)) {
                    throw new Exception(__('taxido::static.rides.invalid_service_type'), 400);
                }

                if ($ride->otp != $request->otp) {
                    throw new Exception(__('taxido::static.rides.invalid_otp'), 400);
                }

                $ride?->update([
                    'is_otp_verified'        => true,
                    'parcel_otp_verified_at' => now(),
                ]);

                DB::commit();
                return $ride;
            }

            throw new Exception(__('taxido::static.rides.parcel_otp_disabled'), 400);
        } catch (Exception $e) {

            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyCoupon($request)
    {
        try {

            $rideDiscount = 0;
            $ride = $this->show($request->ride_id);
            $coupon = $this->getCoupon($request->coupon);
            if ($coupon && $ride) {
                if ($this->isValidCoupon($coupon, $ride)) {
                    switch ($coupon->type) {
                        case 'fixed':
                            $rideDiscount = $this->fixedDiscount($ride?->sub_total, $coupon->amount);
                            break;

                        case 'percentage':
                            $rideDiscount = $this->percentageDiscount($ride?->sub_total, $coupon->amount);
                            break;

                        default:
                            $rideDiscount = 0;
                    }
                }
            }

            return [
                'total_coupon_discount' => (float) $rideDiscount,
                'success'               => true,
            ];
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function payment($request)
    {
        DB::beginTransaction();
        try {

            $ride     = $this->model->findOrFail($request->ride_id);
            $settings = getTaxidoSettings();
            if ($request->driver_tip && ! $ride->driver_tips) {
                if ($settings['activation']['driver_tips']) {
                    $ride->driver_tips = $request->driver_tip;
                    $ride->comment = $request->comment;
                    $ride->sub_total = $ride->sub_total + $request->driver_tip;
                    $ride->total = $ride->total + $request->driver_tip;
                    $ride->save();
                    $ride = $ride->fresh();
                }
            }

            if ($request->coupon) {
                $discount = $this->verifyCoupon($request);
                if (isset($coupon['total_coupon_discount'])) {
                    $coupon = $this->getCoupon($request->coupon);
                    if ($coupon) {
                        $ride->coupon_id = $coupon?->id;
                        $ride->coupon_total_discount = $discount['total_coupon_discount'] ?? 0;
                        $ride->sub_total = $ride->sub_total - $discount['total_coupon_discount'] ?? 0;
                        $ride->total = $ride->total - $discount['total_coupon_discount'] ?? 0;
                        $ride->save();
                    }
                }
            }

            if ($ride->wallet_balance) {
                $riderId = getCurrentUserId();
                if ($this->verifyRiderWallet($riderId, $ride?->sub_total)) {
                    $this->debitRiderWallet($riderId, $ride?->sub_total, "Wallet amount successfully debited for ride #{$ride?->ride_number}.");
                }

                $ride->wallet_balance = - ($ride?->sub_total);
                $ride->total -= $ride?->sub_total;
                $ride->sub_total = 0;
                $ride->save();
            }

            DB::commit();
            if ($request->payment_method != PaymentMethod::CASH) {
                if(!$settings['activation']['online_payments']) {
                    throw new Exception(__('taxido::static.online_payments_is_disabled'), 400);
                }

                $module = Module::find($request->payment_method);
                if (!is_null($module) && $module?->isEnabled()) {
                    $moduleName = $module->getName();
                    $payment    = 'Modules\\' . $moduleName . '\\Payment\\' . $moduleName;
                    if (class_exists($payment) && method_exists($payment, 'getIntent')) {
                        if ($ride->payment_method != PaymentMethod::CASH) {
                            $ride->payment_status = PaymentStatus::PENDING;
                            $ride->payment_mode   = PaymentMode::ONLINE;
                            $processing_fee       = config($request->payment_method)['processing_fee'] ?? 0.0;
                            $ride->processing_fee = $processing_fee ?? 0;
                            $ride->total += $processing_fee;
                            $ride->save();
                        }

                        $request->merge([
                            'type' => 'ride',
                        ]);

                        return $payment::getIntent($ride, $request);

                    } else {

                        throw new Exception(__('static.booking.payment_module_not_found'), 400);
                    }
                }

                throw new Exception(__('taxido::static.rides.selected_payment_module_not_found'), 400);

            } elseif ($request->payment_method == PaymentMethod::CASH) {
                if(!$settings['activation']['cash_payments']) {
                    throw new Exception(__('taxido::static.cash_payments_is_disabled'), 400);
                }

                $ride->payment_mode = PaymentMode::OFFLINE;
                $ride->save();
                $request->merge(['type' => 'ride']);
                return $this->paymentStatus($ride, PaymentStatus::COMPLETED, $request);
            }

            throw new Exception(__('static.invalid_payment_method'), 400);
        } catch (Exception $e) {

            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyPayment($request)
    {
        try {

            $paymentTransaction = self::getPaymentTransactions($request->item_id, $request?->type, $request->transaction_id);
            if ($paymentTransaction) {
                $item           = null;
                $payment_method = $paymentTransaction?->payment_method;

                switch ($paymentTransaction?->type) {
                    case 'wallet':
                        $currentRoleName = getCurrentRoleName();
                        if ($currentRoleName === RoleEnum::DRIVER) {
                            $item = DriverWallet::findOrFail($request?->item_id);
                        } elseif ($currentRoleName === RoleEnum::RIDER) {
                            $item = RiderWallet::findOrFail($request?->item_id);
                        }
                        break;

                    case 'subscription':
                        $item = $this->getDriverSubscription($request?->item_id);
                        break;

                    case 'ride':
                        $item                 = $this->model->findOrFail($request?->item_id);
                        $item->payment_method = $payment_method;
                        $item->save();
                        $item = $item->fresh();
                }

                if (! $paymentTransaction?->is_verified) {
                    if ($item && $payment_method) {
                        if ($payment_method != PaymentMethod::CASH) {
                            $payment = Module::find($payment_method);
                            if (! is_null($payment) && $payment?->isEnabled()) {
                                $request['amount']         = $paymentTransaction?->amount;
                                $request['transaction_id'] = $paymentTransaction?->transaction_id;
                                $payment_status            = $paymentTransaction?->payment_status;
                                $paymentTransaction?->update([
                                    'is_verified' => true,
                                ]);
                                return $this->paymentStatus($item, $payment_status, $request);
                            }
                        } elseif ($payment_method == PaymentMethod::CASH) {
                            $payment_status = PaymentStatus::COMPLETED;
                            $paymentTransaction?->update([
                                'is_verified' => true,
                            ]);
                            return $this->paymentStatus($item, $payment_status, $request);
                        }

                        throw new Exception(__('static.payment_methods.not_found'), 400);
                    }
                }

                return $item;
            }

            throw new Exception(__('static.invalid_details'), 400);
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function getDriverSubscription($item_id)
    {
        return DriverSubscription::findOrFail($item_id);
    }

    public function paymentStatus($item, $status, $request)
    {
        if ($status) {
            if ($request->type == 'ride') {
                $item?->update([
                    'payment_status' => $status,
                ]);

                if ($status == PaymentStatus::COMPLETED) {
                    $ride_status_id = getRideStatusIdBySlug(RideStatusEnum::COMPLETED);
                    if ($ride_status_id) {
                        $item->ride_status_id = $ride_status_id;
                        $item->save();
                        $item = $item?->fresh();
                    }

                    $this->calAdminDriverCommission($item);
                }
            } elseif ($request->type == 'wallet') {
                if ($status == PaymentStatus::COMPLETED) {
                    $item->increment('balance', $request->amount);
                    $transaction_id = $request?->transaction_id;
                    $this->storeTransaction($item, TransactionType::CREDIT, WalletDetail::TOPUP, $request->amount, null, $transaction_id);
                }
            } elseif ($request->type == 'subscription') {
                if ($status == PaymentStatus::COMPLETED) {
                    $item?->update([
                        'payment_status' => $status,
                    ]);

                    return $item;
                }
            }
        }

        return $item;
    }

    public function getInvoice($request)
    {
        try {

            $ride = $this->verifyRideNumber($request->ride_number);
            $roleName = getCurrentRoleName();
            if ($ride->rider_id != getCurrentUserId() && $roleName == RoleEnum::RIDER) {
                throw new Exception(__('errors.not_created_ride'), 400);
            }

            $invoice = [
                'ride'     => $ride,
                'settings' => getTaxidoSettings(),

            ];

            return Pdf::loadView('taxido::emails.invoice', $invoice)->download('ride_invoice_' . $ride->ride_number . '.pdf');
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyRideNumber($ride_number)
    {
        try {

            $ride = $this->model->where('ride_number', $ride_number)?->first();
            if (! $ride) {
                throw new Exception(__('errors.invalid_ride_number'), 400);
            }
            return $ride;
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function fetchTodayScheduleRide()
    {
        try {

            $today = now()->format('Y-m-d');
            $rides = Ride::whereDate('start_time', $today)?->whereNull('deleted_at')?->get();
            foreach ($rides as $ride) {
                $startTime = Carbon::parse($ride->start_time);
                $reminderTime = $startTime->subMinutes(15);
                if (now() >= $reminderTime && now() < $startTime) {
                    $ride?->update([
                        'ride_status_id' => getRideStatusIdBySlug(RideStatusEnum::REQUESTED),
                    ]);
                }
            }

            return $rides;
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
