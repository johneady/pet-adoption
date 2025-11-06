<?php

namespace App\Filament\Resources\Breeds\Pages;

use App\Filament\Resources\Breeds\BreedResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBreed extends CreateRecord
{
    protected static string $resource = BreedResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
