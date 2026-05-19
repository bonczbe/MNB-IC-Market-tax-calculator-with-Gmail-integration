<?php

namespace App\Filament\TaxCalculator\Resources\ForexEvents;

use App\Filament\TaxCalculator\Resources\ForexEvents\Pages\CreateForexEvent;
use App\Filament\TaxCalculator\Resources\ForexEvents\Pages\EditForexEvent;
use App\Filament\TaxCalculator\Resources\ForexEvents\Pages\ListForexEvents;
use App\Filament\TaxCalculator\Resources\ForexEvents\Schemas\ForexEventForm;
use App\Filament\TaxCalculator\Resources\ForexEvents\Tables\ForexEventsTable;
use App\Models\ForexEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ForexEventResource extends Resource
{
    protected static ?string $model = ForexEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDateRange;

    protected static ?string $recordTitleAttribute = 'ForexEvent';

    protected static string|UnitEnum|null $navigationGroup = 'Forex';

    public static function form(Schema $schema): Schema
    {
        return ForexEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForexEventsTable::configure($table);
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
            'index' => ListForexEvents::route('/'),
            'create' => CreateForexEvent::route('/create'),
            'edit' => EditForexEvent::route('/{record}/edit'),
        ];
    }
}
