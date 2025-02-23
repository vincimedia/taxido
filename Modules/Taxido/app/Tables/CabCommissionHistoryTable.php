<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Enums\RoleEnum;
use Modules\Taxido\Models\CabCommissionHistory;

class CabCommissionHistoryTable
{
    protected $commissionHistory;
    protected $request;

    public function __construct(Request $request)
    {
        $this->commissionHistory = new CabCommissionHistory();
        $this->request = $request;
    }

    public function getData()
    {
        $commissionHistory = $this->commissionHistory;
        if (getCurrentRoleName() == RoleEnum::DRIVER) {
            $driverId = getCurrentDriver()?->id;
            $commissionHistory = $commissionHistory->where('driver_id', $driverId);
        }

        if ($this->request->has('filter')) {
            switch ($this->request->filter) {
                case 'active':
                    return $commissionHistory->whereNull('deleted_at')->paginate($this->request?->paginate);
                case 'trash':
                    return $commissionHistory->withTrashed()?->whereNotNull('deleted_at')?->paginate($this->request?->paginate);
            }
        }

        if ($this->request->has('s')) {
            return $commissionHistory->withTrashed()->where('ride_id', 'LIKE', "%" . $this->request->s . "%")?->paginate($this->request?->paginate);
        }

        if ($this->request->has('orderby') && $this->request->has('order')) {
            return $commissionHistory->orderBy($this->request->orderby, $this->request->order)->paginate($this->request?->paginate);
        }

        return $commissionHistory->whereNull('deleted_at')->paginate($this->request?->paginate);
    }

    public function applySearch($commissionHistory)
    {
        if (isset($this->request->s)) {
            $searchTerm = $this->request->s;

            $commissionHistory = $commissionHistory->with(['ride', 'driver'])
            ->where(function ($query) use ($searchTerm) {
                $query->where('ride_id', 'LIKE', "%" . $searchTerm . "%")
                    ->orWhere('admin_commission', 'LIKE', "%" . $searchTerm . "%")
                    ->orWhere('driver_commission', 'LIKE', "%" . $searchTerm . "%")
                    ->orWhereHas('ride', function ($q) use ($searchTerm) {
                        $q->where('ride_number', 'LIKE', "%" . $searchTerm . "%"); 
                    })
                    ->orWhereHas('driver', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', "%" . $searchTerm . "%") 
                            ->orWhere('email', 'LIKE', "%" . $searchTerm . "%"); 
                    });
            });
        }

        return $commissionHistory;
    }
    public function generate()
    {
        $commissionHistories = $this->getData();
        if ($this->request->has('action') && $this->request->has('ids')) {
            $this->bulkActionHandler();
        }

        $commissionHistories->each(function ($commissionHistory) {
            $locale = app()->getLocale();

            $commissionHistory->ride_numb = "#".$commissionHistory->ride?->ride_number;
            $commissionHistory->driver_name = $commissionHistory?->driver->name ?? null;
            $commissionHistory->driver_email =  $commissionHistory?->driver?->email;
            $commissionHistory->driver_profile = $commissionHistory?->driver?->profile_image_id ?? null;
            $commissionHistory->date = $commissionHistory->created_at->format('Y-m-d h:i:s A');

        });

        $tableConfig = [
            'columns' => [
                ['title' => 'Ride Number', 'field' => 'ride_numb',  'sortable' => false, 'type' => 'badge', 'badge_type' => 'light'],
                ['title' => 'Driver', 'field' => 'driver_name', 'email' => 'driver_email', 'profile_image' => 'driver_profile', 'sortable' => true, 'route' => 'admin.driver.show','profile_id' => 'driver_id'],
                ['title' => 'Admin Commission', 'field' => 'admin_commission', 'sortable' => true],
                ['title' => 'Driver Commission', 'field' => 'driver_commission', 'sortable' => true],
                ['title' => 'Created At', 'field' => 'date', 'sortable' => true, 'sortField' => 'created_at'],
            ],
            'data' => $commissionHistories,
            'actions' => [
                ['title' => 'View', 'route' => 'admin.cab-commission-history.view', 'url' => '', 'class' => 'view', 'whenFilter' => ['all'], 'isTranslate' => true,],
                ['title' => 'Move to trash', 'route' => 'admin.cab-commission-history.destroy', 'class' => 'delete', 'whenFilter' => ['all']],
                ['title' => 'Restore', 'route' => 'admin.cab-commission-history.restore', 'class' => 'restore', 'whenFilter' => ['trash']],
                ['title' => 'Delete Permanently', 'route' => 'admin.cab-commission-history.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash']],
            ],
            'filters' => [
                ['title' => 'All', 'slug' => 'all', 'count' => $this->getFilterCount('all')],
                ['title' => 'Trash', 'slug' => 'trash', 'count' => $this->getFilterCount('trash')],
            ],
            'bulkactions' => [
                ['title' => 'Move to Trash', 'permission' => 'cab_commission_history.destroy', 'action' => 'trashed', 'whenFilter' => ['all']],
                ['title' => 'Restore', 'action' => 'restore', 'permission' => 'cab_commission_history.restore', 'whenFilter' => ['trash']],
                ['title' => 'Delete Permanently', 'action' => 'delete', 'permission' => 'cab_commission_history.forceDelete', 'whenFilter' => ['trash']],
            ],
            'total' => $this->commissionHistory->count(),
        ];

        return $tableConfig;
    }

    public function getFilterCount($filter)
    {
        $commissionHistory = $this->commissionHistory;

        if (getCurrentRoleName() == RoleEnum::DRIVER) {
            $driverId = getCurrentDriver()?->id;
            $commissionHistory = $commissionHistory->where('driver_id', $driverId);
        }

        if ($filter == 'active') {
            $commissionHistory = $commissionHistory->whereNull('deleted_at');
        }

        if ($filter == 'trash') {
            $commissionHistory = $commissionHistory->withTrashed()->whereNotNull('deleted_at');
        }

        return $commissionHistory->count();
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
        $this->commissionHistory->whereIn('id', $this->request->ids)->delete();
    }

    public function restoreHandler(): void
    {
        $this->commissionHistory->whereIn('id', $this->request->ids)->restore();
    }

    public function deleteHandler(): void
    {
        $this->commissionHistory->whereIn('id', $this->request->ids)->forceDelete();
    }
}
