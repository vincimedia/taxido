<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Enums\RoleEnum;
use Modules\Taxido\Models\RiderWalletHistory;

class RiderWalletTable
{
    protected $history;
    protected $request;

    public function __construct(Request $request)
    {
        $this->history = new RiderWalletHistory();
        $this->request = $request;
    }

    public function getData()
    {
        if (request()->has('rider_id') || getCurrentRoleName() == RoleEnum::RIDER) {

            $rider_id = request()->rider_id ?? getCurrentUserId();
            $rider_wallet_id = getRiderWalletId($rider_id);

            $histories = $this->history->where('rider_wallet_id', $rider_wallet_id);
            if (request()->has('s')) {
                return $histories->withTrashed()
                    ?->where('type', 'LIKE', "%" . request()->s . "%")
                    ?->orWhere('amount', 'LIKE', "%" . request()->s . "%")
                    ?->orWhere('detail', 'LIKE', "%" . request()->s . "%")?->paginate($this->request?->paginate);
            }

            if ($this->request->has('orderby') && $this->request->has('order')) {
                return $this->applySorting($histories)->paginate($this->request?->paginate);
            }
            return $histories->whereNull('deleted_at')?->latest()->paginate($this->request?->paginate);
        }
        return [];
    }

    public function applySorting($histories)
    {
        $orderby = $this->request->orderby;
        $order = $this->request->order;
        return $histories->orderBy($orderby, $order);
    }

    public function generate()
    {
        $histories = $this->getData();
        $defaultCurrency = getDefaultCurrency()->symbol;

        if (!empty($histories)) {
            $histories?->each(function ($item) use ($defaultCurrency) {
                $item->type = ucfirst($item->type);
                $item->formatted_amount = $defaultCurrency . number_format($item->amount, 2);
            });
        }

        $tableConfig = [
            'columns' => [
                ['title' => 'Amount', 'field' => 'formatted_amount', 'imageField' => null, 'sortable' => true, 'sortField' =>  'amount'],
                ['title' => 'Type', 'field' => 'type', 'type' => 'badge', 'colorClasses' => ['Credit' => 'primary', 'Debit' => 'danger'], 'imageField' => null, 'sortable' => true],
                ['title' => 'Remark', 'field' => 'detail', 'imageField' => null, 'sortable' => true, 'sortField' =>  'detail'],
                ['title' => 'Created At', 'field' => 'created_at', 'sortable' => true],
            ],
            'data' => $histories,
            'total' => $this->history->count()
        ];

        return $tableConfig;
    }
}
