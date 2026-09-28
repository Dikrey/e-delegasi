<?php

namespace App\Models;

use App\Enums\Config as ConfigEnum;
use App\Models\Config;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'description',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'task_category_id', 'id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            return $query->where('name', 'LIKE', '%' . $find . '%');
        });
    }

    public function scopeRender($query, $search)
    {
        return $query
            ->withCount('tasks')
            ->search($search)
            ->latest()
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends(['search' => $search]);
    }
}
