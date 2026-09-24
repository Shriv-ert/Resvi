<?php

namespace App\Enums;

enum FacilityStatusChangeType: string
{
    case OperationalStatus = 'operational_status';
    case IsActive = 'is_active';

    public function label(): string
    {
        return match ($this) {
            self::OperationalStatus => 'Status Operasional',
            self::IsActive => 'Status Aktif',
        };
    }
}
