<?php

namespace App\Filament\Resources\AdoptionApplications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdoptionApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Interview Details')
                    ->relationship('interview')
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Scheduled At')
                            ->seconds(false)
                            ->disabled(),
                        TextInput::make('location')
                            ->maxLength(255)
                            ->disabled(),
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(),
                        DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->seconds(false)
                            ->disabled(),
                    ])
                    ->visible(fn ($record) => $record !== null && in_array($record->status, ['interview_scheduled', 'approved', 'rejected', 'archived']))
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
