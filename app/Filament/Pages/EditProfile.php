<?php

namespace App\Filament\Pages\Auth;

use App\Enums\UserRoleEnum;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                Select::make('role')
                    ->options(UserRoleEnum::options())
                    ->default('user')
                    ->visible(fn () => auth()->user()->role == UserRoleEnum::ADMIN)
                    ->required(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
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
            ])->columns(2)
            ->inlineLabel(false);
    }

    protected function getRedirectUrl(): string
    {
        return Filament::getCurrentPanel()->getUrl();
    }
}
