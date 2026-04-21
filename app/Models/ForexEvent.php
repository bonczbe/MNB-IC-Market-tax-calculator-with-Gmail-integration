<?php

namespace App\Models;

use App\Enums\CountryEnum;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class ForexEvent extends Model
{
    protected $fillable = [
        'date',
        'name',
        'country',
        'previouse',
        'importance',
        'forecast',
    ];

    protected $casts = [
        'date' => 'datetime',
        'country' => CountryEnum::class,
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:s');
    }
}
