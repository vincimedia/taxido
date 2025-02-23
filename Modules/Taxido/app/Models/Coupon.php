<?php

namespace Modules\Taxido\Models;

use App\Models\User;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory, SoftDeletes,HasTranslations;

    /**
     * The Coupons that are mass assignable.
     *
     * @var array
     */

    public $translatable = [
        'title',
        'description'
    ];

    protected $fillable = [
        'title',
        'description',
        'code',
        'used',
        'type',
        'amount',
        'status',
        'content',
        'min_spend',
        'is_expired',
        'start_date',
        'end_date',
        'is_apply_all',
        'is_unlimited',
        'created_by_id',
        'is_first_ride',
        'usage_per_coupon',
        'usage_per_rider',
    ];

    protected $with = [
        'zones:id,name',
        'services:id,name',
        'service_categories:id,name',
        'vehicle_types:id,name',
        'riders:id,name'
    ];

    protected $casts = [
        'min_spend' => 'integer',
        'amount' => 'integer',
        'usage_per_rider' => 'integer',
        'is_expired' => 'integer',
        'is_first_ride' => 'integer',
        'is_unlimited' => 'integer',
        'status' => 'integer',
    ];


    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_by_id = getCurrentUserId();
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
            ->useLogName('Coupon')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->title} - Coupon has been {$eventName}");
    }

    /**
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany
     */
    public function riders(): BelongsToMany
    {
        return $this->belongsToMany(Rider::class, 'coupon_riders', 'coupon_id');
    }

    /**
     * @return BelongsToMany
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'coupon_zones', 'coupon_id');
    }

    /**
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'coupon_service', 'coupon_id');
    }

    /**
     * @return BelongsToMany
     */
    public function service_categories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'coupon_categories', 'coupon_id');
    }

    /**
     * @return BelongsToMany
     */
    public function vehicle_types(): BelongsToMany
    {
        return $this->belongsToMany(VehicleType::class, 'coupon_vehicle_types', 'coupon_id');
    }


    public function rides()
    {
        return $this->hasMany(Ride::class, 'coupon_id');
    }
}
