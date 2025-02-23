<?php

namespace Modules\Taxido\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use Modules\Taxido\Http\Traits\WalletPointsTrait;
use Modules\Taxido\Repositories\Api\RiderWalletRepository;

class RiderWalletController extends Controller
{
    use WalletPointsTrait;

    protected $repository;

    public function __construct(RiderWalletRepository $repository)
    {
        if (riderWalletIsEnable()) {
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
        $rider_id = $request->rider_id ?? getCurrentUserId();

        $riderWallet = $this->repository->findByField('rider_id', $rider_id)->first();

        if (!$riderWallet) {
            $riderWallet = $this->getRiderWallet($request->rider_id ?? getCurrentUserId());
            $riderWallet = $riderWallet->fresh();
        }

        $riderWalletHistory = $riderWallet?->histories()->where('type', 'LIKE', "%{$request->search}%");

        if ($request->start_date && $request->end_date) {
            $riderWalletHistory->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $paginate = $request->paginate ?? $riderWallet?->histories()->count();

        $riderWallet?->setRelation('histories', $riderWalletHistory->paginate($paginate));

        return $riderWallet;
    }

    public function topUp(Request $request)
    {
        return $this->repository->topUp($request);
    }
}
