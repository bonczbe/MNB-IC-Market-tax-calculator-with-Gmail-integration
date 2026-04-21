<?php

namespace App\Models;

use App\Enums\HolidayEnum;
use Illuminate\Database\Eloquent\Model;

class Holyday extends Model
{
    protected $fillable = [
        'date',
        'name',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => HolidayEnum::class,
    ];
}
