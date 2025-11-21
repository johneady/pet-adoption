<?php

namespace App\Filament\Resources\TicketPurchaseRequests\Tables;

use App\Mail\TicketRegistrationConfirmation;
use App\Models\DrawTicket;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class TicketPurchaseRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'fulfilled' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('draw.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Tickets')
                    ->sortable(),
                TextColumn::make('pricing_tier')
                    ->label('Package')
                    ->getStateUsing(function ($record) {
                        $tier = $record->pricing_tier;

                        return $tier['quantity'].' ticket'.($tier['quantity'] > 1 ? 's' : '').' - $'.number_format($tier['price'], 2);
                    }),
                TextColumn::make('created_at')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'fulfilled' => 'Fulfilled',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('draw_id')
                    ->relationship('draw', 'name')
                    ->label('Draw'),
            ])
            ->recordActions([
                Action::make('confirm_payment')
                    ->label('Confirm Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Payment')
                    ->modalDescription(fn ($record) => 'Confirm payment received for '.$record->user->name.'\'s request to purchase '.$record->quantity.' ticket(s) for '.$record->draw->name.'? This will register the tickets and send a confirmation email to '.$record->user->name.'.')
                    ->visible(fn ($record): bool => $record->status === 'pending')
                    ->action(function ($record) {
                        $draw = $record->draw;
                        $user = $record->user;
                        $pricingTier = $record->pricing_tier;
                        $quantity = $record->quantity;

                        // Calculate price per ticket
                        $pricePerTicket = $pricingTier['price'] / $pricingTier['quantity'];
                        $totalPrice = $pricePerTicket * $quantity;

                        // Create tickets
                        $ticketIds = [];
                        for ($i = 0; $i < $quantity; $i++) {
                            $ticket = DrawTicket::create([
                                'draw_id' => $draw->id,
                                'user_id' => $user->id,
                                'ticket_number' => $draw->nextTicketNumber(),
                                'amount_paid' => $pricePerTicket,
                                'is_winner' => false,
                            ]);
                            $ticketIds[] = $ticket->id;
                        }

                        $createdTickets = DrawTicket::whereIn('id', $ticketIds)->get();

                        // Send confirmation email
                        Mail::to($user->email)->queue(new TicketRegistrationConfirmation(
                            $draw,
                            $createdTickets,
                            $totalPrice
                        ));

                        // Update request status
                        $record->update(['status' => 'fulfilled']);

                        Notification::make()
                            ->success()
                            ->title('Payment Confirmed')
                            ->body($quantity.' ticket(s) registered and confirmation email sent to '.$user->name)
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Request')
                    ->modalDescription(fn ($record) => 'Cancel '.$record->user->name.'\'s request to purchase '.$record->quantity.' ticket(s)?')
                    ->visible(fn ($record): bool => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);

                        Notification::make()
                            ->success()
                            ->title('Request Cancelled')
                            ->send();
                    }),
            ]);
    }
}
