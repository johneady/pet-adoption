<?php

namespace App\Filament\Resources\FormQuestions;

use App\Filament\Resources\FormQuestions\Pages\CreateFormQuestion;
use App\Filament\Resources\FormQuestions\Pages\EditFormQuestion;
use App\Filament\Resources\FormQuestions\Pages\ListFormQuestions;
use App\Filament\Resources\FormQuestions\Schemas\FormQuestionForm;
use App\Filament\Resources\FormQuestions\Tables\FormQuestionsTable;
use App\Models\FormQuestion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FormQuestionResource extends Resource
{
    protected static ?string $model = FormQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $navigationLabel = 'Form Questions';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return FormQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormQuestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormQuestions::route('/'),
            'create' => CreateFormQuestion::route('/create'),
            'edit' => EditFormQuestion::route('/{record}/edit'),
        ];
    }
}
