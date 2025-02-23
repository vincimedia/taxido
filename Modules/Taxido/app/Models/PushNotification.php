<?php

namespace Modules\Taxido\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PushNotification extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia,SoftDeletes;

    protected $fillable = [
        'title',
        'message',
        'send_to',
        'user_id',
        'is_read',
        'image_url',
        'url',
        'notification_type',
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
            ->useLogName('PushNotification')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->title} - Push Notification has been {$eventName}");
    }
    
    /**
     * @return BelongsToMany
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'push_notifications_zones', 'push_notifications_id');
    }

}
