<?php

namespace App\Filament\Resources\Menus\Tables;

use App\Models\Menu;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(function ($state, Menu $record) {
                        if ($record->parent_id) {
                            return '└─ '.$state;
                        }

                        return $state;
                    })
                    ->description(fn (Menu $record): ?string => $record->parent_id ? 'Submenu' : null),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('parent.name')
                    ->label('Parent Menu')
                    ->placeholder('Top Level')
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->withoutGlobalScope('order')
                    ->orderByRaw('COALESCE(parent_id, id)')
                    ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
                    ->orderBy('display_order', 'asc');
            })
            ->recordClasses(fn (Menu $record) => $record->parent_id ? 'bg-gray-50/50' : null)
            ->defaultSort('display_order', 'asc')
            ->reorderable('display_order');
    }
}
