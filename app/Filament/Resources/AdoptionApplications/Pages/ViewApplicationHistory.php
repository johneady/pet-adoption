<?php

namespace App\Filament\Resources\AdoptionApplications\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Filament\Resources\AdoptionApplications\Tables\StatusHistoryTable;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ViewApplicationHistory extends Page implements HasTable
{
    use InteractsWithTable;

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

    public function table(Table $table): Table
    {
        return StatusHistoryTable::configure($table)
            ->query(fn (): Builder => $this->record->statusHistory()->getQuery()->with('changedBy'));
    }
}
