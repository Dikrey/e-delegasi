<?php

namespace App\Http\Controllers;

use App\Models\LoginSession;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Attempts allowed before the login throttle kicks in.
     *
     * @var int
     */
    protected const MAX_ATTEMPTS = 5;

    /**
     * Show the (super modern) login page.
     *
     * @return View|RedirectResponse
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(route('home'));
        }

        return view('pages.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = Validator::make($request->only('email', 'password', 'remember'), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ])->validate();

        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => $seconds]),
            ]);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (!$user->is_active) {
            RateLimiter::clear($throttleKey);

            return redirect()
                ->route('blocked')
                ->with('blocked_user', $user->name);
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $request->boolean('remember'));

        // Auth::login() already regenerates the session id; store the final one.
        LoginSession::record($user, $request);

        return redirect()->intended(route('home'))
            ->with('success', __('auth.welcome', ['name' => $user->name]));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            LoginSession::where('user_id', $user->id)
                ->where('session_id', $request->session()->getId())
                ->delete();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', __('auth.logged_out'));
    }

    /**
     * Show the blocked / inactive account page.
     *
     * @return View|RedirectResponse
     */
    public function blocked(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->is_active) {
            return redirect()->route('home');
        }

        return view('pages.blocked', [
            'name' => session('blocked_user'),
        ]);
    }
}