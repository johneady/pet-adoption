<?php

namespace App\Filament\Resources\FormQuestions\Schemas;

use App\Enums\FormType;
use App\Enums\QuestionType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Question Details')
                    ->schema([
                        Select::make('form_type')
                            ->label('Form Type')
                            ->options(collect(FormType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required()
                            ->native(false),

                        TextInput::make('label')
                            ->label('Question Text')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Answer Type')
                            ->options(collect(QuestionType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                            ->required()
                            ->native(false)
                            ->live(),

                        TagsInput::make('options')
                            ->label('Dropdown Options')
                            ->helperText('Press Enter after each option')
                            ->placeholder('Add an option')
                            ->visible(fn ($get) => $get('type') === QuestionType::Dropdown->value),

                        Toggle::make('is_required')
                            ->label('Required')
                            ->default(true),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }
}
