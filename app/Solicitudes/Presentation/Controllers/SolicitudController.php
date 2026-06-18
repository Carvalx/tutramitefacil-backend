<?php

namespace App\Solicitudes\Presentation\Controllers;

use App\Solicitudes\Application\Services\SolicitudService;
use App\Solicitudes\Presentation\Requests\StoreSolicitudRequest;
use App\Solicitudes\Presentation\Requests\UpdateSolicitudRequest;
use App\Solicitudes\Presentation\Resources\SolicitudResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

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
    public function index(): JsonResponse
    {
        $solicitudes = $this->service->listar();

        return SolicitudResource::collection($solicitudes)->response();
    }

    /** GET /api/solicitudes/{solicitud} — público. */
    public function show(string $solicitud): JsonResponse
    {
        $entity = $this->service->buscar($solicitud);

        if (! $entity) {
            return response()->json(['message' => 'Solicitud no encontrada.'], 404);
        }

        return (new SolicitudResource($entity))->response();
    }

    /** POST /api/solicitudes — requiere JWT; también encola ProcesarSolicitudJob. */
    public function store(StoreSolicitudRequest $request): JsonResponse
    {
        $entity = $this->service->crear($request->validated());

        return (new SolicitudResource($entity))
            ->response()
            ->setStatusCode(201);
    }

    /** PUT /api/solicitudes/{solicitud} — requiere JWT; encola Job si cambia el estado. */
    public function update(UpdateSolicitudRequest $request, string $solicitud): JsonResponse
    {
        $entity = $this->service->actualizar($solicitud, $request->validated());

        if (! $entity) {
            return response()->json(['message' => 'Solicitud no encontrada.'], 404);
        }

        return (new SolicitudResource($entity))->response();
    }

    /** DELETE /api/solicitudes/{solicitud} — requiere JWT. */
    public function destroy(string $solicitud): JsonResponse
    {
        $eliminado = $this->service->eliminar($solicitud);

        if (! $eliminado) {
            return response()->json(['message' => 'Solicitud no encontrada.'], 404);
        }

        return response()->json(null, 204);
    }
}