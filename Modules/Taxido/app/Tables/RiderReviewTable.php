<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Models\RiderReview;
use Illuminate\Support\Facades\Schema;
use Modules\Taxido\Enums\ServiceCategoryEnum;

class RiderReviewTable
{
    protected $riderReview;
    protected $request;

    public function __construct(Request $request)
    {
        $this->riderReview = new RiderReview();
        $this->request = $request;
    }

    public function getData()
    {
        $riderReviews = $this->riderReview->with(['services', 'service_category']);

        if ($this->request->has('filter')) {
            switch ($this->request->filter) {
                case ServiceCategoryEnum::INTERCITY:
                    return $riderReviews->whereNull('rider_reviews.deleted_at')
                        ->where('rider_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::INTERCITY))
                        ->paginate($this->request->paginate);
                case ServiceCategoryEnum::RIDE:
                    return $riderReviews->whereNull('rider_reviews.deleted_at')
                        ->where('rider_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::RIDE))
                        ->paginate($this->request->paginate);
                case ServiceCategoryEnum::RENTAL:
                    return $riderReviews->whereNull('rider_reviews.deleted_at')
                        ->where('rider_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::RENTAL))
                        ->paginate($this->request->paginate);
                case ServiceCategoryEnum::SCHEDULE:
                    return $riderReviews->whereNull('rider_reviews.deleted_at')
                        ->where('rider_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::SCHEDULE))
                        ->paginate($this->request->paginate);
                case ServiceCategoryEnum::PACKAGE:
                    return $riderReviews->whereNull('rider_reviews.deleted_at')
                        ->where('rider_reviews.service_category_id', getServiceCategoryIdBySlug(ServiceCategoryEnum::PACKAGE))
                        ->paginate($this->request->paginate);
                case 'all':
                    return $riderReviews->paginate($this->request->paginate);
                case 'trash':
                    $riderReviews = $riderReviews->withTrashed()->whereNotNull('deleted_at');
                    break;
            }
        }

        if ($this->request->has('s')) {
            return $riderReviews->withTrashed()->where('message', 'LIKE', "%" . $this->request->s . "%")->paginate($this->request->paginate);
        }

        if ($this->request->has('orderby') && $this->request->has('order')) {
            $riderReviews = $this->applySorting($riderReviews);
        }

        return $riderReviews->whereNull('rider_reviews.deleted_at')->paginate($this->request->paginate);
    }

    public function applySorting($riderReviews)
    {
        $orderby = $this->request->orderby;
        $order = $this->request->order;

        $riderReviews = $riderReviews->select('rider_reviews.*');

        if (
            Schema::hasColumn('rider_reviews', $orderby) ||
            (str_contains($orderby, 'users.') && Schema::hasColumn('users', str_replace('users.', '', $orderby))) ||
            (str_contains($orderby, 'vehicle_types.') && Schema::hasColumn('vehicle_types', str_replace('vehicle_types.', '', $orderby)))
        ) {
            if (str_contains($orderby, 'users.')) {
                $riderReviews = $riderReviews
                    ->join('users', 'rider_reviews.rider_id', '=', 'users.id')
                    ->addSelect('users.name as rider_name');
            }

            if (str_contains($orderby, 'vehicle_types.')) {
                $riderReviews = $riderReviews
                    ->join('vehicle_types', 'rental_vehicles.vehicle_type_id', '=', 'vehicle_types.id')
                    ->addSelect('vehicle_types.name as vehicle_type_name');
            }

            return $riderReviews->orderBy($orderby, $order);
        }

        return $riderReviews;
    }

    public function generate()
    {
        $riderReviews = $this->getData();

        
        $riderReviews->each(function ($item) {
            $item->rating = $item->rating;
            $item->rider_name = $item?->rider->name ?? null;
            $item->rider_email = $item?->rider->email ?? null;
            $item->rider_profile = $item?->rider->profile_image_id ?? null;
            $item->driver_name = $item?->driver->name ?? null;
            $item->driver_email = $item?->driver?->email;
            $item->driver_profile = $item?->driver?->profile_image_id ?? null;
            $item->services = $item->services()->pluck('name')->implode(', ');
            $item->stars = $this->generateStarsWithRating($item->rating);
        });

        $tableConfig = [
            'columns' => [
                ['title' => 'Rider', 'field' => 'rider_name', 'action' => true, 'imageField' => 'rider_profile', 'sortable' => false,'placeholderLetter' => true],
                ['title' => 'Driver', 'field' => 'driver_name', 'email' => 'driver_email', 'profile_image' => 'driver_profile', 'sortable' => true, 'route' => 'admin.driver.show', 'profile_id' => 'driver_id'],
                ['title' => 'Rating', 'field' => 'stars', 'imageField' => null, 'sortable' => true],
                ['title' => 'Services', 'field' => 'services', 'sortable' => false],
                ['title' => 'Message', 'field' => 'message', 'imageField' => null, 'sortable' => true],
                ['title' => 'Created At', 'field' => 'created_at', 'imageField' => null, 'sortable' => true],
            ],
            'data' => $riderReviews,
            'actions' => [
                ['title' => 'Move to trash', 'route' => 'admin.rider-review.destroy', 'class' => 'delete', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'rider_review.destroy'],
                ['title' => 'Restore', 'route' => 'admin.rider-review.restore', 'class' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'rider_review.destroy'],
                ['title' => 'Delete Permanently', 'route' => 'admin.rider-review.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'rider_review.destroy'],
            ],
            'filters' => [
                ['title' => 'All', 'slug' => 'all', 'count' => $this->riderReview->count()],
                ['title' => ucfirst(ServiceCategoryEnum::INTERCITY), 'slug' => ServiceCategoryEnum::INTERCITY, 'count' => getTotalRiderReviewsByServiceCategory(ServiceCategoryEnum::INTERCITY)],
                ['title' => ucfirst(ServiceCategoryEnum::RIDE), 'slug' => ServiceCategoryEnum::RIDE, 'count' => getTotalRiderReviewsByServiceCategory(ServiceCategoryEnum::RIDE)],
                ['title' => ucfirst(ServiceCategoryEnum::RENTAL), 'slug' => ServiceCategoryEnum::RENTAL, 'count' => getTotalRiderReviewsByServiceCategory(ServiceCategoryEnum::RENTAL)],
                ['title' => ucfirst(ServiceCategoryEnum::SCHEDULE), 'slug' => ServiceCategoryEnum::SCHEDULE, 'count' => getTotalRiderReviewsByServiceCategory(ServiceCategoryEnum::SCHEDULE)],
                ['title' => ucfirst(ServiceCategoryEnum::PACKAGE), 'slug' => ServiceCategoryEnum::PACKAGE, 'count' => getTotalRiderReviewsByServiceCategory(ServiceCategoryEnum::PACKAGE)],
                ['title' => 'Trash', 'slug' => 'trash', 'count' => $this->riderReview->onlyTrashed()->count()],
            ],
            'bulkactions' => [
                ['title' => 'Move to Trash', 'permission' => 'rider_review.destroy', 'action' => 'trashed'],
                ['title' => 'Restore', 'permission' => 'rider_review.restore', 'action' => 'restore', 'whenFilter' => ['trash']],
                ['title' => 'Delete Permanently', 'permission' => 'rider_review.forceDelete', 'action' => 'delete', 'whenFilter' => ['trash']],
            ],
            'total' => $riderReviews->count(),
        ];

        return $tableConfig;
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
        $this->riderReview->whereIn('id', $this->request->ids)->delete();
    }

    public function restoreHandler(): void
    {
        $this->riderReview->whereIn('id', $this->request->ids)->restore();
    }

    public function deleteHandler(): void
    {
        $this->riderReview->whereIn('id', $this->request->ids)->forceDelete();
    }

    public function generateStarsWithRating($rating)
    {
        $fullStars = floor($rating);

        $stars = str_repeat('⭐', $fullStars);

        return $stars . ' (' . number_format($rating, 1) . ')';
    }
}
