<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Colores",
 *     description="API para gestión de colores"
 * )
 */
class ColorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/colores",
     *     summary="Obtener lista de colores",
     *     description="Retorna una lista paginada de todos los colores",
     *     tags={"Colores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Elementos por página (máximo 100)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Filtrar por estado (true/false)",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de colores obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(ref="#/components/schemas/Color")
     *                 ),
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $query = Color::query();

        // Filtrar por estado si se proporciona
        if ($request->has('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }

        $colores = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $colores
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/colores",
     *     summary="Crear un nuevo color",
     *     description="Crea un nuevo color en el sistema",
     *     tags={"Colores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"color", "codigo"},
     *             @OA\Property(property="color", type="string", maxLength=50, example="Azul"),
     *             @OA\Property(property="codigo", type="string", maxLength=7, example="#0066CC"),
     *             @OA\Property(property="estado", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Color creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Color"),
     *             @OA\Property(property="message", type="string", example="Color creado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'color' => 'required|string|max:50|unique:color,color',
                'codigo' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/|unique:color,codigo',
                'estado' => 'boolean'
            ]);

            $validated['estado'] = $validated['estado'] ?? true;

            $color = Color::create($validated);

            return response()->json([
                'success' => true,
                'data' => $color,
                'message' => 'Color creado exitosamente'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/colores/{id}",
     *     summary="Obtener un color específico",
     *     description="Retorna la información de un color por su ID",
     *     tags={"Colores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del color",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Color encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Color")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Color no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Color no encontrado")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $color = Color::find($id);

        if (!$color) {
            return response()->json([
                'success' => false,
                'message' => 'Color no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $color
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/colores/{id}",
     *     summary="Actualizar un color",
     *     description="Actualiza la información de un color existente",
     *     tags={"Colores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del color",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="color", type="string", maxLength=50, example="Azul Claro"),
     *             @OA\Property(property="codigo", type="string", maxLength=7, example="#3399FF"),
     *             @OA\Property(property="estado", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Color actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Color"),
     *             @OA\Property(property="message", type="string", example="Color actualizado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Color no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $color = Color::find($id);

        if (!$color) {
            return response()->json([
                'success' => false,
                'message' => 'Color no encontrado'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'color' => 'sometimes|required|string|max:50|unique:color,color,' . $id,
                'codigo' => 'sometimes|required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/|unique:color,codigo,' . $id,
                'estado' => 'sometimes|boolean'
            ]);

            $color->update($validated);

            return response()->json([
                'success' => true,
                'data' => $color->fresh(),
                'message' => 'Color actualizado exitosamente'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/colores/{id}",
     *     summary="Eliminar un color",
     *     description="Elimina un color del sistema",
     *     tags={"Colores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del color",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Color eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Color eliminado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Color no encontrado"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="No se puede eliminar el color porque está en uso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No se puede eliminar el color porque está en uso")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $color = Color::find($id);

        if (!$color) {
            return response()->json([
                'success' => false,
                'message' => 'Color no encontrado'
            ], 404);
        }

        // Verificar si el color está en uso
        if ($color->estructurasOrganizacionales()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el color porque está en uso'
            ], 409);
        }

        $color->delete();

        return response()->json([
            'success' => true,
            'message' => 'Color eliminado exitosamente'
        ]);
    }
}
