<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        // Garante que as relações essenciais (roles, avatar) sejam carregadas.
        // Usamos loadMissing para otimização, carregando-as apenas se ainda não foram.
        if ($user) {
            $user->loadMissing(['roles', 'avatar']);
        }

        return array_merge(parent::share($request), [
            // Compartilha as informações básicas do usuário autenticado e suas roles/avatar.
            // Isso é o essencial para Home.vue e Navbar.vue neste momento.
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(fn ($role) => [ // Mapeia apenas o ID e nome da role
                        'id' => $role->id,
                        'name' => $role->name,
                    ])->toArray(), // Garante que seja um array simples
                    'avatar' => $user->avatar ? [
                        'url' => $user->avatar->url, // Acessa o accessor 'url' do modelo Avatar
                    ] : null,
                ] : null,
            ],
        ]);
    }
}
