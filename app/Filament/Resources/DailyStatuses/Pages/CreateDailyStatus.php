<?php

namespace App\Filament\Resources\DailyStatuses\Pages;

use App\Filament\Resources\DailyStatuses\DailyStatusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyStatus extends CreateRecord
{
    protected static string $resource = DailyStatusResource::class;
}
