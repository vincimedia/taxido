<?php

namespace Modules\Taxido\Models;

use App\Models\Tax;
use App\Models\Attachment;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VehicleType extends Model implements HasMedia
{
    use HasFactory, Sluggable, InteractsWithMedia, SoftDeletes, HasTranslations;

    /**
     * The Vehicles that are mass assignable.
     *
     * @var array
     */
    public $translatable = [
        'name',
    ];

    protected $fillable = [
        'name',
        'slug',
        'base_amount',
        'vehicle_image_id',
        'vehicle_map_icon_id',
        'min_per_unit_charge',
        'max_per_unit_charge',
        'cancellation_charge',
        'waiting_time_charge',
        'commission_type',
        'commission_rate',
        'tax_id',
        'min_per_min_charge',
        'max_per_min_charge',
        'min_per_weight_charge',
        'max_per_weight_charge',
        'status',
        'created_by_id'
    ];

    protected $with = [
        'tax',
        'vehicle_image',
        'vehicle_map_icon',
        'zones',
    ];

    protected $casts = [
        'status' => 'integer',
        'commission_rate' => 'float',
        'base_amount' => 'float',
        'vehicle_image_id' => 'integer',
        'min_per_unit_charge' => 'float',
        'max_per_unit_charge' => 'float',
        'cancellation_charge' => 'float',
        'waiting_time_charge' => 'float',
        'vehicle_map_icon_id' => 'integer',
        'min_per_min_charge' => 'float',
        'max_per_min_charge' => 'float',
        'min_per_weight_charge' => 'float',
        'max_per_weight_charge' => 'float',
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
            ->useLogName('Vehicle Type')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->name} - Vehicle Type has been {$eventName}");
    }

    /**
     * @return BelongsTo
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    /**
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(Rider::class, 'created_by_id');
    }

    /**
     * @return BelongsTo
     */
    public function vehicle_image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'vehicle_image_id');
    }

    /**
     * @return BelongsTo
     */
    public function vehicle_map_icon(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'vehicle_map_icon_id');
    }

    /**
     * @return BelongsToMany
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'vehicle_type_zones', 'vehicle_type_id');
    }

    /**
     * @return BelongsToMany
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_vehicle_types');
    }

    /**
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'vehicle_services');
    }

    /**
     * @return BelongsToMany
     */
    public function service_categories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'vehicle_categories');
    }

    /**
     * @return BelongsToMany
     */
    public function hourly_packages(): BelongsToMany
    {
        return $this->belongsToMany(HourlyPackage::class, 'vehicle_type_hourly_packages');
    }

    /**
     * @return BelongsToMany
     */
    public function driver_rules(): BelongsToMany
    {
        return $this->belongsToMany(DriverRule::class, 'driver_vehicle_types');
    }
}
