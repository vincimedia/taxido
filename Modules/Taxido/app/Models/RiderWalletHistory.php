<?php

namespace Modules\Taxido\Models;

use App\Models\User;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiderWalletHistory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The Attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rider_wallet_id',
        'ride_id',
        'detail',
        'amount',
        'type',
        'from_user_id',
        'transaction_id'
    ];

    protected $casts = [
        'rider_wallet_id' => 'integer',
        'ride_id' => 'integer',
        'amount' => 'float',
        'from_user_id' => 'integer',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('RiderWalletHistory')
            ->setDescriptionForEvent(fn(string $eventName) => "RiderWalletHistory  has been {$eventName}");
    }

    /**
     * @return HasOne
     */
    public function from(): HasOne
    {
        return $this->hasOne(User::class, 'from_user_id');
    }

    /**
     * @return HasMany
     */
    public function histories(): HasMany
    {
        return $this->hasMany(RiderWalletHistory::class, 'rider_wallet_id')->orderBy('created_at', 'desc');
    }
}
