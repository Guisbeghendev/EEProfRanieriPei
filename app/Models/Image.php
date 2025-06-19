<?php

// app/Models/Image.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Importação CRÍTICA adicionada aqui

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_id',
        'path_original',
        'path_thumb',
        'original_file_name', // CORRIGIDO: Alinhado com sua migração
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
     * Agora, o Job vai salvar APENAS O CAMINHO RELATIVO, e este acessador irá gerar a URL COMPLETA.
     */
    public function getThumbnailUrlAttribute()
    {
        // Se o path_thumb já está vazio ou nulo por algum motivo, não tente gerar a URL
        if (empty($this->path_thumb)) {
            return null;
        }

        // Certifica-se de que path_thumb não é uma URL completa já
        // Remove a parte da URL base gerada por Storage::url() se já existir
        $path = Str::startsWith($this->path_thumb, Storage::url(''))
            ? Str::after($this->path_thumb, Storage::url(''))
            : $this->path_thumb;

        return Storage::url($path);
    }

    /**
     * Get the URL for the original image.
     * Agora, o Job vai salvar APENAS O CAMINHO RELATIVO, e este acessador irá gerar a URL COMPLETA.
     */
    public function getOriginalUrlAttribute()
    {
        // Se o path_original já está vazio ou nulo por algum motivo, não tente gerar a URL
        if (empty($this->path_original)) {
            return null;
        }

        // Certifica-se de que path_original não é uma URL completa já
        // Remove a parte da URL base gerada por Storage::url() se já existir
        $path = Str::startsWith($this->path_original, Storage::url(''))
            ? Str::after($this->path_original, Storage::url(''))
            : $this->path_original;

        return Storage::url($path);
    }

    /**
     * Get the URL for the watermarked image.
     * Esta URL será usada para as imagens que os clientes veem.
     * Assume que 'watermarked_path' no metadata será o caminho relativo.
     */
    public function getWatermarkedUrlAttribute()
    {
        if (isset($this->metadata['watermarked_path']) && !empty($this->metadata['watermarked_path'])) {
            $path = $this->metadata['watermarked_path'];
            // Certifica-se de que watermarked_path não é uma URL completa já
            // Remove a parte da URL base gerada por Storage::url() se já existir
            $path = Str::startsWith($path, Storage::url(''))
                ? Str::after($path, Storage::url(''))
                : $path;
            return Storage::url($path);
        }
        return null;
    }
}
