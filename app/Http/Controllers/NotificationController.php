<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    /**
     * Tandai satu notifikasi dibaca.
     */
    public function markRead(Notification $notification): RedirectResponse
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $notification->markRead();

        return $notification->link
            ? redirect($notification->link)
            : back();
    }

    /**
     * Tandai semua dibaca.
     */
    public function markAllRead(): RedirectResponse
    {
        Notification::forUser()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', __('menu.general.success'));
    }

    /**
     * Hapus notifikasi.
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        if ((int) $notification->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', __('menu.general.success'));
    }

    /**
     * Trigger polling notifikasi pada navbar (AJAX).
     */
    public function poll(): JsonResponse
    {
        $notifications = Notification::forUser()
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'is_read' => $n->is_read,
                'time_ago' => $n->time_ago,
                'link' => $n->link ? route('notification.read', $n->id) : null,
            ]);

        return response()->json([
            'unread' => Notification::forUser()->unread()->count(),
            'items' => $notifications,
        ]);
    }
}