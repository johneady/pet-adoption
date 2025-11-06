<?php

declare(strict_types=1);

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Filament\Resources\AdoptionApplications\Pages\ListAdoptionApplications;
use App\Filament\Resources\AdoptionApplications\Pages\ViewApplicationHistory;
use App\Models\AdoptionApplication;
use App\Models\ApplicationStatusHistory;
use App\Models\Pet;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('history action exists on adoption applications table', function () {
    $application = AdoptionApplication::factory()
        ->for(User::factory())
        ->for(Pet::factory())
        ->create();

    Livewire::test(ListAdoptionApplications::class)
        ->assertActionExists(TestAction::make('history')->table($application));
});

test('history action is visible for all applications', function () {
    $application = AdoptionApplication::factory()
        ->for(User::factory())
        ->for(Pet::factory())
        ->create();

    Livewire::test(ListAdoptionApplications::class)
        ->assertActionVisible(TestAction::make('history')->table($application));
});

test('application has statusHistory relationship', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create();

    $application = AdoptionApplication::factory()
        ->for($user)
        ->for(Pet::factory())
        ->create(['status' => 'approved']);

    ApplicationStatusHistory::factory()->create([
        'adoption_application_id' => $application->id,
        'from_status' => null,
        'to_status' => 'pending',
        'notes' => 'Application submitted',
        'changed_by' => $user->id,
    ]);

    ApplicationStatusHistory::factory()->create([
        'adoption_application_id' => $application->id,
        'from_status' => 'pending',
        'to_status' => 'under_review',
        'notes' => 'Application is being reviewed',
        'changed_by' => $admin->id,
    ]);

    ApplicationStatusHistory::factory()->create([
        'adoption_application_id' => $application->id,
        'from_status' => 'under_review',
        'to_status' => 'approved',
        'notes' => 'Application approved for adoption',
        'changed_by' => $admin->id,
    ]);

    $history = $application->statusHistory()->with('changedBy')->orderBy('created_at', 'asc')->get();

    expect($history)->toHaveCount(3)
        ->and($history->first()->to_status)->toBe('pending')
        ->and($history->first()->notes)->toBe('Application submitted')
        ->and($history->first()->changedBy->id)->toBe($user->id)
        ->and($history->last()->to_status)->toBe('approved')
        ->and($history->last()->notes)->toBe('Application approved for adoption')
        ->and($history->last()->changedBy->id)->toBe($admin->id);
});

test('status history can track who made each change', function () {
    $user = User::factory()->create(['name' => 'John Applicant']);
    $admin = User::factory()->create(['name' => 'Admin Smith']);

    $application = AdoptionApplication::factory()
        ->for($user)
        ->for(Pet::factory())
        ->create();

    ApplicationStatusHistory::factory()->create([
        'adoption_application_id' => $application->id,
        'from_status' => null,
        'to_status' => 'pending',
        'notes' => 'Application submitted',
        'changed_by' => $user->id,
    ]);

    ApplicationStatusHistory::factory()->create([
        'adoption_application_id' => $application->id,
        'from_status' => 'pending',
        'to_status' => 'under_review',
        'notes' => 'Application is being reviewed',
        'changed_by' => $admin->id,
    ]);

    $history = $application->statusHistory()->with('changedBy')->get();

    expect($history->first()->changedBy->name)->toBe('John Applicant')
        ->and($history->last()->changedBy->name)->toBe('Admin Smith');
});

test('history page displays status history correctly', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['name' => 'Admin User']);

    $application = AdoptionApplication::factory()
        ->for($user)
        ->for(Pet::factory())
        ->create();

    ApplicationStatusHistory::factory()->create([
        'adoption_application_id' => $application->id,
        'from_status' => null,
        'to_status' => 'pending',
        'notes' => 'Application submitted',
        'changed_by' => $user->id,
    ]);

    ApplicationStatusHistory::factory()->create([
        'adoption_application_id' => $application->id,
        'from_status' => 'pending',
        'to_status' => 'approved',
        'notes' => 'Approved by admin',
        'changed_by' => $admin->id,
    ]);

    Livewire::test(ViewApplicationHistory::class, ['record' => $application->id])
        ->assertOk()
        ->assertSee('Status History for Application #'.$application->id)
        ->assertSee('Pending')
        ->assertSee('Approved')
        ->assertSee('Application submitted')
        ->assertSee('Approved by admin')
        ->assertSee('Changed by')
        ->assertSee($admin->name);
});

test('history page shows empty state when no history', function () {
    $application = AdoptionApplication::factory()
        ->for(User::factory())
        ->for(Pet::factory())
        ->create();

    Livewire::test(ViewApplicationHistory::class, ['record' => $application->id])
        ->assertOk()
        ->assertSee('No status history available');
});

test('history action navigates to correct URL', function () {
    $application = AdoptionApplication::factory()
        ->for(User::factory())
        ->for(Pet::factory())
        ->create();

    $expectedUrl = AdoptionApplicationResource::getUrl('history', ['record' => $application]);

    expect($expectedUrl)->toContain('/adoption-applications/'.$application->id.'/history');
});
