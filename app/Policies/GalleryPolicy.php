<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Gallery;
use Illuminate\Auth\Access\HandlesAuthorization;

class GalleryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models (para a listagem geral de galerias).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // Administradores e fotógrafos podem ver a lista de galerias.
        return $user->hasRole(['admin', 'fotografo']);
    }

    /**
     * Determine whether the user can view a specific Gallery (usado para o 'show' da galeria, que carrega PreviewImages.vue).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Gallery  $gallery // A galeria é passada, mas não usada na lógica desta permissão.
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Gallery $gallery)
    {
        // Apenas usuários com a role 'admin' ou 'fotografo' podem ver o preview da galeria.
        return $user->hasRole(['admin', 'fotografo']);
    }


    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // Apenas administradores e fotógrafos podem criar galerias.
        return $user->hasRole(['admin', 'fotografo']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Gallery $gallery)
    {
        // Administradores podem atualizar qualquer galeria.
        // Fotógrafos podem atualizar apenas suas próprias galerias.
        return $user->hasRole('admin') ||
            ($user->hasRole('fotografo') && $user->id === $gallery->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Gallery $gallery)
    {
        // Administradores podem deletar qualquer galeria.
        // Fotógrafos podem deletar apenas suas próprias galerias.
        return $user->hasRole('admin') ||
            ($user->hasRole('fotografo') && $user->id === $gallery->user_id);
    }

    /**
     * Determine whether the user can manage a gallery (e.g., upload images, delete individual images).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Gallery  $gallery // A galeria é passada, mas não usada na lógica desta permissão.
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function manageGallery(User $user, Gallery $gallery)
    {
        // Apenas usuários com a role 'admin' ou 'fotografo' podem gerenciar as imagens da galeria.
        return $user->hasRole(['admin', 'fotografo']);
    }
}
