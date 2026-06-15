<?php

use App\Auth\Presentation\Controllers\AuthController;
use App\Solicitantes\Presentation\Controllers\SolicitanteController;
use App\Solicitudes\Presentation\Controllers\SolicitudController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas de autenticación (públicas: login/register).
 */
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

/**
 * Rutas protegidas con JWT (requieren 'Authorization: Bearer <token>').
 */
Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
});

/**
 * Solicitantes: GET públicos, POST/PUT/DELETE requieren JWT.
 *
 * apiResource crea las 5 rutas; las separamos en dos grupos
 * usando 'only' para aplicar el middleware solo donde corresponde.
 */
Route::apiResource('solicitantes', SolicitanteController::class)
    ->parameters(['solicitantes' => 'solicitante'])
    ->only(['index', 'show']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('solicitantes', SolicitanteController::class)
        ->parameters(['solicitantes' => 'solicitante'])
        ->only(['store', 'update', 'destroy']);
});

/**
 * Solicitudes: mismo patrón.
 */
Route::apiResource('solicitudes', SolicitudController::class)
    ->parameters(['solicitudes' => 'solicitud'])
    ->only(['index', 'show']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('solicitudes', SolicitudController::class)
        ->parameters(['solicitudes' => 'solicitud'])
        ->only(['store', 'update', 'destroy']);
});