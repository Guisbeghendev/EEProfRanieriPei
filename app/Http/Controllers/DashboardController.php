<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // CORREÇÃO APLICADA AQUI:
        // A relação 'avatar' agora está diretamente no modelo User.
        // Carrega o usuário autenticado com suas roles, perfil e avatar.
        $user->load('roles', 'profile', 'avatar'); // <-- LINHA CORRIGIDA

        // Removendo a busca pelo grupo 'público' e seu ID aqui,
        // pois a intenção é NÃO filtrar galerias públicas para exibição.
        // Se a lógica de filtro de grupo 'público' for necessária em OUTROS lugares,
        // ela deve ser aplicada APENAS onde for estritamente necessário.

        // Carrega os grupos do usuário logado e, para cada grupo,
        // carrega as últimas 3 galerias com as imagens relacionadas para as miniaturas.
        // A lógica de exclusão do grupo 'público' FOI REMOVIDA aqui.
        $userGroupsWithLatestGalleries = $user->groups()
            ->with(['galleries' => function ($query) {
                // Eager load a primeira imagem de cada galeria para a miniatura
                $query->with(['images' => function ($subQuery) {
                    $subQuery->orderBy('id')->take(1); // Pega apenas a primeira imagem pela ID
                }])
                    // Ordena as galerias pelas mais recentes
                    ->latest('event_date');

                // A lógica para excluir galerias do grupo 'público' foi removida.
                // Agora, todas as galerias pertencentes aos grupos do usuário serão incluídas,
                // independentemente de pertencerem também ao grupo 'público'.

                $query->take(3); // Limita a 3 galerias por grupo
            }])
            // Também removendo o whereHas externo que filtrava grupos sem galerias visíveis,
            // pois o filtro de 'público' foi removido internamente.
            ->whereHas('galleries', function ($query) {
                $query->latest('event_date');
            })
            ->get();

        // Retorna a view 'Dashboard' do Inertia, passando os dados necessários
        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => $user->toArray(), // Passa o usuário com as roles, perfil e avatar carregados
            ],
            'userGroupsWithLatestGalleries' => $userGroupsWithLatestGalleries,
        ]);
    }
}
