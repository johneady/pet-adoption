<?php

use App\Enums\FormType;
use App\Enums\QuestionType;
use App\Filament\Resources\FormQuestions\Pages\CreateFormQuestion;
use App\Filament\Resources\FormQuestions\Pages\EditFormQuestion;
use App\Filament\Resources\FormQuestions\Pages\ListFormQuestions;
use App\Livewire\Applications\Create as CreateApplication;
use App\Models\AdoptionApplication;
use App\Models\ApplicationAnswer;
use App\Models\FormQuestion;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('FormQuestion Model', function () {
    it('can create a form question using factory', function () {
        $question = FormQuestion::factory()->create();

        expect($question)
            ->toBeInstanceOf(FormQuestion::class)
            ->id->toBeInt()
            ->label->toBeString()
            ->is_active->toBeBool();
    });

    it('can create an adoption question', function () {
        $question = FormQuestion::factory()->adoption()->create();

        expect($question->form_type)->toBe(FormType::Adoption);
    });

    it('can create a dropdown question with options', function () {
        $options = ['Option A', 'Option B', 'Option C'];
        $question = FormQuestion::factory()->dropdown($options)->create();

        expect($question->type)->toBe(QuestionType::Dropdown)
            ->and($question->options)->toBe($options);
    });

    it('can scope by form type', function () {
        FormQuestion::factory()->adoption()->count(3)->create();
        FormQuestion::factory()->fostering()->count(2)->create();

        $adoptionQuestions = FormQuestion::forFormType(FormType::Adoption)->get();
        $fosteringQuestions = FormQuestion::forFormType(FormType::Fostering)->get();

        expect($adoptionQuestions)->toHaveCount(3)
            ->and($fosteringQuestions)->toHaveCount(2);
    });

    it('can scope by active status', function () {
        FormQuestion::factory()->active()->count(3)->create();
        FormQuestion::factory()->inactive()->count(2)->create();

        $activeQuestions = FormQuestion::active()->get();

        expect($activeQuestions)->toHaveCount(3);
    });

    it('can generate a snapshot', function () {
        $question = FormQuestion::factory()->create([
            'label' => 'Test Question',
            'type' => QuestionType::String,
            'is_required' => true,
        ]);

        $snapshot = $question->toSnapshot();

        expect($snapshot)
            ->toBeArray()
            ->toHaveKeys(['id', 'label', 'type', 'options', 'is_required'])
            ->and($snapshot['label'])->toBe('Test Question')
            ->and($snapshot['type'])->toBe('string')
            ->and($snapshot['is_required'])->toBeTrue();
    });
});

describe('ApplicationAnswer Model', function () {
    it('can create an application answer', function () {
        $question = FormQuestion::factory()->adoption()->create();
        $application = AdoptionApplication::factory()->create();

        $answer = ApplicationAnswer::create([
            'answerable_type' => AdoptionApplication::class,
            'answerable_id' => $application->id,
            'question_snapshot' => $question->toSnapshot(),
            'answer' => 'Test answer',
            'sort_order' => 1,
        ]);

        expect($answer)
            ->toBeInstanceOf(ApplicationAnswer::class)
            ->answer->toBe('Test answer');
    });

    it('can retrieve question label from snapshot', function () {
        $answer = ApplicationAnswer::factory()->create([
            'question_snapshot' => [
                'id' => 1,
                'label' => 'What is your name?',
                'type' => 'string',
                'options' => null,
                'is_required' => true,
            ],
        ]);

        expect($answer->question_label)->toBe('What is your name?');
    });

    it('formats switch answers correctly', function () {
        $answer = ApplicationAnswer::factory()->create([
            'question_snapshot' => ['type' => 'switch'],
            'answer' => '1',
        ]);

        expect($answer->formatted_answer)->toBe('Yes');

        $answer->answer = '0';
        expect($answer->formatted_answer)->toBe('No');
    });
});

