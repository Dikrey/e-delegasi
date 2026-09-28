<?php

namespace App\Console\Commands;

use App\Enums\AgendaStatus;
use App\Enums\Role;
use App\Helpers\NotificationHelper;
use App\Models\Agenda;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class RefreshTaskStatus extends Command
{
    protected $signature = 'delegasi:refresh-status';

    protected $description = 'Perbarui status task yang terlambat, kirim notifikasi deadline & agenda mendatang.';

    public function handle(): int
    {
        // 1. Tandai task yang melewati deadline sebagai terlambat + notifikasi.
        $overdueTasks = Task::query()
            ->whereNotIn('status', [
                \App\Enums\TaskStatus::DONE->status(),
                \App\Enums\TaskStatus::REJECTED->status(),
                \App\Enums\TaskStatus::LATE->status(),
            ])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->get();

        $overdue = Task::refreshOverdue();

        foreach ($overdueTasks as $task) {
            NotificationHelper::send(
                array_unique(array_filter([$task->staff_id, $task->created_by])),
                'deadline',
                __('notify.task_late_title'),
                __('notify.task_late_message', ['title' => $task->title]),
                route('task.show', $task->id)
            );
        }

        // 2. Notifikasi task mendekati deadline (sisa <= 3 hari).
        $approaching = Task::approaching(3)->get();

        foreach ($approaching as $task) {
            NotificationHelper::send(
                $task->staff_id,
                'deadline',
                __('notify.deadline_close_title'),
                __('notify.deadline_close_message', ['title' => $task->title, 'deadline' => $task->formatted_deadline]),
                route('task.show', $task->id)
            );

            NotificationHelper::send(
                $task->created_by,
                'deadline',
                __('notify.deadline_close_title'),
                __('notify.deadline_close_message', ['title' => $task->title, 'deadline' => $task->formatted_deadline]),
                route('task.show', $task->id)
            );
        }

        // 3. Notifikasi agenda akan dimulai (hari ini).
        $todayAgendas = Agenda::today()
            ->where('status', AgendaStatus::SCHEDULED->status())
            ->with('delegation.tasks')
            ->get();

        foreach ($todayAgendas as $agenda) {
            $userIds = [$agenda->created_by];

            foreach ($agenda->delegation?->tasks ?? [] as $task) {
                $userIds[] = $task->staff_id;
            }

            NotificationHelper::send(
                array_unique($userIds),
                'agenda',
                __('notify.agenda_today_title'),
                __('notify.agenda_today_message', ['title' => $agenda->title, 'time' => $agenda->time_window ?? '-', 'location' => $agenda->location ?? '-']),
                route('agenda-pimpinan.show', $agenda->id)
            );
        }

        $this->info("Overdue: {$overdue} | Approaching: {$approaching->count()} | Today Agenda: {$todayAgendas->count()}");

        return self::SUCCESS;
    }
}