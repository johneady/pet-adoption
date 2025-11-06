<?php

declare(strict_types=1);

use App\Filament\Resources\Interviews\Pages\ListInterviews;
use App\Models\AdoptionApplication;
use App\Models\Interview;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
});

test('interview resource table shows only non-completed interviews', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();
    $application3 = AdoptionApplication::factory()->create();

    // Create non-completed interviews
    $nonCompletedInterview1 = Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office A',
        'completed_at' => null,
    ]);

    $nonCompletedInterview2 = Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Office B',
        'completed_at' => null,
    ]);

    // Create completed interview
    Interview::create([
        'adoption_application_id' => $application3->id,
        'scheduled_at' => now()->subDays(1),
        'location' => 'Office C',
        'completed_at' => now(),
    ]);

    actingAs($this->admin);

    Livewire::test(ListInterviews::class)
        ->assertCanSeeTableRecords([$nonCompletedInterview1, $nonCompletedInterview2])
        ->assertCountTableRecords(2)
        ->assertSee('Office A')
        ->assertSee('Office B')
        ->assertDontSee('Office C');
});

test('interview resource table can search by location', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();

    $interview1 = Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Downtown Office',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Uptown Office',
        'completed_at' => null,
    ]);

    actingAs($this->admin);

    Livewire::test(ListInterviews::class)
        ->searchTable('Downtown')
        ->assertCanSeeTableRecords([$interview1])
        ->assertCountTableRecords(1);
});

test('interview resource table can be sorted by scheduled date', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();

    Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->addDays(5),
        'location' => 'Office A',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office B',
        'completed_at' => null,
    ]);

    actingAs($this->admin);

    Livewire::test(ListInterviews::class)
        ->assertCanSeeTableRecords(Interview::whereNull('completed_at')->get())
        ->sortTable('scheduled_at')
        ->assertCanSeeTableRecords(Interview::whereNull('completed_at')->get());
});

test('interview resource table is sorted by scheduled date ascending by default', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();
    $application3 = AdoptionApplication::factory()->create();

    Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->addDays(5),
        'location' => 'Office A',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office B',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application3->id,
        'scheduled_at' => now()->addDays(8),
        'location' => 'Office C',
        'completed_at' => null,
    ]);

    actingAs($this->admin);

    $interviews = Interview::whereNull('completed_at')
        ->orderBy('scheduled_at', 'asc')
        ->get();

    Livewire::test(ListInterviews::class)
        ->assertCanSeeTableRecords($interviews, inOrder: true);
});

test('interview resource table shows applicant name', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $application = AdoptionApplication::factory()->create(['user_id' => $user->id]);

    Interview::create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office A',
        'completed_at' => null,
    ]);

    actingAs($this->admin);

    Livewire::test(ListInterviews::class)
        ->assertCanSeeTableRecords(Interview::whereNull('completed_at')->get())
        ->assertSee('John Doe');
});

test('interview resource table can search by applicant name', function () {
    $user1 = User::factory()->create(['name' => 'Alice Johnson']);
    $user2 = User::factory()->create(['name' => 'Bob Smith']);

    $application1 = AdoptionApplication::factory()->create(['user_id' => $user1->id]);
    $application2 = AdoptionApplication::factory()->create(['user_id' => $user2->id]);

    $interview1 = Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office A',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Office B',
        'completed_at' => null,
    ]);

    actingAs($this->admin);

    Livewire::test(ListInterviews::class)
        ->searchTable('Alice')
        ->assertCanSeeTableRecords([$interview1])
        ->assertCountTableRecords(1);
});

test('interview resource table shows pet name', function () {
    $application = AdoptionApplication::factory()->create();

    Interview::create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office A',
        'completed_at' => null,
    ]);

    actingAs($this->admin);

    Livewire::test(ListInterviews::class)
        ->assertCanSeeTableRecords(Interview::whereNull('completed_at')->get())
        ->assertSee($application->pet->name);
});

test('interview resource table can search by pet name', function () {
    $application1 = AdoptionApplication::factory()->create();
    $application2 = AdoptionApplication::factory()->create();

    $petName1 = $application1->pet->name;

    $interview1 = Interview::create([
        'adoption_application_id' => $application1->id,
        'scheduled_at' => now()->addDays(2),
        'location' => 'Office A',
        'completed_at' => null,
    ]);

    Interview::create([
        'adoption_application_id' => $application2->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Office B',
        'completed_at' => null,
    ]);

    actingAs($this->admin);

    Livewire::test(ListInterviews::class)
        ->searchTable($petName1)
        ->assertCanSeeTableRecords([$interview1])
        ->assertCountTableRecords(1);
});
