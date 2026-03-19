<?php

namespace App\Http\Controllers;

use App\Services\HolydayService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HolyDayController extends Controller
{
    public function __construct(private readonly HolydayService $holyday_service) {}

    public function getTodayHolyDay(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);
        return $this->holyday_service->getAndMapTodaysHolyDays(Carbon::parse($validated['date']));

    }
}
