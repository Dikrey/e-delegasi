<?php

namespace App\Enums;

enum TaskStatus
{
    case NEW;
    case ACCEPTED;
    case IN_PROGRESS;
    case PENDING_REVIEW;
    case DONE;
    case REJECTED;
    case LATE;

    public function status(): string
    {
        return match ($this) {
            self::NEW => 'baru',
            self::ACCEPTED => 'diterima',
            self::IN_PROGRESS => 'dalam_pengerjaan',
            self::PENDING_REVIEW => 'menunggu_review',
            self::DONE => 'selesai',
            self::REJECTED => 'ditolak',
            self::LATE => 'terlambat',
        };
    }

    public static function options(): array
    {
        return [
            self::NEW->status() => __('enums.task_status.new'),
            self::ACCEPTED->status() => __('enums.task_status.accepted'),
            self::IN_PROGRESS->status() => __('enums.task_status.in_progress'),
            self::PENDING_REVIEW->status() => __('enums.task_status.pending_review'),
            self::DONE->status() => __('enums.task_status.done'),
            self::REJECTED->status() => __('enums.task_status.rejected'),
            self::LATE->status() => __('enums.task_status.late'),
        ];
    }

    public static function label(string $status): string
    {
        return self::options()[$status] ?? $status;
    }

    public static function badge(string $status): string
    {
        return match ($status) {
            self::NEW->status() => 'bg-label-secondary',
            self::ACCEPTED->status() => 'bg-label-info',
            self::IN_PROGRESS->status() => 'bg-label-primary',
            self::PENDING_REVIEW->status() => 'bg-label-warning',
            self::DONE->status() => 'bg-label-success',
            self::REJECTED->status() => 'bg-label-danger',
            self::LATE->status() => 'bg-label-danger',
            default => 'bg-label-secondary',
        };
    }

    /**
     * Status yang dipakai pada Kanban Board.
     *
     * @return array<string, string>
     */
    public static function kanbanColumns(): array
    {
        return [
            self::NEW->status() => __('enums.task_status.new'),
            self::IN_PROGRESS->status() => __('enums.task_status.in_progress'),
            self::PENDING_REVIEW->status() => __('enums.task_status.pending_review'),
            self::DONE->status() => __('enums.task_status.done'),
        ];
    }
}