<?php

namespace App\Filament\Resources\AdoptionApplications\Widgets;

use App\Models\AdoptionApplication;
use Filament\Widgets\Widget;

class ApplicantDetailsWidget extends Widget
{
    protected string $view = 'filament.resources.adoption-applications.widgets.applicant-details-widget';

    protected int|string|array $columnSpan = 'full';

    public ?AdoptionApplication $record = null;

    public function getApplicant()
    {
        return $this->record?->user;
    }

    public function getTotalApplications(): int
    {
        return $this->getApplicant()?->adoptionApplications()->count() ?? 0;
    }
}
