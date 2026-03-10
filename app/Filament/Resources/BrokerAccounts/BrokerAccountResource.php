<?php

namespace App\Filament\Resources\BrokerAccounts;

use App\Filament\Resources\BrokerAccounts\Pages\CreateBrokerAccount;
use App\Filament\Resources\BrokerAccounts\Pages\EditBrokerAccount;
use App\Filament\Resources\BrokerAccounts\Pages\ListBrokerAccounts;
use App\Filament\Resources\BrokerAccounts\Schemas\BrokerAccountForm;
use App\Filament\Resources\BrokerAccounts\Tables\BrokerAccountsTable;
use App\Models\BrokerAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BrokerAccountResource extends Resource
{
    protected static ?string $model = BrokerAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'BrokerAccount';

    public static function form(Schema $schema): Schema
    {
        return BrokerAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrokerAccountsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrokerAccounts::route('/'),
            'create' => CreateBrokerAccount::route('/create'),
            'edit' => EditBrokerAccount::route('/{record}/edit'),
        ];
    }
}
