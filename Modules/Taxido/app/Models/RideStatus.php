<?php

namespace Modules\Taxido\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Modules\Taxido\Enums\RideStatusEnum;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RideStatus extends Model
{
    use Sluggable, HasFactory, SoftDeletes;

    protected $table = 'ride_status';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'status',
        'sequence',
        'created_by_id',
        'system_reserve',
    ];

    protected $casts = [
        'status' => 'integer',
        'sequence' => 'integer',
        'created_by_id' => 'integer',
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
            ->useLogName('Ride Status')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->name} - Ride Status has been {$eventName}");
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

    public static function getAllSequences()
    {
        return self::whereNull('deleted_at')->pluck('sequence')->toArray();
    }

    public static function getNameBySequence($sequence)
    {
        return self::where('sequence', $sequence)->whereNull('deleted_at')->value('name');
    }

    public static function getSequenceByName($name)
    {
        return self::where('name', $name)->whereNull('deleted_at')->value('sequence');
    }

    public static function getCancelSequence()
    {
        return self::getSequenceByName(RideStatusEnum::CANCELLED);
    }

    /**
     * @return HasMany
     */
    public function ride_requests(): HasMany
    {
        return $this->hasMany(RideRequest::class, 'ride_request_status_id');
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
    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class, 'ride_id');
    }

   
}
