<?php

namespace App\Filament\Resources\AdoptionApplications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
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
                Section::make('Application Form')
                    ->description('View the adoption application details')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Application Details')
                            ->columns(2)
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn ($record) => $record !== null),
                                Select::make('pet_id')
                                    ->relationship('pet', 'name', fn ($query) => $query->where('status', 'available'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn ($record) => $record !== null),
                                Select::make('status')
                                    ->options([
                                        'submitted' => 'Submitted',
                                        'under_review' => 'Under Review',
                                        'interview_scheduled' => 'Interview Scheduled',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'archived' => 'Archived',
                                    ])
                                    ->disabled(fn ($record) => $record !== null)
                                    ->visible(fn ($record) => $record !== null),
                                Placeholder::make('created_at')
                                    ->label('Submitted on')
                                    ->content(fn ($record) => $record?->created_at?->format('M d, Y h:i A') ?? 'Not yet submitted')
                                    ->visible(fn ($record) => $record !== null),
                            ]),

                        Section::make('Applicant Information')
                            ->columns(2)
                            ->schema([
                                Select::make('living_situation')
                                    ->options([
                                        'House with yard' => 'House with yard',
                                        'Apartment' => 'Apartment',
                                        'Condo' => 'Condo',
                                        'Farm' => 'Farm',
                                        'Other' => 'Other',
                                    ])
                                    ->required()
                                    ->disabled(fn ($record) => $record !== null),
                                Select::make('employment_status')
                                    ->options([
                                        'Employed Full-time' => 'Employed Full-time',
                                        'Employed Part-time' => 'Employed Part-time',
                                        'Self-employed' => 'Self-employed',
                                        'Retired' => 'Retired',
                                        'Student' => 'Student',
                                        'Unemployed' => 'Unemployed',
                                        'Other' => 'Other',
                                    ])
                                    ->disabled(fn ($record) => $record !== null),
                                TextInput::make('veterinary_reference')
                                    ->disabled(fn ($record) => $record !== null),
                            ]),

                        Section::make('Experience & Household')
                            ->columnSpanFull()
                            ->schema([
                                Textarea::make('experience')
                                    ->label('Experience with pets')
                                    ->rows(3)
                                    ->disabled(fn ($record) => $record !== null),
                                Textarea::make('other_pets')
                                    ->label('Other pets in household')
                                    ->rows(2)
                                    ->disabled(fn ($record) => $record !== null),
                                Textarea::make('household_members')
                                    ->label('Household members')
                                    ->rows(2)
                                    ->disabled(fn ($record) => $record !== null),
                                Textarea::make('reason_for_adoption')
                                    ->label('Why do you want to adopt?')
                                    ->required()
                                    ->rows(4)
                                    ->disabled(fn ($record) => $record !== null),
                            ]),
                    ]),

                Section::make('Interview Details')
                    ->relationship('interview')
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Scheduled At')
                            ->seconds(false),
                        TextInput::make('location')
                            ->maxLength(255),
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                        DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->seconds(false),
                    ])
                    ->visible(fn ($record) => $record !== null && in_array($record->status, ['interview_scheduled', 'approved', 'rejected', 'archived']))
                    ->saveRelationshipsUsing(function ($component, $state, $record) {
                        if ($record && in_array($record->status, ['interview_scheduled', 'approved', 'rejected', 'archived'])) {
                            $component->saveRelationships();
                        }
                    })
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
