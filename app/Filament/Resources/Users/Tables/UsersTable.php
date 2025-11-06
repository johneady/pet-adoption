<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                IconColumn::make('is_admin')
                    ->label('Administrator')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckBadge)
                    ->falseIcon(Heroicon::OutlinedXMark)
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable()
                    ->alignment('center'),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('two_factor_confirmed_at')
                    ->label('2FA Enabled')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Disabled'),
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
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
