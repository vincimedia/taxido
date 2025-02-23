<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Enums\RoleEnum;
use Modules\Taxido\Models\WithdrawRequest;

class WithdrawRequestTable
{
  protected $request;
  protected $withdrawRequest;

  public function __construct(Request $request)
  {
    $this->withdrawRequest = new WithdrawRequest();
    $this->request = $request;
  }

  public function getData()
  {
    $withdrawRequests = $this->withdrawRequest;

    if (getCurrentRoleName() == RoleEnum::DRIVER) {
        $driverId = getCurrentDriver()?->id;
    $withdrawRequests = $withdrawRequests->where('driver_id', $driverId);
    }
    return $withdrawRequests->whereNull('deleted_at')->orderBy('created_at', 'desc')->paginate($this->request?->paginate);
  }


  public function generate()
  {
    $withdrawRequests = $this->getData();
    $defaultCurrency = getDefaultCurrency()->symbol;

    if (!empty($withdrawRequests)) {
      $withdrawRequests?->each(function ($item) use ($defaultCurrency) {
        $item->driver_name = $item?->driver->name ?? null;
        $item->status = ucfirst($item->status);
        $item->driver_email =  $item?->driver?->email;
        $item->driver_profile = $item?->driver?->profile_image_id ?? null;
        $item->formatted_amount = $defaultCurrency . number_format($item->amount, 2);
      });
    }

    $withdrawRequests->each(function ($withdrawRequest) use ($defaultCurrency){
      $withdrawRequest->formatted_amount = $defaultCurrency . number_format($withdrawRequest->amount, 2);
    });

    $tableConfig = [
      'columns' => [
        ['title' => 'Driver', 'field' => 'driver_name', 'email' => 'driver_email', 'profile_image' => 'driver_profile',   'sortable' => true],
        ['title' => 'Amount', 'field' => 'formatted_amount', 'sortable' => true],
        ['title' => 'Status', 'field' => 'status', 'type' => 'badge','colorClasses' => ['Pending' => 'warning', 'Approved' => 'primary', 'Rejected' => 'danger'], 'sortable' => true],
        ['title' => 'Created At', 'field' => 'created_at', 'sortable' => true],
        ['title' => 'Action', 'type' => 'action', 'permission' => ['withdraw_request.action'], 'sortable' => false],
      ],
      'data' => $withdrawRequests,
      'actions' => [[]],
      'bulkactions' => ['title'],
      'viewActionBox' => ['view' => 'taxido::admin.withdraw-request.show', 'field' => 'withdrawRequest', 'type' => 'action'],
      'total' => $this->withdrawRequest->count()
    ];

    return $tableConfig;
  }
}
