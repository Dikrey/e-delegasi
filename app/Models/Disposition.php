<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'to',
        'due_date',
        'received_at',
        'content',
        'note',
        'letter_status',
        'letter_id',
        'user_id',
        'forwarded_to',
        'forwarded_to_custom',
        'honor',
        'honor_custom',
        'instruction',
        'direction',
        'is_received',
        'verification_note',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'forwarded_to' => 'array',
        'honor' => 'array',
        'is_received' => 'boolean',
        'due_date' => 'date',
        'received_at' => 'date',
        'verified_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_due_date',
        'formatted_received_at',
        'forwarded_labels',
        'honor_labels',
    ];

    public function getFormattedDueDateAttribute(): string {
        return Carbon::parse($this->due_date)->isoFormat('dddd, D MMMM YYYY');
    }

    public function getFormattedReceivedAtAttribute(): ?string {
        return $this->received_at ? Carbon::parse($this->received_at)->isoFormat('dddd, D MMMM YYYY') : null;
    }

    /**
     * Human readable labels for "Diteruskan kepada".
     *
     * @return array
     */
    public function getForwardedLabelsAttribute(): array
    {
        $options = [
            'sekretaris' => __('model.disposition.forwarded_to_options.sekretaris'),
            'kabid_tanaman_pangan' => __('model.disposition.forwarded_to_options.kabid_tanaman_pangan'),
            'kabid_hortikultura' => __('model.disposition.forwarded_to_options.kabid_hortikultura'),
            'kabid_peternakan_hewan' => __('model.disposition.forwarded_to_options.kabid_peternakan_hewan'),
            'kabid_perkebunan' => __('model.disposition.forwarded_to_options.kabid_perkebunan'),
            'kabid_prasarana_penyuluhan' => __('model.disposition.forwarded_to_options.kabid_prasarana_penyuluhan'),
            'kabid_ketahanan_pangan' => __('model.disposition.forwarded_to_options.kabid_ketahanan_pangan'),
        ];

        $labels = collect($this->forwarded_to ?? [])
            ->map(fn ($key) => $options[$key] ?? $key)
            ->values()
            ->all();

        if (!empty($this->forwarded_to_custom)) {
            $labels[] = $this->forwarded_to_custom;
        }

        return $labels;
    }

    /**
     * Human readable labels for "Dengan hormat harap".
     *
     * @return array
     */
    public function getHonorLabelsAttribute(): array
    {
        $options = [
            'tanggapan_saran' => __('model.disposition.honor_options.tanggapan_saran'),
            'proses_lebih_lanjut' => __('model.disposition.honor_options.proses_lebih_lanjut'),
            'koordinasi_konfirmasikan' => __('model.disposition.honor_options.koordinasi_konfirmasikan'),
            'tindak_lanjut' => __('model.disposition.honor_options.tindak_lanjut'),
            'hadir_mewakili' => __('model.disposition.honor_options.hadir_mewakili'),
        ];

        $labels = collect($this->honor ?? [])
            ->map(fn ($key) => $options[$key] ?? $key)
            ->values()
            ->all();

        if (!empty($this->honor_custom)) {
            $labels[] = $this->honor_custom;
        }

        return $labels;
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    public function scopeYesterday($query)
    {
        return $query->whereDate('created_at', now()->addDays(-1));
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            $find = trim($find);
            $keywords = preg_split('/\s+/', $find);

            return $query->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $keyword = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);

                    $query->where(function ($query) use ($keyword) {
                        return $query
                            ->whereRaw('LOWER(content) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereRaw('LOWER(note) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereRaw('LOWER(to) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereHas('letter', function ($query) use ($keyword) {
                                return $query
                                    ->whereRaw('LOWER(reference_number) LIKE ?', ['%' . strtolower($keyword) . '%'])
                                    ->orWhereRaw('LOWER(from) LIKE ?', ['%' . strtolower($keyword) . '%'])
                                    ->orWhereRaw('LOWER(to) LIKE ?', ['%' . strtolower($keyword) . '%']);
                            });
                    });
                }
            });
        });
    }

    public function scopeRender($query, Letter $letter, $search)
    {
        $pageSize = Config::code(\App\Enums\Config::PAGE_SIZE)->first();
        return $query
            ->with(['user', 'status', 'letter', 'verifier'])
            ->search($search)
            ->when($letter, function ($query, $letter) {
                return $query
                    ->where('letter_id', $letter->id);
            })
            ->latest('created_at')
            ->paginate($pageSize->value)
            ->appends([
                'search' => $search,
            ]);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(LetterStatus::class, 'letter_status', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class, 'letter_id', 'id');
    }
}
