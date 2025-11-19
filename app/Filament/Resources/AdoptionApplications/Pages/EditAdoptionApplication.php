<?php

namespace App\Filament\Resources\AdoptionApplications\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Filament\Resources\AdoptionApplications\Widgets\ApplicantDetailsWidget;
use App\Filament\Resources\AdoptionApplications\Widgets\ApplicationAnswersWidget;
use App\Filament\Resources\AdoptionApplications\Widgets\InterviewDetailsWidget;
use App\Filament\Resources\AdoptionApplications\Widgets\NotesWidget;
use App\Filament\Resources\AdoptionApplications\Widgets\PetDetailsWidget;
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
                ->visible(fn () => $this->record->status === 'submitted' && $this->record->interview === null)
                ->url(fn () => InterviewResource::getUrl('create', ['adoption_application_id' => $this->record->id])),
            Action::make('archive')
                ->label('Archive')
                ->icon(Heroicon::OutlinedArchiveBox)
                ->color('warning')
                ->outlined()
                ->visible(fn () => in_array($this->record->status, ['approved', 'rejected']))
                ->requiresConfirmation()
                ->modalHeading('Archive Application')
                ->modalDescription('Are you sure you want to archive this application? This will move it to archived status.')
                ->action(function () {
                    $this->record->update(['status' => 'archived']);
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            Action::make('view_status_history')
                ->label('View Status History')
                ->icon(Heroicon::OutlinedClock)
                ->color('gray')
                ->outlined()
                ->url(fn () => AdoptionApplicationResource::getUrl('history', ['record' => $this->record])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PetDetailsWidget::make(['record' => $this->record]),
            ApplicantDetailsWidget::make(['record' => $this->record]),
            ApplicationAnswersWidget::make(['record' => $this->record]),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [
            InterviewDetailsWidget::make(['record' => $this->record]),
            NotesWidget::make(['record' => $this->record]),
        ];
    }
}
