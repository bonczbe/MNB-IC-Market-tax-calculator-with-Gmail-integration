<?php

namespace App\Filament\Resources\Holydays;

use App\Filament\Resources\Holydays\Pages\CreateHolyday;
use App\Filament\Resources\Holydays\Pages\EditHolyday;
use App\Filament\Resources\Holydays\Pages\ListHolydays;
use App\Filament\Resources\Holydays\Schemas\HolydayForm;
use App\Filament\Resources\Holydays\Tables\HolydaysTable;
use App\Models\Holyday;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HolydayResource extends Resource
{
    protected static ?string $model = Holyday::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingLibrary;

    protected static ?string $recordTitleAttribute = 'Holyday';

    protected static string|UnitEnum|null $navigationGroup = 'Forex';

    public static function form(Schema $schema): Schema
    {
        return HolydayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HolydaysTable::configure($table);
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
            'index' => ListHolydays::route('/'),
            'create' => CreateHolyday::route('/create'),
            'edit' => EditHolyday::route('/{record}/edit'),
        ];
    }
}
