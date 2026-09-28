<?php

namespace App\Enums;

enum Priority
{
    case LOW;
    case NORMAL;
    case HIGH;
    case URGENT;

    public function value(): string
    {
        return match ($this) {
            self::LOW => 'rendah',
            self::NORMAL => 'normal',
            self::HIGH => 'tinggi',
            self::URGENT => 'urgent',
        };
    }

    public static function options(): array
    {
        return [
            self::LOW->value() => __('enums.priority.low'),
            self::NORMAL->value() => __('enums.priority.normal'),
            self::HIGH->value() => __('enums.priority.high'),
            self::URGENT->value() => __('enums.priority.urgent'),
        ];
    }

    public static function label(string $value): string
    {
        return self::options()[$value] ?? $value;
    }

    /**
     * Badge class basis bootstrap untuk tiap prioritas.
     */
    public static function badge(string $value): string
    {
        return match ($value) {
            self::LOW->value() => 'bg-label-secondary',
            self::NORMAL->value() => 'bg-label-info',
            self::HIGH->value() => 'bg-label-warning',
            self::URGENT->value() => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }
}