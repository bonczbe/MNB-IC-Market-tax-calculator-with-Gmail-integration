<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRoleEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->revealable()
                    ->password()
                    ->required()
                    ->suffixAction(Action::make('generatePassword')
                        ->icon('heroicon-m-arrow-path')
                        ->tooltip('Generate new password')
                        ->visible(fn ($record) => auth()->user()->role == UserRoleEnum::ADMIN && $record?->id != null)
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
                    ->options(UserRoleEnum::options())
                    ->default('user')
                    ->visible(fn () => auth()->user()->role == UserRoleEnum::ADMIN)
                    ->required(),

                Section::make('Imap Settings')->schema([
                    TextInput::make('imap_host')
                        ->default('imap.gmail.com')
                        ->required()
                        ->required(fn ($record) => $record?->id != null),
                    TextInput::make('imap_port')
                        ->numeric()
                        ->default(993)
                        ->required()
                        ->required(fn ($record) => $record?->id != null),
                    TextInput::make('imap_encryption')
                        ->default('ssl')
                        ->required()
                        ->required(fn ($record) => $record?->id != null),
                    TextInput::make('imap_username')
                        ->placeholder('change-me@change.me')
                        ->visible(fn ($record) => ($record?->id) == auth()->user()->id || $record?->id == null)
                        ->required(fn ($record) => $record?->id != null),
                    TextInput::make('imap_password')
                        ->visible(fn ($record) => ($record?->id) == auth()->user()->id || $record?->id == null)
                        ->password()
                        ->revealable()
                        ->afterStateUpdated(function (string $state, Set $set) {
                            $set('imap_password', str_replace(' ', '', $state ?? ''));
                        })
                        ->live(debounce: 500)
                        ->required(fn ($record) => $record?->id != null),
                    Checkbox::make('imap_validate_cert')
                        ->default(true)
                        ->inline(false)
                        ->required(fn ($record) => $record?->id != null),
                ])->columnSpanFull()
                    ->columns(2),

            ]);
    }
}
