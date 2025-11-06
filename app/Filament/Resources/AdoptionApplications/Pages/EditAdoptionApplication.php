<?php

namespace App\Filament\Resources\AdoptionApplications\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdoptionApplication extends EditRecord
{
    protected static string $resource = AdoptionApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
