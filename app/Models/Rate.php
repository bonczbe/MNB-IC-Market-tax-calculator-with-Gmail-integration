<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Rate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_currency',
        'unit',
        'rate',
        'for_currency',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function booted(): void
    {

        static::created(function (Rate $rate) {
            Cache::clear();
        });

        static::updated(function (Rate $rate) {
            Cache::clear();
        });
    }
}
