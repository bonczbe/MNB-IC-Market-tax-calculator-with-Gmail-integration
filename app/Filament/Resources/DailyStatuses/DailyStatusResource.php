<?php

namespace App\Filament\Resources\DailyStatuses;

use App\Filament\Resources\DailyStatuses\Pages\CreateDailyStatus;
use App\Filament\Resources\DailyStatuses\Pages\EditDailyStatus;
use App\Filament\Resources\DailyStatuses\Pages\ListDailyStatuses;
use App\Filament\Resources\DailyStatuses\Schemas\DailyStatusForm;
use App\Filament\Resources\DailyStatuses\Tables\DailyStatusesTable;
use App\Models\DailyStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DailyStatusResource extends Resource
{
    protected static ?string $model = DailyStatus::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'DailyStatus';

    public static function form(Schema $schema): Schema
    {
        return DailyStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyStatusesTable::configure($table);
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
            'index' => ListDailyStatuses::route('/'),
            'create' => CreateDailyStatus::route('/create'),
            'edit' => EditDailyStatus::route('/{record}/edit'),
        ];
    }
}
