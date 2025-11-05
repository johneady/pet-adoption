<?php

namespace App\Filament\Resources\Pets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, $get) => $set('slug', \Illuminate\Support\Str::slug($state).'-'.random_int(1000, 9999))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('species_id')
                            ->relationship('species', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('breed_id', null)),
                        Select::make('breed_id')
                            ->relationship(
                                'breed',
                                'name',
                                fn ($query, callable $get) => $query->where('species_id', $get('species_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->disabled(fn (callable $get) => ! $get('species_id')),
                        TextInput::make('age')
                            ->numeric()
                            ->suffix('years')
                            ->minValue(0)
                            ->maxValue(30),
                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'unknown' => 'Unknown',
                            ])
                            ->required()
                            ->default('unknown'),
                        Select::make('size')
                            ->options([
                                'small' => 'Small',
                                'medium' => 'Medium',
                                'large' => 'Large',
                                'extra_large' => 'Extra Large',
                            ]),
                        TextInput::make('color'),
                        DatePicker::make('intake_date')
                            ->required()
                            ->default(now())
                            ->maxDate(now()),
                        ToggleButtons::make('status')
                            ->options([
                                'available' => 'Available',
                                'pending' => 'Pending',
                                'adopted' => 'Adopted',
                                'unavailable' => 'Unavailable',
                            ])
                            ->icons([
                                'available' => Heroicon::OutlinedCheckCircle,
                                'pending' => Heroicon::OutlinedClock,
                                'adopted' => Heroicon::OutlinedHeart,
                                'unavailable' => Heroicon::OutlinedXCircle,
                            ])
                            ->colors([
                                'available' => 'success',
                                'pending' => 'warning',
                                'adopted' => 'gray',
                                'unavailable' => 'danger',
                            ])
                            ->inline()
                            ->required()
                            ->default('available')
                            ->columnSpanFull(),
                    ]),

                Section::make('Description & Medical')
                    ->schema([
                        Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
                        Textarea::make('medical_notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Photos')
                    ->schema([
                        Repeater::make('photos')
                            ->relationship()
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('Photo')
                                    ->image()
                                    ->directory('pets')
                                    ->required(),
                                Toggle::make('is_primary')
                                    ->label('Primary Photo')
                                    ->helperText('Only one photo should be marked as primary'),
                                TextInput::make('display_order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible(),
                    ]),
            ]);
    }
}
