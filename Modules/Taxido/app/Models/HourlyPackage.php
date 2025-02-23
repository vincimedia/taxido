<?php

namespace Modules\Taxido\Models;

use App\Models\User;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HourlyPackage extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The Hourly Packages that are mass assignable.
     * @var array
    */

    protected $fillable = [
        'distance',
        'distance_type',
        'status',
        'hour',
        'created_by_id',
    ];

    protected $casts = [
        'status' => 'integer',
        'hour' => 'float',
        'distance' => 'float',
    ];

    protected $with = [
        'vehicle_types'
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
            ->useLogName('Backup')
            ->setDescriptionForEvent(fn(string $eventName) => "Hourly Package has been {$eventName}");
    }

    /**
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * @return BelongsToMany
     */
    public function vehicle_types(): BelongsToMany
    {
        return $this->belongsToMany(VehicleType::class, 'vehicle_type_hourly_packages');
    }

}
