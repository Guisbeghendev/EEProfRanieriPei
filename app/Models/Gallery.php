<?php

// app/Models/Gallery.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany; // Importar HasMany
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Importar BelongsToMany

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'user_id',
        'watermark_file_used' // Adicionando 'watermark_file_used' ao $fillable
    ];

    // Certifica-se de que 'event_date' é tratado como uma data.
    protected $casts = [
        'event_date' => 'date', // Mantido como 'date' conforme sua especificação
    ];

    /**
     * Get the user that owns the gallery.
     */
    public function user(): BelongsTo // Adicionado tipo de retorno
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the images for the gallery.
     */
    public function images(): HasMany // Adicionado tipo de retorno
    {
        // Este relacionamento assume que você terá um modelo Image e uma coluna gallery_id
        // na tabela 'images'.
        return $this->hasMany(Image::class);
    }

    /**
     * Get the groups associated with the gallery.
     */
    public function groups(): BelongsToMany // Adicionado tipo de retorno
    {
        // Este é o relacionamento correto para a associação many-to-many com Group,
        // que usa uma tabela pivô padrão (group_gallery ou gallery_group, Laravel usa o primeiro).
        return $this->belongsToMany(Group::class);
    }
}
