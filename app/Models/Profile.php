<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\HasOne; // Não é mais necessário se 'avatarRelation' for removido

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    // O relacionamento avatarRelation foi removido daqui
    // public function avatarRelation(): HasOne
    // {
    //     return $this->hasOne(Avatar::class);
    // }
}
