<?php

namespace App\Filament\Resources\Memberships\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MembershipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Membership Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->required()
                                    ->preload(),
                                Select::make('plan_id')
                                    ->relationship('plan', 'name')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        if ($state && $plan = \App\Models\MembershipPlan::find($state)) {
                                            $paymentType = $get('payment_type') ?? 'annual';
                                            $amount = $paymentType === 'annual' ? $plan->annual_price : $plan->monthly_price;
                                            $set('amount_paid', $amount);
                                        }
                                    }),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Select::make('payment_type')
                                    ->options([
                                        'annual' => 'Annual',
                                        'monthly' => 'Monthly',
                                    ])
                                    ->required()
                                    ->default('annual')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        if ($planId = $get('plan_id')) {
                                            $plan = \App\Models\MembershipPlan::find($planId);
                                            $amount = $state === 'annual' ? $plan->annual_price : $plan->monthly_price;
                                            $set('amount_paid', $amount);
                                        }
                                    }),
                                Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'expired' => 'Expired',
                                        'canceled' => 'Canceled',
                                        'refunded' => 'Refunded',
                                    ])
                                    ->required()
                                    ->default('active'),
                            ]),
                        TextInput::make('amount_paid')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                    ]),

                Section::make('Stripe Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('stripe_subscription_id')
                                    ->label('Stripe Subscription ID')
                                    ->disabled()
                                    ->dehydrated(true),
                                TextInput::make('stripe_payment_intent_id')
                                    ->label('Stripe Payment Intent ID')
                                    ->disabled()
                                    ->dehydrated(true),
                            ]),
                    ])
                    ->collapsed(),

                Section::make('Dates')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DateTimePicker::make('started_at')
                                    ->required()
                                    ->default(now()),
                                DateTimePicker::make('expires_at')
                                    ->required()
                                    ->default(now()->addYear()),
                                DateTimePicker::make('canceled_at'),
                            ]),
                    ]),
            ]);
    }
}
