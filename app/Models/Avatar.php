<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Avatar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'path',
        'original_file_name',
        'mime_type',
        'size',
    ];

    /**
     * Get the user that owns the avatar.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full URL for the avatar image.
     */
    public function getUrlAttribute(): ?string
    {
        if (empty($this->path)) {
            return null;
        }
        return Storage::url($this->path);
    }

    /**
     * Define the `url` attribute to be appended to the model's array form.
     */
    protected $appends = ['url'];
}
