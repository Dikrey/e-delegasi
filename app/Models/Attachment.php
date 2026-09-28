<?php

namespace App\Models;

use App\Enums\Config as ConfigEnum;
use App\Enums\LetterType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'filename',
        'extension',
        'letter_id',
        'user_id',
    ];

    protected $appends = [
        'path_url',
    ];

    /**
     * @return string
     */
    public function getPathUrlAttribute(): string {
        if (!is_null($this->path)) {
            return $this->path;
        }

        return asset('storage/attachments/' . $this->filename);
    }

    public function scopeType($query, LetterType $type)
    {
        return $query->whereHas('letter', function ($query) use ($type) {
            return $query->where('type', $type->type());
        });
    }

    public function scopeIncoming($query)
    {
        return $this->scopeType($query, LetterType::INCOMING);
    }

    public function scopeOutgoing($query)
    {
        return $this->scopeType($query, LetterType::OUTGOING);
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
                            ->whereRaw('LOWER(filename) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereHas('letter', function ($query) use ($keyword) {
                                return $query
                                    ->whereRaw('LOWER(reference_number) LIKE ?', ['%' . strtolower($keyword) . '%'])
                                    ->orWhereRaw('LOWER(agenda_number) LIKE ?', ['%' . strtolower($keyword) . '%'])
                                    ->orWhereRaw('LOWER(from) LIKE ?', ['%' . strtolower($keyword) . '%'])
                                    ->orWhereRaw('LOWER(to) LIKE ?', ['%' . strtolower($keyword) . '%']);
                            });
                    });
                }
            });
        });
    }

    public function scopeRender($query, $search)
    {
        return $query
            ->with(['letter'])
            ->search($search)
            ->latest('created_at')
            ->paginate(Config::getValueByCode(ConfigEnum::PAGE_SIZE))
            ->appends([
                'search' => $search,
            ]);
    }

    /**
     * @return BelongsTo
     */
    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
