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
                    ->relationship('adoptionApplication', 'id')
                    ->required(),
                DateTimePicker::make('scheduled_at'),
                TextInput::make('location'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
