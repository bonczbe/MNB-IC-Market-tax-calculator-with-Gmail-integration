<?php

namespace App\Enums;

enum HolidayEnum: string
{
    case CLOSED = 'closed';
    case EARLY_CLOSE = 'early-close';

    public function label(): string
    {
        return match ($this) {
            self::CLOSED => 'Closed',
            self::EARLY_CLOSE => 'Early Close',
        };
    }

    public static function options(): array
    {
        return array_column(
            array_map(
                fn (self $holiday) => [
                    'value' => $holiday->value,
                    'label' => $holiday->label(),
                ],
                self::cases()
            ),
            'label',
            'value'
        );
    }

    public static function values(): array
    {
        return array_map(
            fn (self $holiday) => [
                'value' => $holiday->value,
            ],
            self::cases()
        );
    }
}