describe('FormQuestion Filament Resource', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    });

    it('can render list page', function () {
        Livewire::test(ListFormQuestions::class)
            ->assertSuccessful();
    });

    it('can render create page', function () {
        Livewire::test(CreateFormQuestion::class)
            ->assertSuccessful();
    });

    it('can create a form question', function () {
        Livewire::test(CreateFormQuestion::class)
            ->fillForm([
                'form_type' => 'adoption',
                'label' => 'Test Question',
                'type' => 'string',
                'is_required' => true,
                'sort_order' => 1,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(FormQuestion::where('label', 'Test Question')->exists())->toBeTrue();
    });

    it('can edit a form question', function () {
        $question = FormQuestion::factory()->create([
            'label' => 'Original Label',
        ]);

        Livewire::test(EditFormQuestion::class, ['record' => $question->id])
            ->assertFormSet([
                'label' => 'Original Label',
            ])
            ->fillForm([
                'label' => 'Updated Label',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($question->refresh()->label)->toBe('Updated Label');
    });

    it('can display questions in table', function () {
        $questions = FormQuestion::factory()->count(3)->create();

        Livewire::test(ListFormQuestions::class)
            ->assertCanSeeTableRecords($questions);
    });
});

describe('Adoption Application with Dynamic Questions', function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'phone' => '555-1234',
            'address' => '123 Main St',
        ]);

        $this->pet = Pet::factory()->create([
            'status' => 'available',
        ]);

        // Create some adoption questions
        $this->questions = collect([
            FormQuestion::factory()->adoption()->active()->create([
                'label' => 'What is your living situation?',
                'type' => QuestionType::Dropdown,
                'options' => ['House', 'Apartment', 'Condo'],
                'is_required' => true,
                'sort_order' => 1,
            ]),
            FormQuestion::factory()->adoption()->active()->create([
                'label' => 'Do you have experience with pets?',
                'type' => QuestionType::Textarea,
                'is_required' => false,
                'sort_order' => 2,
            ]),
            FormQuestion::factory()->adoption()->active()->create([
                'label' => 'Do you have a fenced yard?',
                'type' => QuestionType::Switch,
                'is_required' => false,
                'sort_order' => 3,
            ]),
        ]);
    });

    it('loads questions in the livewire component', function () {
        Livewire::actingAs($this->user)
            ->test(CreateApplication::class, ['petId' => $this->pet->id])
            ->assertSee('What is your living situation?')
            ->assertSee('Do you have experience with pets?')
            ->assertSee('Do you have a fenced yard?');
    });

    it('can submit an application with dynamic answers', function () {
        $answers = [];
        foreach ($this->questions as $question) {
            if ($question->type === QuestionType::Switch) {
                $answers[$question->id] = true;
            } elseif ($question->type === QuestionType::Dropdown) {
                $answers[$question->id] = 'House';
            } else {
                $answers[$question->id] = 'Test answer for '.$question->label;
            }
        }

        Livewire::actingAs($this->user)
            ->test(CreateApplication::class, ['petId' => $this->pet->id])
            ->set('answers', $answers)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        // Check application was created
        $application = AdoptionApplication::where('user_id', $this->user->id)
            ->where('pet_id', $this->pet->id)
            ->first();

        expect($application)->not->toBeNull();

        // Check answers were stored
        expect($application->answers)->toHaveCount(3);

        // Check snapshot was preserved
        $firstAnswer = $application->answers->first();
        expect($firstAnswer->question_snapshot)
            ->toBeArray()
            ->toHaveKey('label');
    });

    it('validates required questions', function () {
        $answers = [];
        foreach ($this->questions as $question) {
            $answers[$question->id] = ''; // Empty all answers
        }

        Livewire::actingAs($this->user)
            ->test(CreateApplication::class, ['petId' => $this->pet->id])
            ->set('answers', $answers)
            ->call('submit')
            ->assertHasErrors(['answers.'.$this->questions[0]->id]); // First question is required
    });

    it('preserves question snapshot when question is updated', function () {
        // Submit an application
        $answers = [];
        foreach ($this->questions as $question) {
            if ($question->type === QuestionType::Switch) {
                $answers[$question->id] = true;
            } elseif ($question->type === QuestionType::Dropdown) {
                $answers[$question->id] = 'House';
            } else {
                $answers[$question->id] = 'Test answer';
            }
        }

        Livewire::actingAs($this->user)
            ->test(CreateApplication::class, ['petId' => $this->pet->id])
            ->set('answers', $answers)
            ->call('submit');

        $application = AdoptionApplication::where('user_id', $this->user->id)->first();
        $originalLabel = $application->answers->first()->question_snapshot['label'];

        // Update the question label
        $this->questions[0]->update(['label' => 'Updated Question Label']);

        // Verify the snapshot still has the original label
        $application->refresh();
        expect($application->answers->first()->question_snapshot['label'])
            ->toBe($originalLabel)
            ->not->toBe('Updated Question Label');
    });
});
