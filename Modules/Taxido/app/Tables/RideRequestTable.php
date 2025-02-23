<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Enums\RoleEnum;
use Modules\Taxido\Models\RideRequest;
use Modules\Taxido\Enums\ServiceCategoryEnum;

class RideRequestTable
{

  protected $rideRequest;

  protected $request;

  protected $sortableColumns = [
    'rider.name',
    'driver.name',
    'service.name',
    'service_category.name',
    'ride_fare',
    'created_at',
  ];

  public function __construct(Request $request)
  {
    $this->rideRequest = RideRequest::query();
    $this->request = $request;
  }

  public function getRideRequests()
  {
    return $this->rideRequest;
  }

  public function getData()
  {
    $rideRequests = $this->getRideRequests();
    $roleName = getCurrentRoleName();
    if ($roleName == RoleEnum::DRIVER) {
      $rideRequests = $rideRequests->where('driver_id', getCurrentUserId());
    }

    $rideRequests = $this->sorting($rideRequests);

    if ($this->request->has('filter')) {
      if($this->request->get('filter') === 'all') {
        $rideRequests->whereNull('ride_requests.deleted_at');
      }
      else {
        $rideRequests = $rideRequests->whereNull('ride_requests.deleted_at')->where('ride_requests.service_category_id', getServiceCategoryIdBySlug($this->request->get('filter')));
      }
    }

    if (isset($this->request->s)) {
      $rideRequests =  $rideRequests->withTrashed()->where('ride_number', 'LIKE', "%" . $this->request->s . "%")
        ->orWhere('total', 'LIKE', "%" . $this->request->s . "%");
    }

    return $rideRequests?->latest()?->paginate($this->request?->paginate);
  }

  public function generate()
  {
    $rideRequests = $this->getData();
    $defaultCurrency = getDefaultCurrency()?->symbol;
    $rideRequests->each(function ($rideRequest) use ($defaultCurrency) {
      $rideRequest->formatted_total = $defaultCurrency . number_format($rideRequest->ride_fare, 2);
      $rideRequest->date = $rideRequest?->created_at->format('Y-m-d h:i:s A');
      $rideRequest->rider_name = $rideRequest?->rider['name'] ?? null;
      $rideRequest->rider_email =  $rideRequest?->rider['email'] ?? null;
      $rideRequest->rider_profile = $rideRequest?->rider['profile_image_id'] ?? null;
      $rideRequest->driver_name = $rideRequest?->driver?->name;
      $rideRequest->driver_email =  $rideRequest?->driver?->email;
      $rideRequest->driver_profile = $rideRequest?->driver?->profile_image_id ?? null;
      $rideRequest->service = $rideRequest->service?->name;
      $rideRequest->service_category = $rideRequest?->service_category?->name;
      $rideRequest->status = $rideRequest?->ride_status?->name;
      $rideRequest->payment_method = ucfirst($rideRequest?->payment_method);

    });

    $tableConfig = [
      'columns' => [
        ['title' => 'Rider', 'field' => 'rider_name', 'route' => 'admin.rider.show', 'email' => 'rider_email', 'profile_image' => 'rider_profile',  'sortable' => true, 'profile_id' => 'rider_id', 'sortField' => 'rider.name'],
        ['title' => 'Service', 'field' => 'service',  'sortable' => true, 'sortField' => 'service.name'],
        ['title' => 'Service Category', 'field' => 'service_category',  'sortable' => true, 'sortField' => 'service_category.name'],
        ['title' => 'Ride Fare', 'field' => 'formatted_total',  'sortable' => true,  'sortField' => 'total'],
        ['title' => 'Created At', 'field' => 'date',  'sortable' => true, 'sortField' => 'created_at'],
       ['title' => 'Action', 'type' => 'action', 'permission' => ['ride_request.index'], 'sortable' => false],
      ],
      'data' => $rideRequests,
      'actions' => [],
      'filters' => [
        ['title' => 'All', 'slug' => 'all', 'count' => $this->getRideRequests()->count()],
        ['title' => ucfirst(ServiceCategoryEnum::INTERCITY), 'slug' => ServiceCategoryEnum::INTERCITY, 'count' => getTotalRideRequestsByServiceCategory(ServiceCategoryEnum::INTERCITY)],
        ['title' => ucfirst(ServiceCategoryEnum::RIDE), 'slug' => ServiceCategoryEnum::RIDE, 'count' => getTotalRideRequestsByServiceCategory(ServiceCategoryEnum::RIDE)],
        ['title' => ucfirst(ServiceCategoryEnum::RENTAL), 'slug' => ServiceCategoryEnum::RENTAL, 'count' => getTotalRideRequestsByServiceCategory(ServiceCategoryEnum::RENTAL)],
        ['title' => ucfirst(ServiceCategoryEnum::SCHEDULE), 'slug' => ServiceCategoryEnum::SCHEDULE, 'count' => getTotalRideRequestsByServiceCategory(ServiceCategoryEnum::SCHEDULE)],
        ['title' => ucfirst(ServiceCategoryEnum::PACKAGE), 'slug' => ServiceCategoryEnum::PACKAGE, 'count' => getTotalRideRequestsByServiceCategory(ServiceCategoryEnum::PACKAGE)],
      ],
      'bulkactions' => [
        ['whenFilter' => ['all']],
      ],
      'actionButtons' => [
       ['icon' => 'ri-eye-line', 'permission' => 'ride_request.index', 'route' => 'admin.ride-request.details', 'field' => 'id', 'class' => 'dark-icon-box','tooltip' => 'Ride Request details'],
      ],
      'total' => $rideRequests->count()
    ];

    return $tableConfig;
  }

  public function sorting($rideRequests)
  {
    if ($this->request->has('orderby') && $this->request->has('order')) {
      $orderby = $this->request->get('orderby');
      $order = strtolower($this->request->get('order')) === 'asc' ? 'asc' : 'desc';
      if ($this->isSortable($orderby)) {
        if (str_contains($orderby, '.')) {
          $parts = explode('.', $orderby);
          $relation = $parts[0];
          $column = $parts[1];

          switch ($relation) {
            case 'rider':
              $rideRequests = $rideRequests->join('users', 'rideRequests.rider_id', '=', 'users.id')
                ->select('rideRequests.*', 'users.name as rider_name')
                ->orderBy("users.$column", $order);
              break;

            case 'driver':
              $rideRequests = $rideRequests->join('users', 'rideRequests.driver_id', '=', 'users.id')
                ->select('rideRequests.*', 'users.name as driver_name')
                ->orderBy("users.$column", $order);
              break;

            case 'service':
              $rideRequests = $rideRequests->join('services', 'rideRequests.service_id', '=', 'services.id')
                ->select('rideRequests.*', 'services.name as service_name')
                ->orderBy("services.$column", $order);
              break;

            case 'service_category':
              $rideRequests = $rideRequests->join('service_categories', 'rideRequests.service_category_id', '=', 'service_categories.id')
                ->select('rideRequests.*', 'service_categories.name as service_category_name')
                ->orderBy("service_categories.$column", $order);
              break;

            default:
              break;
          }
        } else {
          $rideRequests = $rideRequests->orderBy($orderby, $order);
        }
      }
    }

    return $rideRequests;
  }

  protected function isSortable($column)
  {
    return in_array($column, $this->sortableColumns);
  }
}