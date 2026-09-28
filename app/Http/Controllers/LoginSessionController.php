<?php

namespace App\Http\Controllers;

use App\Models\LoginSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginSessionController extends Controller
{
    /**
     * Display all active login sessions for the current user.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return view('pages.devices', [
            'currentSessionId' => $request->session()->getId(),
        ]);
    }

    /**
     * Realtime JSON list of active sessions (polled by the frontend).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        $currentSessionId = $request->session()->getId();

        $sessions = auth()->user()
            ->loginSessions()
            ->latest('last_activity_at')
            ->get()
            ->map(function (LoginSession $session) use ($currentSessionId) {
                return [
                    'id' => $session->id,
                    'is_current' => $session->session_id === $currentSessionId,
                    'device' => $session->device,
                    'browser' => $session->browser,
                    'platform' => $session->platform,
                    'ip_address' => $session->ip_address,
                    'location' => $session->location,
                    'last_activity' => $session->last_activity_at?->diffForHumans(),
                    'last_activity_raw' => $session->last_activity_at?->toIso8601String(),
                    'logged_in' => $session->created_at?->diffForHumans(),
                    'icon' => \App\Helpers\DeviceHelper::icon($session->device ?? ''),
                ];
            });

        return response()->json([
            'current_session' => $currentSessionId,
            'sessions' => $sessions,
            'count' => $sessions->count(),
        ]);
    }

    /**
     * Sign out a single remote device (not the current one).
     *
     * @param Request $request
     * @param LoginSession $loginSession
     * @return RedirectResponse
     */
    public function logout(Request $request, LoginSession $loginSession): RedirectResponse
    {
        try {
            if ($loginSession->user_id !== auth()->id()) {
                abort(403);
            }

            if ($loginSession->session_id === $request->session()->getId()) {
                return back()->with('error', __('device.cannot_logout_self'));
            }

            $loginSession->delete();

            return back()->with('success', __('device.logged_out'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Sign out every remote device except the current browser.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logoutOthers(Request $request): RedirectResponse
    {
        try {
            auth()->user()
                ->loginSessions()
                ->where('session_id', '!=', $request->session()->getId())
                ->delete();

            return back()->with('success', __('device.logged_out_others'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}