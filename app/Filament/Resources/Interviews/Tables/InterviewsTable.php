<?php

namespace App\Filament\Resources\Interviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InterviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->whereNull('completed_at'))
            ->defaultSort('scheduled_at', 'asc')
            ->columns([
                IconColumn::make('overdue')
                    ->label('')
                    ->icon(fn ($state) => $state ? Heroicon::OutlinedExclamationCircle : null)
                    ->color('danger')
                    ->state(fn ($record) => $record->scheduled_at->isPast())
                    ->tooltip('Overdue'),
                TextColumn::make('scheduled_at')
                    ->dateTime('M j, Y @ H:i a')
                    ->sortable(),
                TextColumn::make('adoptionApplication.user.name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('adoptionApplication.pet.name')
                    ->label('Pet')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
