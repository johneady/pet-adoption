<?php

namespace Database\Seeders;

use App\Models\AdoptionApplication;
use App\Models\ApplicationStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationStatusHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUsers = User::take(3)->get();

        $statusFlow = [
            'pending' => ['under_review'],
            'under_review' => ['interview_scheduled', 'rejected'],
            'interview_scheduled' => ['approved', 'rejected'],
            'approved' => ['archived'],
        ];

        $applications = AdoptionApplication::all();

        foreach ($applications as $application) {
            $currentStatus = $application->status;
            $previousStatus = null;

            if ($currentStatus === 'pending') {
                ApplicationStatusHistory::create([
                    'adoption_application_id' => $application->id,
                    'from_status' => null,
                    'to_status' => 'pending',
                    'notes' => 'Application submitted',
                    'changed_by' => $application->user_id,
                ]);

                continue;
            }

            $statuses = ['pending'];

            if (in_array($currentStatus, ['under_review', 'interview_scheduled', 'approved', 'rejected', 'archived'])) {
                $statuses[] = 'under_review';
            }

            if (in_array($currentStatus, ['interview_scheduled', 'approved', 'rejected', 'archived'])) {
                $statuses[] = 'interview_scheduled';
            }

            if (in_array($currentStatus, ['approved', 'archived'])) {
                $statuses[] = 'approved';
            }

            if ($currentStatus === 'archived') {
                $statuses[] = 'archived';
            } elseif ($currentStatus === 'rejected' && ! in_array('rejected', $statuses)) {
                $statuses[] = 'rejected';
            }

            foreach ($statuses as $status) {
                ApplicationStatusHistory::create([
                    'adoption_application_id' => $application->id,
                    'from_status' => $previousStatus,
                    'to_status' => $status,
                    'notes' => $this->getStatusChangeNote($previousStatus, $status),
                    'changed_by' => $status === 'pending' ? $application->user_id : fake()->randomElement($adminUsers->pluck('id')->toArray()),
                ]);
                $previousStatus = $status;
            }
        }
    }

    private function getStatusChangeNote(?string $from, string $to): string
    {
        return match ($to) {
            'pending' => 'Application submitted by applicant',
            'under_review' => 'Application moved to review by admin',
            'interview_scheduled' => 'Interview has been scheduled with the applicant',
            'approved' => 'Application approved for adoption',
            'rejected' => fake()->randomElement([
                'Application rejected - not a good fit',
                'Application rejected - incomplete information',
                'Application rejected - failed interview',
            ]),
            'archived' => 'Adoption completed successfully',
            default => 'Status updated',
        };
    }
}
