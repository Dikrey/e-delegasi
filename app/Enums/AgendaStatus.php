<?php

namespace App\Enums;

enum AgendaStatus
{
    case SCHEDULED;
    case DONE;
    case CANCELLED;

    public function status(): string
    {
        return match ($this) {
            self::SCHEDULED => 'terjadwal',
            self::DONE => 'selesai',
            self::CANCELLED => 'dibatalkan',
        };
    }

    public static function options(): array
    {
        return [
            self::SCHEDULED->status() => __('enums.agenda_status.scheduled'),
            self::DONE->status() => __('enums.agenda_status.done'),
            self::CANCELLED->status() => __('enums.agenda_status.cancelled'),
        ];
    }

    public static function label(string $status): string
    {
        return self::options()[$status] ?? $status;
    }

    public static function badge(string $status): string
    {
        return match ($status) {
            self::SCHEDULED->status() => 'bg-label-warning',
            self::DONE->status() => 'bg-label-success',
            self::CANCELLED->status() => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }
}