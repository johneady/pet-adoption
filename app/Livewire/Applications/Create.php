<?php

namespace App\Livewire\Applications;

use App\Http\Requests\StoreAdoptionApplicationRequest;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public ?int $pet_id = null;

    public ?Pet $selectedPet = null;

    public string $living_situation = '';

    public string $experience = '';

    public string $other_pets = '';

    public string $veterinary_reference = '';

    public string $household_members = '';

    public string $employment_status = '';

    public string $reason_for_adoption = '';

    public function mount(?int $petId = null): void
    {
        if ($petId) {
            $this->pet_id = $petId;
            $this->selectedPet = Pet::with(['species', 'breed'])->find($petId);
        }
    }

    public function submit(): void
    {
        $validated = $this->validate((new StoreAdoptionApplicationRequest)->rules());

        $application = AdoptionApplication::create([
            'user_id' => Auth::id(),
            'pet_id' => $validated['pet_id'],
            'living_situation' => $validated['living_situation'],
            'experience' => $validated['experience'],
            'other_pets' => $validated['other_pets'],
            'veterinary_reference' => $validated['veterinary_reference'],
            'household_members' => $validated['household_members'],
            'employment_status' => $validated['employment_status'],
            'reason_for_adoption' => $validated['reason_for_adoption'],
            'status' => 'submitted',
        ]);

        // Update the pet's status to pending
        Pet::where('id', $validated['pet_id'])->update(['status' => 'pending']);

        session()->flash('message', 'Your adoption application has been submitted successfully!');

        $this->redirect(route('dashboard'), navigate: true);
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
        ]);
    }
}
