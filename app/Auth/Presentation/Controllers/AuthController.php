<?php

namespace App\Auth\Presentation\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

/**
 * Gestiona login, registro y logout con JWT (tymondesigns/jwt-auth).
 * Se eligió JWT sobre Sanctum porque la API es stateless y puede ser consumida por clientes externos al dominio.
 */
class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * Crea un usuario y devuelve su token JWT.
     */
    #[OA\Post(
        path: '/api/auth/register',
        summary: 'Registrar un nuevo usuario',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Carlos Macero'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'carlos@example.com'),
                    new OA\Property(property: 'password', type: 'string', minLength: 6, example: 'secret123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Usuario creado; devuelve access_token y datos del usuario'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ])->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
        ], 201);
    }

    /**
     * POST /api/auth/login
     * Verifica credenciales y devuelve un token JWT.
     */
    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Iniciar sesión',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'demo@tutramitefacil.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'demo1234'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login exitoso; devuelve access_token'),
            new OA\Response(response: 401, description: 'Credenciales inválidas'),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $token = auth('api')->attempt($credentials);

        if (! $token) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    /**
     * POST /api/auth/logout
     * Invalida el token actual.
     */
    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Cerrar sesión (invalida el token JWT)',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Sesión cerrada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    /**
     * GET /api/auth/me
     * Devuelve el usuario autenticado (útil para el frontend).
     */
    #[OA\Get(
        path: '/api/auth/me',
        summary: 'Obtener datos del usuario autenticado',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Datos del usuario autenticado'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }
}
