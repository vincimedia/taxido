<?php

namespace Modules\Taxido\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Modules\Taxido\Enums\RoleEnum;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use Modules\Taxido\Models\WithdrawRequest;
use Modules\Taxido\Http\Traits\WalletPointsTrait;
use Modules\Taxido\Repositories\Api\DriverWalletRepository;

class DriverWalletController extends Controller
{
    use WalletPointsTrait;

    protected $repository;

    public function __construct(DriverWalletRepository $repository)
    {
        if (driverWalletIsEnable()) {
            $this->repository = $repository;
        } else {
            throw new ExceptionHandler(__('static.wallet.disabled'), 400);
        }
    }

    /**
     * Display Rider Wallet Transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            return $this->filter($request);
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function filter(Request $request)
    {
        $driver_id = $request->driver_id ?? getCurrentUserId();
        $driverWallet = $this->repository->findByField('driver_id', $driver_id)->first();

        if(!$driverWallet)
        {
            $driverWallet = $this->getDriverWallet($request->driver_id ?? getCurrentUserId());
            $driverWallet = $driverWallet->fresh();
        }

        $driverWalletHistory = $driverWallet?->histories()->where('type', 'LIKE', "%{$request->search}%");

        if ($request->start_date && $request->end_date) {
            $driverWalletHistory->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $paginate = $request->paginate ?? $driverWallet?->histories()->count();
        $driverWallet?->setRelation('histories', $driverWalletHistory->paginate($paginate));
        return $driverWallet;
    }

    public function getWithdrawRequest(Request $request) {
        try {
            $withdrawRequest = WithdrawRequest::query();
            $WithdrawRequest = $this->withdrawRequestfilter($withdrawRequest, $request);
            return $WithdrawRequest->latest('created_at')->paginate($request->paginate ?? $WithdrawRequest->count());

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function withdrawRequestfilter($withdrawRequest, $request)
    {
        $roleName = getCurrentRoleName();
        if ($roleName == RoleEnum::DRIVER) {
            $withdrawRequest = $withdrawRequest->where('driver_id',getCurrentUserId());
        }

        if ($request->field && $request->sort) {
            $withdrawRequest = $withdrawRequest->orderBy($request->field, $request->sort);
        }

        if ($request->start_date && $request->end_date) {
            $withdrawRequest = $withdrawRequest->whereBetween('created_at',[$request->start_date, $request->end_date]);
        }

        return $withdrawRequest;
    }


    public function withdrawRequest(Request $request)
    {
        return $this->repository->withdrawRequest($request);
    }
}
