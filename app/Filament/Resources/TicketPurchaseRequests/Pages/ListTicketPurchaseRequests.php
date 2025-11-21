<?php

namespace App\Filament\Resources\TicketPurchaseRequests\Pages;

use App\Filament\Resources\TicketPurchaseRequests\TicketPurchaseRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListTicketPurchaseRequests extends ListRecords
{
    protected static string $resource = TicketPurchaseRequestResource::class;

    protected function getTableFiltersFormInitialState(): array
    {
        return [
            'status' => 'pending',
        ];
    }
}
