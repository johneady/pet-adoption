<?php

namespace App\Filament\Resources\Draws\Pages;

use App\Filament\Resources\Draws\DrawResource;
use App\Filament\Resources\Draws\Widgets\DrawTicketsWidget;
use App\Mail\DrawResultsSummary;
use App\Mail\DrawWinnerNotification;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;

class EditDraw extends EditRecord
{
    protected static string $resource = DrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('selectWinner')
                ->label('Select Random Winner')
                ->icon(Heroicon::OutlinedTrophy)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Select Random Winner')
                ->modalDescription('This will randomly select a winning ticket from all purchased tickets. This action cannot be undone.')
                ->action(function () {
                    $draw = $this->record;

                    if ($draw->tickets()->count() === 0) {
                        Notification::make()
                            ->title('No Tickets')
                            ->body('Cannot select a winner - no tickets have been purchased for this draw.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $winningTicket = $draw->selectRandomWinner();

                    if ($winningTicket) {
                        // Send email to winner
                        Mail::to($winningTicket->user->email)
                            ->queue(new DrawWinnerNotification($draw, $winningTicket));

                        // Send summary to admins with notification preference
                        $admins = User::where('is_admin', true)
                            ->where('receive_draw_result_alerts', true)
                            ->get();

                        foreach ($admins as $admin) {
                            Mail::to($admin->email)
                                ->queue(new DrawResultsSummary($draw, $winningTicket));
                        }

                        Notification::make()
                            ->title('Winner Selected!')
                            ->body("Ticket #{$winningTicket->ticket_number} owned by {$winningTicket->user->name} has won!")
                            ->success()
                            ->send();
                    }
                })
                ->visible(fn () => ! $this->record->is_finalized && $this->record->hasEnded() && $this->record->tickets()->count() > 0),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            DrawTicketsWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
