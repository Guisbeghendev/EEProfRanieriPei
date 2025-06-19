<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Profile;
use App\Models\Group;
use App\Models\Gallery;
use App\Models\Image;

use App\Policies\UserPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\GroupPolicy;
use App\Policies\GalleryPolicy;
use App\Policies\ImagePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Profile::class => ProfilePolicy::class,
        Group::class => GroupPolicy::class,
        Gallery::class => GalleryPolicy::class,
        Image::class => ImagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates úteis para o projeto:

        // SOMENTE ADMIN PODE EXECUTAR (CONVENÇÃO para funções administrativas gerais)
        Gate::define('admin-only', function (User $user) {
            return $user->hasRole('admin');
        });

        // SOMENTE FOTÓGRAFO PODE EXECUTAR (CONVENÇÃO para funções de gerenciamento de conteúdo)
        Gate::define('fotografo-only', function (User $user) {
            return $user->hasRole('fotografo');
        });

        // Usuário pode editar o próprio perfil ou admin pode editar qualquer perfil
        Gate::define('edit-profile', function (User $user, ?Profile $profile = null) {
            if ($profile) {
                // Admin edita qualquer perfil. Usuário edita o próprio.
                return $user->hasRole('admin') || $user->id === $profile->user_id;
            }
            // Se nenhum perfil específico for passado (ex: para a tela de edição do próprio perfil)
            return true;
        });

        // Usuário pode acessar grupo se for membro ou admin
        Gate::define('access-group', function (User $user, ?Group $group = null) {
            // Se não há usuário logado, não pode acessar grupo.
            if (!$user) {
                return false;
            }
            if ($group) {
                // Admin pode acessar qualquer grupo. Usuário pode acessar grupos a que pertence.
                if (!$user->relationLoaded('groups')) { $user->load('groups'); }
                return $user->hasRole('admin') || $user->groups->contains($group->id);
            }
            // Se nenhum grupo for passado (contexto de listagem, por exemplo)
            // A Policy já define que todos podem ver Any.
            // Se essa Gate for para "acessar a funcionalidade de grupos", talvez apenas admin.
            // Se for para "ver os próprios grupos", então $user->groups->isNotEmpty();
            // Estou deixando como admin-only por default para ser mais restritivo.
            return $user->hasRole('admin');
        });

        // Usuário pode criar galeria se for fotógrafo
        Gate::define('create-gallery', fn(User $user) =>
            // Se não há usuário logado, não pode criar galeria.
            $user && $user->hasRole('fotografo')
        );

        // Usuário pode gerenciar galeria (upload de imagens, etc.) se for fotógrafo E dono
        Gate::define('manage-gallery', function (User $user, ?Gallery $gallery = null) {
            // Se não há usuário logado, não pode gerenciar galeria.
            if (!$user) {
                return false;
            }
            if ($gallery) {
                // Fotógrafos gerenciam suas próprias galerias.
                return $user->hasRole('fotografo') && $user->id === $gallery->user_id;
            }
            // Se nenhum modelo for passado (ex: acesso a uma interface geral de gerenciamento de galerias)
            return $user->hasRole('fotografo');
        });

        // GATE PARA ACESSO DE VISUALIZAÇÃO ÀS GALERIAS INDIVIDUAIS
        // Guests não têm acesso a NENHUM conteúdo de galeria.
        Gate::define('view-gallery', function (?User $user, Gallery $gallery) {
            // Se não há usuário logado (é um guest), NUNCA permite acesso.
            if (!$user) {
                return false;
            }

            // Garante que as relações 'groups' estejam carregadas para o usuário
            if (!$user->relationLoaded('groups')) { $user->load('groups'); }
            // Garante que as relações 'groups' estejam carregadas para a galeria
            if (!$gallery->relationLoaded('groups')) { $gallery->load('groups'); }

            // Condição 1: Usuário é fotógrafo (pode ver qualquer galeria para fins de gerenciamento)
            if ($user->hasRole('fotografo')) {
                return true;
            }

            // Condição 2: Usuário é o dono da galeria
            if ($gallery->user_id === $user->id) {
                return true;
            }

            // Condição 3: Usuário pertence a qualquer um dos grupos da galeria
            // Esta lógica agora se aplica a TODOS os grupos, incluindo o 'público'
            // (que é tratado como qualquer outro grupo).
            $userGroupIds = $user->groups->pluck('id')->toArray();
            $galleryGroupIds = $gallery->groups->pluck('id')->toArray();

            return (bool) array_intersect($userGroupIds, $galleryGroupIds);
        });

        // Usuário pode gerenciar imagem (criar, atualizar, deletar) se for fotógrafo E dono da galeria
        Gate::define('manage-image', function (User $user, ?Image $image = null) {
            // Se não há usuário logado, não pode gerenciar imagem.
            if (!$user) {
                return false;
            }
            if ($image) {
                $gallery = $image->gallery;
                if (!$gallery) return false;
                // Fotógrafos gerenciam imagens em suas próprias galerias.
                return $user->hasRole('fotografo') && $user->id === $gallery->user_id;
            }
            return $user->hasRole('fotografo');
        });
    }
}
