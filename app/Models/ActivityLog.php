<?php

namespace App\Models;

use App\Enums\Config as ConfigEnum;
use Config as Cfg;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'reference_id',
        'ip_address',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    protected $appends = [
        'module_label',
    ];

    public function getModuleLabelAttribute(): string
    {
        return __('activity.module.' . ($this->module ?? 'system'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            return $query->where(function ($query) use ($find) {
                return $query
                    ->where('action', 'LIKE', '%' . $find . '%')
                    ->orWhere('module', 'LIKE', '%' . $find . '%')
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'LIKE', '%' . $find . '%'));
            });
        });
    }

    public function scopeRender($query, $search)
    {
        return $query
            ->with('user')
            ->search($search)
            ->latest()
            ->paginate(Cfg::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends(['search' => $search]);
    }
}