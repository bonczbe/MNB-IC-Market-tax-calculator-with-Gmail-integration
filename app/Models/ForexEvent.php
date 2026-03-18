<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForexEvent extends Model
{
    protected $fillable = [
        'date',
        'name',
        'previouse',
        'importance',
        'forecast',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
}
