<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Agenda;
use App\Models\Delegation;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function delegation(Request $request): View
    {
        $since = $request->get('since');
        $until = $request->get('until');
        $staffId = $request->integer('staff_id') ?: null;

        $query = Delegation::query()->with(['tasks.staff', 'letter', 'creator']);

        if ($since && $until) {
            $query->whereBetween('created_at', [$since . ' 00:00:00', $until . ' 23:59:59']);
        }

        if ($staffId) {
            $query->whereHas('tasks', fn ($q) => $q->where('staff_id', $staffId));
        }

        $data = $query->latest()->paginate(20)->withQueryString();

        return view('pages.report.delegation', [
            'data' => $data,
            'since' => $since,
            'until' => $until,
            'staffId' => $staffId,
            'staffs' => User::role(\App\Enums\Role::STAFF)->active()->orderBy('name')->get(),
            'summary' => (object) [
                'total' => $query->count(),
                'active' => (clone $query)->active()->count(),
                'done' => (clone $query)->done()->count(),
                'late' => (clone $query)->late()->count(),
            ],
        ]);
    }

    public function task(Request $request): View
    {
        $since = $request->get('since');
        $until = $request->get('until');
        $staffId = $request->integer('staff_id') ?: null;
        $status = $request->get('status', 'all');

        $query = Task::query()->with(['delegation.letter', 'staff']);

        if ($since && $until) {
            $query->whereBetween('created_at', [$since . ' 00:00:00', $until . ' 23:59:59']);
        }

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $data = $query->latest()->paginate(20)->withQueryString();

        $summaryBase = Task::query();
        if ($since && $until) $summaryBase->whereBetween('created_at', [$since . ' 00:00:00', $until . ' 23:59:59']);
        if ($staffId) $summaryBase->where('staff_id', $staffId);

        return view('pages.report.task', [
            'data' => $data,
            'since' => $since,
            'until' => $until,
            'staffId' => $staffId,
            'status' => $status,
            'staffs' => User::role(\App\Enums\Role::STAFF)->active()->orderBy('name')->get(),
            'statuses' => TaskStatus::options(),
            'summary' => (object) [
                'total' => (clone $summaryBase)->count(),
                'done' => (clone $summaryBase)->done()->count(),
                'late' => (clone $summaryBase)->late()->count(),
                'in_progress' => (clone $summaryBase)->inProgress()->count(),
                'pending_review' => (clone $summaryBase)->pendingReview()->count(),
            ],
        ]);
    }

    public function agenda(Request $request): View
    {
        $period = $request->get('period', 'month');

        $query = Agenda::query()->with(['delegation.letter', 'creator']);

        $query = match ($period) {
            'day' => $query->today(),
            'week' => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]),
            'done' => $query->done(),
            'upcoming' => $query->upcoming(30),
            default => $query->whereYear('date', now()->year)->whereMonth('date', now()->month),
        };

        $data = $query->orderBy('date')->paginate(20)->withQueryString();

        return view('pages.report.agenda', [
            'data' => $data,
            'period' => $period,
            'summary' => (object) [
                'total' => Agenda::count(),
                'this_month' => Agenda::whereMonth('date', now()->month)->whereYear('date', now()->year)->count(),
                'done' => Agenda::done()->count(),
                'upcoming' => Agenda::upcoming(30)->count(),
            ],
        ]);
    }

    public function exportDelegation(Request $request): StreamedResponse
    {
        $since = $request->get('since');
        $until = $request->get('until');
        $staffId = $request->integer('staff_id') ?: null;

        $query = Delegation::query()->with(['tasks.staff', 'letter', 'creator']);

        if ($since && $until) {
            $query->whereBetween('created_at', [$since . ' 00:00:00', $until . ' 23:59:59']);
        }

        if ($staffId) {
            $query->whereHas('tasks', fn ($q) => $q->where('staff_id', $staffId));
        }

        $rows = $query->latest()->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                __('delegation.delegation'),
                __('delegation.source_letter'),
                __('delegation.creator'),
                __('delegation.staff'),
                __('delegation.priority'),
                __('delegation.deadline'),
                __('delegation.progress'),
                __('delegation.status'),
                __('model.general.created_at'),
            ]);

            foreach ($rows as $delegation) {
                $avg = round($delegation->tasks->where('status', '<>', 'ditolak')->avg('progress') ?? 0);

                fputcsv($handle, [
                    $delegation->title,
                    $delegation->letter?->reference_number ?? '',
                    $delegation->creator?->name ?? '',
                    $delegation->tasks->map(fn ($t) => $t->staff?->name)->filter()->implode(', '),
                    $delegation->priority_label,
                    $delegation->formatted_deadline ?? '',
                    $avg . '%',
                    $delegation->status_label,
                    $delegation->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'laporan-delegasi-' . now()->format('Y-m-d-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportTask(Request $request): StreamedResponse
    {
        $since = $request->get('since');
        $until = $request->get('until');
        $staffId = $request->integer('staff_id') ?: null;
        $status = $request->get('status', 'all');

        $query = Task::query()->with(['delegation.letter', 'staff', 'taskCategory']);

        if ($since && $until) {
            $query->whereBetween('created_at', [$since . ' 00:00:00', $until . ' 23:59:59']);
        }

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $rows = $query->latest()->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                __('task.task'),
                __('delegation.delegation'),
                __('delegation.source_letter'),
                __('delegation.staff'),
                __('delegation.task_category'),
                __('delegation.priority'),
                __('delegation.deadline'),
                __('delegation.progress'),
                __('delegation.status'),
                __('model.general.created_at'),
            ]);

            foreach ($rows as $task) {
                fputcsv($handle, [
                    $task->title,
                    $task->delegation?->title ?? '',
                    $task->delegation?->letter?->reference_number ?? '',
                    $task->staff?->name ?? '',
                    $task->taskCategory?->name ?? '',
                    $task->priority_label,
                    $task->formatted_deadline ?? '',
                    $task->progress . '%',
                    $task->status_label,
                    $task->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'laporan-tugas-' . now()->format('Y-m-d-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportAgenda(Request $request): StreamedResponse
    {
        $period = $request->get('period', 'month');

        $query = Agenda::query()->with(['delegation.letter', 'creator']);

        $query = match ($period) {
            'day' => $query->today(),
            'week' => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]),
            'done' => $query->done(),
            'upcoming' => $query->upcoming(30),
            default => $query->whereYear('date', now()->year)->whereMonth('date', now()->month),
        };

        $rows = $query->orderBy('date')->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                __('delegation.agenda_title'),
                __('delegation.agenda_date'),
                __('delegation.start_time'),
                __('delegation.end_time'),
                __('delegation.agenda_type'),
                __('delegation.location'),
                __('delegation.status'),
                __('model.general.created_at'),
            ]);

            foreach ($rows as $agenda) {
                fputcsv($handle, [
                    $agenda->title,
                    $agenda->date?->format('Y-m-d'),
                    $agenda->start_time,
                    $agenda->end_time,
                    $agenda->agenda_type_label,
                    $agenda->location ?? '',
                    $agenda->status_label,
                    $agenda->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'laporan-agenda-' . now()->format('Y-m-d-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}