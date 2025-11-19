<?php

declare(strict_types=1);

namespace App\Enums;

enum FormType: string
{
    case Adoption = 'adoption';
    case Fostering = 'fostering';

    public function label(): string
    {
        return match ($this) {
            self::Adoption => 'Adoption',
            self::Fostering => 'Fostering',
        };
    }
}
