<?php

namespace App\Filament\Resources\AdoptionApplications\Widgets;

use App\Models\AdoptionApplication;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class ApplicationAnswersWidget extends Widget
{
    protected string $view = 'filament.resources.adoption-applications.widgets.application-answers-widget';

    protected int|string|array $columnSpan = 'full';

    public ?AdoptionApplication $record = null;

    public function getAnswers(): Collection
    {
        return $this->record?->answers()->orderBy('sort_order')->get() ?? collect();
    }
}
