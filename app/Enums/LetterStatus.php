<?php

namespace App\Enums;

enum LetterStatus
{
    case WAITING_FINAL_FILE;
    case FINAL;

    public function status(): string
    {
        return match ($this) {
            self::WAITING_FINAL_FILE => 'waiting_for_final_file',
            self::FINAL => 'final',
        };
    }
}