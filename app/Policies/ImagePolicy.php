<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Image;
use App\Models\Gallery; // Importar Gallery para uso no create, update, delete

class ImagePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Image $image)
    {
        $gallery = $image->gallery; // Carrega a galeria associada à imagem

        // Administradores e fotógrafos (sem acento) sempre podem ver
        if ($user->hasRole(['admin', 'fotografo'])) { // CORRIGIDO AQUI
            return true;
        }

        // Verifica se o usuário pertence a algum dos grupos aos quais a galeria da imagem está vinculada
        return $user->groups->pluck('id')->intersect($gallery->groups->pluck('id'))->isNotEmpty();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Gallery $gallery) // Recebe Gallery no lugar de Image
    {
        // Admin ou fotógrafo (sem acento) podem criar imagens em qualquer galeria.
        // Ou o usuário pode criar imagens se for o dono da galeria.
        return $user->hasRole(['admin', 'fotografo']) || $user->id === $gallery->user_id; // CORRIGIDO AQUI
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Image $image)
    {
        $gallery = $image->gallery; // Carrega a galeria associada à imagem
        // Admin ou fotógrafo (sem acento) podem atualizar qualquer imagem.
        // Ou o usuário pode atualizar a imagem se for o dono da galeria à qual ela pertence.
        return $user->hasRole(['admin', 'fotografo']) || $user->id === $gallery->user_id; // CORRIGIDO AQUI
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Image $image)
    {
        $gallery = $image->gallery; // Carrega a galeria associada à imagem
        // Admin ou fotógrafo (sem acento) podem deletar qualquer imagem.
        // Ou o usuário pode deletar a imagem se for o dono da galeria à qual ela pertence.
        return $user->hasRole(['admin', 'fotografo']) || $user->id === $gallery->user_id; // CORRIGIDO AQUI
    }
}
