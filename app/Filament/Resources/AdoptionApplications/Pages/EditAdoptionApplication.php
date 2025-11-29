<?php

namespace App\Filament\Resources\AdoptionApplications\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Filament\Resources\AdoptionApplications\Widgets\ApplicantDetailsWidget;
use App\Filament\Resources\AdoptionApplications\Widgets\ApplicationAnswersWidget;
use App\Filament\Resources\AdoptionApplications\Widgets\InterviewDetailsWidget;
use App\Filament\Resources\AdoptionApplications\Widgets\NotesWidget;
use App\Filament\Resources\AdoptionApplications\Widgets\PetDetailsWidget;
use App\Mail\InterviewScheduled;
use App\Mail\InterviewScheduledAdmin;
use App\Models\ApplicationStatusHistory;
use App\Models\Interview;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;

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
                ->form([
                    DateTimePicker::make('scheduled_at')
                        ->required()
                        ->timezone(auth()->user()->timezone)
                        ->native(false)
                        ->time(true)
                        ->minDate(fn () => today())
                        ->maxDate(fn () => today()->addMonths(6))
                        ->hoursStep(1)
                        ->minutesStep(30)
                        ->seconds(false),
                    TextInput::make('location')
                        ->placeholder('e.g., Home visit, video call, office meeting'),
                ])
                ->modalHeading('Schedule Interview')
                ->modalDescription('Schedule an interview for this adoption application. Email notifications will be sent to the applicant and you.')
                ->modalSubmitActionLabel('Schedule & Send Emails')
                ->action(function (array $data) {
                    $interview = Interview::create([
                        'adoption_application_id' => $this->record->id,
                        'scheduled_at' => $data['scheduled_at'],
                        'location' => $data['location'] ?? null,
                    ]);

                    $interview->load('adoptionApplication.pet.species', 'adoptionApplication.user');

                    if ($interview->adoptionApplication?->pet) {
                        $interview->adoptionApplication->pet->update([
                            'status' => 'pending',
                        ]);
                    }

                    $oldStatus = $this->record->status;

                    $this->record->update([
                        'status' => 'interview_scheduled',
                    ]);

                    ApplicationStatusHistory::create([
                        'adoption_application_id' => $this->record->id,
                        'from_status' => $oldStatus,
                        'to_status' => 'interview_scheduled',
                        'changed_by' => auth()->id(),
                        'notes' => 'Interview scheduled',
                    ]);

                    $applicant = $this->record->user;
                    $admin = auth()->user();

                    Mail::to($applicant)->send(new InterviewScheduled($interview, $admin));
                    Mail::to($admin)->send(new InterviewScheduledAdmin($interview, $admin));
                }),
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
