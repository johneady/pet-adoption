<?php

namespace App\Filament\Resources\AdoptionApplications\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Models\ApplicationStatusHistory;
use Filament\Resources\Pages\CreateRecord;

class CreateAdoptionApplication extends CreateRecord
{
    protected static string $resource = AdoptionApplicationResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? 'submitted';

        return $data;
    }

    protected function afterCreate(): void
    {
        // Create initial status history entry
        ApplicationStatusHistory::create([
            'adoption_application_id' => $this->record->id,
            'from_status' => null,
            'to_status' => $this->record->status,
            'notes' => 'Application submitted',
            'changed_by' => auth()->id(),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
