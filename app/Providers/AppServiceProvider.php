<?php

namespace App\Providers;

use App\Solicitantes\Domain\Repositories\SolicitanteRepositoryInterface;
use App\Solicitantes\Infrastructure\Persistence\EloquentSolicitanteRepository;
use App\Solicitudes\Domain\Repositories\SolicitudRepositoryInterface;
use App\Solicitudes\Infrastructure\Persistence\EloquentSolicitudRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Aquí "conectamos" cada interfaz de Domain con su implementación
     * concreta de Infrastructure. Cuando algo (un Controller, Service, etc.)
     * pida "SolicitanteRepositoryInterface" en su constructor, Laravel
     * le entregará automáticamente una instancia de EloquentSolicitanteRepository.
     *
     * Esto es "Dependency Injection" + "Inversion of Control": el código
     * de Application/Presentation depende de la INTERFAZ (abstracción),
     * y Laravel decide en tiempo de ejecución qué implementación concreta usar.
     */
    public function register(): void
    {
        //Solicitante
        $this->app->bind(
            SolicitanteRepositoryInterface::class,
            EloquentSolicitanteRepository::class,
        );
        //Solicitud
        $this->app->bind(
            SolicitudRepositoryInterface::class,
            EloquentSolicitudRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}