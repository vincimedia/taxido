<?php

namespace App\Tables;

use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleTable
{
  protected $role;
  protected $request;

  public function __construct(Request $request)
  {
    $this->role = new Role();
    $this->request = $request;
  }

  public function generate()
  {
    $roles = $this->getData()->where('name', '!=', RoleEnum::ADMIN)
      ->where('system_reserve', false)
      ->paginate();

    if ($this->request->has('action') && $this->request->has('ids')) {
      $this->bulkActionHandler();
    }

    $roles->each(function ($role) {
      $role->date = $role->created_at->format('Y-m-d h:i:s A');
    });

    $tableConfig = [
      'columns' => [
        ['title' => 'Name', 'field' => 'name', 'route' => 'admin.role.edit', 'action' => true, 'sortable' => true],
        ['title' => 'Status', 'field' => 'status', 'route' => 'admin.role.status', 'type' => 'status', 'sortable' => true],
        ['title' => 'Created At', 'field' => 'date', 'sortable' => true, 'sortField' => 'roles.created_at'],
      ],
      'data' => $roles,
      'actions' => [
        ['title' => 'Edit', 'route' => 'admin.role.edit', 'class' => 'edit', 'whenFilter' => ['all'], 'permission' => 'role.edit'],
        ['title' => 'Delete Permanently', 'route' => 'admin.role.forceDelete', 'class' => 'delete', 'whenFilter' => ['all'], 'permission' => 'role.forceDelete'],
      ],
      'filters' => $this->generateFilters(),
      'bulkactions' => [
        ['title' => 'Delete Permanently', 'permission' => 'role.destroy', 'action' => 'delete'],
      ],
      'total' => $this->role->where('roles.system_reserve', false)->count(),
    ];

    return $tableConfig;
  }

  public function getData()
  {
    $roles = $this->role->newQuery();

    $roles->where('roles.system_reserve', false);

    if ($this->request->has('s')) {
      $roles->where('name', 'LIKE', "%" . $this->request->s . "%");
    }

    if ($this->request->has('orderby') && $this->request->has('order')) {
      $roles->orderBy($this->request->orderby, $this->request->order);
    }

    return $roles;
  }

  public function generateFilters()
  {
    $count = $this->role->where('roles.system_reserve', false)->count();
    $filters = [
      [
        'title' => 'All',
        'slug' => 'all',
        'count' => $count,
      ]
    ];
    return $filters;
  }

  public function bulkActionHandler()
  {
    switch ($this->request->action) {
      case 'delete':
        $this->deleteHandler();
        break;
    }
  }

  public function deleteHandler(): void
  {
    $this->role->whereIn('id', $this->request->ids)->where('system_reserve', false)->forceDelete();
  }
}
