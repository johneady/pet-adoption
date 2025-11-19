<?php

declare(strict_types=1);

use App\Enums\QuestionType;
use App\Livewire\Applications\Create;
use App\Models\AdoptionApplication;
use App\Models\ApplicationAnswer;
use App\Models\FormQuestion;
use App\Models\Pet;
use App\Models\Species;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

test('guests are redirected to login page', function () {
    $pet = Pet::factory()->create(['status' => 'available']);

    $this->get(route('applications.create', ['petId' => $pet->id]))->assertRedirect('/login');
});

test('authenticated users can visit application form', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $pet = Pet::factory()->create(['status' => 'available']);

    // Create at least one form question
    FormQuestion::factory()
        ->adoption()
        ->active()
        ->create([
            'type' => QuestionType::String,
            'label' => 'Test Question',
            'is_required' => false,
        ]);

    actingAs($user)
        ->get(route('applications.create', ['petId' => $pet->id]))
        ->assertSuccessful()
        ->assertSee('Adoption Application');
});

test('can submit application with valid data', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    // Create form questions
    $livingSituationQuestion = FormQuestion::factory()
        ->adoption()
        ->active()
        ->required()
        ->create([
            'type' => QuestionType::Dropdown,
            'label' => 'What is your living situation?',
            'options' => ['House with yard', 'Apartment', 'Condo'],
            'sort_order' => 1,
        ]);

    $reasonQuestion = FormQuestion::factory()
        ->adoption()
        ->active()
        ->required()
        ->create([
            'type' => QuestionType::Textarea,
            'label' => 'Why do you want to adopt this pet?',
            'sort_order' => 2,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set("answers.{$livingSituationQuestion->id}", 'House with yard')
        ->set("answers.{$reasonQuestion->id}", 'Looking for a companion for our family')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    assertDatabaseHas(AdoptionApplication::class, [
        'user_id' => $user->id,
        'pet_id' => $pet->id,
        'status' => 'submitted',
    ]);

    // Verify answers were created
    $application = AdoptionApplication::where('user_id', $user->id)->first();
    expect(ApplicationAnswer::where('answerable_id', $application->id)->count())->toBe(2);
});

test('pet_id is required', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $pet = Pet::factory()->create(['status' => 'available']);

    // Create a form question
    FormQuestion::factory()
        ->adoption()
        ->active()
        ->optional()
        ->create([
            'type' => QuestionType::String,
            'label' => 'Test Question',
            'sort_order' => 1,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('pet_id', null)
        ->call('submit')
        ->assertHasErrors(['pet_id' => 'required']);
});

test('required question must be answered', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id, 'status' => 'available']);

    $requiredQuestion = FormQuestion::factory()
        ->adoption()
        ->active()
        ->required()
        ->create([
            'type' => QuestionType::Textarea,
            'label' => 'Why do you want to adopt?',
            'sort_order' => 1,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set("answers.{$requiredQuestion->id}", '')
        ->call('submit')
        ->assertHasErrors(["answers.{$requiredQuestion->id}" => 'required']);
});

test('pet_id must exist in database', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $pet = Pet::factory()->create(['status' => 'available']);

    // Create a form question
    FormQuestion::factory()
        ->adoption()
        ->active()
        ->optional()
        ->create([
            'type' => QuestionType::String,
            'label' => 'Test Question',
            'sort_order' => 1,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set('pet_id', 999999)
        ->call('submit')
        ->assertHasErrors(['pet_id' => 'exists']);
});

test('string field cannot exceed 255 characters', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id, 'status' => 'available']);

    $stringQuestion = FormQuestion::factory()
        ->adoption()
        ->active()
        ->required()
        ->create([
            'type' => QuestionType::String,
            'label' => 'Short answer question',
            'sort_order' => 1,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set("answers.{$stringQuestion->id}", str_repeat('a', 256))
        ->call('submit')
        ->assertHasErrors(["answers.{$stringQuestion->id}" => 'max']);
});

test('textarea field cannot exceed 2000 characters', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create(['species_id' => $species->id, 'status' => 'available']);

    $textareaQuestion = FormQuestion::factory()
        ->adoption()
        ->active()
        ->required()
        ->create([
            'type' => QuestionType::Textarea,
            'label' => 'Long answer question',
            'sort_order' => 1,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set("answers.{$textareaQuestion->id}", str_repeat('a', 2001))
        ->call('submit')
        ->assertHasErrors(["answers.{$textareaQuestion->id}" => 'max']);
});

test('optional fields can be empty', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    // Create required and optional questions
    $requiredQuestion = FormQuestion::factory()
        ->adoption()
        ->active()
        ->required()
        ->create([
            'type' => QuestionType::Textarea,
            'label' => 'Required question',
            'sort_order' => 1,
        ]);

    FormQuestion::factory()
        ->adoption()
        ->active()
        ->optional()
        ->create([
            'type' => QuestionType::String,
            'label' => 'Optional question',
            'sort_order' => 2,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set("answers.{$requiredQuestion->id}", 'Required answer')
        ->call('submit')
        ->assertHasNoErrors();

    assertDatabaseHas(AdoptionApplication::class, [
        'user_id' => $user->id,
        'pet_id' => $pet->id,
    ]);
});

test('throws 404 when pet does not exist', function () {
    $user = User::factory()->withCompleteProfile()->create();

    actingAs($user)
        ->get(route('applications.create', ['petId' => 999999]))
        ->assertNotFound();
});

test('pet is automatically loaded from route parameter', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
        'name' => 'Test Pet',
    ]);

    FormQuestion::factory()
        ->adoption()
        ->active()
        ->optional()
        ->create([
            'type' => QuestionType::String,
            'label' => 'Test Question',
            'sort_order' => 1,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->assertSet('pet_id', $pet->id)
        ->assertSet('selectedPet.name', 'Test Pet')
        ->assertSee('Test Pet');
});

test('success message is shown after submission', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    $question = FormQuestion::factory()
        ->adoption()
        ->active()
        ->required()
        ->create([
            'type' => QuestionType::Textarea,
            'label' => 'Why adopt?',
            'sort_order' => 1,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set("answers.{$question->id}", 'Looking for a companion')
        ->call('submit')
        ->assertSessionHas('message', 'Your adoption application has been submitted successfully!');
});

test('prefilled pet is shown as protected and not editable', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create(['name' => 'Dog']);
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
        'name' => 'Buddy',
    ]);

    FormQuestion::factory()
        ->adoption()
        ->active()
        ->optional()
        ->create([
            'type' => QuestionType::String,
            'label' => 'Test Question',
            'sort_order' => 1,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->assertSet('pet_id', $pet->id)
        ->assertSet('selectedPet.name', 'Buddy')
        ->assertSee('Applying to Adopt')
        ->assertSee('Buddy')
        ->assertSee('This application is for Buddy')
        ->assertDontSee('Select a pet');
});

test('pet status is updated to pending when application is submitted', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    $question = FormQuestion::factory()
        ->adoption()
        ->active()
        ->required()
        ->create([
            'type' => QuestionType::Textarea,
            'label' => 'Why adopt?',
            'sort_order' => 1,
        ]);

    actingAs($user);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set("answers.{$question->id}", 'Looking for a companion')
        ->call('submit')
        ->assertHasNoErrors();

    assertDatabaseHas(Pet::class, [
        'id' => $pet->id,
        'status' => 'pending',
    ]);
});

test('submission fails when pet is no longer available', function () {
    $user = User::factory()->withCompleteProfile()->create();
    $species = Species::factory()->create();
    $pet = Pet::factory()->create([
        'species_id' => $species->id,
        'status' => 'available',
    ]);

    $question = FormQuestion::factory()
        ->adoption()
        ->active()
        ->required()
        ->create([
            'type' => QuestionType::Textarea,
            'label' => 'Why adopt?',
            'sort_order' => 1,
        ]);

    actingAs($user);

    // Simulate another user adopting the pet while this form is open
    $pet->update(['status' => 'pending']);

    Livewire::test(Create::class, ['petId' => $pet->id])
        ->set("answers.{$question->id}", 'Looking for a companion')
        ->call('submit')
        ->assertHasErrors(['pet_id' => 'This pet is no longer available for adoption.']);

    // Verify no application was created
    expect(AdoptionApplication::where('pet_id', $pet->id)->count())->toBe(0);
});
