<?php

namespace App\Solicitantes\Presentation\Controllers;

use App\Solicitantes\Application\Services\SolicitanteService;
use App\Solicitantes\Presentation\Requests\StoreSolicitanteRequest;
use App\Solicitantes\Presentation\Requests\UpdateSolicitanteRequest;
use App\Solicitantes\Presentation\Resources\SolicitanteResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Solicitantes', description: 'Gestión de solicitantes de ayudas sociales')]
class SolicitanteController extends Controller
{
    public function __construct(
        private readonly SolicitanteService $service,
    ) {
    }

    #[OA\Get(
        path: '/api/solicitantes',
        summary: 'Listar todos los solicitantes',
        tags: ['Solicitantes'],
        responses: [
            new OA\Response(response: 200, description: 'Listado de solicitantes'),
        ]
    )]
    public function index(): JsonResponse
    {
        $solicitantes = $this->service->listar();

        return SolicitanteResource::collection($solicitantes)
            ->response();
    }

    #[OA\Get(
        path: '/api/solicitantes/{solicitante}',
        summary: 'Obtener un solicitante por su UUID',
        tags: ['Solicitantes'],
        parameters: [
            new OA\Parameter(name: 'solicitante', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Solicitante encontrado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $solicitante): JsonResponse
    {
        $entity = $this->service->buscar($solicitante);

        if (! $entity) {
            return response()->json(['message' => 'Solicitante no encontrado.'], 404);
        }

        return (new SolicitanteResource($entity))->response();
    }

    #[OA\Post(
        path: '/api/solicitantes',
        summary: 'Crear un nuevo solicitante',
        security: [['bearerAuth' => []]],
        tags: ['Solicitantes'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'apellidos', 'email', 'telefono', 'comunidad_autonoma', 'fecha_registro'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'Carlos'),
                    new OA\Property(property: 'apellidos', type: 'string', example: 'Macero'),
                    new OA\Property(property: 'email', type: 'string', example: 'carlos@example.com'),
                    new OA\Property(property: 'telefono', type: 'string', example: '600000000'),
                    new OA\Property(property: 'comunidad_autonoma', type: 'string', example: 'Comunidad Valenciana'),
                    new OA\Property(property: 'fecha_registro', type: 'string', format: 'date', example: '2026-06-15'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Solicitante creado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreSolicitanteRequest $request): JsonResponse
    {
        $entity = $this->service->crear($request->validated());

        return (new SolicitanteResource($entity))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/solicitantes/{solicitante}',
        summary: 'Actualizar un solicitante existente',
        security: [['bearerAuth' => []]],
        tags: ['Solicitantes'],
        parameters: [
            new OA\Parameter(name: 'solicitante', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Solicitante actualizado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function update(UpdateSolicitanteRequest $request, string $solicitante): JsonResponse
    {
        $entity = $this->service->actualizar($solicitante, $request->validated());

        if (! $entity) {
            return response()->json(['message' => 'Solicitante no encontrado.'], 404);
        }

        return (new SolicitanteResource($entity))->response();
    }

    #[OA\Delete(
        path: '/api/solicitantes/{solicitante}',
        summary: 'Eliminar un solicitante',
        security: [['bearerAuth' => []]],
        tags: ['Solicitantes'],
        parameters: [
            new OA\Parameter(name: 'solicitante', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Eliminado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $solicitante): JsonResponse
    {
        $eliminado = $this->service->eliminar($solicitante);

        if (! $eliminado) {
            return response()->json(['message' => 'Solicitante no encontrado.'], 404);
        }

        return response()->json(null, 204);
    }
}