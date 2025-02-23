<?php

namespace Modules\Taxido\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DriverWalletHistory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The Attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'driver_wallet_id',
        'ride_id',
        'detail',
        'amount',
        'type',
        'from_user_id',
    ];

    
    protected $casts = [
        'driver_wallet_id' => 'integer',
        'ride_id' => 'integer',
        'amount' => 'float',
        'from_user_id' => 'integer',
    ];
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];


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
        return $this->hasMany(DriverWalletHistory::class, 'driver_wallet_id')->orderBy('created_at','desc');
    }
}
