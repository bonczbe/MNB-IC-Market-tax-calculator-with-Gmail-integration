<?php

namespace App\Http\Controllers;

use App\Services\ForexEventService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ForexEventController extends Controller
{
    public function __construct(private readonly ForexEventService $forex_event_service) {}

    public function getByDate(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        return $this->forex_event_service->getEventsByDate(Carbon::parse($validated['date']));

    }
}
