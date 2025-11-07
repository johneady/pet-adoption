<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Models\AdoptionApplication;
use App\Models\ApplicationStatusHistory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class FinalDecision extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Final Decisions';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.final-decision';

    protected static ?string $title = 'Applications that have a completed interview';

    public static function getNavigationBadge(): ?string
    {
        $count = AdoptionApplication::where('status', 'under_review')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AdoptionApplication::query()
                    ->where('status', 'under_review')
                    ->with(['user', 'pet.species', 'interview'])
            )
            ->recordUrl(fn (AdoptionApplication $record): string => AdoptionApplicationResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pet.name')
                    ->label('Pet')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pet.species.name')
                    ->label('Species')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('living_situation')
                    ->toggleable(),
                TextColumn::make('employment_status')
                    ->toggleable(),
                TextColumn::make('interview.scheduled_at')
                    ->label('Interview Date')
                    ->dateTime('M j, Y @ H:i a')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (AdoptionApplication $record): string => AdoptionApplicationResource::getUrl('edit', ['record' => $record])),
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Application')
                    ->modalDescription('Are you sure you want to approve this adoption application?')
                    ->modalSubmitActionLabel('Approve')
                    ->action(function (AdoptionApplication $record): void {
                        $oldStatus = $record->status;

                        $record->update(['status' => 'approved']);

                        ApplicationStatusHistory::create([
                            'adoption_application_id' => $record->id,
                            'from_status' => $oldStatus,
                            'to_status' => 'approved',
                            'changed_by' => auth()->id(),
                            'notes' => 'Application approved',
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Application Approved')
                            ->body("The application for {$record->pet->name} has been approved.")
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Application')
                    ->modalDescription('Are you sure you want to reject this adoption application?')
                    ->modalSubmitActionLabel('Reject')
                    ->action(function (AdoptionApplication $record): void {
                        $oldStatus = $record->status;

                        $record->update(['status' => 'rejected']);

                        ApplicationStatusHistory::create([
                            'adoption_application_id' => $record->id,
                            'from_status' => $oldStatus,
                            'to_status' => 'rejected',
                            'changed_by' => auth()->id(),
                            'notes' => 'Application rejected',
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Application Rejected')
                            ->body("The application for {$record->pet->name} has been rejected.")
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No Applications Under Review')
            ->emptyStateDescription('There are no adoption applications currently under review.')
            ->emptyStateIcon(Heroicon::OutlinedClipboardDocumentCheck);
    }
}
