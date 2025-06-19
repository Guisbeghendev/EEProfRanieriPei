<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\atabase\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; // Já importado

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // 'avatar', // REMOVIDO: O campo 'avatar' não está mais diretamente nesta tabela.
        'birth_date',
        'address',
        'city',
        'state',
        'whatsapp',
        'other_contact',
        'ranieri_text',
        'biography',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the avatar associated with the profile.
     */
    public function avatarRelation(): HasOne // Usamos 'avatarRelation' para evitar conflito com a coluna antiga
    {
        return $this->hasOne(Avatar::class);
    }
}
