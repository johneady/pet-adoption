<?php

namespace App\Filament\Resources\AdoptionApplications\Widgets;

use App\Models\AdoptionApplication;
use Filament\Widgets\Widget;

class InterviewDetailsWidget extends Widget
{
    protected string $view = 'filament.resources.adoption-applications.widgets.interview-details-widget';

    protected int|string|array $columnSpan = 'full';

    public ?AdoptionApplication $record = null;

    public function getInterview()
    {
        return $this->record?->interview;
    }

    public function hasInterview(): bool
    {
        return $this->getInterview() !== null;
    }
}
