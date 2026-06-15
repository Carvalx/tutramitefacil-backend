<?php

use App\Solicitantes\Presentation\Controllers\SolicitanteController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas de la API.
 *
 * Por ahora, TODAS las rutas de Solicitante son públicas.
 * Cuando añadamos JWT (mañana/miércoles), aplicaremos el middleware
 * 'auth:api' SOLO a las rutas POST/PUT/DELETE, dejando GET públicas,
 * tal y como pide el enunciado.
 */

Route::apiResource('solicitantes', SolicitanteController::class)
    ->parameters(['solicitantes' => 'solicitante']);