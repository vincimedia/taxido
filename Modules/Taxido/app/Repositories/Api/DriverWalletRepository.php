<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use App\Enums\PaymentType;
use App\Enums\WalletPointsDetail;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\DB;
use Modules\Taxido\Enums\RoleEnum;
use Modules\Taxido\Enums\RequestEnum;
use Modules\Taxido\Models\DriverWallet;
use Modules\Taxido\Models\WithdrawRequest;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Modules\Taxido\Http\Traits\WalletPointsTrait;
use Modules\Taxido\Events\CreateWithdrawRequestEvent;

class DriverWalletRepository extends BaseRepository
{
    use WalletPointsTrait;
    protected $fieldSearchable = [
        'title' => 'like'
    ];

    function model()
    {

        return DriverWallet::class;
    }

    public function boot()
    {
        try {

            $this->pushCriteria(app(RequestCriteria::class));
        } catch (ExceptionHandler $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function withdrawRequest($request)
    {
        DB::beginTransaction();
        try {
            $taxidoSettings = getTaxidoSettings();
            $roleName = getCurrentRoleName();

            if ($roleName == RoleEnum::DRIVER) {
                $driver_id = getCurrentUserId();
                $driverPaymentAccount = getPaymentAccount($driver_id);
                $this->verifyPaymentAccount($request, $driverPaymentAccount);

                $driverWallet = $this->getDriverWallet($driver_id);
                $driverBalance = $driverWallet->balance;

                $minWithdrawAmount = $taxidoSettings['driver_commission']['min_withdraw_amount'];

                if ($minWithdrawAmount > $request->amount) {
                    return response()->json([
                        'success' => false,
                        'message' => __('taxido::static.wallets.min_withdraw_amount', ['minWithdrawAmount' => $minWithdrawAmount]),
                    ]);
                }

                if ($driverBalance < $request->amount) {
                    return response()->json([
                        'success' => false,
                        'message' => __('taxido::static.wallets.insufficient_wallet_balance'),
                    ]);
                }

                $withdrawRequest = WithdrawRequest::Create([
                    'amount' => $request->amount,
                    'message' => $request->message,
                    'status' => RequestEnum::PENDING,
                    'driver_id' => $driver_id,
                    'payment_type' => $request->payment_type,
                    'driver_wallet_id' => $driverWallet->id,
                ]);
                $driverWallet = $this->debitDriverWallet($driver_id, $request->amount, WalletPointsDetail::WITHDRAW);
                event(new CreateWithdrawRequestEvent($withdrawRequest));

                $withdrawRequest->user;
                DB::commit();

                return $withdrawRequest;
            }

            throw new Exception(__('taxido::static.wallets.selected_user'), 400);

        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function verifyPaymentAccount($request, $driverPaymentAccount)
    {
        if (! $driverPaymentAccount) {
            throw new Exception(__('taxido::static.wallets.add_payment_account_before_withdrawal'), 400);
        }

        if ($request->payment_type == PaymentType::PAYPAL && ! $driverPaymentAccount->paypal_email) {
            throw new Exception(__('taxido::static.wallets.add_paypal_email_before_withdrawal'), 400);
        }

        if ($request->payment_type == PaymentType::BANK) {
            if (! $driverPaymentAccount->bank_account_no || ! $driverPaymentAccount->swift || ! $driverPaymentAccount->bank_name || ! $driverPaymentAccount->bank_holder_name) {
                throw new Exception(__('taxido::static.wallets.add_bank_details_before_withdrawal'), 400);
            }
        }
    }
}
