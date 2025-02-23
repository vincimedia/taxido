<?php

namespace Modules\Taxido\Models;

use App\Models\Attachment;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model implements HasMedia
{
    use HasFactory,SoftDeletes,Sluggable,InteractsWithMedia, HasTranslations;

    public $translatable = [
        'name',
    ];

    protected $table = 'services';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'service_icon_id',
        'service_image_id',
        'status',
        'is_primary',
        'created_by_id'
    ];

        protected $with = [
            'service_icon',
            'service_image',
            'service_categories'
        ];

    protected $casts = [
        'status' => 'integer',
        'is_primary' => 'integer',
        'service_image_id' => 'integer',
    ];


    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_by_id = getCurrentUserId() ?? getAdmin()?->id;
        });
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => false,
            ]
        ];
    }

    public function toArray()
    {
        $attributes = parent::toArray();
        foreach ($this->getTranslatableAttributes() as $name) {
            $translation = $this->getTranslation($name, app()->getLocale());
            $attributes[$name] = $translation ?? ($attributes[$name] ?? null);

        }
        return $attributes;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('Service')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->name} - Service has been {$eventName}");
    }

    /**
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(Rider::class, 'created_by_id');
    }

    /**
     * @return BelongsTo
     */
    public function service_icon(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'service_icon_id');
    }

    /**
     * @return BelongsTo
     */
    public function service_image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'service_image_id');
    }

    /**
     * @return BelongsToMany
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_service_categories');
    }

    /**
     * @return BelongsToMany
     */
    public function service_categories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'categories_services');
    }
}
