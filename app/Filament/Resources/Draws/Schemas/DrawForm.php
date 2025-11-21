<?php

namespace App\Filament\Resources\Draws\Schemas;

use App\Models\Draw;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                                    ->native(false)
                                    ->default(now()->addDay())
                                    ->afterOrEqual('today')
                                    ->rule('after_or_equal:today')
                                    ->live(onBlur: true)
                                    ->rules([
                                        fn (Get $get, $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            $startsAt = $value;
                                            $endsAt = $get('ends_at');

                                            if (! $startsAt || ! $endsAt) {
                                                return;
                                            }

                                            $query = Draw::query()
                                                ->where(function ($query) use ($startsAt, $endsAt) {
                                                    // Check if new draw overlaps with existing draws
                                                    $query->where(function ($q) use ($startsAt) {
                                                        // New draw starts during an existing draw
                                                        $q->where('starts_at', '<=', $startsAt)
                                                            ->where('ends_at', '>', $startsAt);
                                                    })->orWhere(function ($q) use ($endsAt) {
                                                        // New draw ends during an existing draw
                                                        $q->where('starts_at', '<', $endsAt)
                                                            ->where('ends_at', '>=', $endsAt);
                                                    })->orWhere(function ($q) use ($startsAt, $endsAt) {
                                                        // New draw completely encompasses an existing draw
                                                        $q->where('starts_at', '>=', $startsAt)
                                                            ->where('ends_at', '<=', $endsAt);
                                                    });
                                                });

                                            // Exclude current record when editing
                                            if ($record) {
                                                $query->where('id', '!=', $record->id);
                                            }

                                            $overlappingDraw = $query->first();

                                            if ($overlappingDraw) {
                                                $fail("This draw overlaps with '{$overlappingDraw->name}' (from {$overlappingDraw->starts_at->format('M j, Y')} to {$overlappingDraw->ends_at->format('M j, Y')}).");
                                            }
                                        },
                                    ]),
                                DatePicker::make('ends_at')
                                    ->required()
                                    ->default(now()->addDays(30))
                                    ->native(false)
                                    ->after('starts_at')
                                    ->live(onBlur: true)
                                    ->rules([
                                        fn (Get $get, $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            $startsAt = $get('starts_at');
                                            $endsAt = $value;

                                            if (! $startsAt || ! $endsAt) {
                                                return;
                                            }

                                            $query = Draw::query()
                                                ->where(function ($query) use ($startsAt, $endsAt) {
                                                    // Check if new draw overlaps with existing draws
                                                    $query->where(function ($q) use ($startsAt) {
                                                        // New draw starts during an existing draw
                                                        $q->where('starts_at', '<=', $startsAt)
                                                            ->where('ends_at', '>', $startsAt);
                                                    })->orWhere(function ($q) use ($endsAt) {
                                                        // New draw ends during an existing draw
                                                        $q->where('starts_at', '<', $endsAt)
                                                            ->where('ends_at', '>=', $endsAt);
                                                    })->orWhere(function ($q) use ($startsAt, $endsAt) {
                                                        // New draw completely encompasses an existing draw
                                                        $q->where('starts_at', '>=', $startsAt)
                                                            ->where('ends_at', '<=', $endsAt);
                                                    });
                                                });

                                            // Exclude current record when editing
                                            if ($record) {
                                                $query->where('id', '!=', $record->id);
                                            }

                                            $overlappingDraw = $query->first();

                                            if ($overlappingDraw) {
                                                $fail("This draw overlaps with '{$overlappingDraw->name}' (from {$overlappingDraw->starts_at->format('M j, Y')} to {$overlappingDraw->ends_at->format('M j, Y')}).");
                                            }
                                        },
                                    ]),
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
                    ->collapsible()
                    ->collapsed(fn ($record): bool => $record && $record->starts_at->isFuture() === false)
                    ->disabled(fn ($record): bool => $record && ($record->isActive() || $record->is_finalized)),
            ]);
    }
}
