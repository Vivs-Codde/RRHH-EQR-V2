<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Usuarios",
 *     description="API para gestión de usuarios"
 * )
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Get(
     *     path="/api/users",
     *     operationId="getUsersList",
     *     tags={"Usuarios"},
     *     summary="Obtener lista de usuarios",
     *     description="Retorna una lista de todos los usuarios registrados",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de usuarios obtenida exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => true,
            'message' => 'Lista de usuarios obtenida exitosamente',
            'data' => $users
        ]);
    }

    /**
     * Create a new employee in the system.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Post(
     *     path="/api/users/create-employee",
     *     operationId="createEmployee",
     *     tags={"Usuarios"},
     *     summary="Crear nuevo empleado",
     *     description="Crea un nuevo empleado en el sistema RRHH",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="Juan Pérez García", description="Nombre completo del empleado"),
     *             @OA\Property(property="email", type="string", format="email", example="juan.perez@empresa.com", description="Correo electrónico corporativo (único)"),
     *             @OA\Property(property="password", type="string", format="password", example="empleado123", description="Contraseña temporal (mínimo 8 caracteres)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empleado creado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Empleado creado exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez García"),
     *                 @OA\Property(property="email", type="string", format="email", example="juan.perez@empresa.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z")
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
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     */
    public function createEmployee(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ], [
                'name.regex' => 'El nombre solo puede contener letras y espacios.',
                'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
            ]);

            $employee = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Empleado creado exitosamente',
                'data' => $employee
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
                'message' => 'Error al crear el empleado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Get(
     *     path="/api/users/{id}",
     *     operationId="getUserById",
     *     tags={"Usuarios"},
     *     summary="Obtener detalles de un usuario",
     *     description="Retorna los datos de un usuario específico por ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuario encontrado"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Usuario no encontrado"),
     *             @OA\Property(property="error", type="string", example="No query results for model [App\\Models\\User] 1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Usuario encontrado',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Put(
     *     path="/api/users/{id}",
     *     operationId="updateUser",
     *     tags={"Usuarios"},
     *     summary="Actualizar un usuario existente",
     *     description="Actualiza los datos de un usuario específico por ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe Updated"),
     *             @OA\Property(property="email", type="string", format="email", example="john_updated@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuario actualizado exitosamente"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe Updated"),
     *                 @OA\Property(property="email", type="string", format="email", example="john_updated@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-21T20:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8',
            ]);

            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($validatedData);

            return response()->json([
                'status' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $user
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
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     operationId="deleteUser",
     *     tags={"Usuarios"},
     *     summary="Eliminar un usuario",
     *     description="Elimina un usuario específico por ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario eliminado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuario eliminado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
