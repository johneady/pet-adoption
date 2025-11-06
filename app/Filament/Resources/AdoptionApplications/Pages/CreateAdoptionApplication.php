<?php

namespace App\Filament\Resources\AdoptionApplications\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdoptionApplication extends CreateRecord
{
    protected static string $resource = AdoptionApplicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? 'submitted';

        return $data;
    }
}
