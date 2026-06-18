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
     * Vincula cada interfaz de Domain con su implementación Eloquent concreta.
     * Laravel inyectará la clase correcta en los constructores vía IoC automáticamente.
     */
    public function register(): void
    {
        // Dominio Solicitantes
        $this->app->bind(
            SolicitanteRepositoryInterface::class,
            EloquentSolicitanteRepository::class,
        );
        // Dominio Solicitudes
        $this->app->bind(
            SolicitudRepositoryInterface::class,
            EloquentSolicitudRepository::class,
        );
    }

    /**
     * Inicialización de servicios (se ejecuta después de register).
     */
    public function boot(): void
    {
        //
    }
}