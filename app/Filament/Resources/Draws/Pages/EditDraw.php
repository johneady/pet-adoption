<?php

namespace App\Filament\Resources\Draws\Pages;

use App\Filament\Resources\Draws\DrawResource;
use App\Filament\Resources\Draws\Widgets\DrawTicketsWidget;
use App\Mail\DrawResultsSummary;
use App\Mail\DrawWinnerNotification;
use App\Mail\TicketRegistrationConfirmation;
use App\Models\DrawTicket;
use App\Models\User;
use Filament\Actions;
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
            Action::make('registerTickets')
                ->label('Register Tickets')
                ->icon(Heroicon::OutlinedPlus)
                ->color('success')
                ->form(function () {
                    $draw = $this->record;
                    $pricingOptions = collect($draw->ticket_price_tiers)
                        ->mapWithKeys(function ($tier) {
                            $priceFormatted = number_format($tier['price'], 2);
                            $pricePerTicket = $tier['price'] / $tier['quantity'];
                            $pricePerTicketFormatted = number_format($pricePerTicket, 2);

                            return [
                                json_encode($tier) => "{$tier['quantity']} ticket(s) for \${$priceFormatted} (\${$pricePerTicketFormatted} each)",
                            ];
                        })
                        ->toArray();

                    return [
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('tickets.user', 'name')
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->preload(),
                        \Filament\Forms\Components\Select::make('pricing_tier')
                            ->label('Ticket Package')
                            ->options($pricingOptions)
                            ->required()
                            ->helperText('Select a pricing tier to register tickets'),
                    ];
                })
                ->action(function (array $data) {
                    $draw = $this->record;
                    $user = User::find($data['user_id']);
                    $tier = json_decode($data['pricing_tier'], true);

                    $quantity = (int) $tier['quantity'];
                    $totalPrice = (float) $tier['price'];
                    $pricePerTicket = $totalPrice / $quantity;

                    $createdTickets = collect();

                    // Create individual tickets for fair random selection
                    for ($i = 0; $i < $quantity; $i++) {
                        $ticket = DrawTicket::create([
                            'draw_id' => $draw->id,
                            'user_id' => $user->id,
                            'ticket_number' => $draw->nextTicketNumber(),
                            'amount_paid' => $pricePerTicket,
                            'is_winner' => false,
                        ]);

                        $createdTickets->push($ticket);
                    }

                    // Send confirmation email to the customer
                    Mail::to($user->email)->queue(
                        new TicketRegistrationConfirmation($draw, $createdTickets, $totalPrice)
                    );

                    Notification::make()
                        ->title('Tickets Registered')
                        ->body("Successfully registered {$quantity} ticket(s) for {$user->name} (\${$totalPrice})")
                        ->success()
                        ->send();
                })
                ->visible(fn () => ! $this->record->is_finalized && $this->record->isActive()),

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

            Actions\DeleteAction::make()
                ->visible(fn () => ! $this->record->is_finalized),
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
