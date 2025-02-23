<?php

namespace Modules\Taxido\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxidoSetting extends Model 
{
    use HasFactory;

    /**
     * The values that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'taxido_values',
    ];

    protected $casts = [
        'taxido_values' => 'json',
    ];

    public function setValuesAttribute($value)
    {
        $this->attributes['taxido_values'] = json_encode($value);
    }
}