<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailExtract extends Model
{
    protected $fillable = [
        'date',
        'content',
        'broker_account_id',
    ];
}
