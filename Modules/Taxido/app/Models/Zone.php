<?php

namespace Modules\Taxido\Models;

use App\Models\Currency;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Zone extends Model
{
    use HasFactory, HasSpatial, SoftDeletes, HasTranslations;

    public $translatable = [
        'name',
    ];
    
    protected $fillable = [
        'id',
        'name',
        'place_points',
        'locations',
        'amount',
        'status',
        'currency_id',
        'distance_type',
        'created_by_id',
    ];

    protected $spatialFields = [
        'place_points',
        'zones:id,name'
    ];

    protected $casts = [
        'place_points' => Polygon::class,
        'locations' => 'json',
        'status' => 'string',
        'amount' => 'integer'
    ];
 
    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_by_id = getCurrentUserId() ?? getAdmin()?->id;
        });
    }

    public function toArray()
    {
        $attributes = parent::toArray();
        foreach ($this->getTranslatableAttributes() as $name) {
            $translation = $this->getTranslation($name, app()->getLocale());
            $attributes[$name] = $translation ?? ($attributes[$name] ?? null);

        }
        return $attributes;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('Zone')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->name} - Zone has been {$eventName}");
    }
    
    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class,'currency_id');
    }

    /**
     * @return BelongsToMany
     */
    public function banners(): BelongsToMany
    {
        return $this->belongsToMany(Banner::class, 'banner_zones');
    }

    /**
     * @return BelongsToMany
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_zones');
    }

    /**
     * @return BelongsToMany
     */
    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class,'driver_zones');
    }

    /**
     * @return BelongsToMany
     */
    public function push_notifications(): BelongsToMany
    {
        return $this->belongsToMany(PushNotification::class, 'push_notifications_zones');
    }

    public function rides(): BelongsToMany
    {
        return $this->belongsToMany(Ride::class, 'ride_zones');
    }

}
