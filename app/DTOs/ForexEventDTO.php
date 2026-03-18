<?php

namespace App\DTOs;

use Carbon\CarbonImmutable;

final readonly class ForexEventDTO
{
    public function __construct(public CarbonImmutable $date, public string $name, public ?string $previouse, public ?string $forecast, public string $importance) {}

    public function toArray(): array
    {
        return [
            'date' => $this->date->format('Y-m-d H:i'),
            'name' => $this->name,
            'previouse' => $this->previouse,
            'importance' => $this->importance,
            'forecast' => $this->forecast,
        ];
    }
}
