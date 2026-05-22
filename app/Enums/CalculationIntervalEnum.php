<?php

namespace App\Enums;

enum CalculationIntervalEnum: string
{
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';
}
