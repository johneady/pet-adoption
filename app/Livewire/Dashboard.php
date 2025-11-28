<?php

namespace App\Livewire;

use App\Models\AdoptionApplication;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function getUserApplicationsProperty(): Collection
    {
        return AdoptionApplication::query()
            ->with(['pet.species', 'pet.breed', 'pet.primaryPhoto', 'interview', 'statusHistory'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    public function getApplicationStatusesProperty(): array
    {
        return [
            'submitted' => [
                'label' => 'Submitted',
                'description' => 'Your application has been received. The next step is you will be contacted for an interview.',
                'color' => 'blue',
            ],
            'under_review' => [
                'label' => 'Under Review',
                'description' => 'Our team is currently reviewing your application.',
                'color' => 'yellow',
            ],
            'interview_scheduled' => [
                'label' => 'Interview Scheduled',
                'description' => 'An interview has been scheduled. Check your email for details.',
                'color' => 'purple',
            ],
            'approved' => [
                'label' => 'Approved',
                'description' => 'Congratulations! Your adoption has been approved.',
                'color' => 'green',
            ],
            'rejected' => [
                'label' => 'Declined',
                'description' => 'Unfortunately, your adoption was not approved at this time.',
                'color' => 'red',
            ],
            'withdrawn' => [
                'label' => 'Withdrawn',
                'description' => 'This application has been withdrawn.',
                'color' => 'gray',
            ],
        ];
    }

    public function getCurrentStatusProperty(): ?string
    {
        $application = $this->userApplications->first();

        return $application?->status;
    }

    public function formatDateForUser(?Carbon $date, string $format = 'M j, Y \a\t g:i A'): ?string
    {
        if (! $date) {
            return null;
        }

        $timezone = Auth::user()->timezone ?? config('app.timezone');

        return $date->setTimezone($timezone)->format($format);
    }

    public function getStatusTimestamp(AdoptionApplication $application, string $status): ?string
    {
        $statusHistory = $application->statusHistory
            ->where('to_status', $status)
            ->first();

        return $this->formatDateForUser($statusHistory?->created_at);
    }

    public function render(): mixed
    {
        return view('livewire.dashboard', [
            'userApplications' => $this->userApplications,
            'applicationStatuses' => $this->applicationStatuses,
            'currentStatus' => $this->currentStatus,
        ]);
    }
}
