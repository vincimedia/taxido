<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Models\VehicleType;

class VehicleTypeTable
{
    protected $vehicleType;
    protected $request;

    public function __construct(Request $request)
    {
        $this->vehicleType = new VehicleType();
        $this->request = $request;
    }

    public function getData()
    {
        $vehicleTypes = $this->vehicleType;
        if ($this->request->has('filter')) {
            switch ($this->request->filter) {
                case 'active':
                    $vehicleTypes = $vehicleTypes->where('status', true);
                    break;
                case 'deactive':
                    $vehicleTypes = $vehicleTypes->where('status', false);
                    break;
                case 'trash':
                    $vehicleTypes = $vehicleTypes->withTrashed()?->whereNotNull('deleted_at');
                    break;
            }
        }

        if ($this->request->has('s')) {
            return $vehicleTypes->withTrashed()->where('name', 'LIKE', "%" . $this->request->s . "%")?->paginate($this->request?->paginate);
        }

        if ($this->request->has('orderby') && $this->request->has('order')) {
            return $vehicleTypes->orderBy($this->request->orderby, $this->request->order)->paginate($this->request?->paginate);
        }

        return $vehicleTypes?->paginate($this->request?->paginate);
    }


    public function generate()
    {
        $vehicleTypes = $this->getData();
        if ($this->request->has('action') && $this->request->has('ids')) {
            $this->bulkActionHandler();
        }

        $vehicleTypes->each(function ($vehicleType) {
            $vehicleType->title = $vehicleType->getTranslation('name', app()->getLocale());
            $vehicleType->services = $vehicleType->services()->pluck('name')->implode(', ');
            $vehicleType->service_categories = $vehicleType->service_categories()->pluck('name')->implode(', ');
            $vehicleType->date = $vehicleType->created_at->format('Y-m-d h:i:s A');
        });

        $tableConfig = [
            'columns' => [
                ['title' => 'Name', 'field' => 'name', 'imageField' => 'vehicle_image_id', 'action' => true, 'sortable' => true],
                ['title' => 'Services', 'field' => 'services', 'sortable' => false],
                ['title' => 'Service Categories', 'field' => 'service_categories', 'sortable' => false],
                ['title' => 'Status', 'field' => 'status', 'route' => 'admin.vehicle-type.status', 'type' => 'status', 'sortable' => true],
                ['title' => 'Created At', 'field' => 'date', 'sortable' => true, 'sortField' => 'created_at']
            ],
            'data' => $vehicleTypes,
            'actions' => [
                ['title' => 'Edit',  'route' => 'admin.vehicle-type.edit', 'url' => '', 'class' => 'edit', 'whenFilter' => ['all', 'active', 'deactive'], 'isTranslate' => true, 'permission' => 'vehicle_type.edit'],
                ['title' => 'Move to trash', 'route' => 'admin.vehicle-type.destroy', 'class' => 'delete', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'vehicle_type.destroy'],
                ['title' => 'Restore', 'route' => 'admin.vehicle-type.restore', 'class' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'vehicle_type.restore'],
                ['title' => 'Delete Permanently', 'route' => 'admin.vehicle-type.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'vehicle_type.forceDelete']
            ],
            'filters' => [
                ['title' => 'All', 'slug' => 'all', 'count' => $this->vehicleType->count()],
                ['title' => 'Active', 'slug' => 'active', 'count' => $this->vehicleType->where('status', true)->count()],
                ['title' => 'Deactive', 'slug' => 'deactive', 'count' => $this->vehicleType->where('status', false)->count()],
                ['title' => 'Trash', 'slug' => 'trash', 'count' => $this->vehicleType->withTrashed()?->whereNotNull('deleted_at')?->count()]
            ],
            'bulkactions' => [
                ['title' => 'Active', 'permission' => 'vehicle_type.edit', 'action' => 'active', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => 'Deactive', 'permission' => 'vehicle_type.edit', 'action' => 'deactive', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => 'Move to Trash', 'permission' => 'vehicle_type.destroy', 'action' => 'trashed', 'whenFilter' => ['all', 'active', 'deactive']],
                ['title' => 'Restore', 'action' => 'restore', 'permission' => 'vehicle_type.restore', 'whenFilter' => ['trash']],
                ['title' => 'Delete Permanently', 'action' => 'delete', 'permission' => 'vehicle_type.forceDelete', 'whenFilter' => ['trash']],
            ],
            'total' => $this->vehicleType->count()
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
        $this->vehicleType->whereIn('id', $this->request->ids)->update(['status' => true]);
    }

    public function deactiveHandler(): void
    {
        $this->vehicleType->whereIn('id', $this->request->ids)->update(['status' => false]);
    }

    public function trashedHandler(): void
    {
        $this->vehicleType->whereIn('id', $this->request->ids)->delete();
    }

    public function restoreHandler(): void
    {
        $this->vehicleType->whereIn('id', $this->request->ids)->restore();
    }

    public function deleteHandler(): void
    {
        $this->vehicleType->whereIn('id', $this->request->ids)->forceDelete();
    }
}
