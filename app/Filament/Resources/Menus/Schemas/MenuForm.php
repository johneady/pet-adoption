<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menu Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL-friendly version of the name'),
                        Select::make('parent_id')
                            ->label('Parent Menu')
                            ->relationship(
                                name: 'parent',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->whereNull('parent_id')
                            )
                            ->searchable()
                            ->preload()
                            ->helperText('Leave empty for top-level menu')
                            ->columnSpanFull(),
                        TextInput::make('display_order')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->helperText('Lower numbers appear first'),
                    ]),

                Section::make('Visibility Settings')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_visible')
                            ->label('Visible')
                            ->default(true)
                            ->helperText('Whether this menu item is shown in navigation'),
                        Toggle::make('requires_auth')
                            ->label('Requires Authentication')
                            ->default(false)
                            ->helperText('Only show to logged-in users'),
                    ]),
            ]);
    }
}
