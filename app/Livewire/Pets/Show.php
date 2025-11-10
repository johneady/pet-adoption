<?php

namespace App\Livewire\Pets;

use App\Models\Pet;
use Livewire\Component;

class Show extends Component
{
    public Pet $pet;

    public ?int $selectedPhotoIndex = 0;

    public function mount(string $slug): void
    {
        $this->pet = Pet::query()
            ->with(['species', 'breed', 'photos'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->selectedPhotoIndex = 0;
    }

    public function selectPhoto(int $index): void
    {
        $this->selectedPhotoIndex = $index;
    }

    public function nextPhoto(): void
    {
        $photoCount = $this->pet->photos->count();

        if ($photoCount > 0) {
            $this->selectedPhotoIndex = ($this->selectedPhotoIndex + 1) % $photoCount;
        }
    }

    public function previousPhoto(): void
    {
        $photoCount = $this->pet->photos->count();

        if ($photoCount > 0) {
            $this->selectedPhotoIndex = ($this->selectedPhotoIndex - 1 + $photoCount) % $photoCount;
        }
    }

    public function render(): mixed
    {
        return view('livewire.pets.show');
    }
}
