<?php

namespace Modules\Taxido\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The documents that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'location',
        'type',
        'rider_id',
    ];

    protected $casts = [
        'rider_id' => 'integer',
        'created_at' => 'datetime:Y-m-d',
    ];

    /**
     * @return BelongsTo
    */
    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class, 'rider_id');
    }
}
