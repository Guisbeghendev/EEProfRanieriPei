<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
// Importações de Controllers de autenticação
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
// Importar o DashboardController
use App\Http\Controllers\DashboardController;
// Importar o ProfileController
use App\Http\Controllers\ProfileController;

// NOVOS IMPORTS DE DASHBOARDS E CONTROLLERS ESPECÍFICOS
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\FotografoDashboardController;
use App\Http\Controllers\Fotografo\GalleryController; // Do subdiretório
use App\Http\Controllers\PublicGalleryController; // NOVO: Importa o PublicGalleryController

// IMPORTS PARA ADMINISTRADOR
use App\Http\Controllers\Admin\GroupController; // Importa o GroupController
use App\Http\Controllers\Admin\UserGroupAssignmentController; // Importa o UserGroupAssignmentController
use App\Http\Controllers\Admin\RoleController; // Importa o RoleController
use App\Http\Controllers\Admin\UserController; // Importa o UserController

// --- ROTAS PÚBLICAS GERAIS ---
Route::get('/', function () {
    return Inertia::render('Home');
});

// Outras rotas públicas informativas
Route::get('/sobre-a-escola', function () {
    return Inertia::render('Sobre/SobreEscola');
})->name('sobre-a-escola');

Route::get('/gremio', function () {
    return Inertia::render('Gremio/Gremio');
})->name('gremio');

Route::get('/brincando-dialogando', function () {
    return Inertia::render('BrincandoDialogando/BrincandoDialogando');
})->name('brincando-dialogando');

Route::get('/simoninhanacozinha', function () {
    return Inertia::render('Simoninhanacozinha/Simoninhanacozinha');
})->name('simoninhanacozinha');

Route::get('/coral-ranieri', function () {
    return Inertia::render('Coral/CoralRanieri');
})->name('coral-ranieri');

// --- ROTAS DE AUTENTICAÇÃO (Breeze Padrão) ---
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// --- NOVAS ROTAS PÚBLICAS PARA GALERIAS (USUÁRIO FINAL) ---
// Estas rotas NÃO estão dentro de middlewares 'auth' ou 'guest'
// para permitir que o PublicGalleryController decida o acesso.
Route::prefix('galleries')->name('public.galleries.')->group(function () {
    // Rota principal para a seleção de grupos ou lista pública de galerias
    Route::get('/', [PublicGalleryController::class, 'index'])->name('index');

    // Rota para listar galerias por grupo (acessível via ID do grupo)
    // Ex: /galleries/group/1
    Route::get('/group/{group}', [PublicGalleryController::class, 'listByGroup'])->name('list-by-group');

    // Rota para exibir os detalhes de uma galeria específica (e suas imagens)
    // Ex: /galleries/10
    Route::get('/{gallery}', [PublicGalleryController::class, 'show'])->name('show');
});


