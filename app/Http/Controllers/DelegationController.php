<?php

namespace App\Http\Controllers;

use App\Enums\DelegationStatus;
use App\Enums\LetterVerification;
use App\Helpers\ActivityLogHelper;
use App\Helpers\NotificationHelper;
use App\Http\Requests\StoreDelegationRequest;
use App\Http\Requests\UpdateDelegationRequest;
use App\Models\Agenda;
use App\Models\Delegation;
use App\Models\Letter;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DelegationController extends Controller
{
    /**
     * Daftar semua delegasi.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status');

        $sortable = ['title', 'priority', 'deadline', 'created_at'];
        $sort = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'created_at';
        $direction = in_array($request->get('direction'), ['asc', 'desc']) ? $request->get('direction') : 'desc';

        $query = Delegation::query()->with(['tasks.staff', 'letter', 'creator']);

        if ($status && $status !== 'all') {
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'done') {
                $query->done();
            } elseif ($status === 'late') {
                $query->late();
            } elseif ($status === 'draft') {
                $query->draft();
            }
        }

        $data = $query->when($request->search, function ($q, $s) {
            return $q->where(function ($q2) use ($s) {
                return $q2
                    ->where('title', 'LIKE', '%' . $s . '%')
                    ->orWhereHas('letter', fn ($q) => $q->where('reference_number', 'LIKE', '%' . $s . '%'));
            });
        })->orderBy($sort, $direction)->paginate(\App\Models\Config::getValueByCode(\App\Enums\Config::PAGE_SIZE))
            ->withQueryString();

        return view('pages.delegation.index', [
            'data' => $data,
            'search' => $request->search,
            'status' => $status ?? 'all',
            'sort' => $sort,
            'direction' => $direction,
            'stats' => (object) [
                'total' => Delegation::count(),
                'active' => Delegation::active()->count(),
                'done' => Delegation::done()->count(),
                'late' => Delegation::late()->count(),
                'draft' => Delegation::draft()->count(),
            ],
            'today' => (object) [
                'created' => Delegation::whereDate('created_at', now()->toDateString())->count(),
                'deadline' => Delegation::whereNotNull('deadline')
                    ->whereDate('deadline', now()->toDateString())
                    ->whereNotIn('status', [DelegationStatus::DONE->status(), DelegationStatus::REJECTED->status()])
                    ->count(),
                'task' => Task::whereNotNull('deadline')
                    ->whereDate('deadline', now()->toDateString())
                    ->whereNotIn('status', ['selesai', 'ditolak'])
                    ->count(),
                'agenda' => Agenda::whereDate('date', now()->toDateString())->count(),
            ],
        ]);
    }

    /**
     * Halaman buat delegasi.
     */
    public function create(Request $request): View
    {
        $letterId = $request->integer('letter_id') ?: null;

        return view('pages.delegation.create', [
            'letters' => $this->availableLetters(),
            'staffs' => User::role(\App\Enums\Role::STAFF)->active()->orderBy('name')->get(),
            'selectedLetter' => $letterId ? Letter::with('classification')->find($letterId) : null,
            'edit' => null,
        ]);
    }

    /**
     * Simpan delegasi baru + task + agenda otomatis.
     */
    public function store(StoreDelegationRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $payload = $request->validated();

            $delegation = new Delegation([
                'letter_id' => $payload['letter_id'] ?? null,
                'title' => $payload['title'],
                'description' => $payload['description'] ?? null,
                'instruction' => $payload['instruction'] ?? null,
                'priority' => $payload['priority'],
                'deadline' => $payload['deadline'] ?? null,
                'created_by' => auth()->id(),
            ]);

            if ($request->hasFile('attachment')) {
                $delegation->attachment = $this->storeFile($request->file('attachment'), 'delegations');
            }

            $delegation->save();

            // Buat task untuk setiap staff.
            $staffIds = $payload['staff_ids'] ?? [];
            if (!empty($staffIds)) {
                foreach ($staffIds as $staffId) {
                    Task::create([
                        'delegation_id' => $delegation->id,
                        'title' => $payload['title'],
                        'description' => $payload['description'] ?? null,
                        'staff_id' => $staffId,
                        'created_by' => auth()->id(),
                        'priority' => $payload['priority'],
                        'task_category_id' => $payload['task_category_id'] ?? null,
                        'deadline' => $payload['deadline'] ?? null,
                        'status' => 'baru',
                    ]);
                }
            }

            // Agenda otomatis bila dipilih.
            $agenda = null;
            if (!empty($payload['add_to_agenda']) && !empty($payload['agenda_date'])) {
                $agenda = Agenda::create([
                    'title' => $payload['agenda_title'] ?? $payload['title'],
                    'description' => $payload['description'] ?? null,
                    'agenda_type' => $payload['agenda_type'] ?? 'rapat',
                    'delegation_id' => $delegation->id,
                    'letter_id' => $payload['letter_id'] ?? null,
                    'date' => $payload['agenda_date'],
                    'start_time' => $payload['start_time'] ?? null,
                    'end_time' => $payload['end_time'] ?? null,
                    'location' => $payload['location'] ?? null,
                    'status' => 'terjadwal',
                    'created_by' => auth()->id(),
                    'note' => $payload['note'] ?? null,
                ]);

                foreach ($delegation->tasks as $task) {
                    $task->update(['agenda_id' => $agenda->id]);
                }
            }

            // Update status verifikasi surat.
            if ($delegation->letter_id) {
                $delegation->letter()->update([
                    'verification_status' => LetterVerification::DELEGATED->value,
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                ]);
            }

            $sendNow = !empty($payload['send_now']) || $request->boolean('send_now');
            if ($sendNow && !$delegation->tasks->isEmpty()) {
                $delegation->update(['status' => DelegationStatus::SENT->status()]);

                foreach ($delegation->tasks as $task) {
                    NotificationHelper::send(
                        $task->staff_id,
                        'task',
                        __('notify.task_new_title'),
                        __('notify.task_new_message', ['title' => $task->title]),
                        route('task.show', $task->id)
                    );
                }

                if ($agenda) {
                    NotificationHelper::send(
                        auth()->id(),
                        'agenda',
                        __('notify.agenda_created_title'),
                        __('notify.agenda_created_message', ['title' => $agenda->title]),
                        route('agenda-pimpinan.show', $agenda->id)
                    );
                }

                ActivityLogHelper::log(__('activity.delegation_send'), 'delegation', $delegation->id, ['title' => $delegation->title]);
            }

            ActivityLogHelper::log(__('activity.delegation_create'), 'delegation', $delegation->id, ['title' => $delegation->title]);

            DB::commit();

            return redirect()
                ->route('delegation.show', $delegation->id)
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Detail delegasi.
     */
    public function show(Delegation $delegation): View
    {
        $delegation->load(['letter', 'creator', 'tasks.staff', 'tasks.updates.user', 'agenda']);

        $timeline = $this->buildTimeline($delegation);

        return view('pages.delegation.show', [
            'data' => $delegation,
            'timeline' => $timeline,
        ]);
    }

    /**
     * Form edit delegasi (draft).
     */
    public function edit(Delegation $delegation): View
    {
        return view('pages.delegation.create', [
            'letters' => $this->availableLetters(),
            'staffs' => User::role(\App\Enums\Role::STAFF)->active()->orderBy('name')->get(),
            'selectedLetter' => $delegation->letter,
            'edit' => $delegation->load('tasks'),
        ]);
    }

    /**
     * Perbarui delegasi.
     */
    public function update(UpdateDelegationRequest $request, Delegation $delegation): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $payload = $request->validated();

            $delegation->fill([
                'letter_id' => $payload['letter_id'] ?? $delegation->letter_id,
                'title' => $payload['title'],
                'description' => $payload['description'] ?? null,
                'instruction' => $payload['instruction'] ?? null,
                'priority' => $payload['priority'],
                'deadline' => $payload['deadline'] ?? $delegation->deadline,
            ]);

            if ($request->hasFile('attachment')) {
                if ($delegation->attachment) {
                    Storage::delete('public/' . str_replace('storage/', '', $delegation->attachment));
                }
                $delegation->attachment = $this->storeFile($request->file('attachment'), 'delegations');
            }

            $delegation->save();

            // Sinkronkan staff pada task: hapus yang dicabut, tambah yang baru.
            $staffIds = array_map('intval', $payload['staff_ids'] ?? []);
            $currentStaffIds = $delegation->tasks->pluck('staff_id')->map(fn ($id) => (int) $id)->all();

            if (!empty(array_diff($staffIds, $currentStaffIds)) || !empty(array_diff($currentStaffIds, $staffIds))) {
                $delegation->tasks()->delete();
                foreach ($staffIds as $staffId) {
                    Task::create([
                        'delegation_id' => $delegation->id,
                        'title' => $delegation->title,
                        'description' => $delegation->description,
                        'staff_id' => $staffId,
                        'created_by' => auth()->id(),
                        'priority' => $delegation->priority,
                        'task_category_id' => $payload['task_category_id'] ?? $delegation->tasks->first()?->task_category_id,
                        'deadline' => $delegation->deadline,
                        'status' => 'baru',
                    ]);
                }
                $delegation->update(['status' => DelegationStatus::DRAFT->status()]);
            }

            ActivityLogHelper::log(__('activity.delegation_update'), 'delegation', $delegation->id, ['title' => $delegation->title]);

            DB::commit();

            return redirect()
                ->route('delegation.show', $delegation->id)
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Hapus delegasi.
     */
    public function destroy(Request $request, Delegation $delegation): RedirectResponse
    {
        try {
            if ($delegation->attachment) {
                Storage::delete('public/' . str_replace('storage/', '', $delegation->attachment));
            }
            $delegation->delete();

            ActivityLogHelper::log(__('activity.delegation_delete'), 'delegation', null, ['title' => $delegation->title]);

            $redirectTo = $request->input('redirect_to');
            $target = $redirectTo && \Illuminate\Support\Str::startsWith($redirectTo, ['http'])
                ? $redirectTo
                : route('delegation.index');

            return redirect($target)->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Kirim delegasi (draft -> dikirim).
     */
    public function send(Delegation $delegation): RedirectResponse
    {
        try {
            if ($delegation->status !== DelegationStatus::DRAFT->status()) {
                throw new \Exception(__('delegation.not_draft'));
            }

            if ($delegation->tasks->isEmpty()) {
                throw new \Exception(__('delegation.no_staff'));
            }

            $delegation->update(['status' => DelegationStatus::SENT->status()]);

            foreach ($delegation->tasks as $task) {
                NotificationHelper::send(
                    $task->staff_id,
                    'task',
                    __('notify.task_new_title'),
                    __('notify.task_new_message', ['title' => $task->title]),
                    route('task.show', $task->id)
                );
            }

            ActivityLogHelper::log(__('activity.delegation_send'), 'delegation', $delegation->id, ['title' => $delegation->title]);

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Monitoring delegasi (papan).
     */
    public function monitoring(Request $request): View
    {
        $status = $request->get('status', 'all');

        $query = Delegation::query()->with(['tasks.staff', 'letter', 'creator']);

        if ($status === 'active') $query->active();
        if ($status === 'done') $query->done();
        if ($status === 'late') $query->late();

        $data = $query->latest()->paginate(20)->withQueryString();

        return view('pages.delegation.monitoring', [
            'data' => $data,
            'status' => $status,
            'stats' => (object) [
                'active' => Delegation::active()->count(),
                'done' => Delegation::done()->count(),
                'late' => Delegation::late()->count(),
            ],
        ]);
    }

    /**
     * Delegasikan ulang delegasi yang ditolak.
     */
    public function reDelegate(Delegation $delegation): RedirectResponse
    {
        if ($delegation->status !== DelegationStatus::REJECTED->status()) {
            return redirect()->route('delegation.show', $delegation->id);
        }

        return redirect()
            ->route('delegation.create', [
                'letter_id' => $delegation->letter_id,
                'title' => $delegation->title,
            ]);
    }

    protected function availableLetters()
    {
        return Letter::incoming()
            ->latest('letter_date')
            ->get();
    }

    protected function storeFile($file, string $folder): string
    {
        $filename = time() . '-' . str_replace(' ', '-', $file->getClientOriginalName());
        $file->storeAs('public/' . $folder, $filename);
        return 'storage/' . $folder . '/' . $filename;
    }

    protected function buildTimeline(Delegation $delegation): array
    {
        $events = [];

        $events[] = [
            'time' => $delegation->created_at,
            'title' => __('activity.delegation_create', ['title' => $delegation->title]),
            'desc' => $delegation->title,
            'icon' => 'bx-plus-circle',
            'color' => 'primary',
        ];

        foreach ($delegation->tasks as $task) {
            $events[] = [
                'time' => $task->created_at,
                'title' => __('activity.task_sent', ['staff' => $task->staff?->name, 'title' => $task->title]),
                'desc' => $task->title,
                'icon' => 'bx-send',
                'color' => 'info',
            ];

            foreach ($task->updates as $update) {
                $events[] = [
                    'time' => $update->created_at,
                    'title' => __('activity.progress_updated', [
                        'user' => $update->user?->name,
                        'progress' => $update->progress . '%',
                        'title' => $task->title,
                    ]),
                    'desc' => $update->note,
                    'icon' => 'bx-loader-circle',
                    'color' => 'warning',
                ];
            }

            if ($task->completed_at) {
                $events[] = [
                    'time' => $task->completed_at,
                    'title' => __('activity.task_done', ['staff' => $task->staff?->name, 'title' => $task->title]),
                    'desc' => $task->title,
                    'icon' => 'bx-check-circle',
                    'color' => 'success',
                ];
            }

            if ($task->status === 'ditolak') {
                $events[] = [
                    'time' => $task->updated_at,
                    'title' => __('activity.task_rejected', ['staff' => $task->staff?->name, 'title' => $task->title]),
                    'desc' => $task->title,
                    'icon' => 'bx-x-circle',
                    'color' => 'danger',
                ];
            }
        }

        return collect($events)
            ->sortByDesc('time')
            ->values()
            ->all();
    }
}