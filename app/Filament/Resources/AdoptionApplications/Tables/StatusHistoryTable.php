<?php

namespace App\Filament\Resources\AdoptionApplications\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusHistoryTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_status')
                    ->label('From')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'interview_scheduled' => 'warning',
                        'under_review' => 'info',
                        'pending', 'archived' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => $state ? ucwords(str_replace('_', ' ', $state)) : '—')
                    ->placeholder('—'),
                TextColumn::make('to_status')
                    ->label('To')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'interview_scheduled' => 'warning',
                        'under_review' => 'info',
                        'pending', 'archived' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->wrap()
                    ->placeholder('No notes')
                    ->grow(),
                TextColumn::make('changedBy.name')
                    ->label('Changed by')
                    ->placeholder('System'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime('M d, Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'asc')
            ->paginated(false)
            ->emptyStateHeading('No status history available');
    }
}
