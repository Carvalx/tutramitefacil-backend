<?php

namespace App\Auth\Presentation\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * AuthController: login y registro para obtener tokens JWT.
 *
 * No forma parte de Solicitantes/Solicitudes (los dominios de negocio),
 * es infraestructura transversal de autenticación. Por eso vive en
 * su propio "módulo" Auth.
 */
class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * Crea un usuario y devuelve su token JWT.
     */
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
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // auth('api')->attempt() valida credenciales contra la tabla 'users'
        // y, si son correctas, genera un token JWT.
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
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    /**
     * GET /api/auth/me
     * Devuelve el usuario autenticado (útil para el frontend).
     */
    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }
}