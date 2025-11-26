<?php

declare(strict_types=1);

use App\Filament\Resources\Interviews\Pages\ListInterviews;
use App\Mail\InterviewRescheduled;
use App\Mail\InterviewRescheduledAdmin;
use App\Mail\InterviewScheduled;
use App\Mail\InterviewScheduledAdmin;
use App\Models\AdoptionApplication;
use App\Models\Interview;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

test('creating an interview updates pet status to pending', function () {
    $application = AdoptionApplication::factory()->create();
    $pet = $application->pet;

    // Ensure pet starts with available status
    $pet->update(['status' => 'available']);
    expect($pet->fresh()->status)->toBe('available');

    actingAs($this->admin);

    Livewire::test(\App\Filament\Resources\Interviews\Pages\CreateInterview::class)
        ->set('data.adoption_application_id', $application->id)
        ->set('data.scheduled_at', now()->addDays(3))
        ->set('data.location', 'Main Office')
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    // Verify interview was created
    expect(Interview::where('adoption_application_id', $application->id)->exists())->toBeTrue();

    // Verify pet status is now pending
    expect($pet->fresh()->status)->toBe('pending');
});

test('creating an interview updates application status to interview_scheduled', function () {
    $application = AdoptionApplication::factory()->create(['status' => 'submitted']);

    expect($application->fresh()->status)->toBe('submitted');

    actingAs($this->admin);

    Livewire::test(\App\Filament\Resources\Interviews\Pages\CreateInterview::class)
        ->set('data.adoption_application_id', $application->id)
        ->set('data.scheduled_at', now()->addDays(3))
        ->set('data.location', 'Main Office')
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    // Verify interview was created
    expect(Interview::where('adoption_application_id', $application->id)->exists())->toBeTrue();

    // Verify application status is now interview_scheduled
    expect($application->fresh()->status)->toBe('interview_scheduled');
});

test('creating an interview records application status history', function () {
    $application = AdoptionApplication::factory()->create(['status' => 'submitted']);

    actingAs($this->admin);

    Livewire::test(\App\Filament\Resources\Interviews\Pages\CreateInterview::class)
        ->set('data.adoption_application_id', $application->id)
        ->set('data.scheduled_at', now()->addDays(3))
        ->set('data.location', 'Main Office')
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    // Verify status history was created
    $history = \App\Models\ApplicationStatusHistory::where('adoption_application_id', $application->id)
        ->where('from_status', 'submitted')
        ->where('to_status', 'interview_scheduled')
        ->first();

    expect($history)->not->toBeNull()
        ->and($history->changed_by)->toBe($this->admin->id)
        ->and($history->notes)->toBe('Interview scheduled');
});

test('creating interview notes creates status history entry', function () {
    $application = AdoptionApplication::factory()->create(['status' => 'interview_scheduled']);
    $interview = Interview::create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Main Office',
        'notes' => null,
    ]);

    actingAs($this->admin);

    Livewire::test(\App\Filament\Resources\Interviews\Pages\EditInterview::class, ['record' => $interview->id])
        ->set('data.notes', 'These are the initial interview notes.')
        ->call('save')
        ->assertHasNoFormErrors();

    // Verify interview notes were updated
    expect($interview->fresh()->notes)->toBe('These are the initial interview notes.');

    // Verify status history was created with notes created message
    $history = \App\Models\ApplicationStatusHistory::where('adoption_application_id', $application->id)
        ->where('notes', 'Interview notes created')
        ->first();

    expect($history)->not->toBeNull()
        ->and($history->from_status)->toBe('interview_scheduled')
        ->and($history->to_status)->toBe('interview_scheduled')
        ->and($history->changed_by)->toBe($this->admin->id);
});

test('updating interview notes creates status history entry', function () {
    $application = AdoptionApplication::factory()->create(['status' => 'interview_scheduled']);
    $interview = Interview::create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Main Office',
        'notes' => 'Initial notes',
    ]);

    actingAs($this->admin);

    Livewire::test(\App\Filament\Resources\Interviews\Pages\EditInterview::class, ['record' => $interview->id])
        ->set('data.notes', 'Updated interview notes with more details.')
        ->call('save')
        ->assertHasNoFormErrors();

    // Verify interview notes were updated
    expect($interview->fresh()->notes)->toBe('Updated interview notes with more details.');

    // Verify status history was created with notes updated message
    $history = \App\Models\ApplicationStatusHistory::where('adoption_application_id', $application->id)
        ->where('notes', 'Interview notes updated')
        ->first();

    expect($history)->not->toBeNull()
        ->and($history->from_status)->toBe('interview_scheduled')
        ->and($history->to_status)->toBe('interview_scheduled')
        ->and($history->changed_by)->toBe($this->admin->id);
});

