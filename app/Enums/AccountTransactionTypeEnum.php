<?php

namespace App\Enums;

enum AccountTransactionTypeEnum: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => 'Deposit',
            self::WITHDRAWAL => 'Withdrawal',
        };
    }

    public static function options(): array
    {
        return array_column(
            array_map(
                fn (self $transactionType) => [
                    'value' => $transactionType->value,
                    'label' => $transactionType->label(),
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
            fn (self $transactionType) => [
                'value' => $transactionType->value,
            ],
            self::cases()
        );
    }
}
