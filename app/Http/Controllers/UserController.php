<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return view('pages.user', [
            'data' => User::render($request->search),
            'search' => $request->search,
            'departments' => \App\Models\Department::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $newUser = $request->validated();

            User::create([
                'name' => $newUser['name'],
                'email' => $newUser['email'],
                'phone' => $newUser['phone'] ?? null,
                'role' => $newUser['role'],
                'department_id' => $newUser['department_id'] ?? null,
                'password' => Hash::make($newUser['password']),
                'is_active' => true,
            ]);

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $isSelf = $user->id === Auth::id();
            $validated = $request->validated();

            // Never allow an admin to demote the final admin account.
            if ($user->role === Role::ADMIN->status()
                && isset($validated['role'])
                && $validated['role'] !== Role::ADMIN->status()
                && User::role(Role::ADMIN)->count() <= 1) {
                return back()->with('error', __('user.last_admin_role'));
            }

            $payload = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
            ];

            if (isset($validated['role']) && $request->user()->role === Role::ADMIN->status()) {
                $payload['role'] = $validated['role'];
            }

            $payload['is_active'] = $request->boolean('is_active');

            $passwordChanged = !empty($validated['password']);
            if ($passwordChanged) {
                $payload['password'] = Hash::make($validated['password']);
            }

            $user->update($payload);

            // A password change or deactivation must log the account out everywhere.
            if ($passwordChanged || !$payload['is_active']) {
                $user->revokeAllSessions();

                // Editing your own account revokes the current browser too.
                if ($isSelf) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->with('info', __($passwordChanged ? 'auth.password_changed' : 'auth.account_deactivated'));
                }
            }

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        try {
            if ($user->id === Auth::id()) {
                return back()->with('error', __('user.cannot_delete_self'));
            }

            if ($user->role === Role::ADMIN->status()
                && User::role(Role::ADMIN)->count() <= 1) {
                return back()->with('error', __('user.cannot_delete_last_admin'));
            }

            $user->revokeAllSessions();
            $user->delete();

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}