<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'TuTramiteFacil API',
    description: 'API REST para gestión de solicitantes y solicitudes de ayudas sociales (DDD + Clean Architecture).'
)]
#[OA\Server(
    url: 'http://localhost:8010',
    description: 'Servidor local de desarrollo'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
class BaseInfo
{
}
