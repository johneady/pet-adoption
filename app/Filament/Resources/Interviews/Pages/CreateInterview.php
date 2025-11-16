<?php

namespace App\Filament\Resources\Interviews\Pages;

use App\Filament\Resources\Interviews\InterviewResource;
use App\Mail\InterviewScheduled;
use App\Models\AdoptionApplication;
use App\Models\ApplicationStatusHistory;
use App\Models\Interview;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;

class CreateInterview extends CreateRecord
{
    protected static string $resource = InterviewResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Schedule Interview')
            ->modalDescription('This will schedule the interview and send email notifications to the applicant and you.')
            ->modalSubmitActionLabel('Schedule & Send Emails');
    }

    protected function afterCreate(): void
    {
        /** @var Interview $interview */
        $interview = $this->record;

        $interview->load('adoptionApplication.pet.species', 'adoptionApplication.user');

        if ($interview->adoptionApplication?->pet) {
            $interview->adoptionApplication->pet->update([
                'status' => 'pending',
            ]);
        }

        if ($interview->adoptionApplication) {
            $oldStatus = $interview->adoptionApplication->status;

            $interview->adoptionApplication->update([
                'status' => 'interview_scheduled',
            ]);

            ApplicationStatusHistory::create([
                'adoption_application_id' => $interview->adoptionApplication->id,
                'from_status' => $oldStatus,
                'to_status' => 'interview_scheduled',
                'changed_by' => auth()->id(),
                'notes' => 'Interview scheduled',
            ]);

            // Send email notifications to applicant and admin who scheduled the interview
            $applicant = $interview->adoptionApplication->user;
            $admin = auth()->user();

            Mail::to($applicant)->send(new InterviewScheduled($interview, $admin));
            Mail::to($admin)->send(new InterviewScheduled($interview, $admin));
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
