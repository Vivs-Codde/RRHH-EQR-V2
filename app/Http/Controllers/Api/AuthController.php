<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Autenticación",
 *     description="API para la autenticación de usuarios"
 * )
 */
class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Post(
     *     path="/api/register",
     *     operationId="register",
     *     tags={"Autenticación"},
     *     summary="Registrar nuevo usuario",
     *     description="Crea un nuevo usuario en el sistema y retorna un token de autenticación",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe", description="Nombre completo del usuario"),
     *             @OA\Property(property="email", type="string", format="email", example="usuario@ejemplo.com", description="Correo electrónico (único)"),
     *             @OA\Property(property="password", type="string", format="password", example="contraseña", description="Contraseña (mínimo 8 caracteres)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registro exitoso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuario registrado exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="usuario@ejemplo.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="El correo ya está en uso.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Iniciar sesión y crear token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Post(
     *     path="/api/login",
     *     operationId="login",
     *     tags={"Autenticación"},
     *     summary="Iniciar sesión",
     *     description="Inicia sesión en el sistema y retorna un token de autenticación",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="usuario@ejemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="contraseña")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Inicio de sesión exitoso"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="usuario@ejemplo.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Credenciales incorrectas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="Las credenciales proporcionadas son incorrectas.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al iniciar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar sesión (revocar token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Post(
     *     path="/api/logout",
     *     operationId="logout",
     *     tags={"Autenticación"},
     *     summary="Cerrar sesión",
     *     description="Cierra la sesión actual revocando el token de autenticación",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cierre de sesión exitoso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cierre de sesión exitoso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Cierre de sesión exitoso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el perfil del usuario autenticado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Get(
     *     path="/api/profile",
     *     operationId="getProfile",
     *     tags={"Autenticación"},
     *     summary="Obtener perfil del usuario",
     *     description="Retorna los datos del usuario autenticado actualmente",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Perfil de usuario obtenido exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="usuario@ejemplo.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function profile(Request $request)
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Perfil de usuario obtenido exitosamente',
                'data' => $request->user()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener el perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
