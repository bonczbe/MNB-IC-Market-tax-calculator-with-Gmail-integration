<?php

namespace App\DTOs;

use Carbon\CarbonImmutable;

final readonly class HolyDayDTO
{
    public function __construct(public CarbonImmutable $date, public string $name, public string $status) {}

    public function toArray(): array
    {
        return [
            'date' => $this->date->format('Y-m-d H:i'),
            'name' => $this->name,
            'status' => $this->status,
        ];
    }
}
