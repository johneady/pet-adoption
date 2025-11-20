<?php

namespace App\Filament\Resources\Draws;

use App\Filament\Resources\Draws\Pages\CreateDraw;
use App\Filament\Resources\Draws\Pages\EditDraw;
use App\Filament\Resources\Draws\Pages\ListDraws;
use App\Filament\Resources\Draws\Schemas\DrawForm;
use App\Filament\Resources\Draws\Tables\DrawsTable;
use App\Models\Draw;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DrawResource extends Resource
{
    protected static ?string $model = Draw::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static UnitEnum|string|null $navigationGroup = 'Fundraising';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return DrawForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DrawsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Draw::query()
            ->where('ends_at', '<', now())
            ->where('is_finalized', false)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDraws::route('/'),
            'create' => CreateDraw::route('/create'),
            'edit' => EditDraw::route('/{record}/edit'),
        ];
    }
}
