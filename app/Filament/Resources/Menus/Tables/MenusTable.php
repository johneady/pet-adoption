<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('parent.name')
                    ->label('Parent Menu')
                    ->placeholder('Top Level')
                    ->toggleable(),
                TextColumn::make('display_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean()
                    ->alignCenter(),
                IconColumn::make('requires_auth')
                    ->label('Auth Required')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                    ->label('Visibility')
                    ->placeholder('All menus')
                    ->trueLabel('Visible only')
                    ->falseLabel('Hidden only'),
                TernaryFilter::make('requires_auth')
                    ->label('Authentication')
                    ->placeholder('All menus')
                    ->trueLabel('Auth required only')
                    ->falseLabel('Public only'),
                TernaryFilter::make('parent_id')
                    ->label('Level')
                    ->placeholder('All levels')
                    ->trueLabel('Submenus only')
                    ->falseLabel('Top level only')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('parent_id'),
                        false: fn ($query) => $query->whereNull('parent_id'),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('display_order', 'asc')
            ->reorderable('display_order');
    }
}
