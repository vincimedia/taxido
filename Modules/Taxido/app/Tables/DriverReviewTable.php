<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Enums\RoleEnum;
use Illuminate\Support\Facades\Schema;
use Modules\Taxido\Models\DriverReview;
use Modules\Taxido\Enums\ServiceCategoryEnum;

class DriverReviewTable
{
    protected $request;
    
    protected $driverReview;

    public function __construct(Request $request)
    {
        $this->driverReview = new DriverReview();
        $this->request = $request;
    }

    public function getData()
    {
        $driverReviews = $this->driverReview;

        if (getCurrentRoleName() == RoleEnum::DRIVER) {
            $driverId = getCurrentDriver()?->id;
            $driverReviews = $driverReviews->where('driver_id', $driverId);
        }

        if ($this->request->has('filter')) {
            switch ($this->request->filter) {
                case ServiceCategoryEnum::INTERCITY:
                    return $driverReviews->whereNull('driver_reviews.deleted_at')
                        ->where('driver_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::INTERCITY))
                        ->paginate($this->request->paginate);
                case ServiceCategoryEnum::RIDE:
                    return $driverReviews->whereNull('driver_reviews.deleted_at')
                        ->where('driver_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::RIDE))
                        ->paginate($this->request->paginate);
                case ServiceCategoryEnum::RENTAL:
                    return $driverReviews->whereNull('driver_reviews.deleted_at')
                        ->where('driver_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::RENTAL))
                        ->paginate($this->request->paginate);
                case ServiceCategoryEnum::SCHEDULE:
                    return $driverReviews->whereNull('driver_reviews.deleted_at')
                        ->where('driver_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::SCHEDULE))
                        ->paginate($this->request->paginate);
                case ServiceCategoryEnum::PACKAGE:
                    return $driverReviews->whereNull('driver_reviews.deleted_at')
                        ->where('driver_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::PACKAGE))
                        ->paginate($this->request->paginate);
                case 'all':
                    return $driverReviews->paginate($this->request->paginate);
                case 'trash':
                    return $driverReviews->onlyTrashed()->paginate($this->request->paginate);
            }
        }

        if ($this->request->has('s')) {
            return $driverReviews->withTrashed()
                ->where('ride_number', 'LIKE', "%" . $this->request->s . "%")
                ->orWhere('total', 'LIKE', "%" . $this->request->s . "%")
                ->paginate($this->request->paginate);
        }

        return $driverReviews->whereNull('deleted_at')->paginate($this->request->paginate);
    }

    public function applySorting($driverReviews)
    {
        $orderby = $this->request->orderby;
        $order = $this->request->order;
        $driverReviews = $driverReviews->select('driver_reviews.*');

        if (
            Schema::hasColumn('driver_reviews', $orderby) ||
            (str_contains($orderby, 'users.') && Schema::hasColumn('users', str_replace('users.', '', $orderby))) ||
            (str_contains($orderby, 'vehicle_types.') && Schema::hasColumn('vehicle_types', str_replace('vehicle_types.', '', $orderby)))
        ) {
            if (str_contains($orderby, 'users.')) {
                $driverReviews = $driverReviews
                    ->join('users', 'driver_reviews.driver_id', '=', 'users.id')
                    ->addSelect('users.name as driver_name');
            }

            if (str_contains($orderby, 'vehicle_types.')) {
                $driverReviews = $driverReviews
                    ->join('vehicle_types', 'rental_vehicles.vehicle_type_id', '=', 'vehicle_types.id')
                    ->addSelect('vehicle_types.name as vehicle_type_name');
            }

            return $driverReviews->orderBy($orderby, $order);
        }

        return $driverReviews;
    }

    public function generateStarsWithRating($rating)
    {
        $fullStars = floor($rating);
        $stars = str_repeat('⭐', $fullStars);
        return $stars . ' (' . number_format($rating, 1) . ')';
    }
    public function generate()
    {
        $driverReview = $this->getData();

        $driverReview?->each(function ($item) {
            $item->date = $item->created_at?->format('Y-m-d h:i:s A');
            $item->rating = $item->rating;
            $item->rider_name = $item?->rider->name ?? null;
            $item->rider_email = $item?->rider->email ?? null;
            $item->rider_profile = $item?->rider->profile_image_id ?? null;
            $item->driver_name = $item?->driver->name ?? null;
            $item->driver_email = $item?->driver?->email;
            $item->services = $item->services()->pluck('name')->implode(', ');
            $item->driver_profile = $item?->driver?->profile_image_id ?? null;

            $item->stars = $this->generateStarsWithRating($item->rating);
        });

        $tableConfig = [
            'columns' => [
                ['title' => 'Driver', 'field' => 'driver_name', 'imageField' => 'driver_profile', 'action' => true, 'sortable' => false],
                ['title' => 'Rider', 'field' => 'rider_name', 'email' => 'rider_email', 'profile_image' => 'rider_profile', 'sortable' => true, 'route' => 'admin.rider.show', 'profile_id' => 'rider_id'],
                ['title' => 'Rating', 'field' => 'stars', 'imageField' => null, 'sortable' => true],
                ['title' => 'Services', 'field' => 'services', 'sortable' => false],
                ['title' => 'Message', 'field' => 'message', 'imageField' => null, 'sortable' => true],
                ['title' => 'Created At', 'field' => 'date', 'imageField' => null, 'sortable' => true],
            ],
            'data' => $driverReview,
            'actions' => [
                ['title' => 'Move to trash', 'route' => 'admin.driver-review.destroy', 'class' => 'delete', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'driver_review.destroy'],
                ['title' => 'Restore', 'route' => 'admin.driver-review.restore', 'class' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'driver_review.restore'],
                ['title' => 'Delete Permanently', 'route' => 'admin.driver-review.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'driver_review.forceDelete'],
            ],
            'filters' => [
                ['title' => 'All', 'slug' => 'all', 'count' => $this->getFilterCount('all')],
                ['title' => ucfirst(ServiceCategoryEnum::INTERCITY), 'slug' => ServiceCategoryEnum::INTERCITY, 'count' => $this->getFilterCount(ServiceCategoryEnum::INTERCITY)],
                ['title' => ucfirst(ServiceCategoryEnum::RIDE), 'slug' => ServiceCategoryEnum::RIDE, 'count' => $this->getFilterCount(ServiceCategoryEnum::RIDE)],
                ['title' => ucfirst(ServiceCategoryEnum::RENTAL), 'slug' => ServiceCategoryEnum::RENTAL, 'count' => $this->getFilterCount(ServiceCategoryEnum::RENTAL)],
                ['title' => ucfirst(ServiceCategoryEnum::SCHEDULE), 'slug' => ServiceCategoryEnum::SCHEDULE, 'count' => $this->getFilterCount(ServiceCategoryEnum::SCHEDULE)],
                ['title' => ucfirst(ServiceCategoryEnum::PACKAGE), 'slug' => ServiceCategoryEnum::PACKAGE, 'count' => $this->getFilterCount(ServiceCategoryEnum::PACKAGE)],
                ['title' => 'Trash', 'slug' => 'trash', 'count' => $this->getFilterCount('trash')],
            ],
            'bulkactions' => [
                ['title' => 'Move to Trash', 'action' => 'trashed', 'permission' => 'driver_review.destroy'],
                ['title' => 'Restore', 'action' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'driver_review.restore'],
                ['title' => 'Delete Permanently', 'action' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'driver_review.forceDelete'],
            ],
            'total' => $driverReview?->count(),
        ];

        return $tableConfig;
    }

    public function getFilterCount($filter)
    {
        $driverReviews = $this->driverReview;

        if ($this->request->has('driver_id')) {
            $driverReviews = $driverReviews->where('driver_id', $this->request->driver_id);
        }

        if ($filter === 'trash') {
            return $driverReviews->onlyTrashed()->count();
        }

        if ($filter !== 'all') {
            $serviceCategoryId = getServiceCategoryIdBySlug($filter);
            return $driverReviews->where('service_category_id', $serviceCategoryId)->count();
        }

        return $driverReviews->count();
    }


    public function bulkActionHandler()
    {
        switch ($this->request->action) {
            case 'trashed':
                $this->trashedHandler();
                break;
            case 'restore':
                $this->restoreHandler();
                break;
            case 'delete':
                $this->deleteHandler();
                break;
        }
    }

    public function trashedHandler(): void
    {
        $this->driverReview->whereIn('id', $this->request->ids)->delete();
    }

    public function restoreHandler(): void
    {
        $this->driverReview->whereIn('id', $this->request->ids)->restore();
    }

    public function deleteHandler(): void
    {
        $this->driverReview->whereIn('id', $this->request->ids)->forceDelete();
    }
}
