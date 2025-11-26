<?php

namespace App\Filament\Resources\Pets\Pages;

use App\Filament\Resources\Pets\PetResource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EditPet extends EditRecord
{
    protected static string $resource = PetResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Only allow deletion if status is available or coming_soon (Coming Soon)
        if (in_array($this->record->status, ['available', 'coming_soon'])) {
            $actions[] = DeleteAction::make();
        }

        return $actions;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function form(Schema $schema): Schema
    {
        $currentStatus = $this->record->status;

        // Determine allowed status options based on current status
        // Pending and adopted are protected - only available and coming_soon can be changed
        $allowedStatuses = match ($currentStatus) {
            'available' => [
                'available' => 'Available',
                'coming_soon' => 'Coming Soon',
            ],
            'coming_soon' => [
                'coming_soon' => 'Coming Soon',
                'available' => 'Available',
            ],
            'pending' => [
                'pending' => 'Pending',
            ],
            'adopted' => [
                'adopted' => 'Adopted',
            ],
            default => [
                'available' => 'Available',
                'coming_soon' => 'Coming Soon',
            ],
        };

        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state).'-'.random_int(1000, 9999)))
                            ->disabled($currentStatus === 'adopted'),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled($currentStatus === 'adopted'),
                        Select::make('species_id')
                            ->relationship('species', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('breed_id', null))
                            ->disabled($currentStatus === 'adopted'),
                        Select::make('breed_id')
                            ->relationship(
                                'breed',
                                'name',
                                fn ($query, callable $get) => $query->where('species_id', $get('species_id'))
                            )
                            ->searchable()
                            ->preload()
                            ->disabled(fn (callable $get) => ! $get('species_id') || $currentStatus === 'adopted'),
                        TextInput::make('age')
                            ->numeric()
                            ->suffix('years')
                            ->minValue(0)
                            ->maxValue(30)
                            ->disabled($currentStatus === 'adopted'),
                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'unknown' => 'Unknown',
                            ])
                            ->required()
                            ->default('unknown')
                            ->disabled($currentStatus === 'adopted'),
                        Select::make('size')
                            ->options([
                                'small' => 'Small',
                                'medium' => 'Medium',
                                'large' => 'Large',
                                'extra_large' => 'Extra Large',
                            ])
                            ->disabled($currentStatus === 'adopted'),
                        TextInput::make('color')
                            ->disabled($currentStatus === 'adopted'),
                        Toggle::make('vaccination_status')
                            ->label('Vaccinated')
                            ->default(false)
                            ->disabled($currentStatus === 'adopted'),
                        Toggle::make('special_needs')
                            ->label('Special Needs')
                            ->default(false)
                            ->disabled($currentStatus === 'adopted'),
                        DatePicker::make('intake_date')
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->disabled($currentStatus === 'adopted'),
                        ToggleButtons::make('status')
                            ->options($allowedStatuses)
                            ->icons(array_intersect_key([
                                'available' => Heroicon::OutlinedCheckCircle,
                                'pending' => Heroicon::OutlinedClock,
                                'adopted' => Heroicon::OutlinedHeart,
                                'coming_soon' => Heroicon::OutlinedXCircle,
                            ], $allowedStatuses))
                            ->colors(array_intersect_key([
                                'available' => 'success',
                                'pending' => 'warning',
                                'adopted' => 'gray',
                                'coming_soon' => 'danger',
                            ], $allowedStatuses))
                            ->inline()
                            ->required()
                            ->default('available')
                            ->columnSpanFull(),
                    ]),

                Section::make('Description & Medical')
                    ->schema([
                        Textarea::make('description')
                            ->rows(5)
                            ->disabled($currentStatus === 'adopted')
                            ->columnSpanFull(),
                        Textarea::make('medical_notes')
                            ->rows(5)
                            ->disabled($currentStatus === 'adopted')
                            ->columnSpanFull(),
                    ]),

                Section::make('Photos')
                    ->schema([
                        Repeater::make('photos')
                            ->relationship()
                            ->disabled($currentStatus === 'adopted')
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
