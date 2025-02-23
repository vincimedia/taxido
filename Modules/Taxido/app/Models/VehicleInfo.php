<?php

namespace Modules\Taxido\Models;

use App\Models\Attachment;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VehicleInfo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_info';
    
    /**
     * The Vehicles that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'color',
        'plate_number',
        'seat',
        'model',
        'vehicle_type_id',
        'driver_id'
    ];

    protected $casts = [
        'vehicle_type_id' => 'integer',
        'driver_id' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('VehicleInfo')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->model} - Vehicle Info has been {$eventName}");
    }
    
    

    /**
     * @return BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
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
    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    /**
     * @return BelongsToMany
     */
    public function vehicle_galleries(): BelongsToMany
    {
        return $this->belongsToMany(Attachment::class, 'vehicle_images')->orderBy('id', 'desc');
    }

}
