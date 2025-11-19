<?php

namespace App\Filament\Resources\FormQuestions\Tables;

use App\Enums\FormType;
use App\Enums\QuestionType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FormQuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('form_type')
                    ->label('Form')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof FormType ? $state->label() : $state)
                    ->color(fn ($state) => match ($state) {
                        FormType::Adoption => 'success',
                        FormType::Fostering => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Question')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof QuestionType ? $state->label() : $state)
                    ->color('gray'),

                IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('form_type')
                    ->label('Form Type')
                    ->options(collect(FormType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),

                SelectFilter::make('type')
                    ->label('Answer Type')
                    ->options(collect(QuestionType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('is_required')
                    ->label('Required'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order');
    }
}
