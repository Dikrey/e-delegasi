<?php

namespace App\Enums;

enum LetterVerification: string
{
    case WAITING = 'menunggu_verifikasi';
    case VERIFIED = 'terverifikasi';
    case NO_FOLLOW_UP = 'tidak_memerlukan_tindak_lanjut';
    case NEEDS_FOLLOW_UP = 'memerlukan_tindak_lanjut';
    case DELEGATED = 'didelegasikan';
    case COMPLETED = 'selesai';
    case ARCHIVED = 'diarsipkan';

    public function label(): string
    {
        return match ($this) {
            self::WAITING => __('enums.letter_verification.waiting'),
            self::VERIFIED => __('enums.letter_verification.verified'),
            self::NO_FOLLOW_UP => __('enums.letter_verification.no_follow_up'),
            self::NEEDS_FOLLOW_UP => __('enums.letter_verification.needs_follow_up'),
            self::DELEGATED => __('enums.letter_verification.delegated'),
            self::COMPLETED => __('enums.letter_verification.completed'),
            self::ARCHIVED => __('enums.letter_verification.archived'),
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    public static function badge(string $status): string
    {
        return match ($status) {
            self::WAITING->value => 'bg-label-secondary',
            self::VERIFIED->value => 'bg-label-info',
            self::NO_FOLLOW_UP->value => 'bg-label-dark',
            self::NEEDS_FOLLOW_UP->value => 'bg-label-warning',
            self::DELEGATED->value => 'bg-label-primary',
            self::COMPLETED->value => 'bg-label-success',
            self::ARCHIVED->value => 'bg-label-secondary',
            default => 'bg-label-secondary',
        };
    }
}