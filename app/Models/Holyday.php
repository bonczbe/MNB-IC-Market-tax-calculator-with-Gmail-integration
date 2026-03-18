<?php

namespace App\Models;

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
    ];
}
