<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use App\Models\Traits\VerifieClotureComptable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read string $url
 */
class Photo extends Model
{
    use Blameable;
    
    protected $fillable = [
        'user_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'is_profile',
        'disk',
        'caption'
    ];

    protected $casts = [
        'is_profile' => 'boolean',
        'size' => 'integer',
    ];

    protected $appends = ['url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeProfile($query)
    {
        return $query->where('is_profile', true);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk ?? 'public')->url($this->path);
    }

    public function setAsProfile(): void
    {
        DB::transaction(function () {
            self::where('user_id', $this->user_id)
                ->where('is_profile', true)
                ->update(['is_profile' => false]);

            $this->update(['is_profile' => true]);
        });
    }

    protected static function booted()
    {
        static::deleting(function (Photo $photo) {
            Storage::disk($photo->disk ?? 'public')->delete($photo->path);
        });
    }
}
