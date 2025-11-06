<?php

namespace App\Filament\Resources\AdoptionApplications\Widgets;

use App\Models\AdoptionApplication;
use App\Models\AdoptionApplicationNote;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class NotesWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.resources.adoption-applications.widgets.notes-widget';

    protected int|string|array $columnSpan = 'full';

    public ?AdoptionApplication $record = null;

    public function addNoteAction(): Action
    {
        return Action::make('addNote')
            ->label('Add Note')
            ->form([
                Textarea::make('note')
                    ->label('Note')
                    ->required()
                    ->rows(3)
                    ->placeholder('Enter your note here...'),
            ])
            ->action(function (array $data): void {
                AdoptionApplicationNote::create([
                    'adoption_application_id' => $this->record->id,
                    'note' => $data['note'],
                    'created_by' => Auth::id(),
                ]);

                Notification::make()
                    ->success()
                    ->title('Note added successfully')
                    ->send();

                $this->dispatch('$refresh');
            });
    }

    public function getNotes()
    {
        return $this->record
            ->notes()
            ->with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
