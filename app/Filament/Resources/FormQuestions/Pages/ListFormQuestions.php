<?php

namespace App\Filament\Resources\FormQuestions\Pages;

use App\Filament\Resources\FormQuestions\FormQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFormQuestions extends ListRecords
{
    protected static string $resource = FormQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
