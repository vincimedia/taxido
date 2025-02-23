<?php

namespace Modules\Taxido\Models;

use App\Models\Attachment;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ride extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'ride_number',
        'rider_id',
        'ride_status_id',
        'service_id',
        'service_category_id',
        'vehicle_type_id',
        'driver_id',
        'coupon_id',
        'coupon_total_discount',
        'rider',
        'otp',
        'weight',
        'start_time',
        'end_time',
        'cargo_image_id',
        'is_otp_verified',
        'parcel_delivered_otp',
        'parcel_receiver',
        'locations',
        'location_coordinates',
        'duration',
        'distance',
        'distance_unit',
        'payment_method',
        'payment_mode',
        'payment_status',
        'ride_fare',
        'driver_tips',
        'tax',
        'platform_fees',
        'zone_charge',
        'wallet_balance',
        'sub_total',
        'description',
        'total',
        'comment',
        'cancellation_reason',
        'hourly_package_id',
        'invoice_url',
        'created_by_id',
        'dropped_at',
        'parcel_otp_verified_at',
        'assigned_driver',
        'rental_vehicle_id',
        'is_with_driver',
        'vehicle_per_day_price',
        'driver_per_day_charge',
        'processing_fee',
    ];

    protected $with = [
        'ride_status:id,name,sequence,slug',
        'driver',
        'cargo_image',
        'service',
        'service_category',
        'rental_vehicle',
        'vehicle_type'
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',

    ];

    protected $casts = [
        'rider_id' => 'integer',
        'ride_number' => 'integer',
        'service_id' => 'integer',
        'vehicle_type_id' => 'integer',
        'driver_id' => 'integer',
        'coupon_id' => 'integer',
        'store_id' => 'integer',
        'rider' => 'json',
        'locations' => 'json',
        'parcel_receiver' => 'json',
        'otp' => 'integer',
        'parcel_delivered_otp' => 'integer',
        'tax' => 'float',
        'total' => 'float',
        'ride_fare' => 'float',
        'sub_total' => 'float',
        'driver_tips' => 'float',
        'platform_fees' => 'float',
        'zone_charge' => 'float',
        'rating_count' => 'float',
        'coupon_total_discount' => 'float',
        'location_coordinates' => 'json',
        'is_otp_verified' => 'integer',
        'ride_status_id' => 'integer',
        'reviews_count' => 'integer',
        'created_by_id' => 'integer',
        'cargo_image_id' => 'integer',
        'service_category_id' => 'integer',
        'assigned_driver' => 'json',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_by_id = getCurrentUserId();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('Ride')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->ride_number} - Ride has been {$eventName}");
    }

    public function getReviewRatingsAttribute()
    {
        return getReviewRatings($this->id);
    }

    public function getRatingCountAttribute()
    {
        return $this->reviews->avg('rating');
    }

    /**
     * @return HasOne
     */
    public function ride_status(): HasOne
    {
        return $this->hasOne(RideStatus::class, 'id', 'ride_status_id');
    }

    /**
     * @return HasOne
     */
    public function riderReview(): HasOne
    {
        return $this->hasOne(RiderReview::class, 'ride_id');
    }

    /**
     * @return HasOne
     */
    public function driverReview(): HasOne
    {
        return $this->hasOne(DriverReview::class, 'ride_id');
    }

    /**
     * @return HasOne
     */
    public function commission_history()
    {
        return $this->hasOne(CabCommissionHistory::class, 'ride_id');
    }

    /**
     * @return HasMany
     */
    public function ride_status_activities(): HasMany
    {
        return $this->hasMany(RideStatusActivity::class, 'ride_id');
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
    public function rental_vehicle(): BelongsTo
    {
        return $this->belongsTo(RentalVehicle::class, 'rental_vehicle_id');
    }

    /**
     * @return BelongsTo
     */
    public function hourly_packages(): BelongsTo
    {
        return $this->belongsTo(HourlyPackage::class, 'hourly_package_id');
    }

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * @return BelongsTo
     */
    public function service_category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     * @return BelongsTo
     */
    public function vehicle_info(): BelongsTo
    {
        return $this->belongsTo(VehicleInfo::class, 'vehicle_type_id');
    }

    /**
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /**
     * @return BelongsTo
     */
    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class, 'rider_id');
    }

    /**
     * @return BelongsTo
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * @return BelongsTo
     */
    public function cargo_image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'cargo_image_id');
    }

    /**
     * @return BelongsToMany
     */
    public function bids(): BelongsToMany
    {
        return $this->belongsToMany(Bid::class, 'ride_bids');
    }

    /**
     * @return BelongsToMany
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'ride_zones');
    }
}
