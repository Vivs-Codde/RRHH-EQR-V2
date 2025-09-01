<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoContrato;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Tipos de Contrato",
 *     description="API para gestión de tipos de contrato"
 * )
 */
class TipoContratoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tipos-contrato",
     *     summary="Obtener lista de tipos de contrato",
     *     description="Retorna una lista paginada de todos los tipos de contrato",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth":{}}},
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
     *         description="Lista de tipos de contrato obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(ref="#/components/schemas/TipoContrato")
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
        $query = TipoContrato::query();

        if ($request->has('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }

        $tiposContrato = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tiposContrato
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/tipos-contrato",
     *     summary="Crear un nuevo tipo de contrato",
     *     description="Crea un nuevo tipo de contrato en el sistema",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tipo"},
     *             @OA\Property(property="tipo", type="string", maxLength=100, example="Indefinido"),
     *             @OA\Property(property="estado", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de contrato creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TipoContrato"),
     *             @OA\Property(property="message", type="string", example="Tipo de contrato creado exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tipo' => 'required|string|max:100|unique:tipo_contrato,tipo',
                'estado' => 'boolean'
            ]);

            $validated['estado'] = $validated['estado'] ?? true;
            $tipoContrato = TipoContrato::create($validated);

            return response()->json([
                'success' => true,
                'data' => $tipoContrato,
                'message' => 'Tipo de contrato creado exitosamente'
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
     *     path="/api/tipos-contrato/{id}",
     *     summary="Obtener un tipo de contrato específico",
     *     description="Retorna la información de un tipo de contrato por su ID",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de contrato",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de contrato encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/TipoContrato")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de contrato no encontrado"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $tipoContrato = TipoContrato::find($id);

        if (!$tipoContrato) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de contrato no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tipoContrato
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/tipos-contrato/{id}",
     *     summary="Actualizar un tipo de contrato",
     *     description="Actualiza un tipo de contrato existente",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de contrato a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", example="Contrato por Horas", description="Nombre del tipo de contrato"),
     *             @OA\Property(property="descripcion", type="string", example="Contrato para empleados que trabajan por horas", description="Descripción detallada del tipo de contrato"),
     *             @OA\Property(property="estado", type="boolean", example=true, description="Estado del tipo de contrato")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de contrato actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tipo de contrato actualizado correctamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/TipoContrato")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de contrato no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="nombre", type="array", @OA\Items(type="string", example="El campo nombre es obligatorio."))
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tipoContrato = TipoContrato::find($id);

        if (!$tipoContrato) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de contrato no encontrado'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'tipo' => 'sometimes|required|string|max:100|unique:tipo_contrato,tipo,' . $id,
                'estado' => 'sometimes|boolean'
            ]);

            $tipoContrato->update($validated);

            return response()->json([
                'success' => true,
                'data' => $tipoContrato->fresh(),
                'message' => 'Tipo de contrato actualizado exitosamente'
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
     *     path="/api/tipos-contrato/{id}",
     *     summary="Eliminar un tipo de contrato",
     *     description="Elimina un tipo de contrato existente. Nota: Solo se puede eliminar si no tiene empleados asociados.",
     *     tags={"Tipos de Contrato"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del tipo de contrato a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tipo de contrato eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tipo de contrato eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tipo de contrato no encontrado"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflicto - No se puede eliminar porque tiene empleados asociados",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No se puede eliminar el tipo de contrato porque tiene empleados asociados")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $tipoContrato = TipoContrato::find($id);

        if (!$tipoContrato) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de contrato no encontrado'
            ], 404);
        }

        if ($tipoContrato->empleados()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el tipo de contrato porque está en uso'
            ], 409);
        }

        $tipoContrato->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de contrato eliminado exitosamente'
        ]);
    }
}
