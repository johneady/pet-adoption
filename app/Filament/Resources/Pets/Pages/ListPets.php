<?php

namespace App\Filament\Resources\Pets\Pages;

use App\Filament\Resources\Pets\PetResource;
use App\Filament\Resources\Pets\Tables\PetsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListPets extends ListRecords
{
    protected static string $resource = PetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return PetsTable::configure($table)
            ->modifyQueryUsing(function (Builder $query) {
                // Get the current status filter value
                $statusFilter = $this->tableFilters['status']['value'] ?? null;

                // If no status filter is selected, exclude adopted pets by default
                if (! $statusFilter) {
                    $query->where('status', '!=', 'adopted');
                }

                return $query;
            });
    }
}
