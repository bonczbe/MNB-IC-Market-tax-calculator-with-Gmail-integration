<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::USER => 'User',
        };
    }

    public static function options(): array
    {
        return array_column(
            array_map(
                fn (self $role) => [
                    'value' => $role->value,
                    'label' => $role->label(),
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
            fn (self $role) => [
                'value' => $role->value,
            ],
            self::cases()
        );
    }
}
