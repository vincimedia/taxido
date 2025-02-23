<?php

namespace Modules\Taxido\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory, SoftDeletes, Sluggable, HasTranslations;

    /**
     * The documents that are mass assignable.
     *
     * @var array
     */
    public $translatable = [
        'name',
    ];

    protected $fillable = [
        'name',
        'slug',
        'is_required',
        'status',
        'created_by_id',
    ];

    protected $casts = [
        'status' => 'integer',
        'is_required' => 'integer',
        'created_at' => 'datetime:Y-m-d',

    ];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_by_id = getCurrentUserId();
        });
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => true,
            ]
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('Document')
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->name} - Document has been {$eventName}");
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

    /**
     * @return BelongsTo
    */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(Rider::class, 'created_by_id');
    }
}
