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
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                        Toggle::make('vaccination_status')
                            ->label('Vaccinated')
                            ->default(false),
                        Toggle::make('special_needs')
                            ->label('Special Needs')
                            ->default(false),
                        DatePicker::make('intake_date')
                            ->required()
                            ->native(false)
                            ->timezone(auth()->user()->timezone)
                            ->default(now())
                            ->maxDate(now()),
                        ToggleButtons::make('status')
                            ->options([
                                'available' => 'Available',
                                'pending' => 'Pending',
                                'adopted' => 'Adopted',
                                'coming_soon' => 'Coming Soon',
                            ])
                            ->icons([
                                'available' => Heroicon::OutlinedCheckCircle,
                                'pending' => Heroicon::OutlinedClock,
                                'adopted' => Heroicon::OutlinedHeart,
                                'coming_soon' => Heroicon::OutlinedXCircle,
                            ])
                            ->colors([
                                'available' => 'success',
                                'pending' => 'warning',
                                'adopted' => 'gray',
                                'coming_soon' => 'danger',
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
                            ->rows(5)
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
                                    ->disk('public')
                                    ->directory('pets')
                                    ->visibility('public')
                                    ->maxSize(8192)
                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, callable $get): string {
                                        // Get the old file path if updating
                                        $oldFilePath = $get('file_path');

                                        // Delete old file if it exists
                                        if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                                            Storage::disk('public')->delete($oldFilePath);
                                        }

                                        // Generate unique filename
                                        $filename = 'pets/'.uniqid().'.'.$file->getClientOriginalExtension();

                                        // Compress and resize image to max 800x600
                                        $image = Image::read($file->getRealPath());
                                        $image->cover(800, 600);

                                        // Store compressed image with explicit public visibility
                                        Storage::disk('public')->put($filename, (string) $image->encode(), 'public');

                                        return $filename;
                                    })
                                    ->deleteUploadedFileUsing(function (?string $file): void {
                                        if ($file && Storage::disk('public')->exists($file)) {
                                            Storage::disk('public')->delete($file);
                                        }
                                    })
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
