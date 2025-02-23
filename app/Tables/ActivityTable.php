<?php
namespace App\Tables;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityTable
{
  protected $activity;
  protected $request;


  public function __construct(Request $request)
  {
    $this->activity = new Activity();
    $this->request = $request;
  }
  public function getData()
  {
    $activities = $this->activity;

    if ($this->request->has('s')) {
      return $activities->where('description', 'LIKE', "%" . $this->request->s . "%")?->paginate($this->request?->paginate);
    }

    if ($this->request->has('orderby') && $this->request->has('order')) {
      return $activities->orderBy($this->request->orderby, $this->request->order)->paginate($this->request?->paginate);
    }

    return $activities->paginate($this->request?->paginate);

  }

  public function generate()
  {
    $activities = $this->getData();

    $activities->each(function ($activity) {
      $activity->date = $activity->created_at->format('Y-m-d h:i:s A');
    });

    $tableConfig = [
      'columns' => [
        ['title' => 'Description', 'field' => 'description', 'imageField' => null, 'action' => true, 'sortable' => true],
        ['title' => 'Created At', 'field' => 'date', 'sortable' => false],
      ],
      'data' => $activities,
      'actions' => [
        ['title' => 'Move to trash', 'route' => 'admin.activity-log.destroy', 'class' => 'delete','permission'=> 'system-tool.destroy'],
      ],
      'filters' => [],
      'bulkactions' => [],
      'total' => $this->activity->count(),
    ];

    return $tableConfig;
  }




}
