<?php

namespace App\Filament\Resources\Draws\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DrawsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('tickets')->withSum('tickets', 'amount_paid'))
            ->columns([
                IconColumn::make('pending draw')
                    ->label('')
                    ->icon(fn ($state) => $state ? Heroicon::OutlinedExclamationCircle : null)
                    ->color('danger')
                    ->state(fn ($record) => $record->ends_at->isPast() && ! $record->is_finalized)
                    ->tooltip('Ready for winner selection'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('tickets_count')
                    ->label('Tickets Sold')
                    ->sortable(),
                TextColumn::make('total_collected')
                    ->label('Total Collected')
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->totalAmountCollected())
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderBy('tickets_sum_amount_paid', $direction);
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->is_finalized) {
                            return 'Finalized';
                        }
                        if ($record->hasEnded()) {
                            return 'Ended';
                        }
                        if ($record->isActive()) {
                            return 'Active';
                        }

                        return 'Upcoming';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Ended' => 'warning',
                        'Finalized' => 'gray',
                        'Upcoming' => 'info',
                        default => 'gray',
                    }),
                IconColumn::make('is_finalized')
                    ->boolean()
                    ->label('Winner Selected'),
                TextColumn::make('created_at')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('starts_at', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'ended' => 'Ended',
                        'finalized' => 'Finalized',
                        'upcoming' => 'Upcoming',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value']) {
                            'active' => $query->where('starts_at', '<=', now())
                                ->where('ends_at', '>', now())
                                ->where('is_finalized', false),
                            'ended' => $query->where('ends_at', '<=', now())
                                ->where('is_finalized', false),
                            'finalized' => $query->where('is_finalized', true),
                            'upcoming' => $query->where('starts_at', '>', now()),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record): bool => ! $record->isActive() && ! $record->is_finalized),
            ])
            ->toolbarActions([
                // DeleteBulkAction::make(),
            ]);
    }
}
