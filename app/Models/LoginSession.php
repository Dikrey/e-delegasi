<?php

namespace App\Models;

use App\Helpers\DeviceHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginSession extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'platform',
        'location',
        'last_activity_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    /**
     * Create a login session record for a freshly authenticated user.
     *
     * @param User $user
     * @param \Illuminate\Http\Request $request
     * @return static
     */
    public static function record(User $user, $request): static
    {
        $session = static::where('user_id', $user->id)
            ->where('session_id', $request->session()->getId())
            ->first();

        if ($session) {
            $session->update(['last_activity_at' => now()]);
            return $session;
        }

        $info = DeviceHelper::detect($request->header('User-Agent'));

        return static::create([
            'user_id' => $user->id,
            'session_id' => $request->session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr($request->header('User-Agent', ''), 0, 500),
            'device' => $info['device'],
            'browser' => $info['browser'],
            'platform' => $info['platform'],
            'location' => DeviceHelper::location($request->ip()),
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Touch the activity timestamp.
     *
     * @return $this
     */
    public function touchActivity(): static
    {
        $this->update(['last_activity_at' => now()]);
        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}