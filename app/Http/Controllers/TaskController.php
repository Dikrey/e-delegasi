<?php

namespace App\Http\Controllers;

use App\Enums\DelegationStatus;
use App\Enums\TaskStatus;
use App\Helpers\ActivityLogHelper;
use App\Helpers\NotificationHelper;
use App\Http\Requests\StoreTaskUpdateRequest;
use App\Models\Config;
use App\Models\Delegation;
use App\Models\Task;
use App\Models\TaskUpdate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Daftar tugas. Staff melihat tugasnya sendiri; sekretaris/admin semua.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');
        $priority = $request->get('priority', 'all');
        $user = auth()->user();

        $sortable = ['title', 'deadline', 'priority', 'progress', 'status', 'created_at'];
        $sort = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'created_at';
        $direction = in_array($request->get('direction'), ['asc', 'desc']) ? $request->get('direction') : 'desc';

        $query = Task::query()->with(['delegation.letter', 'staff', 'delegation.agenda']);

        if ($user->role === 'staff') {
            $query->forStaff();
        }

        if ($priority && $priority !== 'all') {
            $query->where('priority', $priority);
        }

        if ($status && $status !== 'all') {
            $query = match ($status) {
                'baru' => $query->new(),
                'dalam_pengerjaan' => $query->inProgress(),
                'menunggu_review' => $query->pendingReview(),
                'selesai' => $query->done(),
                'terlambat' => $query->late(),
                default => $query->status($status),
            };
        }

        $data = $query->when($request->search, function ($q, $s) {
            return $q->where(function ($q2) use ($s) {
                return $q2
                    ->where('title', 'LIKE', '%' . $s . '%')
                    ->orWhereHas('staff', fn ($q) => $q->where('name', 'LIKE', '%' . $s . '%'))
                    ->orWhereHas('delegation.letter', fn ($q) => $q->where('reference_number', 'LIKE', '%' . $s . '%'));
            });
        })->orderBy($sort, $direction)->paginate(Config::getValueByCode(\App\Enums\Config::PAGE_SIZE))
            ->withQueryString();

        return view('pages.task.index', [
            'data' => $data,
            'search' => $request->search,
            'status' => $status,
            'priority' => $priority,
            'sort' => $sort,
            'direction' => $direction,
            'isStaff' => $user->role === 'staff',
            'stats' => $this->taskStats($user),
        ]);
    }

    /**
     * Kanban Board tugas.
     */
    public function kanban(Request $request): View
    {
        $user = auth()->user();

        $base = Task::query()->with(['delegation.letter', 'staff', 'delegation.agenda']);

        if ($user->role === 'staff') {
            $base->forStaff();
        } elseif ($request->get('staff_id')) {
            $base->where('staff_id', $request->integer('staff_id'));
        }

        $tasks = $base->get();

        $columnKeys = \App\Enums\TaskStatus::kanbanColumns();
        $columns = array_fill_keys(array_keys($columnKeys), collect());

        foreach ($tasks as $task) {
            $key = match ($task->status) {
                'baru', 'ditolak' => TaskStatus::NEW->status(),
                'diterima', 'dalam_pengerjaan', 'terlambat' => TaskStatus::IN_PROGRESS->status(),
                'menunggu_review' => TaskStatus::PENDING_REVIEW->status(),
                'selesai' => TaskStatus::DONE->status(),
                default => TaskStatus::NEW->status(),
            };
            $columns[$key][] = $task;
        }

        return view('pages.task.kanban', [
            'columns' => $columns,
            'staffs' => $user->role === 'staff'
                ? collect()
                : \App\Models\User::role(\App\Enums\Role::STAFF)->active()->orderBy('name')->get(),
            'selectedStaff' => $request->get('staff_id'),
            'isStaff' => $user->role === 'staff',
            'isSekretaris' => $user->role === 'sekretaris',
            'isAdmin' => $user->role === 'admin',
        ]);
    }

    /**
     * Ubah status task via Kanban (drag & drop).
     */
    public function kanbanUpdate(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:baru,diterima,dalam_pengerjaan,menunggu_review,selesai,ditolak,terlambat'],
        ]);

        try {
            $user = auth()->user();

            if ($user->role === 'staff' && (int) $task->staff_id !== (int) $user->id) {
                return response()->json(['status' => false, 'message' => __('delegation.forbidden')], 403);
            }

            // Hanya sekretaris yang boleh verifikasi (selesai).
            if ($request->status === 'selesai' && $user->role !== 'sekretaris') {
                return response()->json(['status' => false, 'message' => __('delegation.forbidden')], 403);
            }

            $this->applyTaskStatus($task, $request->status, auth()->id());

            return response()->json(['status' => true, 'message' => __('menu.general.success')]);
        } catch (\Throwable $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Detail tugas.
     */
    public function show(Task $task): View
    {
        $task->load(['delegation.letter', 'delegation.creator', 'staff', 'creator', 'agenda', 'updates.user']);

        $this->authorizeTaskView($task);

        $user = auth()->user();

        return view('pages.task.show', [
            'data' => $task,
            'isStaff' => $user->role === 'staff',
            'isSekretaris' => $user->role === 'sekretaris',
            'isAdmin' => $user->role === 'admin',
            // Admin tidak boleh aksi; staff hanya untuk tugasnya; sekretaris boleh semua.
            'canManage' => $user->role === 'sekretaris'
                || ($user->role === 'staff' && (int) $task->staff_id === (int) $user->id),
        ]);
    }

    /**
     * Terima tugas oleh staff.
     */
    public function accept(Task $task): RedirectResponse
    {
        try {
            $this->authorizeTaskStaff($task);

            $this->applyTaskStatus($task, TaskStatus::ACCEPTED->status(), auth()->id());

            NotificationHelper::send(
                $task->created_by,
                'task',
                __('notify.task_accepted_title'),
                __('notify.task_accepted_message', ['staff' => $task->staff?->name, 'title' => $task->title]),
                route('task.show', $task->id)
            );

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Tolak tugas oleh staff.
     */
    public function reject(Request $request, Task $task): RedirectResponse
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->authorizeTaskStaff($task);

            $this->applyTaskStatus($task, TaskStatus::REJECTED->status(), auth()->id(), $request->reason);

            NotificationHelper::send(
                $task->created_by,
                'task',
                __('notify.task_rejected_title'),
                __('notify.task_rejected_message', ['staff' => $task->staff?->name, 'title' => $task->title]),
                route('delegation.show', $task->delegation_id)
            );

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Perbarui progress / status / unggah dokumen.
     */
    public function updateProgress(StoreTaskUpdateRequest $request, Task $task): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $payload = $request->validated();

            $document = null;
            if ($request->hasFile('document')) {
                $filename = time() . '-' . str_replace(' ', '-', $request->file('document')->getClientOriginalName());
                $request->file('document')->storeAs('public/task-documents', $filename);
                $document = 'storage/task-documents/' . $filename;
            }

            TaskUpdate::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'progress' => $payload['progress'],
                'note' => $payload['note'] ?? null,
                'document' => $document,
            ]);

            $newStatus = $request->input('action') === 'review' && $payload['progress'] >= 100
                ? TaskStatus::PENDING_REVIEW->status()
                : ($payload['status'] ?? $this->deriveStatusFromProgress($task, $payload['progress']));

            $this->applyTaskStatus($task, $newStatus, auth()->id(), $payload['note'] ?? null, $payload['progress']);

            ActivityLogHelper::log(
                __('activity.progress_updated', [
                    'user' => auth()->user()->name,
                    'progress' => $payload['progress'] . '%',
                    'title' => $task->title,
                ]),
                'task',
                $task->id,
                ['title' => $task->title]
            );

            DB::commit();

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Verifikasi penyelesaian tugas oleh sekretaris.
     */
    public function verify(Request $request, Task $task): RedirectResponse
    {
        try {
            $user = auth()->user();
            if ($user->role !== 'sekretaris') {
                abort(403, __('delegation.forbidden'));
            }

            if ($task->status !== TaskStatus::PENDING_REVIEW->status()) {
                throw new \Exception(__('task.not_pending_review'));
            }

            $this->applyTaskStatus($task, TaskStatus::DONE->status(), auth()->id(), $request->note);

            NotificationHelper::send(
                $task->staff_id,
                'task',
                __('notify.task_verified_title'),
                __('notify.task_verified_message', ['title' => $task->title]),
                route('task.show', $task->id)
            );

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Unduh lampiran tugas.
     */
    public function download(Task $task)
    {
        $this->authorizeTaskView($task);

        if (!$task->attachment || !Storage::exists('public/' . str_replace('storage/', '', $task->attachment))) {
            abort(404);
        }

        return Storage::download('public/' . str_replace('storage/', '', $task->attachment));
    }

    /**
     * Unduh dokumen lampiran hasil kerja dari pembaruan progres tugas.
     */
    public function downloadUpdate(Task $task, TaskUpdate $update)
    {
        $this->authorizeTaskView($task);

        if (!$update->document || !Storage::exists('public/' . str_replace('storage/', '', $update->document))) {
            abort(404);
        }

        return Storage::download('public/' . str_replace('storage/', '', $update->document));
    }

    protected function applyTaskStatus(Task $task, string $status, int $userId, ?string $note = null, ?int $progress = null): void
    {
        $log = ['title' => $task->title];

        if ($status === TaskStatus::DONE->status()) {
            $task->update([
                'status' => $status,
                'progress' => 100,
                'completed_at' => now(),
                'note' => $note ?? $task->note,
            ]);
            $log['note'] = 'selesai';

            if ((int) $task->created_by !== (int) $userId) {
                NotificationHelper::send(
                    $task->created_by,
                    'task',
                    __('notify.task_done_title'),
                    __('notify.task_done_message', ['title' => $task->title]),
                    route('task.show', $task->id)
                );
            }
        } elseif ($status === TaskStatus::REJECTED->status()) {
            $task->update([
                'status' => $status,
                'note' => $note ?? $task->note,
            ]);
            $log['note'] = 'ditolak';
        } elseif ($status === TaskStatus::LATE->status()) {
            $task->update(['status' => $status]);
            $log['note'] = 'terlambat';
        } else {
            $task->update([
                'status' => $status,
                'progress' => $progress ?? $task->progress,
                'note' => $note ?? $task->note,
            ]);
            $log['note'] = $note;
        }

        ActivityLogHelper::log(
            __('activity.task_status_changed', ['title' => $task->title]),
            'task',
            $task->id,
            $log
        );

        $task->delegation?->syncStatusFromTasks();
    }

    protected function deriveStatusFromProgress(Task $task, int $progress): string
    {
        if ($progress >= 100) return TaskStatus::PENDING_REVIEW->status();

        if ($progress > 0) return $task->status === TaskStatus::NEW->status()
            ? TaskStatus::IN_PROGRESS->status()
            : $task->status;

        return $task->status;
    }

    protected function authorizeTaskView(Task $task): void
    {
        $user = auth()->user();

        if ($user->role === 'staff' && (int) $task->staff_id !== (int) $user->id) {
            abort(403, __('delegation.forbidden'));
        }
    }

    protected function authorizeTaskStaff(Task $task): void
    {
        $user = auth()->user();

        // Admin tidak boleh melakukan aksi staff/sekretaris.
        if ($user->role === 'admin') {
            abort(403, __('delegation.forbidden'));
        }

        if ($user->role === 'staff' && (int) $task->staff_id !== (int) $user->id) {
            abort(403, __('delegation.forbidden'));
        }
    }

    protected function taskStats($user): object
    {
        $base = Task::query();

        if ($user->role === 'staff') {
            $base->forStaff();
        }

        $clone = fn () => clone $base;

        return (object) [
            'baru' => $clone()->new()->count(),
            'dalam_pengerjaan' => $clone()->inProgress()->count(),
            'menunggu_review' => $clone()->pendingReview()->count(),
            'selesai' => $clone()->done()->count(),
            'terlambat' => $clone()->late()->count(),
            'total' => $clone()->count(),
        ];
    }
}