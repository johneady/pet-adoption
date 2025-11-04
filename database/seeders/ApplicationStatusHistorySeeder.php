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
            'submitted' => ['under_review'],
            'under_review' => ['interview_scheduled', 'rejected'],
            'interview_scheduled' => ['approved', 'rejected'],
            'approved' => ['completed'],
        ];

        $applications = AdoptionApplication::all();

        foreach ($applications as $application) {
            $currentStatus = $application->status;
            $previousStatus = null;

            if ($currentStatus === 'submitted') {
                ApplicationStatusHistory::create([
                    'adoption_application_id' => $application->id,
                    'from_status' => null,
                    'to_status' => 'submitted',
                    'notes' => 'Application submitted',
                    'changed_by' => $application->user_id,
                ]);

                continue;
            }

            $statuses = ['submitted'];

            if (in_array($currentStatus, ['under_review', 'interview_scheduled', 'approved', 'rejected', 'completed'])) {
                $statuses[] = 'under_review';
            }

            if (in_array($currentStatus, ['interview_scheduled', 'approved', 'rejected', 'completed'])) {
                $statuses[] = 'interview_scheduled';
            }

            if (in_array($currentStatus, ['approved', 'completed'])) {
                $statuses[] = 'approved';
            }

            if ($currentStatus === 'completed') {
                $statuses[] = 'completed';
            } elseif ($currentStatus === 'rejected' && ! in_array('rejected', $statuses)) {
                $statuses[] = 'rejected';
            }

            foreach ($statuses as $status) {
                ApplicationStatusHistory::create([
                    'adoption_application_id' => $application->id,
                    'from_status' => $previousStatus,
                    'to_status' => $status,
                    'notes' => $this->getStatusChangeNote($previousStatus, $status),
                    'changed_by' => $status === 'submitted' ? $application->user_id : fake()->randomElement($adminUsers->pluck('id')->toArray()),
                ]);
                $previousStatus = $status;
            }
        }
    }

    private function getStatusChangeNote(?string $from, string $to): string
    {
        return match ($to) {
            'submitted' => 'Application submitted by applicant',
            'under_review' => 'Application moved to review by admin',
            'interview_scheduled' => 'Interview has been scheduled with the applicant',
            'approved' => 'Application approved for adoption',
            'rejected' => fake()->randomElement([
                'Application rejected - not a good fit',
                'Application rejected - incomplete information',
                'Application rejected - failed interview',
            ]),
            'completed' => 'Adoption completed successfully',
            default => 'Status updated',
        };
    }
}
