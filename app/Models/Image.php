<?php

// app/Models/Image.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
// A importação de Str não é mais estritamente necessária para esta parte,
// mas pode ser útil para outras operações futuras, então podemos mantê-la ou remover se preferir.
// use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_id',
        'path_original',
        'path_thumb',
        'original_file_name',
        'watermark_applied',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'watermark_applied' => 'boolean',
    ];

    // Adiciona os atributos "virtuais" ao array JSON da Model
    protected $appends = [
        'thumbnail_url',
        'original_url',
        'watermarked_url',
    ];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }

    /**
     * Get the URL for the image thumbnail.
     * Supõe que 'path_thumb' armazena APENAS o caminho relativo (ex: 'galleries/1/thumbs/image.jpg').
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (empty($this->path_thumb)) {
            return null;
        }
        return Storage::url($this->path_thumb);
    }

    /**
     * Get the URL for the original image.
     * Supõe que 'path_original' armazena APENAS o caminho relativo.
     */
    public function getOriginalUrlAttribute(): ?string
    {
        if (empty($this->path_original)) {
            return null;
        }
        return Storage::url($this->path_original);
    }

    /**
     * Get the URL for the watermarked image.
     * Esta URL será usada para as imagens que os clientes veem.
     * Supõe que 'watermarked_path' no metadata será o caminho relativo.
     */
    public function getWatermarkedUrlAttribute(): ?string
    {
        if (isset($this->metadata['watermarked_path']) && !empty($this->metadata['watermarked_path'])) {
            return Storage::url($this->metadata['watermarked_path']);
        }
        return null;
    }
}
