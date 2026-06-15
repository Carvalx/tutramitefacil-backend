<?php

namespace App\Solicitantes\Presentation\Controllers;

use App\Solicitantes\Application\Services\SolicitanteService;
use App\Solicitantes\Presentation\Requests\StoreSolicitanteRequest;
use App\Solicitantes\Presentation\Requests\UpdateSolicitanteRequest;
use App\Solicitantes\Presentation\Resources\SolicitanteResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Controller de Solicitantes.
 *
 * Fíjate en lo "delgado" que es: cada método solo hace 3 cosas:
 * 1. Recibe datos ya validados (gracias a las Form Requests)
 * 2. Llama al Application Service (que llama al Repository)
 * 3. Devuelve la respuesta formateada (con el Resource)
 *
 * Toda la lógica real vive en capas inferiores (Application/Domain).
 * Esto es justo lo que se busca con Clean Architecture: el Controller
 * es solo "pegamento" entre HTTP y el negocio.
 */
class SolicitanteController extends Controller
{
    public function __construct(
        private readonly SolicitanteService $service,
    ) {
    }

    /**
     * GET /api/solicitantes
     * Público (sin autenticación), según el enunciado.
     */
    public function index(): JsonResponse
    {
        $solicitantes = $this->service->listar();

        return SolicitanteResource::collection($solicitantes)
            ->response();
    }

    /**
     * GET /api/solicitantes/{solicitante}
     * Público.
     */
    public function show(string $solicitante): JsonResponse
    {
        $entity = $this->service->buscar($solicitante);

        if (! $entity) {
            return response()->json(['message' => 'Solicitante no encontrado.'], 404);
        }

        return (new SolicitanteResource($entity))->response();
    }

    /**
     * POST /api/solicitantes
     * Requiere JWT (se configurará en las rutas).
     */
    public function store(StoreSolicitanteRequest $request): JsonResponse
    {
        $entity = $this->service->crear($request->validated());

        return (new SolicitanteResource($entity))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT /api/solicitantes/{solicitante}
     * Requiere JWT.
     */
    public function update(UpdateSolicitanteRequest $request, string $solicitante): JsonResponse
    {
        $entity = $this->service->actualizar($solicitante, $request->validated());

        if (! $entity) {
            return response()->json(['message' => 'Solicitante no encontrado.'], 404);
        }

        return (new SolicitanteResource($entity))->response();
    }

    /**
     * DELETE /api/solicitantes/{solicitante}
     * Requiere JWT.
     */
    public function destroy(string $solicitante): JsonResponse
    {
        $eliminado = $this->service->eliminar($solicitante);

        if (! $eliminado) {
            return response()->json(['message' => 'Solicitante no encontrado.'], 404);
        }

        return response()->json(null, 204);
    }
}