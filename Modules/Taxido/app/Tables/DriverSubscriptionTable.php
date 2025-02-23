<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Models\DriverSubscription;

class DriverSubscriptionTable
{

    protected $request;
    protected $driverSubscription;

    public function __construct(Request $request)
    {
        $this->driverSubscription = new DriverSubscription();
        $this->request = $request;
    }

    public function getData()
    {
        $subscriptions = $this->driverSubscription;

        if ($this->request->has('filter')) {
            switch ($this->request->filter) {
                case 'active':
                    return $subscriptions->where('is_active', true)->paginate($this->request?->paginate);
                case 'deactive':
                    return $subscriptions->where('is_active', false)->paginate($this->request?->paginate);
            }
        }

        if ($this->request->has('s')) {
            return $subscriptions->withTrashed()->where('driver_id', 'LIKE', "%" . $this->request->s . "%")
                ->orWhere('plan_id', 'LIKE', "%" . $this->request->s . "%")
                ->paginate($this->request?->paginate);
        }

        if ($this->request->has('orderby') && $this->request->has('order')) {
            return $subscriptions->orderBy($this->request->orderby, $this->request->order)->paginate($this->request?->paginate);
        }

        return $subscriptions->paginate($this->request?->paginate);
    }


    public function generate()
    {
        $subscriptions = $this->getData();

        // Handle bulk actions
        if ($this->request->has('action') && $this->request->has('ids')) {
            $this->bulkActionHandler();
        }

        $subscriptions->each(function ($subscription) {
            // $subscription->driver_name = $subscription->driver ? $subscription->driver->name;
            // $subscription->plan_name = $subscription->plan ? $subscription->plan->name;
            $subscription->status = $subscription->is_active ? 'Active' : 'Inactive';
            $subscription->date = $subscription->created_at->format('Y-m-d h:i:s A');
        });

        $tableConfig = [
            'columns' => [
                ['title' => 'Driver', 'field' => 'driver_name', 'sortable' => true],
                ['title' => 'Plan', 'field' => 'plan_name', 'sortable' => true],
                ['title' => 'Total', 'field' => 'total', 'sortable' => true],
                ['title' => 'End Date', 'field' => 'end_date', 'sortable' => true],
                ['title' => 'Start Date', 'field' => 'start_date', 'sortable' => true],
                ['title' => 'Status', 'field' => 'status', 'type' => 'status', 'sortable' => true],
                ['title' => 'Created At', 'field' => 'date', 'sortable' => true],
            ],
            'data' => $subscriptions,
            'actions' => [
                ['title' => 'Edit', 'route' => 'admin.driver-subscription.edit', 'url' => '', 'class' => 'edit', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'subscription.edit'],
                ['title' => 'Move to trash', 'route' => 'admin.driver-subscription.destroy', 'class' => 'delete', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'subscription.destroy'],
                ['title' => 'Restore', 'route' => 'admin.driver-subscription.restore', 'class' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'subscription.restore'],
                ['title' => 'Delete Permanently', 'route' => 'admin.driver-subscription.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'subscription.forceDelete'],
            ],
            'filters' => [
                ['title' => 'All', 'slug' => 'all', 'count' => $this->driverSubscription->count()],
                ['title' => 'Active', 'slug' => 'active', 'count' => $this->driverSubscription->where('is_active', true)->count()],
                ['title' => 'Deactive', 'slug' => 'deactive', 'count' => $this->driverSubscription->where('is_active', false)->count()],
            ],
            'bulkactions' => [
                ['title' => 'Activate', 'permission' => 'subscription.edit', 'action' => 'activate', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => 'Deactivate', 'permission' => 'subscription.edit', 'action' => 'deactivate', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => 'Move to Trash', 'permission' => 'subscription.destroy', 'action' => 'trashed', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => 'Restore', 'action' => 'restore', 'permission' => 'subscription.restore', 'whenFilter' => ['trash']],
                ['title' => 'Delete Permanently', 'action' => 'delete', 'permission' => 'subscription.forceDelete', 'whenFilter' => ['trash']],
            ],
            'total' => $this->driverSubscription->count(),
        ];

        return $tableConfig;
    }


    public function bulkActionHandler()
    {
        switch ($this->request->action) {
            case 'active':
                $this->activeHandler();
                break;
            case 'deactive':
                $this->deactiveHandler();
                break;
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

    public function activeHandler(): void
    {
        $this->driverSubscription->whereIn('id', $this->request->ids)->update(['status' => true]);
    }

    public function deactiveHandler(): void
    {
        $this->driverSubscription->whereIn('id', $this->request->ids)->update(['status' => false]);
    }

    public function trashedHandler(): void
    {
        $this->driverSubscription->whereIn('id', $this->request->ids)->delete();
    }

    public function restoreHandler(): void
    {
        $this->driverSubscription->whereIn('id', $this->request->ids)->restore();
    }

    public function deleteHandler(): void
    {
        $this->driverSubscription->whereIn('id', $this->request->ids)->forceDelete();
    }

}   
