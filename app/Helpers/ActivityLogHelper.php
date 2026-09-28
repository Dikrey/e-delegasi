<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogHelper
{
    /**
     * Catat aktivitas penting pengguna.
     *
     * @param string $action
     * @param string|null $module
     * @param int|null $referenceId
     * @param array|null $payload
     * @return ActivityLog|null
     */
    public static function log(string $action, ?string $module = null, ?int $referenceId = null, ?array $payload = []): ?ActivityLog
    {
        try {
            return ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'module' => $module,
                'reference_id' => $referenceId ?? $payload['reference_id'] ?? null,
                'ip_address' => request()->ip(),
                'payload' => $payload,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }
}