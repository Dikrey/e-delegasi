<?php

namespace App\Models;

use App\Enums\AgendaStatus;
use App\Enums\Config as ConfigEnum;
use Config as Cfg;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'agenda_type',
        'delegation_id',
        'letter_id',
        'date',
        'start_time',
        'end_time',
        'location',
        'status',
        'created_by',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $appends = [
        'formatted_date',
        'time_window',
        'status_label',
        'agenda_status_label',
        'agenda_type_label',
    ];

    public function getAgendaStatusLabelAttribute(): string
    {
        return AgendaStatus::label($this->status);
    }

    public function getAgendaTypeLabelAttribute(): string
    {
        return __('enums.agenda_type.' . ($this->agenda_type ?? 'lainnya'));
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->isoFormat('dddd, D MMMM YYYY');
    }

    public function getTimeWindowAttribute(): ?string
    {
        if (!$this->start_time) return null;

        $start = \Carbon\Carbon::parse($this->start_time)->format('H:i');
        $end = $this->end_time ? \Carbon\Carbon::parse($this->end_time)->format('H:i') : null;

        return $end ? "{$start} - {$end}" : $start;
    }

    public function getStatusLabelAttribute(): string
    {
        return AgendaStatus::options()[$this->status] ?? $this->status;
    }

    public function delegation(): BelongsTo
    {
        return $this->belongsTo(Delegation::class, 'delegation_id', 'id');
    }

    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class, 'letter_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    public function scopeUpcoming($query, int $days = 14)
    {
        return $query
            ->whereBetween('date', [now()->toDateString(), now()->addDays($days)->toDateString()])
            ->where('status', AgendaStatus::SCHEDULED->status());
    }

    public function scopeDone($query)
    {
        return $query->where('status', AgendaStatus::DONE->status());
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', AgendaStatus::SCHEDULED->status());
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            return $query->where(function ($query) use ($find) {
                return $query
                    ->where('title', 'LIKE', '%' . $find . '%')
                    ->orWhere('location', 'LIKE', '%' . $find . '%')
                    ->orWhereHas('delegation', fn ($q) => $q->where('title', 'LIKE', '%' . $find . '%'))
                    ->orWhereHas('letter', fn ($q) => $q
                        ->where('reference_number', 'LIKE', '%' . $find . '%')
                        ->orWhere('description', 'LIKE', '%' . $find . '%'));
            });
        });
    }

    public function scopeRender($query, $search)
    {
        return $query
            ->with(['delegation.letter', 'letter', 'creator'])
            ->search($search)
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(Cfg::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends(['search' => $search]);
    }
}