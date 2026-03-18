<?php

namespace App\Http\Controllers;

use App\Services\HolydayService;
use Illuminate\Http\Resources\Json\JsonResource;

class HolyDayController extends Controller
{
    public function __construct(private readonly HolydayService $holyday_service) {}

    public function getTodayHolyDay()
    {
        $response = $this->holyday_service->getAndMapTodaysHolyDays();

        return JsonResource::make($response);
    }
}
