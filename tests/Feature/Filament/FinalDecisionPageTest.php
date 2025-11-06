<?php

declare(strict_types=1);

use App\Filament\Pages\FinalDecision;
use App\Models\AdoptionApplication;
use App\Models\ApplicationStatusHistory;
use App\Models\Pet;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
    $this->pet = Pet::factory()->create();
});

test('final decision page only shows under_review applications', function () {
    $submitted = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'submitted']);
    $underReview = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'under_review']);
    $approved = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'approved']);
    $rejected = AdoptionApplication::factory()->for($this->admin, 'user')->for($this->pet)->create(['status' => 'rejected']);

    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->assertCanSeeTableRecords([$underReview])
        ->assertCanNotSeeTableRecords([$submitted, $approved, $rejected])
        ->assertCountTableRecords(1);
});

test('can approve application from final decision page', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->callAction(TestAction::make('approve')->table($application))
        ->assertNotified();

    expect($application->refresh()->status)->toBe('approved');
});

test('can reject application from final decision page', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->callAction(TestAction::make('reject')->table($application))
        ->assertNotified();

    expect($application->refresh()->status)->toBe('rejected');
});

test('approving application creates status history', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->callAction(TestAction::make('approve')->table($application));

    assertDatabaseHas(ApplicationStatusHistory::class, [
        'adoption_application_id' => $application->id,
        'from_status' => 'under_review',
        'to_status' => 'approved',
        'changed_by' => $this->admin->id,
    ]);
});

test('rejecting application creates status history', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->callAction(TestAction::make('reject')->table($application));

    assertDatabaseHas(ApplicationStatusHistory::class, [
        'adoption_application_id' => $application->id,
        'from_status' => 'under_review',
        'to_status' => 'rejected',
        'changed_by' => $this->admin->id,
    ]);
});

test('navigation badge shows correct count of under_review applications', function () {
    AdoptionApplication::factory()->count(3)->for($this->pet)->create(['status' => 'under_review']);
    AdoptionApplication::factory()->count(2)->for($this->pet)->create(['status' => 'submitted']);
    AdoptionApplication::factory()->count(1)->for($this->pet)->create(['status' => 'approved']);

    expect(FinalDecision::getNavigationBadge())->toBe('3');
});

test('navigation badge is null when no under_review applications', function () {
    AdoptionApplication::factory()->count(2)->for($this->pet)->create(['status' => 'submitted']);

    expect(FinalDecision::getNavigationBadge())->toBeNull();
});

test('final decision page shows empty state when no under_review applications', function () {
    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->assertCountTableRecords(0);
});

test('approved application is removed from final decision list', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    $component = Livewire::test(FinalDecision::class)
        ->assertCanSeeTableRecords([$application])
        ->callAction(TestAction::make('approve')->table($application));

    $component->assertCanNotSeeTableRecords([$application->refresh()]);
});

test('rejected application is removed from final decision list', function () {
    $application = AdoptionApplication::factory()
        ->for($this->admin, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    $component = Livewire::test(FinalDecision::class)
        ->assertCanSeeTableRecords([$application])
        ->callAction(TestAction::make('reject')->table($application));

    $component->assertCanNotSeeTableRecords([$application->refresh()]);
});
