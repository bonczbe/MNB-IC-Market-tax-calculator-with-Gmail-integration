<?php

namespace App\Http\Controllers;

use App\Services\ForexEventService;
use Illuminate\Http\Resources\Json\JsonResource;

class ForexEventController extends Controller
{
    public function __construct(private readonly ForexEventService $forex_event_service) {}

    public function getTodaysEvents()
    {
        return $this->forex_event_service->getTodayEventsAndMap();

    }
}
