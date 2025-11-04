<?php

namespace App\Filament\Resources\AdoptionApplications;

use App\Filament\Resources\AdoptionApplications\Pages\CreateAdoptionApplication;
use App\Filament\Resources\AdoptionApplications\Pages\EditAdoptionApplication;
use App\Filament\Resources\AdoptionApplications\Pages\ListAdoptionApplications;
use App\Filament\Resources\AdoptionApplications\Schemas\AdoptionApplicationForm;
use App\Filament\Resources\AdoptionApplications\Tables\AdoptionApplicationsTable;
use App\Models\AdoptionApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdoptionApplicationResource extends Resource
{
    protected static ?string $model = AdoptionApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AdoptionApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdoptionApplicationsTable::configure($table);
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
            'index' => ListAdoptionApplications::route('/'),
            'create' => CreateAdoptionApplication::route('/create'),
            'edit' => EditAdoptionApplication::route('/{record}/edit'),
        ];
    }
}
