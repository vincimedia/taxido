<?php

namespace Modules\Taxido\Tables;

use Illuminate\Http\Request;
use Modules\Taxido\Enums\RoleEnum;
use Illuminate\Support\Facades\Schema;
use Modules\Taxido\Models\DriverDocument;
use Modules\Taxido\Enums\DocumentStatusEnum;

class DriverDocumentTable
{
  protected $request;
  protected $driverDocument;

  public function __construct(Request $request)
  {
    $this->driverDocument = new DriverDocument();
    $this->request = $request;
  }

  public function getData()
  {
    $driverDocuments = $this->applyFilters();
    $driverDocuments = $this->applySearch($driverDocuments);
    $driverDocuments = $this->applySorting($driverDocuments);

    return $driverDocuments->paginate($this->request->paginate);
  }

  protected function applyFilters()
  {
    $driverDocuments = $this->driverDocument;

    $currentUserRole = getCurrentRoleName();
    $currentUserId = getCurrentUserId();

    if ($currentUserRole == RoleEnum::DRIVER) {
      $driverDocuments = $driverDocuments->where('driver_documents.driver_id', $currentUserId);
    }

    if ($this->request->has('driver_id')) {
      $driverDocuments = $driverDocuments->where('driver_documents.driver_id', $this->request->driver_id);
    }

    if ($this->request->has('filter')) {
      $driverDocuments = $this->applyStatusFilter($driverDocuments, $this->request->filter);
    }

    return $driverDocuments;
  }

  protected function applyStatusFilter($driverDocuments, $filter)
  {
    switch ($filter) {
      case 'pending':
        return $driverDocuments->whereNull('driver_documents.deleted_at')->where('driver_documents.status', DocumentStatusEnum::PENDING);
      case 'approved':
        return $driverDocuments->whereNull('driver_documents.deleted_at')->where('driver_documents.status', DocumentStatusEnum::APPROVED);
      case 'rejected':
        return $driverDocuments->whereNull('driver_documents.deleted_at')->where('driver_documents.status', DocumentStatusEnum::REJECTED);
      case 'trash':
        return $driverDocuments->withTrashed()->whereNotNull('driver_documents.deleted_at');
      default:
        return $driverDocuments->whereNull('driver_documents.deleted_at');
    }
  }

  protected function applySearch($driverDocuments)
  {
    if (isset($this->request->s)) {
      $searchTerm = $this->request->s;

      $driverDocuments = $driverDocuments->with(['driver', 'document'])
        ->where(function ($query) use ($searchTerm) {
          $query->where('document_no', 'LIKE', "%$searchTerm%")
            ->orWhereHas('driver', function ($q) use ($searchTerm) {
              $q->where('name', 'LIKE', "%$searchTerm%")
                ->orWhere('email', 'LIKE', "%$searchTerm%");
            })
            ->orWhereHas('document', function ($q) use ($searchTerm) {
              $q->where('name', 'LIKE', "%$searchTerm%");
            });
        });
    }

    return $driverDocuments;
  }

  protected function applySorting($driverDocuments)
  {
    if ($this->request->has('orderby') && $this->request->has('order')) {
      $orderby = $this->request->orderby;
      $order = $this->request->order;
      if (Schema::hasColumn('driver_documents', $orderby)) {
        return $driverDocuments->orderBy($orderby, $order);
      }

      if (str_contains($orderby, 'users.')) {
        $field = str_replace('users.', '', $orderby);
        if (Schema::hasColumn('users', $field)) {
          $driverDocuments = $driverDocuments
            ->join('users', 'driver_documents.driver_id', '=', 'users.id')
            ->addSelect('driver_documents.*', 'users.name'); 
          return $driverDocuments->orderBy($orderby, $order);
        }
      }

      if (str_contains($orderby, 'documents.')) {
        $field = str_replace('documents.', '', $orderby);
        if (Schema::hasColumn('documents', $field)) {
          $driverDocuments = $driverDocuments
            ->join('documents', 'driver_documents.document_id', '=', 'documents.id')
            ->addSelect('driver_documents.*', 'documents.name'); 
          return $driverDocuments->orderBy($orderby, $order);
        }
      }
    }

    return $driverDocuments;
  }

