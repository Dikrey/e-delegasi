<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    /**
     * Kirim notifikasi ke satu atau banyak user sekaligus.
     *
     * @param int|array $userIds
     * @param string $type
     * @param string $title
     * @param string|null $message
     * @param string|null $link
     * @return int jumlah notifikasi dibuat
     */
    public static function send($userIds, string $type, string $title, ?string $message = null, ?string $link = null): int
    {
        $ids = is_array($userIds) ? array_unique($userIds) : [$userIds];
        $ids = array_values(array_filter($ids));

        if (empty($ids)) return 0;

        $count = 0;
        foreach ($ids as $userId) {
            try {
                Notification::create([
                    'user_id' => $userId,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'link' => $link,
                ]);
                $count++;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $count;
    }
}