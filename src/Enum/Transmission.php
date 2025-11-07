<?php

namespace App\Enum;

enum Transmission: string
{
    case Manual = 'manual';
    case Automatic = 'automatic';

    public function getLabel(): string
    {
        return match ($this) {
            self::Manual => 'Manuelle',
            self::Automatic => 'Automatique',
        };
    }

    public static function getChoices(): array
    {
        return [
            'Manuelle' => self::Manual->value,
            'Automatique' => self::Automatic->value,
        ];
    }
}

