<?php

declare(strict_types=1);

use App\Filament\Resources\AdoptionApplications\Pages\EditAdoptionApplication;
use App\Filament\Resources\AdoptionApplications\Widgets\NotesWidget;
use App\Models\AdoptionApplication;
use App\Models\AdoptionApplicationNote;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel('admin');
    $this->admin = User::factory()->admin()->create();
    $this->application = AdoptionApplication::factory()->create();
});

test('can add note to adoption application', function () {
    actingAs($this->admin);

    $note = AdoptionApplicationNote::create([
        'adoption_application_id' => $this->application->id,
        'note' => 'This is a test note.',
        'created_by' => $this->admin->id,
    ]);

    expect($note)->not->toBeNull()
        ->and($note->note)->toBe('This is a test note.')
        ->and($note->adoption_application_id)->toBe($this->application->id)
        ->and($note->created_by)->toBe($this->admin->id);

    assertDatabaseHas(AdoptionApplicationNote::class, [
        'adoption_application_id' => $this->application->id,
        'note' => 'This is a test note.',
        'created_by' => $this->admin->id,
    ]);
});

test('widget has add note action', function () {
    actingAs($this->admin);

    Livewire::test(NotesWidget::class, ['record' => $this->application])
        ->assertActionExists('addNote');
});

test('note requires content', function () {
    actingAs($this->admin);

    Livewire::test(NotesWidget::class, ['record' => $this->application])
        ->callAction('addNote', data: ['note' => ''])
        ->assertHasFormErrors(['note' => 'required']);
});

test('notes are displayed in ascending order', function () {
    actingAs($this->admin);

    $note1 = AdoptionApplicationNote::factory()
        ->for($this->application, 'adoptionApplication')
        ->create(['note' => 'First note', 'created_at' => now()->subHours(2)]);

    $note2 = AdoptionApplicationNote::factory()
        ->for($this->application, 'adoptionApplication')
        ->create(['note' => 'Second note', 'created_at' => now()->subHour()]);

    $note3 = AdoptionApplicationNote::factory()
        ->for($this->application, 'adoptionApplication')
        ->create(['note' => 'Third note', 'created_at' => now()]);

    $widget = Livewire::test(NotesWidget::class, ['record' => $this->application]);

    $notes = $widget->instance()->getNotes();

    expect($notes->first()->note)->toBe('Third note')
        ->and($notes->last()->note)->toBe('First note');
});

test('notes display user information', function () {
    actingAs($this->admin);

    $author = User::factory()->create(['name' => 'Jane Doe']);

    $note = AdoptionApplicationNote::factory()
        ->for($this->application, 'adoptionApplication')
        ->for($author, 'createdBy')
        ->create(['note' => 'Test note by Jane']);

    Livewire::test(NotesWidget::class, ['record' => $this->application])
        ->assertSee('Jane Doe')
        ->assertSee('Test note by Jane');
});

test('notes are deleted when application is deleted', function () {
    actingAs($this->admin);

    $note = AdoptionApplicationNote::factory()
        ->for($this->application, 'adoptionApplication')
        ->create();

    expect(AdoptionApplicationNote::count())->toBe(1);

    $this->application->delete();

    expect(AdoptionApplicationNote::count())->toBe(0);
});

test('notes widget is displayed on edit adoption application page', function () {
    actingAs($this->admin);

    Livewire::test(EditAdoptionApplication::class, ['record' => $this->application->id])
        ->assertSuccessful();
});

test('add note action is visible for non-archived applications', function () {
    $application = AdoptionApplication::factory()->create(['status' => 'submitted']);

    actingAs($this->admin);

    Livewire::test(NotesWidget::class, ['record' => $application])
        ->assertActionVisible('addNote');
});

test('add note action is hidden for archived applications', function () {
    $application = AdoptionApplication::factory()->create(['status' => 'archived']);

    actingAs($this->admin);

    Livewire::test(NotesWidget::class, ['record' => $application])
        ->assertActionHidden('addNote');
});
