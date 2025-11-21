<?php

namespace App\Filament\Resources\TicketPurchaseRequests;

use App\Filament\Resources\TicketPurchaseRequests\Pages\ListTicketPurchaseRequests;
use App\Filament\Resources\TicketPurchaseRequests\Tables\TicketPurchaseRequestsTable;
use App\Models\TicketPurchaseRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TicketPurchaseRequestResource extends Resource
{
    protected static ?string $model = TicketPurchaseRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static UnitEnum|string|null $navigationGroup = 'Fundraising';

    protected static ?string $navigationLabel = 'Ticket Requests';

    protected static ?int $navigationSort = 20;

    public static function table(Table $table): Table
    {
        return TicketPurchaseRequestsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = TicketPurchaseRequest::query()
            ->where('status', 'pending')
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
            'index' => ListTicketPurchaseRequests::route('/'),
        ];
    }
}
