<?php

namespace App\Enum;

enum CarStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Rented = 'rented';
    case Maintenance = 'maintenance';
    case Retired = 'retired';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Reserved => 'Réservée',
            self::Rented => 'Louée',
            self::Maintenance => 'Maintenance',
            self::Retired => 'Retirée',
        };
    }

    public static function getChoices(): array
    {
        return [
            'Disponible' => self::Available->value,
            'Réservée' => self::Reserved->value,
            'Louée' => self::Rented->value,
            'Maintenance' => self::Maintenance->value,
            'Retirée' => self::Retired->value,
        ];
    }
}

