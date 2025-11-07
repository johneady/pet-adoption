<?php

namespace App\Filament\Resources\Interviews\Pages;

use App\Filament\Resources\Interviews\InterviewResource;
use App\Models\AdoptionApplication;
use App\Models\Interview;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\View\View;

class CreateInterview extends CreateRecord
{
    protected static string $resource = InterviewResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        /** @var Interview $interview */
        $interview = $this->record;

        $interview->load('adoptionApplication.pet');

        if ($interview->adoptionApplication?->pet) {
            $interview->adoptionApplication->pet->update([
                'status' => 'pending',
            ]);
        }
    }

    public function getFooter(): ?View
    {
        $adoptionApplicationId = request()->query('adoption_application_id');

        if (! $adoptionApplicationId) {
            return null;
        }

        $adoptionApplication = AdoptionApplication::with(['user', 'pet'])->find($adoptionApplicationId);

        if (! $adoptionApplication) {
            return null;
        }

        return view('filament.resources.interviews.pages.create-interview-footer', [
            'adoptionApplication' => $adoptionApplication,
        ]);
    }
}
