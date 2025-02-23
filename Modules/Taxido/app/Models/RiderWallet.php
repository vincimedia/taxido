<?php

namespace Modules\Taxido\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiderWallet extends Model
{
     use HasFactory, SoftDeletes;

    /**
     * The Attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rider_id',
        'balance'
    ];

    protected $with = [
        'histories',
    ];
    protected $casts = [
        'rider_id' => 'integer',
        'balance' => 'float',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('RiderWallet')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->rider_id} - Rider Wallet has been {$eventName}");
    }

    /**
     * @return HasMany
     */
    public function histories(): HasMany
    {
        return $this->hasMany(RiderWalletHistory::class, 'rider_wallet_id')->orderBy('created_at','desc');
    }
}
