<?php

/*
|--------------------------------------------------------------------------
| Caso base de tests
|--------------------------------------------------------------------------
|
| El closure que pasas a cada test se enlaza a una clase de PHPUnit.
| Por defecto es "PHPUnit\Framework\TestCase"; usa pest() para cambiarlo
| o para añadir traits (p.ej. RefreshDatabase en los tests de Feature).
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectativas personalizadas
|--------------------------------------------------------------------------
|
| Aquí puedes extender la API de expect() con tus propias expectativas
| para no repetir la misma lógica de aserción en varios archivos de test.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Funciones auxiliares globales
|--------------------------------------------------------------------------
|
| Helpers globales disponibles en todos los archivos de test.
| Úsalos para extraer lógica de preparación (factories, fixtures, etc.)
| que se repetiría en múltiples tests.
|
*/

function something()
{
    // ..
}
