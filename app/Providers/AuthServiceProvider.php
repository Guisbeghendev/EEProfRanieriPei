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
            if ($group) {
                // Admin pode acessar qualquer grupo. Usuário pode acessar grupos a que pertence.
                if (!$user->relationLoaded('groups')) { $user->load('groups'); }
                return $user->hasRole('admin') || $user->groups->contains($group->id);
            }
            // Se nenhum grupo for passado (contexto de listagem, por exemplo)
            // A Policy já define que todos podem ver Any.
            return $user->hasRole('admin');
        });

        // Usuário pode criar galeria se for fotógrafo
        Gate::define('create-gallery', fn(User $user) =>
        $user->hasRole('fotografo')
        );

        // Usuário pode gerenciar galeria (upload de imagens, etc.) se for fotógrafo E dono
        Gate::define('manage-gallery', function (User $user, ?Gallery $gallery = null) {
            if ($gallery) {
                // Fotógrafos gerenciam suas próprias galerias.
                return $user->hasRole('fotografo') && $user->id === $gallery->user_id;
            }
            // Se nenhum modelo for passado (ex: acesso a uma interface geral de gerenciamento de galerias)
            return $user->hasRole('fotografo');
        });

        // GATE PARA ACESSO PÚBLICO OU RESTRITO ÀS GALERIAS INDIVIDUAIS
        // Lida com guests e usuários autenticados para visualização específica.
        Gate::define('view-public-gallery', function (?User $user, Gallery $gallery) {
            if (!$gallery->relationLoaded('groups')) { $gallery->load('groups'); }

            $publicGroup = Group::where('name', 'público')->first();
            $publicGroupId = $publicGroup ? $publicGroup->id : null;

            // Condição 1: Galeria é pública (associada ao grupo 'público')
            if ($publicGroupId && $gallery->groups->contains($publicGroupId)) {
                return true;
            }

            // Se não é pública, o usuário DEVE estar autenticado para continuar
            if (!$user) {
                return false;
            }

            // Garante que as relações 'groups' estejam carregadas para o usuário (se autenticado)
            if (!$user->relationLoaded('groups')) { $user->load('groups'); }

            // Condição 2: Usuário é fotógrafo (pode ver qualquer galeria para fins de gerenciamento)
            if ($user->hasRole('fotografo')) {
                return true;
            }

            // Condição 3: Usuário é o dono da galeria
            if ($gallery->user_id === $user->id) {
                return true;
            }

            // Condição 4: Usuário pertence a qualquer um dos grupos da galeria
            $userGroupIds = $user->groups->pluck('id')->toArray();
            $galleryGroupIds = $gallery->groups->pluck('id')->toArray();

            if (array_intersect($userGroupIds, $galleryGroupIds)) {
                return true;
            }

            return false;
        });

        // Usuário pode gerenciar imagem (criar, atualizar, deletar) se for fotógrafo E dono da galeria
        Gate::define('manage-image', function (User $user, ?Image $image = null) {
            if ($image) {
                $gallery = $image->gallery;
                if (!$gallery) return false;
                // Fotógrafos gerenciam imagens em suas próprias galerias.
                return $user->hasRole('fotografo') && $user->id === $gallery->user_id;
            }
            // Se nenhum modelo for passado (ex: acesso a uma interface geral de upload de imagens)
            return $user->hasRole('fotografo');
        });
    }
}
