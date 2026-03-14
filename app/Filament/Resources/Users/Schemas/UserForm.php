<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->disabled(fn ($record) => $record?->id != null && $record->id != auth()->user()->id)
                    ->password()
                    ->required()
                    ->suffixAction(Action::make('generatePassword')
                        ->icon('heroicon-m-arrow-path')
                        ->tooltip('Generate new password')
                        ->visible(fn ($record) => auth()->user()->role == 'admin' && $record?->id != null)
                        ->action(function ($record) {
                            $newPassword = Str::password(12);

                            $record->update([
                                'password' => Hash::make($newPassword),
                            ]);

                            Notification::make()
                                ->title('New password generated')
                                ->body("Password: {$newPassword}")
                                ->success()
                                ->persistent()
                                ->send();
                        })),
                Select::make('role')
                    ->options(['admin' => 'Admin', 'user' => 'User'])
                    ->default('user')
                    ->required(),
            ]);
    }
}