test('editing interview without changing notes does not create status history entry', function () {
    $application = AdoptionApplication::factory()->create(['status' => 'interview_scheduled']);
    $interview = Interview::create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Main Office',
        'notes' => 'Some notes',
    ]);

    actingAs($this->admin);

    $historyCountBefore = \App\Models\ApplicationStatusHistory::where('adoption_application_id', $application->id)->count();

    Livewire::test(\App\Filament\Resources\Interviews\Pages\EditInterview::class, ['record' => $interview->id])
        ->set('data.location', 'Updated Location')
        ->call('save')
        ->assertHasNoFormErrors();

    $historyCountAfter = \App\Models\ApplicationStatusHistory::where('adoption_application_id', $application->id)->count();

    // Verify no new status history was created
    expect($historyCountAfter)->toBe($historyCountBefore);
});

test('creating an interview sends email to applicant and admin', function () {
    Mail::fake();

    $application = AdoptionApplication::factory()->create(['status' => 'submitted']);
    $applicant = $application->user;

    actingAs($this->admin);

    Livewire::test(\App\Filament\Resources\Interviews\Pages\CreateInterview::class)
        ->set('data.adoption_application_id', $application->id)
        ->set('data.scheduled_at', now()->addDays(3))
        ->set('data.location', 'Main Office')
        ->call('create')
        ->assertHasNoFormErrors();

    // Verify emails were queued for both applicant and admin
    Mail::assertQueued(InterviewScheduled::class, function ($mail) use ($applicant) {
        return $mail->hasTo($applicant->email);
    });

    Mail::assertQueued(InterviewScheduledAdmin::class, function ($mail) {
        return $mail->hasTo($this->admin->email);
    });

    // Verify exactly 1 InterviewScheduled email was queued (to applicant)
    Mail::assertQueued(InterviewScheduled::class, 1);

    // Verify exactly 1 InterviewScheduledAdmin email was queued (to admin)
    Mail::assertQueued(InterviewScheduledAdmin::class, 1);
});

test('updating interview scheduled_at sends reschedule email to applicant and admin', function () {
    Mail::fake();

    $application = AdoptionApplication::factory()->create(['status' => 'interview_scheduled']);
    $applicant = $application->user;
    $originalScheduledAt = now()->addDays(3);
    $newScheduledAt = now()->addDays(5);

    $interview = Interview::create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => $originalScheduledAt,
        'location' => 'Main Office',
    ]);

    actingAs($this->admin);

    Livewire::test(\App\Filament\Resources\Interviews\Pages\EditInterview::class, ['record' => $interview->id])
        ->set('data.scheduled_at', $newScheduledAt)
        ->call('save')
        ->assertHasNoFormErrors();

    // Verify interview scheduled_at was updated (check that it changed from original)
    $updatedScheduledAt = $interview->fresh()->scheduled_at;
    expect($updatedScheduledAt->ne($originalScheduledAt))->toBeTrue()
        ->and($updatedScheduledAt->gt($originalScheduledAt))->toBeTrue();

    // Verify reschedule emails were queued for both applicant and admin
    Mail::assertQueued(InterviewRescheduled::class, function ($mail) use ($applicant) {
        return $mail->hasTo($applicant->email);
    });

    Mail::assertQueued(InterviewRescheduledAdmin::class, function ($mail) {
        return $mail->hasTo($this->admin->email);
    });

    // Verify exactly 1 InterviewRescheduled email was queued (to applicant)
    Mail::assertQueued(InterviewRescheduled::class, 1);

    // Verify exactly 1 InterviewRescheduledAdmin email was queued (to admin)
    Mail::assertQueued(InterviewRescheduledAdmin::class, 1);
});

test('updating interview location does not send emails', function () {
    Mail::fake();

    $application = AdoptionApplication::factory()->create(['status' => 'interview_scheduled']);
    $interview = Interview::create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Main Office',
    ]);

    actingAs($this->admin);

    Livewire::test(\App\Filament\Resources\Interviews\Pages\EditInterview::class, ['record' => $interview->id])
        ->set('data.location', 'Updated Office Location')
        ->call('save')
        ->assertHasNoFormErrors();

    // Verify location was updated
    expect($interview->fresh()->location)->toBe('Updated Office Location');

    // Verify no emails were sent
    Mail::assertNothingSent();
});

test('updating interview notes does not send emails', function () {
    Mail::fake();

    $application = AdoptionApplication::factory()->create(['status' => 'interview_scheduled']);
    $interview = Interview::create([
        'adoption_application_id' => $application->id,
        'scheduled_at' => now()->addDays(3),
        'location' => 'Main Office',
        'notes' => 'Initial notes',
    ]);

    actingAs($this->admin);

    Livewire::test(\App\Filament\Resources\Interviews\Pages\EditInterview::class, ['record' => $interview->id])
        ->set('data.notes', 'Updated notes')
        ->call('save')
        ->assertHasNoFormErrors();

    // Verify notes were updated
    expect($interview->fresh()->notes)->toBe('Updated notes');

    // Verify no emails were sent
    Mail::assertNothingSent();
});
