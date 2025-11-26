<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_picture')
                    ->label('Avatar')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(url('/images/default-avatar.svg')),
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
                    ->timezone(auth()->user()->timezone)
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('two_factor_confirmed_at')
                    ->label('2FA Enabled')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Disabled'),
                TextColumn::make('created_at')
                    ->timezone(auth()->user()->timezone)
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->timezone(auth()->user()->timezone)
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
            ->defaultSort('created_at', 'desc');
    }
}
