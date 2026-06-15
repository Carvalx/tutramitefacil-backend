<?php

namespace App\Solicitudes\Presentation\Controllers;

use App\Solicitudes\Application\Services\SolicitudService;
use App\Solicitudes\Presentation\Requests\StoreSolicitudRequest;
use App\Solicitudes\Presentation\Requests\UpdateSolicitudRequest;
use App\Solicitudes\Presentation\Resources\SolicitudResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SolicitudController extends Controller
{
    public function __construct(
        private readonly SolicitudService $service,
    ) {
    }

    public function index(): JsonResponse
    {
        $solicitudes = $this->service->listar();

        return SolicitudResource::collection($solicitudes)->response();
    }

    public function show(string $solicitud): JsonResponse
    {
        $entity = $this->service->buscar($solicitud);

        if (! $entity) {
            return response()->json(['message' => 'Solicitud no encontrada.'], 404);
        }

        return (new SolicitudResource($entity))->response();
    }

    public function store(StoreSolicitudRequest $request): JsonResponse
    {
        $entity = $this->service->crear($request->validated());

        return (new SolicitudResource($entity))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateSolicitudRequest $request, string $solicitud): JsonResponse
    {
        $entity = $this->service->actualizar($solicitud, $request->validated());

        if (! $entity) {
            return response()->json(['message' => 'Solicitud no encontrada.'], 404);
        }

        return (new SolicitudResource($entity))->response();
    }

    public function destroy(string $solicitud): JsonResponse
    {
        $eliminado = $this->service->eliminar($solicitud);

        if (! $eliminado) {
            return response()->json(['message' => 'Solicitud no encontrada.'], 404);
        }

        return response()->json(null, 204);
    }
}