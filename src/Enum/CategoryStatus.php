<?php
// src/Enum/CategoryStatus.php
namespace App\Enum;

enum CategoryStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';
    
    public function getLabel(): string
    {
        return match($this) {
            self::ACTIVE => 'Actif',
            self::INACTIVE => 'Inactif',
            self::ARCHIVED => 'Archivé',
        };
    }
}