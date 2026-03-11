<?php

namespace App\Filament\Resources\YearlyTaxCalculations;

use App\Filament\Resources\YearlyTaxCalculations\Pages\CreateYearlyTaxCalculation;
use App\Filament\Resources\YearlyTaxCalculations\Pages\EditYearlyTaxCalculation;
use App\Filament\Resources\YearlyTaxCalculations\Pages\ListYearlyTaxCalculations;
use App\Filament\Resources\YearlyTaxCalculations\Schemas\YearlyTaxCalculationForm;
use App\Filament\Resources\YearlyTaxCalculations\Tables\YearlyTaxCalculationsTable;
use App\Models\YearlyTaxCalculation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class YearlyTaxCalculationResource extends Resource
{
    protected static ?string $model = YearlyTaxCalculation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    public static function form(Schema $schema): Schema
    {
        return YearlyTaxCalculationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return YearlyTaxCalculationsTable::configure($table);
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
            'index' => ListYearlyTaxCalculations::route('/'),
            'create' => CreateYearlyTaxCalculation::route('/create'),
            'edit' => EditYearlyTaxCalculation::route('/{record}/edit'),
        ];
    }
}
