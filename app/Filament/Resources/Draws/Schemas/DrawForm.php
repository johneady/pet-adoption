<?php

namespace App\Filament\Resources\Draws\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DrawForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Draw Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->required()
                            ->rows(4)
                            ->helperText('This description will be displayed to users on the public draws page'),
                    ])
                    ->disabled(fn ($record): bool => $record && ($record->isActive() || $record->is_finalized)),

                Section::make('Schedule')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('starts_at')
                                    ->required()
                                    ->default(now()->addDay())
                                    ->afterOrEqual('today')
                                    ->rule('after_or_equal:today'),
                                DatePicker::make('ends_at')
                                    ->required()
                                    ->default(now()->addDays(30))
                                    ->after('starts_at'),
                            ]),
                    ])
                    ->disabled(fn ($record): bool => $record && ($record->isActive() || $record->is_finalized)),

                Section::make('Ticket Pricing')
                    ->description('Define ticket pricing tiers (e.g., 1 for $1, 5 for $3)')
                    ->schema([
                        Repeater::make('ticket_price_tiers')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('quantity')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->label('Number of Tickets'),
                                        TextInput::make('price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01)
                                            ->minValue(0.01)
                                            ->label('Price'),
                                    ]),
                            ])
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Add Pricing Tier')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['quantity'] && $state['price']
                                ? "{$state['quantity']} ticket(s) for \${$state['price']}"
                                : null),
                    ])
                    ->disabled(fn ($record): bool => $record && ($record->isActive() || $record->is_finalized)),
            ]);
    }
}
