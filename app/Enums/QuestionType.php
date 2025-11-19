<?php

declare(strict_types=1);

namespace App\Enums;

enum QuestionType: string
{
    case String = 'string';
    case Textarea = 'textarea';
    case Dropdown = 'dropdown';
    case Switch = 'switch';

    public function label(): string
    {
        return match ($this) {
            self::String => 'Text Input',
            self::Textarea => 'Text Area',
            self::Dropdown => 'Dropdown',
            self::Switch => 'Yes/No Switch',
        };
    }
}
