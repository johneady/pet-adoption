<?php

namespace App\Filament\Resources\Interviews\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Filament\Resources\Interviews\InterviewResource;
use App\Models\ApplicationStatusHistory;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;

class EditInterview extends EditRecord
{
    protected static string $resource = InterviewResource::class;

    protected ?string $originalNotes = null;

    protected function beforeSave(): void
    {
        // Store the original notes value right before save
        $this->originalNotes = $this->record->getOriginal('notes');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewApplication')
                ->label('View Application')
                ->icon(Heroicon::OutlinedDocumentText)
                ->color('gray')
                ->url(fn () => AdoptionApplicationResource::getUrl('edit', ['record' => $this->record->adoptionApplication])),
            // DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Action::make('completeInterview')
                ->label('Complete Interview')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Complete Interview')
                ->modalDescription('This will mark the interview as completed and move the application to under review status for final decisioning.')
                ->action(function () {
                    // Save any pending changes first
                    $this->save();

                    // Get the interview and application
                    $interview = $this->record;
                    $application = $interview->adoptionApplication;

                    // Mark interview as completed
                    $interview->completed_at = now();
                    $interview->save();

                    // Update application status if it's currently interview_scheduled
                    if ($application->status === 'interview_scheduled') {
                        $oldStatus = $application->status;
                        $application->status = 'under_review';
                        $application->save();

                        // Create status history record
                        ApplicationStatusHistory::create([
                            'adoption_application_id' => $application->id,
                            'from_status' => $oldStatus,
                            'to_status' => 'under_review',
                            'notes' => 'Interview completed',
                            'changed_by' => auth()->id(),
                        ]);
                    }

                    // Send success notification
                    Notification::make()
                        ->success()
                        ->title('Interview completed successfully')
                        ->send();

                    // Redirect to interview list
                    return redirect($this->getResource()::getUrl('index'));
                }),
            $this->getCancelFormAction(),
        ];
    }

    protected function afterSave(): void
    {
        // Check if interview notes were created or updated
        $currentNotes = $this->record->notes;

        // Only create history entry if notes actually changed
        if ($this->originalNotes !== $currentNotes) {
            $application = $this->record->adoptionApplication;

            // Determine if notes were created or updated
            $wasEmpty = empty($this->originalNotes);
            $noteAction = $wasEmpty ? 'Interview notes created' : 'Interview notes updated';

            // Create status history entry (no status change, just noting the notes update)
            ApplicationStatusHistory::create([
                'adoption_application_id' => $application->id,
                'from_status' => $application->status,
                'to_status' => $application->status,
                'notes' => $noteAction,
                'changed_by' => auth()->id(),
            ]);

            // Update the original notes to the current value for subsequent saves
            $this->originalNotes = $currentNotes;
        }
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
