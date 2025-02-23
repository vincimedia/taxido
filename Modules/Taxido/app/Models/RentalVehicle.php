<?php

namespace Modules\Taxido\Models;

use App\Models\Attachment;
use Modules\Taxido\Models\Ride;
use Spatie\Activitylog\LogOptions;
use Modules\Taxido\Models\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RentalVehicle extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'id',
        'name',
        'description',
        'vehicle_type_id',
        'normal_image_id',
        'front_view_id',
        'side_view_id',
        'boot_view_id',
        'interior_image_id',
        'vehicle_per_day_price',
        'allow_with_driver',
        'driver_per_day_charge',
        'vehicle_subtype',
        'fuel_type',
        'gear_type',
        'vehicle_speed',
        'mileage',
        'interior',
        'created_by_id',
        'status',
        'driver_id',
        'registration_no',
        'registration_image_id',
        'verified_status',
        'bag_count',
        'is_ac'
    ];

    protected $with = [
        'zones',
        'normal_image',
        'side_view',
        'boot_view',
        'interior_image',
        'front_view',
        'registration_image',
        'driver'
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
    ];

    protected $casts = [
        'vehicle_type_id' => 'integer',
        'normal_image_id' => 'integer',
        'side_view_id' => 'integer',
        'front_view_id' => 'integer',
        'boot_view_id' => 'integer',
        'interior_image_id' => 'integer',
        'vehicle_per_day_price' => 'float',
        'driver_per_day_charge' => 'float',
        'created_by_id' => 'float',
        'registration_image_id' => 'integer'

    ];
    protected $appends = ['rental_vehicle_galleries'];


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
            ->useLogName('RentalVehicle')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->name} - Rental Vehicle has been {$eventName}");
    }

    public function getInteriorAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return $value ? explode(',', $value) : [];
    }

    public function getRentalVehicleGalleriesAttribute()
    {
        $images = [
            $this->normal_image?->original_url,
            $this->side_view?->original_url,
            $this->boot_view?->original_url,
            $this->interior_image?->original_url,
            $this->front_view?->original_url,
        ];

        return array_filter($images);
    }

    /**
     * @return HasMany
     */
    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class, 'rental_vehicle_id');
    }
    
    /**
     * @return BelongsTo
     */
    public function normal_image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'normal_image_id');
    }

    /**
     * @return BelongsTo
     */
    public function side_view(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'side_view_id');
    }

    /**
     * @return BelongsTo
     */
    public function boot_view(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'boot_view_id');
    }

    /**
     * @return BelongsTo
     */
    public function interior_image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'interior_image_id');
    }

    /**
     * @return BelongsTo
     */
    public function front_view(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'front_view_id');
    }

        /**
     * @return BelongsTo
     */
    public function registration_image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'registration_image_id');
    }

    /**
     * @return BelongsTo
     */
    public function vehicle_type(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id')->with('profile_image')->without(['address', 'zones', 'payment_account', 'vehicle_info']);
    }

    /**
     * @return BelongsTo
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'rental_vehicle_zones');
    }

}
