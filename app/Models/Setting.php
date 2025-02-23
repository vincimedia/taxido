<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The values that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'values',
    ];

    protected $casts = [
        'values' => 'json',
    ];

    public function getValuesAttribute($value)
    {
        $values = json_decode($value, true);
        $lightLogoImage = getMedia($values['general']['light_logo_image_id'] ?? null);
        $darkLogoImage = getMedia($values['general']['dark_logo_image_id'] ?? null);
        $faviconImage = getMedia($values['general']['favicon_image_id'] ?? null);
        $defaultCurrency = Currency::find($values['general']['default_currency_id']);
        $logoImage = getMedia($values['app_setting']['logo_image_id'] ?? null);

        $values['general']['light_logo_image'] = $lightLogoImage;
        $values['general']['dark_logo_image'] = $darkLogoImage;
        $values['general']['favicon_image'] = $faviconImage;
        $values['general']['default_currency'] = $defaultCurrency;
        $values['app_setting']['logo_image'] = $logoImage;

        return $values;
    }

    public function setValuesAttribute($value)
    {
        $this->attributes['values'] = json_encode($value);
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logAll()
    //         ->useLogName('Setting')
    //         ->setDescriptionForEvent(fn(string $eventName) => "Setting has been {$eventName}");
    // }
}
