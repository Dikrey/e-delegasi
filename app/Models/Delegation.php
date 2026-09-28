<?php

namespace App\Models;

use App\Enums\DelegationStatus;
use App\Enums\Priority;
use Config as Cfg;
use App\Enums\Config as ConfigEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delegation extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_id',
        'title',
        'description',
        'instruction',
        'priority',
        'deadline',
        'status',
        'attachment',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    protected $appends = [
        'formatted_deadline',
        'status_label',
        'priority_label',
    ];

    public function getFormattedDeadlineAttribute(): ?string
    {
        return $this->deadline ? $this->deadline->isoFormat('D MMMM YYYY, HH:mm') : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return DelegationStatus::label($this->status);
    }

    public function getPriorityLabelAttribute(): string
    {
        return Priority::label($this->priority);
    }

    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class, 'letter_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'delegation_id', 'id');
    }

    public function agenda(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Agenda::class, 'delegation_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            DelegationStatus::DRAFT->status(),
            DelegationStatus::DONE->status(),
            DelegationStatus::REJECTED->status(),
        ]);
    }

    public function scopeDone($query)
    {
        return $query->where('status', DelegationStatus::DONE->status());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', DelegationStatus::DRAFT->status());
    }

    public function scopeLate($query)
    {
        return $query->whereNotIn('status', [
            DelegationStatus::DONE->status(),
            DelegationStatus::REJECTED->status(),
            DelegationStatus::DRAFT->status(),
        ])->whereNotNull('deadline')->where('deadline', '<', now());
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            return $query->where(function ($query) use ($find) {
                return $query
                    ->where('title', 'LIKE', '%' . $find . '%')
                    ->orWhere('description', 'LIKE', '%' . $find . '%')
                    ->orWhereHas('letter', function ($query) use ($find) {
                        return $query
                            ->where('reference_number', 'LIKE', '%' . $find . '%')
                            ->orWhere('description', 'LIKE', '%' . $find . '%');
                    })
                    ->orWhereHas('tasks', function ($query) use ($find) {
                        return $query->whereHas('staff', function ($query) use ($find) {
                            return $query->where('name', 'LIKE', '%' . $find . '%');
                        });
                    });
            });
        });
    }

    public function scopeRender($query, $search)
    {
        return $query
            ->with(['tasks.staff', 'letter'])
            ->search($search)
            ->latest()
            ->paginate(Cfg::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends(['search' => $search]);
    }

    /**
     * Sinkronisasi status delegasi berdasarkan task-nya.
     */
    public function syncStatusFromTasks(): void
    {
        if ($this->status === DelegationStatus::DONE->status()) {
            return;
        }

        $tasks = $this->tasks()->get();

        if ($tasks->isEmpty()) {
            return;
        }

        if ($tasks->contains('status', 'ditolak')) {
            $newStatus = DelegationStatus::REJECTED->status();
        } elseif ($tasks->every(fn ($task) => $task->status === 'selesai')) {
            $newStatus = DelegationStatus::DONE->status();
        } elseif ($tasks->contains(fn ($task) => in_array($task->status, ['dalam_pengerjaan', 'menunggu_review']))) {
            $newStatus = DelegationStatus::IN_PROGRESS->status();
        } elseif ($tasks->contains(fn ($task) => in_array($task->status, ['diterima', 'baru']))) {
            $newStatus = DelegationStatus::ACCEPTED->status();
        } else {
            $newStatus = $this->status;
        }

        if ($newStatus !== $this->status) {
            $this->update(['status' => $newStatus]);

            if ($newStatus === DelegationStatus::DONE->status() && $this->letter_id) {
                $this->letter()?->update([
                    'verification_status' => \App\Enums\LetterVerification::DELEGATED->value,
                ]);
            }
        }
    }
}