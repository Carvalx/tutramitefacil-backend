<?php

namespace App\Solicitudes\Presentation\Controllers;

use App\Solicitudes\Application\Services\SolicitudService;
use App\Solicitudes\Presentation\Requests\StoreSolicitudRequest;
use App\Solicitudes\Presentation\Requests\UpdateSolicitudRequest;
use App\Solicitudes\Presentation\Resources\SolicitudResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

/**
 * Thin controller de Solicitudes: valida → llama al Service → devuelve Resource.
 * POST/PUT/DELETE requieren JWT (middleware configurado en routes/api.php).
 */
class SolicitudController extends Controller
{
    public function __construct(
        private readonly SolicitudService $service,
    ) {
    }

    /** GET /api/solicitudes — público. */
    #[OA\Get(
        path: '/api/solicitudes',
        summary: 'Listar todas las solicitudes',
        tags: ['Solicitudes'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de solicitudes'),
        ]
    )]
    public function index(): JsonResponse
    {
        $solicitudes = $this->service->listar();

        return SolicitudResource::collection($solicitudes)->response();
    }

    /** GET /api/solicitudes/{solicitud} — público. */
    #[OA\Get(
        path: '/api/solicitudes/{solicitud}',
        summary: 'Ver detalle de una solicitud',
        tags: ['Solicitudes'],
        parameters: [
            new OA\Parameter(name: 'solicitud', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Datos de la solicitud'),
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

    /** POST /api/solicitudes — requiere JWT; también encola ProcesarSolicitudJob. */
    #[OA\Post(
        path: '/api/solicitudes',
        summary: 'Crear una nueva solicitud',
        description: 'Al crear la solicitud se encola ProcesarSolicitudJob en Horizon para procesamiento asíncrono.',
        tags: ['Solicitudes'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['solicitante_id', 'tipo_ayuda', 'fecha_solicitud', 'importe_estimado'],
                properties: [
                    new OA\Property(property: 'solicitante_id', type: 'string', format: 'uuid', example: 'uuid-del-solicitante'),
                    new OA\Property(
                        property: 'tipo_ayuda',
                        type: 'string',
                        enum: ['Alquiler', 'ChequeBebe', 'IngresoMinimoVital', 'BonoCulturalJoven'],
                        example: 'Alquiler'
                    ),
                    new OA\Property(property: 'fecha_solicitud', type: 'string', format: 'date', example: '2026-06-15'),
                    new OA\Property(property: 'fecha_resolucion', type: 'string', format: 'date', nullable: true, example: null),
                    new OA\Property(property: 'importe_estimado', type: 'number', format: 'float', example: 500.00),
                    new OA\Property(
                        property: 'estado',
                        type: 'string',
                        enum: ['Pendiente', 'EnRevision', 'Concedida', 'Denegada'],
                        example: 'Pendiente'
                    ),
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

    /** PUT /api/solicitudes/{solicitud} — requiere JWT; encola Job si cambia el estado. */
    #[OA\Put(
        path: '/api/solicitudes/{solicitud}',
        summary: 'Actualizar una solicitud',
        description: 'Si el campo estado cambia, se encola ProcesarSolicitudJob para notificar el nuevo estado.',
        tags: ['Solicitudes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'solicitud', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'solicitante_id', type: 'string', format: 'uuid'),
                    new OA\Property(
                        property: 'tipo_ayuda',
                        type: 'string',
                        enum: ['Alquiler', 'ChequeBebe', 'IngresoMinimoVital', 'BonoCulturalJoven']
                    ),
                    new OA\Property(property: 'fecha_solicitud', type: 'string', format: 'date'),
                    new OA\Property(property: 'fecha_resolucion', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'importe_estimado', type: 'number', format: 'float'),
                    new OA\Property(
                        property: 'estado',
                        type: 'string',
                        enum: ['Pendiente', 'EnRevision', 'Concedida', 'Denegada']
                    ),
                ]
            )
        ),
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

    /** DELETE /api/solicitudes/{solicitud} — requiere JWT. */
    #[OA\Delete(
        path: '/api/solicitudes/{solicitud}',
        summary: 'Eliminar una solicitud',
        tags: ['Solicitudes'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'solicitud', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
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
