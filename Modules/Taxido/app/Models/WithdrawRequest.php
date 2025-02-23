<?php

namespace Modules\Taxido\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WithdrawRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'withdraw_requests';

    /**
     * The Attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'amount',
        'message',
        'status',
        'driver_wallet_id',
        'is_used',
        'payment_type',
        'driver_id',
    ];

    protected $with = [
        'driver:id,name,email,profile_image_id'
    ];

    protected $casts = [
        'amount' => 'float',
        'message' => 'string',
        'ride_id' => 'integer',
        'driver_wallet_id' => 'integer',
        'driver_id' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('WithdrawRequest')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->amount} - WithdrawRequest has been {$eventName}");
    }
    
    /**
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
