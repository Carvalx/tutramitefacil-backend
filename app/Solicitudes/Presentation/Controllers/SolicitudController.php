<?php

namespace App\Solicitudes\Presentation\Controllers;

use App\Solicitudes\Application\Services\SolicitudService;
use App\Solicitudes\Presentation\Requests\StoreSolicitudRequest;
use App\Solicitudes\Presentation\Requests\UpdateSolicitudRequest;
use App\Solicitudes\Presentation\Resources\SolicitudResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Solicitudes', description: 'Gestión de solicitudes de ayudas sociales')]
class SolicitudController extends Controller
{
    public function __construct(
        private readonly SolicitudService $service,
    ) {
    }

    #[OA\Get(
        path: '/api/solicitudes',
        summary: 'Listar todas las solicitudes',
        tags: ['Solicitudes'],
        responses: [
            new OA\Response(response: 200, description: 'Listado de solicitudes'),
        ]
    )]
    public function index(): JsonResponse
    {
        $solicitudes = $this->service->listar();

        return SolicitudResource::collection($solicitudes)->response();
    }

    #[OA\Get(
        path: '/api/solicitudes/{solicitud}',
        summary: 'Obtener una solicitud por su UUID',
        tags: ['Solicitudes'],
        parameters: [
            new OA\Parameter(name: 'solicitud', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Solicitud encontrada'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function show(string $solicitud): JsonResponse
    {
        $entity = $this->service->buscar($solicitud);

        if (! $entity) {
            return response()->json(['message' => 'Solicitud no encontrada.'], 404);
        }

        return (new SolicitudResource($entity))->response();
    }

    #[OA\Post(
        path: '/api/solicitudes',
        summary: 'Crear una nueva solicitud (encola Job de procesamiento)',
        security: [['bearerAuth' => []]],
        tags: ['Solicitudes'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['solicitante_id', 'tipo_ayuda', 'fecha_solicitud', 'importe_estimado'],
                properties: [
                    new OA\Property(property: 'solicitante_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'tipo_ayuda', type: 'string', enum: ['Alquiler', 'ChequeBebe', 'IngresoMinimoVital', 'BonoCulturalJoven']),
                    new OA\Property(property: 'fecha_solicitud', type: 'string', format: 'date', example: '2026-06-15'),
                    new OA\Property(property: 'fecha_resolucion', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'importe_estimado', type: 'number', format: 'float', example: 1200.50),
                    new OA\Property(property: 'estado', type: 'string', enum: ['Pendiente', 'EnRevision', 'Concedida', 'Denegada']),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Solicitud creada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreSolicitudRequest $request): JsonResponse
    {
        $entity = $this->service->crear($request->validated());

        return (new SolicitudResource($entity))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Put(
        path: '/api/solicitudes/{solicitud}',
        summary: 'Actualizar una solicitud (encola Job si cambia el estado)',
        security: [['bearerAuth' => []]],
        tags: ['Solicitudes'],
        parameters: [
            new OA\Parameter(name: 'solicitud', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Solicitud actualizada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function update(UpdateSolicitudRequest $request, string $solicitud): JsonResponse
    {
        $entity = $this->service->actualizar($solicitud, $request->validated());

        if (! $entity) {
            return response()->json(['message' => 'Solicitud no encontrada.'], 404);
        }

        return (new SolicitudResource($entity))->response();
    }

    #[OA\Delete(
        path: '/api/solicitudes/{solicitud}',
        summary: 'Eliminar una solicitud',
        security: [['bearerAuth' => []]],
        tags: ['Solicitudes'],
        parameters: [
            new OA\Parameter(name: 'solicitud', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Eliminada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrada'),
        ]
    )]
    public function destroy(string $solicitud): JsonResponse
    {
        $eliminado = $this->service->eliminar($solicitud);

        if (! $eliminado) {
            return response()->json(['message' => 'Solicitud no encontrada.'], 404);
        }

        return response()->json(null, 204);
    }
}