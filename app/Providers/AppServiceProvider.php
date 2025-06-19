<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
// As linhas abaixo foram REMOVIDAS, pois o UserObserver é registrado no EventServiceProvider.
// use App\Models\User;
// use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // REMOVIDO: Registro do observer (User::observe(UserObserver::class);)
        // Esta linha foi removida daqui porque o UserObserver já foi registrado
        // corretamente no App\Providers\EventServiceProvider.php.
        // Registrá-lo aqui novamente causaria duplicação.
    }
}
