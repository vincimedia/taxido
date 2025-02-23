<?php

namespace Modules\Taxido\Models;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Modules\Taxido\Enums\BidStatusEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RideRequest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'rider_id',
        'ride_request_status_id',
        'service_id',
        'service_category_id',
        'vehicle_type_id',
        'cargo_image_id',
        'rider',
        'locations',
        'location_coordinates',
        'parcel_receiver',
        'parcel_delivered_otp',
        'duration',
        'distance',
        'distance_unit',
        'payment_method',
        'ride_fare',
        'created_by_id',
        'hourly_package_id',
        'description',
        'weight',
        'start_time',
        'end_time',
        'is_with_driver',
        'rental_vehicle_id',
        'no_of_days'
    ];

    protected $with = [
        'drivers',
        'cargo_image',
        'service_category',
        'vehicle_type',
        'rental_vehicle',
        'service:id,name,slug'
    ];

    protected $casts = [
        'rider_id' => 'integer',
        'service_id' => 'integer',
        'service_category_id' => 'integer',
        'hourly_package_id' => 'integer',
        'vehicle_type_id' => 'integer',
        'driver_id' => 'integer',
        'parcel_receiver' => 'json',
        'rider' => 'json',
        'locations' => 'json',
        'location_coordinates' => 'json',
        'otp' => 'integer',
        'parcel_delivered_otp' => 'integer',
        'ride_request_status_id' => 'integer',
        'ride_fare' => 'float',
        'created_by_id' => 'integer',
        'cargo_image_id' => 'integer',
        'rental_vehicle_id' => 'integer',
        'no_of_days' => 'integer'
    ];
    protected $hidden = [
        'deleted_at',
        'updated_at',
    ];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_by_id = getCurrentUserId();
        });
    }

    public function getAcceptedBid()
    {
        return $this->bids()?->where('status', BidStatusEnum::ACCEPTED)?->first();
    }

    /**
     * @return HasMany
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'ride_request_id');
    }

    /**
     * @return BelongsTo
     */
    public function cargo_image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'cargo_image_id');
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
    public function service_category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
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
    public function vehicle_type(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

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
     * @return BelongsToMany
     */
    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'ride_request_drivers');
    }

    /**
     * @return BelongsToMany
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'ride_request_zones');
    }
}
