<?php

namespace Database\Seeders;

use App\Models\AdoptionApplication;
use App\Models\Interview;
use Illuminate\Database\Seeder;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Main Shelter - Room A',
            'Main Shelter - Room B',
            'Downtown Office',
            'Virtual Meeting (Zoom)',
            'Home Visit',
        ];

        $interviewScheduledApps = AdoptionApplication::where('status', 'interview_scheduled')->get();
        foreach ($interviewScheduledApps as $application) {
            Interview::create([
                'adoption_application_id' => $application->id,
                'scheduled_at' => fake()->dateTimeBetween('now', '+2 weeks'),
                'location' => fake()->randomElement($locations),
                'notes' => fake()->optional()->sentence(),
            ]);
        }

        $approvedApps = AdoptionApplication::where('status', 'approved')->get();
        foreach ($approvedApps as $application) {
            Interview::create([
                'adoption_application_id' => $application->id,
                'scheduled_at' => fake()->dateTimeBetween('-2 weeks', '-3 days'),
                'location' => fake()->randomElement($locations),
                'notes' => 'Interview went well. Applicant shows great understanding of pet care.',
                'completed_at' => fake()->dateTimeBetween('-2 days', 'now'),
            ]);
        }

        $completedApps = AdoptionApplication::where('status', 'completed')->get();
        foreach ($completedApps as $application) {
            Interview::create([
                'adoption_application_id' => $application->id,
                'scheduled_at' => fake()->dateTimeBetween('-1 month', '-2 weeks'),
                'location' => fake()->randomElement($locations),
                'notes' => 'Excellent interview. Applicant is a perfect match for this pet.',
                'completed_at' => fake()->dateTimeBetween('-10 days', '-5 days'),
            ]);
        }

        $someRejectedApps = AdoptionApplication::where('status', 'rejected')
            ->whereHas('statusHistory', function ($q) {
                $q->where('to_status', 'interview_scheduled');
            })
            ->take(2)
            ->get();

        foreach ($someRejectedApps as $application) {
            Interview::create([
                'adoption_application_id' => $application->id,
                'scheduled_at' => fake()->dateTimeBetween('-3 weeks', '-1 week'),
                'location' => fake()->randomElement($locations),
                'notes' => fake()->randomElement([
                    'Applicant did not show sufficient knowledge of pet care requirements.',
                    'Living situation not suitable for this type of pet.',
                    'Applicant was not responsive to follow-up questions.',
                ]),
                'completed_at' => fake()->dateTimeBetween('-5 days', '-1 day'),
            ]);
        }
    }
}
