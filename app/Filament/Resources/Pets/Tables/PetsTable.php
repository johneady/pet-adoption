<?php

namespace App\Filament\Resources\Pets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primaryPhoto.0.file_path')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-pet.png')),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('species.name')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('breed.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('age')
                    ->suffix(' yrs')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'pending' => 'warning',
                        'adopted' => 'gray',
                        'unavailable' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('intake_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('species_id')
                    ->label('Species')
                    ->relationship('species', 'name')
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'pending' => 'Pending',
                        'adopted' => 'Adopted',
                        'unavailable' => 'Unavailable',
                    ]),
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'unknown' => 'Unknown',
                    ]),
                SelectFilter::make('size')
                    ->options([
                        'small' => 'Small',
                        'medium' => 'Medium',
                        'large' => 'Large',
                        'extra_large' => 'Extra Large',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
