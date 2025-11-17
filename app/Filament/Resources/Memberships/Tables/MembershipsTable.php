<?php

namespace App\Filament\Resources\Memberships\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MembershipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', $record->user_id)),
                TextColumn::make('plan.name')
                    ->badge()
                    ->color(fn ($record) => match ($record->plan->slug) {
                        'bronze' => 'warning',
                        'silver' => 'gray',
                        'gold' => 'success',
                        default => 'info',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'canceled' => 'warning',
                        'refunded' => 'gray',
                        default => 'info',
                    })
                    ->sortable(),
                TextColumn::make('payment_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('started_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->date()
                    ->sortable()
                    ->description(fn ($record) => $record->isActive() ? $record->daysRemaining().' days remaining' : null),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'canceled' => 'Canceled',
                        'refunded' => 'Refunded',
                    ]),
                SelectFilter::make('plan')
                    ->relationship('plan', 'name'),
                SelectFilter::make('payment_type')
                    ->options([
                        'annual' => 'Annual',
                        'monthly' => 'Monthly',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('refund')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->refund())
                    ->visible(fn ($record) => $record->status === 'active'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
