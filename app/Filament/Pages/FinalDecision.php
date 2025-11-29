<?php

namespace App\Filament\Pages;

use App\Events\ApplicationDecisionMade;
use App\Filament\Resources\AdoptionApplications\AdoptionApplicationResource;
use App\Models\AdoptionApplication;
use App\Models\ApplicationStatusHistory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard;
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

    protected static ?string $title = 'Applications ready for final decisioning';

    public static function getNavigationBadge(): ?string
    {
        $count = AdoptionApplication::where('status', 'under_review')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function getBreadcrumbs(): array
    {
        return [
            Dashboard::getUrl() => 'Adoption Applications',
            static::getUrl() => 'Adoption Applications',
        ];
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
                    ->sortable()
                    ->description(fn ($record) => implode(' • ', array_filter([
                        $record->user->email,
                        $record->user->phone,
                    ]))),
                TextColumn::make('pet.name')
                    ->label('Pet')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pet.species.name')
                    ->label('Species')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Application')
                    ->modalDescription('Are you sure you want to approve this adoption application? This will also notify the applicant by email.')
                    ->modalSubmitActionLabel('Approve')
                    ->action(function (AdoptionApplication $record): void {
                        $oldStatus = $record->status;

                        $record->update(['status' => 'approved']);

                        // Update pet status to adopted
                        if ($record->pet) {
                            $record->pet->update(['status' => 'adopted']);
                        }

                        ApplicationStatusHistory::create([
                            'adoption_application_id' => $record->id,
                            'from_status' => $oldStatus,
                            'to_status' => 'approved',
                            'changed_by' => auth()->id(),
                            'notes' => 'Application approved',
                        ]);

                        ApplicationDecisionMade::dispatch($record, 'approved', 'Application approved');

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
                    ->modalDescription('Are you sure you want to reject this adoption application? This will also notify the applicant by email.')
                    ->modalSubmitActionLabel('Reject')
                    ->action(function (AdoptionApplication $record): void {
                        $oldStatus = $record->status;

                        $record->update(['status' => 'rejected']);

                        // Update pet status to available only if no other active applications exist
                        if ($record->pet) {
                            $hasOtherActiveApplications = $record->pet->adoptionApplications()
                                ->whereIn('status', ['under_review', 'interview_scheduled'])
                                ->where('id', '!=', $record->id)
                                ->exists();

                            if (! $hasOtherActiveApplications) {
                                $record->pet->update(['status' => 'available']);
                            }
                        }

                        ApplicationStatusHistory::create([
                            'adoption_application_id' => $record->id,
                            'from_status' => $oldStatus,
                            'to_status' => 'rejected',
                            'changed_by' => auth()->id(),
                            'notes' => 'Application rejected',
                        ]);

                        ApplicationDecisionMade::dispatch($record, 'rejected', 'Application rejected');

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
