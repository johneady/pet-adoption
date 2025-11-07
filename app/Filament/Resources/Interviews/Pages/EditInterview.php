<?php

namespace App\Filament\Resources\Interviews\Pages;

use App\Filament\Resources\Interviews\InterviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;

class EditInterview extends EditRecord
{
    protected static string $resource = InterviewResource::class;

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

    public function getFooter(): ?View
    {
        $this->record->load(['adoptionApplication.user', 'adoptionApplication.pet']);

        return view('filament.resources.interviews.pages.create-interview-footer', [
            'adoptionApplication' => $this->record->adoptionApplication,
        ]);
    }
}
