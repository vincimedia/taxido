<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactions extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'payment_gateways_transactions';

    protected $fillable = [
        'item_id',
        'amount',
        'transaction_id',
        'payment_method',
        'payment_status',
        'is_verified',
        'type',
    ];

    protected $casts = [
        'item_id' => 'integer',
        'amount' => 'float',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('PaymentTransaction')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->transaction_id} - PaymentTransaction has been {$eventName}");
    }
}
