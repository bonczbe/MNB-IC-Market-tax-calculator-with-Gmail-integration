<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'base_currency',
        'unit',
        'rate',
        'for_currency',
        'date',
    ];
}
