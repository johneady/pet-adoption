<?php

namespace App\Filament\Resources\AdoptionApplications\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Filament\Resources\AdoptionApplications\Widgets\NotesWidget;
use App\Filament\Resources\Interviews\InterviewResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAdoptionApplication extends EditRecord
{
    protected static string $resource = AdoptionApplicationResource::class;

    public function getHeading(): string
    {
        return "{$this->record->pet->name} - (Adopting Parent: {$this->record->user->name})";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('schedule_interview')
                ->label('Schedule Interview')
                ->icon(Heroicon::OutlinedCalendar)
                ->color('primary')
                ->visible(fn () => $this->record->interview === null)
                ->url(fn () => InterviewResource::getUrl('create', ['adoption_application_id' => $this->record->id])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [
            NotesWidget::make(['record' => $this->record]),
        ];
    }
}
