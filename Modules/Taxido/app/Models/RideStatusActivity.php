<?php

namespace Modules\Taxido\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RideStatusActivity extends Model
{
    use HasFactory;

    protected $table = 'ride_status_activities';

    protected $fillable = [
        'id',
        'status',
        'ride_id',
        'changed_at'
    ];

    protected $casts = [
        'ride_id' => 'integer',
    ];
}
