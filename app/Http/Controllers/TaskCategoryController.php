<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    /**
     * Display a listing of task categories.
     */
    public function index(Request $request): View
    {
        return view('pages.reference.task-category', [
            'data' => TaskCategory::render($request->search),
            'search' => $request->search,
        ]);
    }

    /**
     * Store a newly created task category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            TaskCategory::create($validated);

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Update the specified task category.
     */
    public function update(Request $request, TaskCategory $taskCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $taskCategory->update($validated);

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Remove the specified task category.
     */
    public function destroy(TaskCategory $taskCategory): RedirectResponse
    {
        try {
            $taskCategory->delete();

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
