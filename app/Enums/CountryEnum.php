<?php

namespace App\Enums;

enum CountryEnum: string
{
    case US = 'US';
    case DE = 'DE';
    case FR = 'FR';
    case IT = 'IT';
    case ES = 'ES';
    case UK = 'UK';
    case JP = 'JP';
    case CN = 'CN';
    case CH = 'CH';
    case CA = 'CA';
    case AU = 'AU';

    public function label(): string
    {
        return match ($this) {
            self::US => 'United States',
            self::DE => 'Germany',
            self::FR => 'France',
            self::IT => 'Italy',
            self::ES => 'Spain',
            self::UK => 'United Kingdom',
            self::JP => 'Japan',
            self::CN => 'China',
            self::CH => 'Switzerland',
            self::CA => 'Canada',
            self::AU => 'Australia',
        };
    }

    public static function options(): array
    {
        return array_column(
            array_map(
                fn (self $country) => [
                    'value' => $country->value,
                    'label' => $country->label(),
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
            fn (self $country) => [
                'value' => $country->value,
            ],
            self::cases()
        );
    }
}
