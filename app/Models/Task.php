<?php

namespace App\Models;

use App\Enums\Config as ConfigEnum;
use App\Enums\Priority;
use App\Enums\TaskStatus;
use Config as Cfg;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'delegation_id',
        'title',
        'description',
        'staff_id',
        'created_by',
        'priority',
        'task_category_id',
        'deadline',
        'progress',
        'status',
        'agenda_id',
        'attachment',
        'note',
        'completed_at',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer',
    ];

    protected $appends = [
        'formatted_deadline',
        'status_label',
        'priority_label',
        'deadline_status',
        'deadline_status_label',
        'deadline_status_badge',
    ];

    public function getFormattedDeadlineAttribute(): ?string
    {
        return $this->deadline ? $this->deadline->isoFormat('D MMMM YYYY, HH:mm') : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return TaskStatus::label($this->status);
    }

    public function getPriorityLabelAttribute(): string
    {
        return Priority::label($this->priority);
    }

    /**
     * Status tenggat otomatis: normal | approaching | late | done.
     * Mengikuti aturan: >3 hari Normal, <3 hari Mendekati Deadline, terlewat Terlambat, selesai Selesai.
     */
    public function getDeadlineStatusAttribute(): ?string
    {
        if ($this->status === TaskStatus::DONE->status()) return 'done';
        if (!$this->deadline) return null;
        if ($this->deadline->isPast()) return 'late';
        if ($this->deadline->lessThanOrEqualTo(now()->addDays(3))) return 'approaching';

        return 'normal';
    }

    public function getDeadlineStatusLabelAttribute(): ?string
    {
        $key = $this->deadline_status;

        return $key ? __('task.deadline_status.' . $key) : null;
    }

    public function getDeadlineStatusBadgeAttribute(): ?string
    {
        return match ($this->deadline_status) {
            'approaching' => 'bg-label-warning',
            'late' => 'bg-label-danger',
            'done' => 'bg-label-success',
            'normal' => 'bg-label-info',
            default => 'bg-label-secondary',
        };
    }

    public function delegation(): BelongsTo
    {
        return $this->belongsTo(Delegation::class, 'delegation_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class, 'agenda_id', 'id');
    }

    public function taskCategory(): BelongsTo
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id', 'id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(TaskUpdate::class, 'task_id', 'id');
    }

    /**
     * Sumber surat: lewat relasi delegasi.
     */
    public function sourceLetter(): ?Letter
    {
        return $this->delegation?->letter;
    }

    public function scopeForStaff($query, ?int $staffId = null)
    {
        return $query->where('staff_id', $staffId ?? auth()->id());
    }

    public function scopeStatus($query, $status)
    {
        return $query->when($status, fn ($q) => $q->where('status', $status));
    }

    public function scopeNew($query)
    {
        return $query->where('status', TaskStatus::NEW->status());
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', TaskStatus::IN_PROGRESS->status());
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', TaskStatus::PENDING_REVIEW->status());
    }

    public function scopeDone($query)
    {
        return $query->where('status', TaskStatus::DONE->status());
    }

    public function scopeLate($query)
    {
        return $query
            ->whereNotIn('status', [TaskStatus::DONE->status(), TaskStatus::REJECTED->status()])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now());
    }

    public function scopeApproaching($query, int $days = 3)
    {
        return $query
            ->whereNotIn('status', [TaskStatus::DONE->status(), TaskStatus::REJECTED->status(), TaskStatus::LATE->status()])
            ->whereNotNull('deadline')
            ->whereBetween('deadline', [now(), now()->addDays($days)]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            return $query->where(function ($query) use ($find) {
                return $query
                    ->where('title', 'LIKE', '%' . $find . '%')
                    ->orWhereHas('staff', fn ($q) => $q->where('name', 'LIKE', '%' . $find . '%'))
                    ->orWhereHas('delegation.letter', fn ($q) => $q
                        ->where('reference_number', 'LIKE', '%' . $find . '%')
                        ->orWhere('description', 'LIKE', '%' . $find . '%'));
            });
        });
    }

    public function scopeRender($query, $search)
    {
        return $query
            ->with(['delegation.letter', 'staff'])
            ->search($search)
            ->latest()
            ->paginate(Cfg::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends(['search' => $search]);
    }

    /**
     * Tandai task yang melewati deadline sebagai terlambat.
     */
    public static function refreshOverdue(): int
    {
        $affected = static::query()
            ->whereNotIn('status', [TaskStatus::DONE->status(), TaskStatus::REJECTED->status(), TaskStatus::LATE->status()])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->get();

        foreach ($affected as $task) {
            $task->update(['status' => TaskStatus::LATE->status()]);
        }

        return $affected->count();
    }
}