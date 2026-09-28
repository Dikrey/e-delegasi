<?php

namespace App\Models;

use App\Enums\Config as ConfigEnum;
use App\Models\Config;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            return $query
                ->where('name', 'LIKE', '%' . $find . '%')
                ->orWhere('code', 'LIKE', '%' . $find . '%');
        });
    }

    public function scopeRender($query, $search)
    {
        return $query
            ->withCount('users')
            ->search($search)
            ->latest()
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends(['search' => $search]);
    }
}
