<?php

namespace App\Filament\Resources\AdoptionApplications\Widgets;

use App\Mail\InterviewScheduled;
use App\Mail\InterviewScheduledAdmin;
use App\Models\AdoptionApplication;
use App\Models\ApplicationStatusHistory;
use App\Models\Interview;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Mail;

class InterviewDetailsWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.resources.adoption-applications.widgets.interview-details-widget';

    protected int|string|array $columnSpan = 'full';

    public ?AdoptionApplication $record = null;

    public function getInterview()
    {
        return $this->record?->interview;
    }

    public function hasInterview(): bool
    {
        return $this->getInterview() !== null;
    }

    public function scheduleInterviewAction(): Action
    {
        return Action::make('scheduleInterview')
            ->label('Schedule Interview')
            ->icon(Heroicon::OutlinedCalendar)
            ->color('primary')
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
            });
    }
}
