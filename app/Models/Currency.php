<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

     /**
     * The currencies that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'status',
        'symbol',
        'no_of_decimal',
        'exchange_rate',
        'created_by_id',
        'system_reserve',
    ];

    protected $casts = [
        'no_of_decimal' => 'integer',
        'status' => 'integer',
        'system_reserve' =>  'integer'
    ];
    
    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_by_id = getCurrentUserId() ?? getAdmin()?->id;
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('DriverWallet')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->code} - Currency has been {$eventName}");
    }
    
    
}
