<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Traits\HasRoles;
// Importe o modelo Avatar que acabamos de criar
use App\Models\Avatar; // <-- IMPORTAÇÃO ADICIONADA

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at', // Manter aqui, pois é preenchido pelo Laravel
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the profile associated with the user.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the groups the user belongs to.
     */
    public function groups(): BelongsToMany
    {
        // Simplificado: o Laravel infere 'group_user', 'user_id', 'group_id'
        return $this->belongsToMany(Group::class);
    }

    /**
     * Get the galleries owned by the user.
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'user_id');
    }

    /**
     * Get the avatar associated with the user.
     * Este é o relacionamento que liga o User ao seu Avatar.
     */
    public function avatar(): HasOne // <-- RELACIONAMENTO ADICIONADO
    {
        return $this->hasOne(Avatar::class);
    }

    // O relacionamento 'roles()' é fornecido pelo trait HasRoles.
}
