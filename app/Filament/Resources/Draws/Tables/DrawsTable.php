<?php

namespace App\Filament\Resources\Draws\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DrawsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('tickets_count')
                    ->counts('tickets')
                    ->label('Tickets Sold')
                    ->sortable(),
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
            ->defaultSort('created_at', 'desc')
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
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
