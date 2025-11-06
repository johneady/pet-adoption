<?php

namespace App\Filament\Resources\Species\Pages;

use App\Filament\Resources\Species\SpeciesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecies extends CreateRecord
{
    protected static string $resource = SpeciesResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