  public function generate()
  {
    $driverDocuments = $this->getData();

    if ($this->request->has('action') && $this->request->has('ids')) {
      $this->bulkActionHandler();
      $driverDocuments = $this->getData();
    }

    $driverDocuments->each(function ($driverDocument) {
      $driverDocument->driver_name = $driverDocument?->driver?->name;
      $driverDocument->driver_email = $driverDocument?->driver?->email;
      $driverDocument->driver_profile = $driverDocument?->driver?->profile_image_id ?? null;
      $driverDocument->document_name = $driverDocument?->document?->name;
      $driverDocument->date = $driverDocument->created_at ? $driverDocument->created_at->format('Y-m-d h:i:s A') : null; 
      $driverDocument->status = ucfirst($driverDocument->status);
    });

    return [
      'columns' => [
        ['title' => 'Document', 'field' => 'document_name', 'imageField' => 'document_image_id', 'sortable' => true, 'sortField' => 'documents.name', 'action' => true],
        ['title' => 'Driver', 'field' => 'driver_name', 'route' => 'admin.driver.show', 'email' => 'driver_email', 'profile_image' => 'driver_profile', 'sortable' => true, 'profile_id' => 'driver_id', 'sortField' => 'users.name'],
        ['title' => 'Document No', 'field' => 'document_no', 'sortable' => true],
        ['title' => 'Status', 'field' => 'status', 'route' => 'admin.user.status', 'type' => 'badge', 'colorClasses' => ['Pending' => 'warning', 'Approved' => 'primary', 'Rejected' => 'danger'], 'sortable' => true],
        ['title' => 'Created At', 'field' => 'date', 'sortable' => true, 'sortField' => 'created_at'],
        ['title' => 'Action', 'type' => 'action', 'permission' => ['driver_document.index'], 'sortable' => false],
      ],
      'data' => $driverDocuments,
      'actions' => [
        ['title' => 'Edit',  'route' => 'admin.driver-document.edit', 'class' => 'edit', 'whenFilter' => ['all', 'approved', 'rejected'], 'permission' => 'driver_document.edit'],
        ['title' => 'Move to trash', 'route' => 'admin.driver-document.destroy', 'class' => 'delete', 'whenFilter' => ['all', 'approved', 'rejected'], 'permission' => 'driver_document.destroy'],
        ['title' => 'Restore', 'route' => 'admin.driver-document.restore', 'class' => 'restore', 'whenFilter' => ['trash'], 'permission' => 'driver_document.restore'],
        ['title' => 'Delete Permanently', 'route' => 'admin.driver-document.forceDelete', 'class' => 'delete', 'whenFilter' => ['trash'], 'permission' => 'driver_document.forceDelete'],
      ],
      'filters' => [
        ['title' => 'All', 'slug' => 'all', 'count' => $this->getFilterCount('all')],
        ['title' => 'Pending', 'slug' => 'pending', 'count' => $this->getFilterCount('pending')],
        ['title' => 'Approved', 'slug' => 'approved', 'count' => $this->getFilterCount('approved')],
        ['title' => 'Rejected', 'slug' => 'rejected', 'count' => $this->getFilterCount('rejected')],
        ['title' => 'Trash', 'slug' => 'trash', 'count' => $this->getFilterCount('trash')],
      ],
      'bulkactions' => [
        ['title' => 'Pending', 'action' => 'pending', 'permission' => 'driver_document.edit', 'whenFilter' => ['all', 'pending', 'approved', 'rejected']],
        ['title' => 'Approved', 'action' => 'approved', 'permission' => 'driver_document.edit', 'whenFilter' => ['all', 'pending', 'approved', 'rejected']],
        ['title' => 'Rejected', 'action' => 'rejected', 'permission' => 'driver_document.edit', 'whenFilter' => ['all', 'pending', 'approved', 'rejected']],
        ['title' => 'Move to Trash', 'action' => 'trash', 'permission' => 'driver_document.destroy', 'whenFilter' => ['all', 'pending', 'approved', 'rejected']],
        ['title' => 'Restore', 'action' => 'restore', 'permission' => 'driver_document.restore', 'whenFilter' => ['trash']],
        ['title' => 'Delete Permanently', 'action' => 'delete', 'permission' => 'driver_document.forceDelete', 'whenFilter' => ['trash']],
      ],
      'viewActionBox' => ['view' => 'taxido::admin.driver-document.show', 'field' => 'document', 'type' => 'action'],
      'total' => $driverDocuments->count(),
    ];
  }

  public function getFilterCount($filter)
  {
    $driverDocuments = $this->driverDocument;

    $currentUserRole = getCurrentRoleName();
    $currentUserId = getCurrentUserId();

    if ($currentUserRole == RoleEnum::DRIVER) {
      $driverDocuments = $driverDocuments->where('driver_id', $currentUserId);
    }

    if ($this->request->has('driver_id')) {
      $driverDocuments = $driverDocuments->where('driver_id', $this->request->driver_id);
    }

    if (isset($this->request->s)) {
      $searchTerm = $this->request->s;
      $driverDocuments = $driverDocuments->where(function ($query) use ($searchTerm) {
        $query->where('document_no', 'LIKE', "%$searchTerm%")
        ->orWhereHas('driver', function ($q) use ($searchTerm) {
          $q->where('name', 'LIKE', "%$searchTerm%")
          ->orWhere('email', 'LIKE', "%$searchTerm%");
        })
          ->orWhereHas('document', function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%$searchTerm%");
          });
      });
    }

    $driverDocuments = $this->applyStatusFilter($driverDocuments, $filter);

    return $driverDocuments->count();
  }


  public function bulkActionHandler()
  {
    switch ($this->request->action) {
      case 'pending':
        $this->pendingHandler();
        break;
      case 'approved':
        $this->approvedHandler();
        break;
      case 'rejected':
        $this->rejectedHandler();
        break;
      case 'trash':
        $this->trashHandler();
        break;
      case 'restore':
        $this->restoreHandler();
        break;
      case 'delete':
        $this->deleteHandler();
        break;
    }
  }

  protected function pendingHandler()
  {
    $this->driverDocument->whereIn('id', $this->request->ids)->update(['status' => DocumentStatusEnum::PENDING]);
  }

  protected function approvedHandler()
  {
    $this->driverDocument->whereIn('id', $this->request->ids)->update(['status' => DocumentStatusEnum::APPROVED]);
  }

  protected function rejectedHandler()
  {
    $this->driverDocument->whereIn('id', $this->request->ids)->update(['status' => DocumentStatusEnum::REJECTED]);
  }

  protected function trashHandler()
  {
    $this->driverDocument->whereIn('id', $this->request->ids)->delete();
  }

  protected function restoreHandler()
  {
    $this->driverDocument->whereIn('id', $this->request->ids)->restore();
  }

  protected function deleteHandler()
  {
    $this->driverDocument->whereIn('id', $this->request->ids)->forceDelete();
  }
}
