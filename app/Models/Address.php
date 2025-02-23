<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The Addresses that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'is_primary',
        'address',
        'street_address',
        'area_locality',
        'postal_code',
        'city',
        'country_id',
        'state_id',
        'longitude',
        'latitude',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'country_id' => 'integer',
        'user_id' => 'integer',
        'state_id' => 'integer',
    ];

    protected $with = [
        'country:id,name',
        'state:id,name',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id')->select(['id', 'name']);
    }

    /**
     * @return BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }
}
