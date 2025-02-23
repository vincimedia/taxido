<?php

namespace App\Tables;

use App\Models\Backup;
use Illuminate\Http\Request;

class BackupTable
{
  protected $backup;
  protected $request;

  public function __construct(Request $request)
  {
    $this->backup = new Backup();
    $this->request = $request;
  }
  public function getData()
  {
    $backups = $this->backup;

    if ($this->request->has('s')) {
      return $backups->withTrashed()->where('title', 'LIKE', "%" . $this->request->s . "%")?->paginate($this->request?->paginate);
    }

    if ($this->request->has('orderby') && $this->request->has('order')) {
      return $backups->orderBy($this->request->orderby, $this->request->order)->paginate($this->request?->paginate);
    }

    return $backups->whereNull('deleted_at')->paginate($this->request?->paginate);
  }


  public function generate()
  {
    $backups = $this->getData();
    // if ($this->request->has('action') && $this->request->has('ids')) {
    //   $this->bulkActionHandler();
    // }

    $backups->each(function ($backup) {
      $backup->date = $backup->created_at->format('Y-m-d h:i:s A');
    });

    $tableConfig = [
      'columns' => [
        ['title' => 'Title', 'field' => 'title', 'imageField' => null, 'action' => true, 'sortable' => true],
        ['title' => 'description', 'field' => 'description', 'imageField' => null, 'sortable' => true],

        // ['title' => 'Status', 'field' => 'status', 'route' => 'admin.backup.status', 'type' => 'status', 'sortable' => false],
        ['title' => 'Created At', 'field' => 'date', 'sortable' => false],
        ['title' => 'Action', 'type' => 'action', 'permission' => ['system-tool.index'], 'sortable' => false],
      ],
      'data' => $backups,
      'actions' => [],
      'filters' => [],
      'bulkactions' => [],
      'actionButtons' => [
        ['icon' => 'ri-download-2-line', 'route' => 'admin.backup.downloadDbBackup', 'class' => 'dark-icon-box', 'permission' => 'system-tool.index'],
        ['icon' => 'ri-file-download-line', 'route' => 'admin.backup.downloadFilesBackup', 'class' => 'dark-icon-box', 'permission' => 'system-tool.index'],
        ['icon' => 'ri-folder-download-line', 'route' => 'admin.backup.downoadUploadsBackup', 'class' => 'dark-icon-box', 'permission' => 'system-tool.index'],
        ['title' => 'Restore', 'route' => 'admin.backup.restoreBackup', 'class' => 'dark-icon-box', 'permission' => 'system-tool.index'],
      ],
      'total' => $this->backup->count()
    ];

    return $tableConfig;
  }

  //   public function bulkActionHandler()
  //   {
  //     switch ($this->request->action) {
  //       case 'active':
  //         $this->activeHandler();
  //         break;
  //       case 'deactive':
  //         $this->deactiveHandler();
  //         break;
  //       case 'trashed':
  //         $this->trashedHandler();
  //         break;
  //       case 'restore':
  //         $this->restoreHandler();
  //         break;
  //       case 'delete':
  //         $this->deleteHandler();
  //         break;
  //     }
  //   }

  //   public function activeHandler(): void
  //   {
  //     $this->backup->whereIn('id', $this->request->ids)->update(['status' => true]);
  //   }

  //   public function deactiveHandler(): void
  //   {
  //     $this->backup->whereIn('id', $this->request->ids)->update(['status' => false]);
  //   }

  //   public function trashedHandler(): void
  //   {
  //     $this->backup->whereIn('id', $this->request->ids)->delete();
  //   }

  //   public function restoreHandler(): void
  //   {
  //     $this->backup->whereIn('id', $this->request->ids)->restore();
  //   }

  //   public function deleteHandler(): void
  //   {
  //     $this->backup->whereIn('id', $this->request->ids)->forceDelete();
  //   }
}
