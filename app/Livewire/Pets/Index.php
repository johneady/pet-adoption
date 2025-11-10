<?php

namespace App\Livewire\Pets;

use App\Models\Breed;
use App\Models\Pet;
use App\Models\Species;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $speciesId = null;

    public ?int $breedId = null;

    public ?string $gender = null;

    public ?string $size = null;

    public ?int $minAge = null;

    public ?int $maxAge = null;

    public function mount(): void
    {
        //
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSpeciesId(): void
    {
        $this->breedId = null;
        $this->resetPage();
    }

    public function updatedBreedId(): void
    {
        $this->resetPage();
    }

    public function updatedGender(): void
    {
        $this->resetPage();
    }

    public function updatedSize(): void
    {
        $this->resetPage();
    }

    public function updatedMinAge(): void
    {
        $this->resetPage();
    }

    public function updatedMaxAge(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'speciesId', 'breedId', 'gender', 'size', 'minAge', 'maxAge']);
        $this->resetPage();
    }

    public function getSpeciesProperty(): Collection
    {
        return Species::query()
            ->orderBy('name')
            ->get();
    }

    public function getBreedsProperty(): Collection
    {
        if (! $this->speciesId) {
            return collect();
        }

        return Breed::query()
            ->where('species_id', $this->speciesId)
            ->orderBy('name')
            ->get();
    }

    public function getPetsProperty(): mixed
    {
        return Pet::query()
            ->with(['species', 'breed', 'photos'])
            ->where('status', 'available')
            ->when($this->search, function (Builder $query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->speciesId, function (Builder $query) {
                $query->where('species_id', $this->speciesId);
            })
            ->when($this->breedId, function (Builder $query) {
                $query->where('breed_id', $this->breedId);
            })
            ->when($this->gender, function (Builder $query) {
                $query->where('gender', $this->gender);
            })
            ->when($this->size, function (Builder $query) {
                $query->where('size', $this->size);
            })
            ->when($this->minAge !== null, function (Builder $query) {
                $query->where('age', '>=', $this->minAge);
            })
            ->when($this->maxAge !== null, function (Builder $query) {
                $query->where('age', '<=', $this->maxAge);
            })
            ->latest('created_at')
            ->paginate(12);
    }

    public function render(): mixed
    {
        return view('livewire.pets.index', [
            'pets' => $this->pets,
            'species' => $this->species,
            'breeds' => $this->breeds,
        ]);
    }
}
