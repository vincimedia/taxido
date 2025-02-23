<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscribes extends Model
{
    use HasFactory;

    public $fillable = [
        'email',

    ];

}
