<?php

namespace App\Filament\Resources\Interviews\Schemas;

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
                    ->relationship('adoptionApplication', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->user->name} :: Adopting {$record->pet->name}")
                    ->searchable(['user.name', 'pet.name'])
                    ->preload()
                    ->required()
                    ->default(fn () => request()->query('adoption_application_id'))
                    ->disabled(fn (string $operation) => $operation === 'edit' || request()->has('adoption_application_id'))
                    ->dehydrated(),
                DateTimePicker::make('scheduled_at')
                    ->required(),
                TextInput::make('location'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('completed_at')
                    ->hidden(),
            ]);
    }
}
