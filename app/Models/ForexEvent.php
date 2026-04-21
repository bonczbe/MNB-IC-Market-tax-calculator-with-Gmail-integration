<?php

namespace App\Models;

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
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:s');
    }
}
