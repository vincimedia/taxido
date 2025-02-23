<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Modules\Taxido\Models\Zone;

class ZoneTable
{
  protected $zone;
  protected $request;

  public function __construct(Request $request)
  {
    $this->zone = new Zone();
    $this->request = $request;
  }
  public function getData()
  {
    $zones = $this->zone->with(['currency']);

    if ($this->request->has('filter')) {
      switch ($this->request->filter) {
        case 'active':
          $zones = $zones->where('status', true);
          break;
        case 'deactive':
          $zones = $zones->where('status', false);
          break;
        case 'trash':
          $zones = $zones->withTrashed()?->whereNotNull('deleted_at');
          break;
      }
    }

    if ($this->request->has('s')) {
      return $zones->withTrashed()->where(function ($query) {
        $query->where('name', 'LIKE', "%" . $this->request->s . "%");
      })->paginate($this->request->paginate);
    }

    if ($this->request->has('orderby') && $this->request->has('order')) {
      return $this->applySorting($zones)?->paginate($this->request?->paginate);
    }

    return $zones?->latest()?->paginate($this->request?->paginate);
  }

  public function generate()
  {
    $zones = $this->getData();
    if ($this->request->has('action') && $this->request->has('ids')) {
      $this->bulkActionHandler();
    }

    $defaultCurrency = getDefaultCurrency()->symbol;

    if (!empty($zones)) {
      $zones?->each(function ($item) use ($defaultCurrency) {
        $item->formatted_amount = $defaultCurrency . number_format($item->amount, 2);
      });
    }

    $zones->each(function ($zone) {
      $zone->name = $zone->getTranslation('name', app()->getLocale());
      $zone->date = $zone->created_at->format('Y-m-d h:i:s A');
      $zone->distance_type = ucfirst($zone->distance_type);
      $zone->currency = $zone->currency?->code;
    });


    $tableConfig = [
      'columns' => [
        ['title' => 'Name', 'field' => 'name', 'imageField' => null, 'action' => true, 'sortable' => true],
        ['title' => 'Currency', 'field' => 'currency', 'imageField' => null, 'sortable' => true, 'sortField' => 'currencies.code'],
        ['title' => 'Distance Type ', 'field' => 'distance_type', 'sortable' => true],
        ['title' => 'Status', 'field' => 'status', 'route' => 'admin.zone.status', 'type' => 'status', 'sortable' => true],
        ['title' => 'Created At', 'field' => 'date', 'sortable' => true, 'sortField' => 'created_at']
      ],
      'data' => $zones,
      'actions' => [
        ['title' => 'Edit',  'route' => 'admin.zone.edit', 'url' => '', 'class' => 'edit', 'whenFilter' => ['all', 'active', 'deactive'], 'isTranslate' => true, 'permission' => 'zone.edit'],
        ['title' => 'Move to trash', 'route' => 'admin.zone.destroy', 'class' => 'delete', 'whenFilter' => ['all', 'active', 'deactive'], 'permission' => 'zone.destroy'],
        ['title' => 'Restore', 'route' => 'admin.zone.restore', 'class' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'zone.restore'],
        ['title' => 'Delete Permanently', 'route' => 'admin.zone.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'zone.forceDelete']
      ],
      'filters' => [
        ['title' => 'All', 'slug' => 'all', 'count' => $this->zone->count()],
        ['title' => 'Active', 'slug' => 'active', 'count' => $this->zone->where('status', true)->count()],
        ['title' => 'Deactive', 'slug' => 'deactive', 'count' => $this->zone->where('status', false)->count()],
        ['title' => 'Trash', 'slug' => 'trash', 'count' => $this->zone->withTrashed()?->whereNotNull('deleted_at')?->count()]
      ],
      'bulkactions' => [
        ['title' => 'Active', 'permission' => 'zone.edit', 'action' => 'active', 'whenFilter' => ['all', 'active', 'deactive']],
        ['title' => 'Deactive', 'permission' => 'zone.edit', 'action' => 'deactive', 'whenFilter' => ['all', 'active', 'deactive']],
        ['title' => 'Move to Trash', 'permission' => 'zone.destroy', 'action' => 'trashed', 'whenFilter' => ['all', 'active', 'deactive']],
        ['title' => 'Restore', 'action' => 'restore', 'permission' => 'zone.restore', 'whenFilter' => ['trash']],
        ['title' => 'Delete Permanently', 'action' => 'delete', 'permission' => 'zone.forceDelete', 'whenFilter' => ['trash']],
      ],
      'total' => $this->zone->count()
    ];

    return $tableConfig;
  }

  public function applySorting($zones)
  {
    $orderby = $this->request->orderby;
    $order = $this->request->order;
    if (Schema::hasColumn('zones', $orderby) ||
        Schema::hasColumn('currencies', str_replace('currencies.', '', $orderby))) {
        if (str_contains($orderby, 'currencies.')) {
          $zones->join('currencies', 'zones.currency_id', '=', 'currencies.id');
        }
      return $zones->orderBy($orderby, $order);
    }

    return $zones;
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
    $this->zone->whereIn('id', $this->request->ids)->update(['status' => true]);
  }

  public function deactiveHandler(): void
  {
    $this->zone->whereIn('id', $this->request->ids)->update(['status' => false]);
  }

  public function trashedHandler(): void
  {
    $this->zone->whereIn('id', $this->request->ids)->delete();
  }

  public function restoreHandler(): void
  {
    $this->zone->whereIn('id', $this->request->ids)->restore();
  }

  public function deleteHandler(): void
  {
    $this->zone->whereIn('id', $this->request->ids)->forceDelete();
  }
}
