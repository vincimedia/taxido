<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menus extends Model
{
    use Sluggable, HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'menus';

    protected $fillable = [
      'name',
      'slug',
      'status',
      'system_reserve',
      'created_by_id'
    ];

    public static function boot()
    {
      parent::boot();
      static::saving(function ($model) {
        $model->created_by_id = getCurrentUserId() ?? getAdmin()?->id;
      });
    }

    public function sluggable(): array
    {
      return [
        'slug' => [
          'source' => 'name',
          'onUpdate' => true,
        ]
      ];
    }

    public static function byName($name)
    {
      return self::where('name', '=', $name)->first();
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
      return $this->hasMany(MenuItems::class, 'menu')->with('child')->where('parent', 0)->orderBy('sort', 'ASC');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('Menu')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->name} - Menu has been {$eventName}");
    }

}
