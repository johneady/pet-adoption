<?php

namespace App\Filament\Resources\MembershipPlans\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MembershipPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Plan Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('annual_price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0),
                                TextInput::make('monthly_price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('stripe_annual_price_id')
                                    ->label('Stripe Annual Price ID')
                                    ->maxLength(255)
                                    ->helperText('Optional: Stripe Price ID for annual payments'),
                                TextInput::make('stripe_monthly_price_id')
                                    ->label('Stripe Monthly Price ID')
                                    ->maxLength(255)
                                    ->helperText('Optional: Stripe Price ID for monthly subscriptions'),
                            ]),
                    ]),

                Section::make('Features')
                    ->schema([
                        Repeater::make('features')
                            ->simple(
                                TextInput::make('feature')
                                    ->required()
                            )
                            ->defaultItems(3)
                            ->addActionLabel('Add Feature')
                            ->columnSpanFull(),
                    ]),

                Section::make('Badge Customization')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ColorPicker::make('badge_color')
                                    ->required()
                                    ->default('#94a3b8'),
                                TextInput::make('badge_icon')
                                    ->required()
                                    ->default('star')
                                    ->helperText('Heroicon name'),
                                TextInput::make('display_order')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ]),
                    ]),

                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Inactive plans will not be visible to users'),
                    ]),
            ]);
    }
}
