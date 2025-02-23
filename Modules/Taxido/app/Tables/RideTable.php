<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Models\Ride;
use Modules\Taxido\Enums\RoleEnum;
use Modules\Taxido\Enums\ServiceCategoryEnum;

class RideTable
{

  protected $ride;
  protected $request;

  protected $sortableColumns = [
    'ride_number',
    'rider.name',
    'driver.name',
    'service.name',
    'service_category.name',
    'total',
    'created_at',
  ];

  public function __construct(Request $request)
  {
    $this->ride = Ride::query();
    $this->request = $request;
  }

  public function getRides()
  {
    return $this->ride;
  }

  public function getData()
  {
    $rides = $this->getRides();
    $roleName = getCurrentRoleName();
    if ($roleName == RoleEnum::DRIVER) {
      $rides = $rides->where('driver_id', getCurrentUserId());
    }

    $rides = $this->sorting($rides);

    if ($this->request->has('status')) {
          $rides = $rides->whereNull('rides.deleted_at')->where('rides.ride_status_id', getRideStatusIdByName($this->request->get('status')));
    }

    if ($this->request->has('filter')) {
      if($this->request->get('filter') === 'all') {
        $rides->whereNull('rides.deleted_at');
      }
      else {
        $rides = $rides->whereNull('rides.deleted_at')->where('rides.service_category_id', getServiceCategoryIdBySlug($this->request->get('filter')));
      }
    }

    if (isset($this->request->s)) {
      $rides =  $rides->withTrashed()->where('ride_number', 'LIKE', "%" . $this->request->s . "%")
        ->orWhere('total', 'LIKE', "%" . $this->request->s . "%");
    }

    return $rides?->paginate($this->request?->paginate);
  }

  public function getRideCountByStatus($rides, $status)
  {
    return $this->getRides()?->where('ride_status_id', getRideStatusIdByName($status))?->count();
  }

  public function generate()
  {
    $rides = $this->getData();
    $defaultCurrency = getDefaultCurrency()?->symbol;
    $rides->each(function ($ride) use ($defaultCurrency) {
      $ride->formatted_total = $defaultCurrency . number_format($ride->total, 2);
      $ride->date = $ride?->created_at->format('Y-m-d h:i:s A');
      $ride->rider_name = $ride?->rider['name'] ?? null;
      $ride->rider_email =  $ride?->rider['email'] ?? null;
      $ride->rider_profile = $ride?->rider['profile_image_id'] ?? null;
      $ride->driver_name = $ride?->driver?->name;
      $ride->driver_email =  $ride?->driver?->email;
      $ride->driver_profile = $ride?->driver?->profile_image_id ?? null;
      $ride->service = $ride->service?->name;
      $ride->ride_numb = "#$ride?->ride_number";
      $ride->service_category = $ride?->service_category?->name;
      $ride->status = $ride?->ride_status?->name;
      $ride->payment_status = ucfirst($ride?->payment_status);
      $ride->payment_method = ucfirst($ride?->payment_method);
    });

    $tableConfig = [
      'columns' => [
        ['title' => 'Ride Number', 'field' => 'ride_numb',  'sortable' => true, 'sortField' => 'ride_number', 'type' => 'badge', 'badge_type' => 'light'],
        ['title' => 'Rider', 'field' => 'rider_name', 'route' => 'admin.rider.show', 'email' => 'rider_email', 'profile_image' => 'rider_profile',  'sortable' => true, 'profile_id' => 'rider_id', 'sortField' => 'rider.name'],
        ['title' => 'Driver', 'field' => 'driver_name',  'route' => 'admin.driver.show', 'email' => 'driver_email', 'profile_image' => 'driver_profile',  'sortable' => true, 'profile_id' => 'driver_id', 'sortField' => 'driver.name'],
        ['title' => 'Service', 'field' => 'service',  'sortable' => true, 'sortField' => 'service.name'],
        ['title' => 'Service Category', 'field' => 'service_category',  'sortable' => true, 'sortField' => 'service_category.name'],
        ['title' => 'Payment Status', 'field' => 'payment_status', 'sortable' => true, 'type' => 'badge', 'colorClasses' => getPaymentStatusColorClasses(), 'sortable' => true],
        ['title' => 'Total', 'field' => 'formatted_total',  'sortable' => true,  'sortField' => 'total'],
        ['title' => 'Created At', 'field' => 'date',  'sortable' => true, 'sortField' => 'created_at'],
        ['title' => 'Action', 'type' => 'action', 'permission' => ['ride.index'], 'sortable' => false],
      ],
      'data' => $rides,
      'actions' => [],
      'filters' => [
        ['title' => 'All', 'slug' => 'all', 'count' => $this->getRides()->count()],
        ['title' => ucfirst(ServiceCategoryEnum::INTERCITY), 'slug' => ServiceCategoryEnum::INTERCITY, 'count' => getTotalRidesByServiceCategory(ServiceCategoryEnum::INTERCITY)],
        ['title' => ucfirst(ServiceCategoryEnum::RIDE), 'slug' => ServiceCategoryEnum::RIDE, 'count' => getTotalRidesByServiceCategory(ServiceCategoryEnum::RIDE)],
        ['title' => ucfirst(ServiceCategoryEnum::RENTAL), 'slug' => ServiceCategoryEnum::RENTAL, 'count' => getTotalRidesByServiceCategory(ServiceCategoryEnum::RENTAL)],
        ['title' => ucfirst(ServiceCategoryEnum::SCHEDULE), 'slug' => ServiceCategoryEnum::SCHEDULE, 'count' => getTotalRidesByServiceCategory(ServiceCategoryEnum::SCHEDULE)],
        ['title' => ucfirst(ServiceCategoryEnum::PACKAGE), 'slug' => ServiceCategoryEnum::PACKAGE, 'count' => getTotalRidesByServiceCategory(ServiceCategoryEnum::PACKAGE)],
      ],
      'bulkactions' => [
        ['whenFilter' => ['all']],
      ],
      'actionButtons' => [
        ['icon' => 'ri-eye-line', 'permission' => 'ride.edit', 'route' => 'admin.ride.details', 'field' => 'ride_number', 'class' => 'dark-icon-box', 'permission' => 'ride.edit', 'tooltip' => 'Ride details'],
      ],
      'total' => $rides->count()
    ];

    return $tableConfig;
  }

  public function sorting($rides)
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
              $rides = $rides->join('users', 'rides.rider_id', '=', 'users.id')
                ->select('rides.*', 'users.name as rider_name')
                ->orderBy("users.$column", $order);
              break;

            case 'driver':
              $rides = $rides->join('users', 'rides.driver_id', '=', 'users.id')
                ->select('rides.*', 'users.name as driver_name')
                ->orderBy("users.$column", $order);
              break;

            case 'service':
              $rides = $rides->join('services', 'rides.service_id', '=', 'services.id')
                ->select('rides.*', 'services.name as service_name')
                ->orderBy("services.$column", $order);
              break;

            case 'service_category':
              $rides = $rides->join('service_categories', 'rides.service_category_id', '=', 'service_categories.id')
                ->select('rides.*', 'service_categories.name as service_category_name')
                ->orderBy("service_categories.$column", $order);
              break;

            default:
              break;
          }
        } else {
          $rides = $rides->orderBy($orderby, $order);
        }
      }
    }

    return $rides;
  }

  protected function isSortable($column)
  {
    return in_array($column, $this->sortableColumns);
  }
}
