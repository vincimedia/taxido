<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuItems extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'menu_items';

  protected $fillable = [
    'label',
    'route',
    'params',
    'parent',
    'module',
    'permission',
    'sort',
    'slug',
    'class',
    'section',
    'badge',
    'menu',
    'depth',
    'badgeable',
    'icon',
    'role_id',
    'status',
    'created_by_id'
  ];

  public $with = [
    'child'
  ];

  public $casts = [
    'params' => 'json',
    'badgeable' => 'boolean', 
  ];

  public static function boot()
  {
    parent::boot();
    static::saving(function ($model) {
      $model->slug = $model->slug ?? Str::slug($model->label);
      $model->created_by_id =   1;
    });
  }

  /**
   * Find the next root
   * @param string $menu
   * @return array
   */
  public static function getNextSortRoot($menu)
  {
    return self::where('menu', $menu)->max('sort') + 1;
  }


  public static function isSlugExists($slug)
  {
    try {
      return self::where('slug', $slug)->whereNull('deleted_at')?->first();
    } catch (Exception $e) {
      //
    }
  }

  public static function getByMenuId($id)
  {
    return parent::with(['child'])->where(['menu' => $id])->orderby('sort', 'asc')->get();
  }

  public static function getParentMenuById($id)
  {
    return parent::with(['child'])->where(['menu' => $id, 'parent' => 0, 'status' => 1])->orderby('sort', 'asc')->get();
  }

  public function parent_menu()
  {
    return $this->belongsTo(Menus::class, 'menu');
  }

  public function child()
  {
    return $this->hasMany(MenuItems::class, 'parent')->orderBy('sort', 'ASC');
  }

  public function isParent()
  {
    return $this->child->isNotEmpty();
  }

  public function getRouteNames()
  {
    $routeNames = [];
    if ($this->isParent()) {
      foreach ($this->child as $child) {
        $routeNames[] = $child->route;
      }
    }
    $routeNames[] = $this->route;
    return $routeNames;
  }

  public function getRoutePrefixes($item)
  {
    $routePrefixes = [];
    if (!empty($item?->child)) {
      foreach ($item->child as $child) {
        $routeName = $child->route;
        $route = collect(Route::getRoutes())->first(function ($value) use ($routeName) {
          return $value->getName() === $routeName;
        });

        $prefix = $route ? $route->getPrefix() : null;
        if ($prefix) {
          $routePrefixes[] = $prefix;
        }
      }
    }
    return $routePrefixes;
  }

  public function isActiveRoute()
  {
    return in_array(Request::route()?->getName(), $this->getRouteNames());
  }

  public function isActiveMenuRoute($item)
  {
    $currentRoutePrefix = substr(Request::route()?->getName(), 0, strrpos(Request::route()?->getName(), '.'));
    $itemRoutePrefix = substr($item->route, 0, strrpos($item->route, '.'));
    if ($currentRoutePrefix === $itemRoutePrefix) {
      if ($itemRoutePrefix . '.index') {
        return true;
      }
      return false;
    }
    return in_array($currentRoutePrefix . '.index', $this->getRouteNames());
  }
}
