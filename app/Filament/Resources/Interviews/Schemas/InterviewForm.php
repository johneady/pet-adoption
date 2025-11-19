<?php

namespace App\Filament\Resources\Interviews\Schemas;

use Coolsam\Flatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InterviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('adoption_application_id')
                    ->label('Adoption Application')
                    ->relationship('adoptionApplication', 'id', modifyQueryUsing: fn ($query) => $query->with(['user', 'pet']))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->user->name} :: Adopting {$record->pet->name}")
                    ->searchable(['user.name', 'pet.name'])
                    ->preload()
                    ->required()
                    ->default(fn () => request()->query('adoption_application_id'))
                    ->disabled(fn (string $operation) => $operation === 'edit' || request()->has('adoption_application_id'))
                    ->dehydrated(),
                DateTimePicker::make('scheduled_at')
                    ->required()
                    ->timezone(auth()->user()->timezone)
                    ->native(false)
                    ->time(true)
                    ->minDate(fn () => today()) // Set the minimum allowed date
                    ->maxDate(fn () => today()->addMonths(6)) // Set the maximum allowed date.
                    ->hoursStep(1) // Intervals of incrementing hours in a time picker
                    ->minutesStep(30) // Intervals of minute increment in a time picker
                    ->seconds(false),
                TextInput::make('location')
                    ->placeholder('e.g., Home visit, video call, office meeting'),
                Textarea::make('notes')
                    ->rows(6)
                    ->hiddenOn('create')
                    ->columnSpanFull(),
                DateTimePicker::make('completed_at')
                    ->hidden(),
            ]);
    }
}
