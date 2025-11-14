<?php

namespace App\Filament\Resources\AdoptionApplications\Widgets;

use App\Models\AdoptionApplication;
use Filament\Widgets\Widget;

class PetDetailsWidget extends Widget
{
    protected string $view = 'filament.resources.adoption-applications.widgets.pet-details-widget';

    protected int|string|array $columnSpan = 'full';

    public ?AdoptionApplication $record = null;

    public function getPet()
    {
        return $this->record?->pet()->with(['species', 'breed', 'photos'])->first();
    }
}
