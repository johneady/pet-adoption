<?php

namespace App\Filament\Resources\FormQuestions\Pages;

use App\Filament\Resources\FormQuestions\FormQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFormQuestion extends EditRecord
{
    protected static string $resource = FormQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
