<?php

namespace App\Filament\TaxCalculator\Resources\YearlyTaxCalculations;

use App\Filament\TaxCalculator\Resources\YearlyTaxCalculations\Pages\CreateYearlyTaxCalculation;
use App\Filament\TaxCalculator\Resources\YearlyTaxCalculations\Pages\EditYearlyTaxCalculation;
use App\Filament\TaxCalculator\Resources\YearlyTaxCalculations\Pages\ListYearlyTaxCalculations;
use App\Filament\TaxCalculator\Resources\YearlyTaxCalculations\Schemas\YearlyTaxCalculationForm;
use App\Filament\TaxCalculator\Resources\YearlyTaxCalculations\Tables\YearlyTaxCalculationsTable;
use App\Models\YearlyTaxCalculation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class YearlyTaxCalculationResource extends Resource
{
    protected static ?string $model = YearlyTaxCalculation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    protected static ?int $navigationSort = 4;

    protected static string|UnitEnum|null $navigationGroup = 'Broker Statuses';

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
