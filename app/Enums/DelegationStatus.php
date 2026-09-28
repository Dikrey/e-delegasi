<?php

namespace App\Enums;

enum DelegationStatus
{
    case DRAFT;
    case SENT;
    case ACCEPTED;
    case IN_PROGRESS;
    case PENDING_VERIFICATION;
    case DONE;
    case REJECTED;

    public function status(): string
    {
        return match ($this) {
            self::DRAFT => 'draft',
            self::SENT => 'dikirim',
            self::ACCEPTED => 'diterima',
            self::IN_PROGRESS => 'dalam_pengerjaan',
            self::PENDING_VERIFICATION => 'menunggu_verifikasi',
            self::DONE => 'selesai',
            self::REJECTED => 'ditolak',
        };
    }

    /**
     * Semua status yang tersedia untuk select / filter.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::DRAFT->status() => __('enums.delegation_status.draft'),
            self::SENT->status() => __('enums.delegation_status.sent'),
            self::ACCEPTED->status() => __('enums.delegation_status.accepted'),
            self::IN_PROGRESS->status() => __('enums.delegation_status.in_progress'),
            self::PENDING_VERIFICATION->status() => __('enums.delegation_status.pending_verification'),
            self::DONE->status() => __('enums.delegation_status.done'),
            self::REJECTED->status() => __('enums.delegation_status.rejected'),
        ];
    }

    public static function label(string $status): string
    {
        return self::options()[$status] ?? $status;
    }

    public static function badge(string $status): string
    {
        return match ($status) {
            self::DRAFT->status() => 'bg-label-secondary',
            self::SENT->status() => 'bg-label-info',
            self::ACCEPTED->status() => 'bg-label-primary',
            self::IN_PROGRESS->status() => 'bg-label-warning',
            self::PENDING_VERIFICATION->status() => 'bg-label-secondary',
            self::DONE->status() => 'bg-label-success',
            self::REJECTED->status() => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }
}