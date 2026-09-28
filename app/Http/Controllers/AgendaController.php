<?php

namespace App\Http\Controllers;

use App\Enums\AgendaStatus;
use App\Helpers\ActivityLogHelper;
use App\Helpers\NotificationHelper;
use App\Http\Requests\StoreAgendaRequest;
use App\Models\Agenda;
use App\Models\Delegation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Halaman utama agenda pimpinan (kalender).
     */
    public function index(): View
    {
        return view('pages.agenda-pimpinan.calendar', [
            'staffs' => \App\Models\User::role(\App\Enums\Role::STAFF)->active()->orderBy('name')->get(),
            'delegations' => Delegation::orderBy('title')->get(['id', 'title']),
        ]);
    }

    /**
     * Daftar agenda (tab hari ini / mendatang / selesai).
     */
    public function list(Request $request): View
    {
        $tab = $request->get('tab', 'today');

        $query = Agenda::query()->with(['delegation.letter', 'letter', 'creator']);

        $query = match ($tab) {
            'upcoming' => $query->upcoming(30),
            'done' => $query->done(),
            'all' => $query->orderBy('date', 'desc'),
            default => $query->today(),
        };

        $data = $query->when($request->search, function ($q, $s) {
            return $q->where(function ($q2) use ($s) {
                return $q2
                    ->where('title', 'LIKE', '%' . $s . '%')
                    ->orWhereHas('delegation', fn ($q) => $q->where('title', 'LIKE', '%' . $s . '%'));
            });
        })->paginate(\App\Models\Config::getValueByCode(\App\Enums\Config::PAGE_SIZE))
            ->withQueryString();

        return view('pages.agenda-pimpinan.list', [
            'data' => $data,
            'tab' => $tab,
            'search' => $request->search,
            'stats' => (object) [
                'today' => Agenda::today()->count(),
                'upcoming' => Agenda::upcoming(30)->count(),
                'done' => Agenda::done()->count(),
                'total' => Agenda::count(),
            ],
        ]);
    }

    /**
     * Data JSON untuk FullCalendar.
     */
    public function data(Request $request): JsonResponse
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $status = $request->get('status');
        $type = $request->get('type');

        $query = Agenda::query()->with(['delegation.letter', 'letter']);

        if ($start && $end) {
            $query->whereBetween('date', [$start, $end]);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($type && $type !== 'all') {
            $query->where('agenda_type', $type);
        }

        // scope=mine → staff hanya melihat agenda yang terhubung ke delegasi
        // di mana dirinya benar-benar ditugaskan. Agenda tanpa delegasi
        // (umum) tidak ditampilkan.
        if ($request->get('scope') === 'mine') {
            $userId = auth()->id();

            $query->whereNotNull('delegation_id')
                ->whereHas('delegation.tasks', fn ($task) => $task->where('staff_id', $userId));
        }

        $events = $query->get()->map(function (Agenda $agenda) {
            $color = match ($agenda->status) {
                AgendaStatus::DONE->status() => '#02bc7d',
                AgendaStatus::CANCELLED->status() => '#ff3e1d',
                default => '#696cff',
            };

            $start = $agenda->date->format('Y-m-d');
            if ($agenda->start_time) {
                $start .= 'T' . \Carbon\Carbon::parse($agenda->start_time)->format('H:i:s');
            }

            $end = $agenda->date->format('Y-m-d');
            if ($agenda->end_time) {
                $end .= 'T' . \Carbon\Carbon::parse($agenda->end_time)->format('H:i:s');
            }

            return [
                'id' => $agenda->id,
                'title' => $agenda->title,
                'start' => $start,
                'end' => $end,
                'color' => $color,
                'extendedProps' => [
                    'status' => $agenda->status,
                    'status_label' => $agenda->status_label,
                    'agenda_type' => $agenda->agenda_type,
                    'location' => $agenda->location,
                    'date' => $agenda->formatted_date,
                    'time' => $agenda->time_window,
                    'url' => route('agenda-pimpinan.show', $agenda->id),
                ],
            ];
        })->values();

        // scope=mine → selain agenda, tampilkan juga tugas milik staff pada
        // kalender agar staff melihat seluruh tanggung jawabnya dalam satu layar.
        if ($request->get('scope') === 'mine') {
            $userId = auth()->id();

            $taskEvents = \App\Models\Task::with('delegation.letter')
                ->where('staff_id', $userId)
                ->whereNotNull('deadline')
                ->orderBy('deadline')
                ->get()
                ->map(function (\App\Models\Task $task) {
                    $color = match ($task->status) {
                        'selesai' => '#02bc7d',
                        'menunggu_review' => '#f59e0b',
                        'terlambat' => '#ff3e1d',
                        'ditolak' => '#6b7280',
                        default => '#0f9d8a',
                    };

                    return [
                        'id' => 'task-' . $task->id,
                        'title' => '⚡ ' . $task->title,
                        'start' => $task->deadline->format('Y-m-d'),
                        'color' => $color,
                        'extendedProps' => [
                            'status' => $task->status,
                            'status_label' => $task->status_label,
                            'agenda_type' => 'task',
                            'location' => $task->delegation?->letter?->reference_number,
                            'date' => $task->deadline->isoFormat('dddd, D MMMM YYYY'),
                            'time' => __('task.deadline'),
                            'url' => route('task.show', $task->id),
                        ],
                    ];
                });

            $events = $events->concat($taskEvents)->values();
        }

        return response()->json($events);
    }

    /**
     * Form tambah agenda.
     */
    public function create(Request $request): View
    {
        return view('pages.agenda-pimpinan.form', [
            'edit' => null,
            'delegations' => Delegation::with('letter')->orderBy('title')->get(),
            'preset' => $request->get('delegation_id'),
        ]);
    }

    /**
     * Simpan agenda.
     */
    public function store(StoreAgendaRequest $request): RedirectResponse
    {
        try {
            $payload = $request->validated();

            $agenda = Agenda::create([
                'title' => $payload['title'],
                'description' => $payload['description'] ?? null,
                'agenda_type' => $payload['agenda_type'] ?? 'rapat',
                'delegation_id' => $payload['delegation_id'] ?? null,
                'letter_id' => isset($payload['delegation_id'])
                    ? Delegation::find($payload['delegation_id'])?->letter_id
                    : null,
                'date' => $payload['date'],
                'start_time' => $payload['start_time'] ?? null,
                'end_time' => $payload['end_time'] ?? null,
                'location' => $payload['location'] ?? null,
                'status' => $payload['status'] ?? AgendaStatus::SCHEDULED->status(),
                'created_by' => auth()->id(),
                'note' => $payload['note'] ?? null,
            ]);

            if ($agenda->delegation_id) {
                $agenda->delegation()->update([
                    'deadline' => $agenda->date->format('Y-m-d') . ' ' . ($agenda->end_time ?? '17:00') . ':00',
                ]);
                foreach ($agenda->delegation->tasks as $task) {
                    $task->update(['agenda_id' => $agenda->id]);
                }
            }

            if ($agenda->delegation_id) {
                $staffIds = $agenda->delegation->tasks->pluck('staff_id')->all();
                NotificationHelper::send(
                    $staffIds,
                    'agenda',
                    __('notify.agenda_created_title'),
                    __('notify.agenda_created_message', ['title' => $agenda->title]),
                    route('agenda-pimpinan.show', $agenda->id)
                );
            }

            ActivityLogHelper::log(__('activity.agenda_create'), 'agenda', $agenda->id, ['title' => $agenda->title]);

            return redirect()
                ->route('agenda-pimpinan.show', $agenda->id)
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Detail agenda.
     */
    public function show(Agenda $agenda): View
    {
        $agenda->load(['delegation.tasks.staff', 'delegation.letter', 'letter', 'creator']);

        return view('pages.agenda-pimpinan.show', [
            'data' => $agenda,
        ]);
    }

    /**
     * Form edit agenda.
     */
    public function edit(Agenda $agenda): View
    {
        return view('pages.agenda-pimpinan.form', [
            'edit' => $agenda,
            'delegations' => Delegation::with('letter')->orderBy('title')->get(),
            'preset' => null,
        ]);
    }

    /**
     * Perbarui agenda.
     */
    public function update(StoreAgendaRequest $request, Agenda $agenda): RedirectResponse
    {
        try {
            $payload = $request->validated();

            $agenda->update([
                'title' => $payload['title'],
                'description' => $payload['description'] ?? null,
                'agenda_type' => $payload['agenda_type'] ?? $agenda->agenda_type,
                'delegation_id' => $payload['delegation_id'] ?? $agenda->delegation_id,
                'date' => $payload['date'],
                'start_time' => $payload['start_time'] ?? null,
                'end_time' => $payload['end_time'] ?? null,
                'location' => $payload['location'] ?? null,
                'status' => $payload['status'] ?? $agenda->status,
                'note' => $payload['note'] ?? null,
            ]);

            ActivityLogHelper::log(__('activity.agenda_update'), 'agenda', $agenda->id, ['title' => $agenda->title]);

            return redirect()
                ->route('agenda-pimpinan.show', $agenda->id)
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Hapus agenda.
     */
    public function destroy(Agenda $agenda): RedirectResponse
    {
        try {
            $agenda->delete();

            ActivityLogHelper::log(__('activity.agenda_delete'), 'agenda', null, ['title' => $agenda->title]);

            return redirect()
                ->route('agenda-pimpinan.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Ubah status agenda (selesai/dibatalkan).
     */
    public function changeStatus(Request $request, Agenda $agenda): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:terjadwal,selesai,dibatalkan'],
        ]);

        try {
            $agenda->update(['status' => $request->status]);

            ActivityLogHelper::log(__('activity.agenda_status'), 'agenda', $agenda->id, [
                'title' => $agenda->title,
                'status' => $request->status,
            ]);

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * Reschedule agenda (drag & drop kalender).
     */
    public function reschedule(Request $request, Agenda $agenda): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['nullable'],
        ]);

        try {
            $payload = ['date' => $request->date];
            if ($request->filled('start_time')) {
                $payload['start_time'] = \Carbon\Carbon::parse($request->start_time)->format('H:i:s');
            }
            $agenda->update($payload);

            return response()->json(['status' => true, 'message' => __('menu.general.success')]);
        } catch (\Throwable $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}