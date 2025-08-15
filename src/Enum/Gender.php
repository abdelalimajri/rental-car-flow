<?php

namespace App\Enum;

enum Gender: string
{
    case MALE = 'M';
    case FEMALE = 'F';

    public function getLabel(): string
    {
        return match ($this) {
            self::MALE => 'Homme',
            self::FEMALE => 'Femme',
        };
    }

    public static function getChoices(): array
    {
        return [
            'Homme' => self::MALE->value,
            'Femme' => self::FEMALE->value,
        ];
    }
}
