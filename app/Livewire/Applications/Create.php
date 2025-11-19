<?php

namespace App\Livewire\Applications;

use App\Enums\FormType;
use App\Mail\AdoptionApplicationReceived;
use App\Mail\NewAdoptionApplication;
use App\Models\AdoptionApplication;
use App\Models\ApplicationAnswer;
use App\Models\FormQuestion;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Create extends Component
{
    public ?int $pet_id = null;

    public ?Pet $selectedPet = null;

    /** @var array<int, string|bool|null> */
    public array $answers = [];

    /** @var Collection<int, FormQuestion> */
    public Collection $questions;

    public function mount(int $petId): void
    {
        if (! Auth::user()->hasCompletedProfileForAdoption()) {
            session()->flash('message', 'Please complete your profile (address and phone number) before submitting an adoption application.');

            $this->redirect(route('profile.edit'), navigate: true);

            return;
        }

        $this->pet_id = $petId;
        $this->selectedPet = Pet::with(['species', 'breed', 'photos'])->findOrFail($petId);

        // Load active questions for adoption form
        $this->questions = FormQuestion::query()
            ->forFormType(FormType::Adoption)
            ->active()
            ->orderBy('sort_order')
            ->get();

        // Initialize answers array with empty values
        foreach ($this->questions as $question) {
            $this->answers[$question->id] = $question->type->value === 'switch' ? false : '';
        }
    }

    public function submit(): void
    {
        $validated = $this->validate($this->rules());

        // Verify the pet is still available
        $pet = Pet::find($validated['pet_id']);

        if (! $pet || $pet->status !== 'available') {
            $this->addError('pet_id', 'This pet is no longer available for adoption.');

            return;
        }

        // Create the application
        $application = AdoptionApplication::create([
            'user_id' => Auth::id(),
            'pet_id' => $validated['pet_id'],
            'status' => 'submitted',
        ]);

        // Store answers with question snapshots
        foreach ($this->questions as $question) {
            $answer = $validated['answers'][$question->id] ?? null;

            // Convert boolean/checkbox values to string for storage
            if ($question->type->value === 'switch') {
                $answer = $answer ? '1' : '0';
            }

            ApplicationAnswer::create([
                'answerable_type' => AdoptionApplication::class,
                'answerable_id' => $application->id,
                'question_snapshot' => $question->toSnapshot(),
                'answer' => $answer,
                'sort_order' => $question->sort_order,
            ]);
        }

        // Load relationships for email
        $application->load(['user', 'pet.species']);

        // Send confirmation email to the applicant
        Mail::to(Auth::user())->send(new AdoptionApplicationReceived($application));

        // Notify admins who have opted in to receive new adoption alerts
        $adminsToNotify = User::where('is_admin', true)
            ->where('receive_new_adoption_alerts', true)
            ->get();

        foreach ($adminsToNotify as $admin) {
            Mail::to($admin)->send(new NewAdoptionApplication($application));
        }

        // Update the pet's status to pending
        Pet::where('id', $validated['pet_id'])->update(['status' => 'pending']);

        session()->flash('message', 'Your adoption application has been submitted successfully!');

        $this->redirect(route('dashboard'), navigate: true);
    }

    /**
     * Build validation rules dynamically based on questions.
     *
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        $rules = [
            'pet_id' => ['required', 'exists:pets,id'],
        ];

        foreach ($this->questions as $question) {
            $questionRules = [];

            if ($question->is_required) {
                $questionRules[] = 'required';
            } else {
                $questionRules[] = 'nullable';
            }

            // Add type-specific validation
            switch ($question->type->value) {
                case 'string':
                    $questionRules[] = 'string';
                    $questionRules[] = 'max:255';
                    break;
                case 'textarea':
                    $questionRules[] = 'string';
                    $questionRules[] = 'max:2000';
                    break;
                case 'dropdown':
                    $questionRules[] = 'string';
                    if ($question->options) {
                        $questionRules[] = 'in:'.implode(',', $question->options);
                    }
                    break;
                case 'switch':
                    $questionRules[] = 'boolean';
                    break;
            }

            $rules["answers.{$question->id}"] = $questionRules;
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        $messages = [
            'pet_id.required' => 'Please select a pet to adopt.',
            'pet_id.exists' => 'The selected pet is no longer available.',
        ];

        foreach ($this->questions as $question) {
            $label = $question->label;
            $messages["answers.{$question->id}.required"] = "Please answer: {$label}";
            $messages["answers.{$question->id}.max"] = "{$label} is too long.";
            $messages["answers.{$question->id}.in"] = "Please select a valid option for: {$label}";
        }

        return $messages;
    }

    public function getAvailablePetsProperty(): Collection
    {
        return Pet::query()
            ->where('status', 'available')
            ->with(['species', 'breed'])
            ->orderBy('name')
            ->get();
    }

    public function render(): mixed
    {
        return view('livewire.applications.create', [
            'availablePets' => $this->availablePets,
            'selectedPet' => $this->selectedPet,
            'questions' => $this->questions,
        ]);
    }
}
