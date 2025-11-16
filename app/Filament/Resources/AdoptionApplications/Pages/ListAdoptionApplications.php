<?php

namespace App\Filament\Resources\AdoptionApplications\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use Filament\Resources\Pages\ListRecords;

class ListAdoptionApplications extends ListRecords
{
    protected static string $resource = AdoptionApplicationResource::class;

    public function getHeading(): string
    {
        return 'Adoption Applications Requiring Interviews';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
