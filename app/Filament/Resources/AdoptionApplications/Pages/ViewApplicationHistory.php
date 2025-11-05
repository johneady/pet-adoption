<?php

namespace App\Filament\Resources\AdoptionApplications\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use Filament\Resources\Pages\Page;

class ViewApplicationHistory extends Page
{
    protected static string $resource = AdoptionApplicationResource::class;

    protected string $view = 'filament.resources.adoption-applications.pages.view-application-history';

    public $record;

    public function mount($record): void
    {
        $this->record = AdoptionApplicationResource::resolveRecordRouteBinding($record);
    }

    public function getTitle(): string
    {
        return 'Application History - #'.$this->record->id;
    }

    public function getHeading(): string
    {
        return 'Status History for Application #'.$this->record->id;
    }

    public function getSubheading(): ?string
    {
        return 'Applicant: '.$this->record->user->name.' | Pet: '.$this->record->pet->name;
    }
}
