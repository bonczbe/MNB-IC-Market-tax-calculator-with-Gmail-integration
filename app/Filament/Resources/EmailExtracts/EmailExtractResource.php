<?php

namespace App\Filament\Resources\EmailExtracts;

use App\Filament\Resources\EmailExtracts\Pages\ListEmailExtracts;
use App\Filament\Resources\EmailExtracts\Pages\ViewEmailExtract;
use App\Filament\Resources\EmailExtracts\Schemas\EmailExtractForm;
use App\Filament\Resources\EmailExtracts\Tables\EmailExtractsTable;
use App\Models\EmailExtract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmailExtractResource extends Resource
{
    protected static ?string $model = EmailExtract::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::EnvelopeOpen;

    protected static ?string $recordTitleAttribute = 'EmailExtract';

    public static function form(Schema $schema): Schema
    {
        return EmailExtractForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailExtractsTable::configure($table);
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
            'index' => ListEmailExtracts::route('/'),
            'view' => ViewEmailExtract::route('/{record}'),
        ];
    }
}