// --- ROTAS AUTENTICADAS ---
Route::middleware('auth')->group(function () {
    // Rota de Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Rotas de verificação de e-mail (Laravel Breeze padrão)
    Route::get('verify-email', [VerifyEmailController::class, 'create'])
        ->middleware('throttle:6,1')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [VerifyEmailController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Rota do Dashboard Geral
    Route::get('/dashboard', DashboardController::class)
        ->middleware(['verified'])
        ->name('dashboard');

    // Rotas de Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    // Para atualizar o perfil, Inertia.js envia POST com _method=patch, que Laravel interpreta como PATCH.
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rota para confirmar senha (necessária para acesso a certas seções ou ações sensíveis)
    Route::get('confirm-password', [AuthenticatedSessionController::class, 'confirmPassword'])
        ->name('password.confirm');
    Route::post('confirm-password', [AuthenticatedSessionController::class, 'storeConfirmedPassword']);

    // --- GRUPO DE ROTAS DO FOTÓGRAFO ---
    // Protegidas com o middleware 'check.permission' e a gate 'fotografo-only'
    Route::prefix('fotografo')->name('fotografo.')->middleware(['check.permission:gate,fotografo-only'])->group(function () {
        Route::get('/dashboard', FotografoDashboardController::class)
            ->name('dashboard');

        // ROTAS PARA O FLUXO DE CRIAÇÃO E UPLOAD DE GALERIAS
        // Rota para listar todas as galerias
        Route::get('galleries', [GalleryController::class, 'index'])
            ->name('galleries.index'); // O nome da rota será 'fotografo.galleries.index'

        // Rota para buscar marcas d'água disponíveis (usada no Create e UploadImg)
        Route::get('galleries/watermarks', [GalleryController::class, 'getAvailableWatermarks'])
            ->name('galleries.watermarks.index');

        // Rota para exibir o formulário de criação de galeria
        Route::get('galleries/create', [GalleryController::class, 'create'])
            ->name('galleries.create');

        // Rota para armazenar a nova galeria (POST do formulário de criação)
        Route::post('galleries', [GalleryController::class, 'store'])
            ->name('galleries.store');

        // Rota para exibir o formulário de edição de galeria
        Route::get('galleries/{gallery}/edit', [GalleryController::class, 'edit'])
            ->name('galleries.edit');

        // Rota para atualizar uma galeria (Inertia usa POST com _method=patch, que Laravel interpreta como PATCH)
        Route::match(['put', 'patch'], 'galleries/{gallery}', [GalleryController::class, 'update'])
            ->name('galleries.update');

        // Rota para deletar uma galeria
        Route::delete('galleries/{gallery}', [GalleryController::class, 'destroy'])
            ->name('galleries.destroy');

        // Rota para exibir a página de upload de imagens para uma galeria específica
        Route::get('galleries/{gallery}/upload-images', [GalleryController::class, 'uploadImages'])
            ->name('galleries.upload-images');

        // Rota para lidar com o upload de imagens individuais para uma galeria específica (POST de imagens)
        Route::post('galleries/{gallery}/images', [GalleryController::class, 'storeImage'])
            ->name('galleries.store-image');

        // ROTAS PARA O FLUXO DE PREVIEW E DELEÇÃO DE IMAGENS
        // Rota para exibir a página de preview de uma galeria
        Route::get('galleries/{gallery}/preview', [GalleryController::class, 'previewImages'])
            ->name('galleries.preview');

        // Rota para deletar uma imagem específica de uma galeria
        // {gallery} é o ID da galeria e {image} é o ID da imagem
        Route::delete('galleries/{gallery}/images/{image}', [GalleryController::class, 'destroyImage'])
            ->name('galleries.images.destroy');

        // Adicione outras rotas específicas do fotógrafo aqui, conforme necessário, passo a passo.
    });

    // --- GRUPO DE ROTAS DO ADMINISTRADOR ---
    Route::prefix('admin')->name('admin.')->middleware(['check.permission:gate,admin-only'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Rotas de Recurso para Grupos (CRUD)
        Route::resource('groups', GroupController::class);

        // Rotas de Recurso para Papéis (CRUD)
        Route::resource('roles', RoleController::class);

        // ROTAS DE ASSOCIAÇÃO EM MASSA (DEVEM VIR ANTES DO RESOURCE DE USUÁRIOS)
        // Rotas para Associação em Massa de Usuários a Grupos
        Route::get('/users/mass-assign-groups', [UserController::class, 'massAssignGroupsIndex'])
            ->name('users.mass-assign-groups.index');
        Route::post('/users/mass-assign-groups', [UserController::class, 'massAssignGroupsStore'])
            ->name('users.mass-assign-groups.store');

        // Rotas para Associação em Massa de Papéis a Usuários
        Route::get('/users/mass-assign-roles', [UserController::class, 'massAssignRolesIndex'])
            ->name('users.mass-assign-roles.index');
        Route::post('/users/mass-assign-roles', [UserController::class, 'massAssignRolesStore'])
            ->name('users.mass-assign-roles.store');

        // Rotas de Recurso para Usuários (CRUD) - DEPOIS das rotas mais específicas
        Route::resource('users', UserController::class);
    });
});
