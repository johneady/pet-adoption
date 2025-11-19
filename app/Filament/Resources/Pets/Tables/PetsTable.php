<?php

namespace App\Filament\Resources\Pets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                ImageColumn::make('primaryPhoto.file_path')
                    ->label('Image')
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
                        'coming_soon' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('intake_date')
                    ->timezone(auth()->user()->timezone)
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->timezone(auth()->user()->timezone)
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
                        'coming_soon' => 'Coming Soon',
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
                SelectFilter::make('vaccination_status')
                    ->label('Vaccinated')
                    ->options([
                        true => 'Yes',
                        false => 'No',
                    ]),
                SelectFilter::make('special_needs')
                    ->label('Special Needs')
                    ->options([
                        true => 'Yes',
                        false => 'No',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
