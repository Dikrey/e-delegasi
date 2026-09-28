<?php

namespace App\Http\Middleware;

use App\Models\LoginSession;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EnsureActiveSession
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Deactivated accounts are forced onto the blocked page immediately.
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('blocked');
        }

        // No login session record => the session was revoked (password changed,
        // device signed out remotely, or the account is managed from another
        // location).
        $loginSession = LoginSession::where('user_id', $user->id)
            ->where('session_id', $request->session()->getId())
            ->first();

        if (!$loginSession) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('info', __('auth.session_revoked'));
        }

        // Throttled activity heartbeat so the "active sessions" list stays fresh.
        if ($loginSession->last_activity_at === null || $loginSession->last_activity_at->diffInMinutes(now()) >= 1) {
            $loginSession->touchActivity();
        }

        return $next($request);
    }
}