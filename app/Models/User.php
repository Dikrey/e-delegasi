<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use App\Enums\Config as ConfigEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'department_id',
        'is_active',
        'profile_picture',
        'session_version',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user's profile picture
     *
     * @return Attribute
     */
    public function profilePicture(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value) return $value;

                $url = 'https://ui-avatars.com/api/?background=6D67E4&color=fff&name=';
                return $url . urlencode($this->name);
            },
        );
    }

    /**
     * Human readable label for the role.
     *
     * @return string
     */
    public function getRoleLabelAttribute(): string
    {
        return match (strtolower($this->role)) {
            'admin' => __('model.user.admin'),
            'sekretaris' => __('model.user.sekretaris'),
            default => __('model.user.staff'),
        };
    }

    public function getIsAdminAttribute(): bool
    {
        return strtolower($this->role) === 'admin';
    }

    public function getIsSekretarisAttribute(): bool
    {
        return strtolower($this->role) === 'sekretaris';
    }

    public function getIsStaffAttribute(): bool
    {
        return strtolower($this->role) === 'staff';
    }

    /**
     * Unit/Bagian tempat user bertugas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function loginSessions(): HasMany
    {
        return $this->hasMany(LoginSession::class);
    }

    /**
     * Delegasi yang dibuat oleh user (sekretaris).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function delegatedTasks(): HasMany
    {
        return $this->hasMany(\App\Models\Task::class, 'created_by', 'id');
    }

    /**
     * Tugas yang diterima oleh user (staff).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(\App\Models\Task::class, 'staff_id', 'id');
    }

    /**
     * Delegasi yang dibuat oleh user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function delegations(): HasMany
    {
        return $this->hasMany(\App\Models\Delegation::class, 'created_by', 'id');
    }

    /**
     * Notifikasi user.
     *
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(\App\Models\Notification::class, 'user_id', 'id');
    }

    /**
     * Force every device of this user to sign out.
     *
     * @return void
     */
    public function revokeAllSessions(): void
    {
        $this->loginSessions()->delete();
        $this->increment('session_version');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, Role $role)
    {
        return $query->where('role', $role->status());
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($query, $find) {
            return $query->where(function ($query) use ($find) {
                return $query
                    ->where('name', 'LIKE', '%' . $find . '%')
                    ->orWhereRaw('LOWER(phone) = ?', [strtolower($find)])
                    ->orWhereRaw('LOWER(email) = ?', [strtolower($find)]);
            });
        });
    }

    public function scopeRender($query, $search)
    {
        return $query
            ->search($search)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends([
                'search' => $search,
            ]);
    }
}