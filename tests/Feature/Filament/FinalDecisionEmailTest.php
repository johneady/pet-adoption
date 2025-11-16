<?php

declare(strict_types=1);

use App\Events\ApplicationDecisionMade;
use App\Filament\Pages\FinalDecision;
use App\Mail\AdoptionApplicationApproved;
use App\Mail\AdoptionApplicationRejected;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
    $this->applicant = User::factory()->create();
    $this->pet = Pet::factory()->create();
});

test('approving application dispatches ApplicationDecisionMade event', function () {
    Event::fake([ApplicationDecisionMade::class]);

    $application = AdoptionApplication::factory()
        ->for($this->applicant, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->callTableAction('approve', $application);

    Event::assertDispatched(ApplicationDecisionMade::class, function ($event) use ($application) {
        return $event->application->id === $application->id
            && $event->decision === 'approved'
            && $event->notes === 'Application approved';
    });
});

test('rejecting application dispatches ApplicationDecisionMade event', function () {
    Event::fake([ApplicationDecisionMade::class]);

    $application = AdoptionApplication::factory()
        ->for($this->applicant, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->callTableAction('reject', $application);

    Event::assertDispatched(ApplicationDecisionMade::class, function ($event) use ($application) {
        return $event->application->id === $application->id
            && $event->decision === 'rejected'
            && $event->notes === 'Application rejected';
    });
});

test('approving application sends approval email to applicant', function () {
    Mail::fake();

    $application = AdoptionApplication::factory()
        ->for($this->applicant, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->callTableAction('approve', $application);

    Mail::assertQueued(AdoptionApplicationApproved::class, function ($mail) use ($application) {
        return $mail->hasTo($application->user->email)
            && $mail->application->id === $application->id;
    });
});

test('rejecting application sends rejection email to applicant', function () {
    Mail::fake();

    $application = AdoptionApplication::factory()
        ->for($this->applicant, 'user')
        ->for($this->pet)
        ->create(['status' => 'under_review']);

    actingAs($this->admin);

    Livewire::test(FinalDecision::class)
        ->callTableAction('reject', $application);

    Mail::assertQueued(AdoptionApplicationRejected::class, function ($mail) use ($application) {
        return $mail->hasTo($application->user->email)
            && $mail->application->id === $application->id;
    });
});

test('approval email contains correct subject', function () {
    $application = AdoptionApplication::factory()
        ->for($this->applicant, 'user')
        ->for($this->pet)
        ->create(['status' => 'approved']);

    $mailable = new AdoptionApplicationApproved($application);

    $mailable->assertHasSubject('Congratulations! Your Adoption Application Has Been Approved');
});

test('rejection email contains correct subject', function () {
    $application = AdoptionApplication::factory()
        ->for($this->applicant, 'user')
        ->for($this->pet)
        ->create(['status' => 'rejected']);

    $mailable = new AdoptionApplicationRejected($application);

    $mailable->assertHasSubject('Update on Your Adoption Application');
});

test('approval email is queued', function () {
    $application = AdoptionApplication::factory()
        ->for($this->applicant, 'user')
        ->for($this->pet)
        ->create(['status' => 'approved']);

    $mailable = new AdoptionApplicationApproved($application);

    expect($mailable)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('rejection email is queued', function () {
    $application = AdoptionApplication::factory()
        ->for($this->applicant, 'user')
        ->for($this->pet)
        ->create(['status' => 'rejected']);

    $mailable = new AdoptionApplicationRejected($application);

    expect($mailable)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});
