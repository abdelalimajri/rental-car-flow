<?php

namespace App\Enum;

enum CarCategory: string
{
    case Economy = 'economy';
    case Compact = 'compact';
    case Midsize = 'midsize';
    case SUV = 'suv';
    case Van = 'van';
    case Luxury = 'luxury';

    public function getLabel(): string
    {
        return match ($this) {
            self::Economy => 'Économie',
            self::Compact => 'Compacte',
            self::Midsize => 'Intermédiaire',
            self::SUV => 'SUV',
            self::Van => 'Van/Monospace',
            self::Luxury => 'Luxe',
        };
    }

    public static function getChoices(): array
    {
        return [
            'Économie' => self::Economy->value,
            'Compacte' => self::Compact->value,
            'Intermédiaire' => self::Midsize->value,
            'SUV' => self::SUV->value,
            'Van/Monospace' => self::Van->value,
            'Luxe' => self::Luxury->value,
        ];
    }
}

