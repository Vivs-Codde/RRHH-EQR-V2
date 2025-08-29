<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Finca;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Fincas",
 *     description="API para gestión de fincas"
 * )
 */
class FincaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/fincas",
     *     summary="Listar todas las fincas",
     *     description="Obtiene una lista paginada de todas las fincas con opciones de filtrado",
     *     tags={"Fincas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página para paginación",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Cantidad de registros por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Búsqueda por nombre de finca",
     *         required=false,
     *         @OA\Schema(type="string", example="La Esperanza")
     *     ),
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Filtrar por estado (activo/inactivo)",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="departamento",
     *         in="query",
     *         description="Filtrar por departamento",
     *         required=false,
     *         @OA\Schema(type="string", example="Antioquia")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de fincas obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=72),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Finca")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $query = Finca::query();

        if ($request->has('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }

        $fincas = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $fincas
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/fincas",
     *     summary="Crear una nueva finca",
     *     description="Crea una nueva finca en el sistema",
     *     tags={"Fincas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "departamento", "municipio"},
     *             @OA\Property(property="nombre", type="string", example="Finca El Paraíso", description="Nombre de la finca"),
     *             @OA\Property(property="departamento", type="string", example="Antioquia", description="Departamento donde se ubica la finca"),
     *             @OA\Property(property="municipio", type="string", example="Medellín", description="Municipio donde se ubica la finca"),
     *             @OA\Property(property="direccion", type="string", example="Vereda El Retiro, Km 5", description="Dirección específica de la finca"),
     *             @OA\Property(property="telefono", type="string", example="3001234567", description="Teléfono de contacto"),
     *             @OA\Property(property="email", type="string", example="contacto@elparaiso.com", description="Email de contacto"),
     *             @OA\Property(property="area_total", type="number", format="decimal", example=250.75, description="Área total en hectáreas"),
     *             @OA\Property(property="area_cultivada", type="number", format="decimal", example=180.50, description="Área cultivada en hectáreas"),
     *             @OA\Property(property="tipo_cultivo", type="string", example="Café", description="Tipo principal de cultivo"),
     *             @OA\Property(property="estado", type="boolean", example=true, description="Estado de la finca (activa/inactiva)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Finca creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Finca creada correctamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/Finca")
     *         )
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
     *                 @OA\Property(property="nombre", type="array", @OA\Items(type="string", example="El campo nombre es obligatorio.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="El campo email debe ser una dirección de correo válida."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:100|unique:fincas,nombre',
                'estado' => 'boolean'
            ]);

            $validated['estado'] = $validated['estado'] ?? true;
            $finca = Finca::create($validated);

            return response()->json([
                'success' => true,
                'data' => $finca,
                'message' => 'Finca creada exitosamente'
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
     *     path="/api/fincas/{id}",
     *     summary="Obtener una finca específica",
     *     description="Retorna la información de una finca por su ID",
     *     tags={"Fincas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la finca",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Finca encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Finca")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Finca no encontrada"
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $finca = Finca::find($id);

        if (!$finca) {
            return response()->json([
                'success' => false,
                'message' => 'Finca no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $finca
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/fincas/{id}",
     *     summary="Actualizar una finca",
     *     description="Actualiza una finca existente",
     *     tags={"Fincas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la finca a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "departamento", "municipio"},
     *             @OA\Property(property="nombre", type="string", example="Finca El Paraíso Renovada", description="Nombre de la finca"),
     *             @OA\Property(property="departamento", type="string", example="Antioquia", description="Departamento donde se ubica la finca"),
     *             @OA\Property(property="municipio", type="string", example="Medellín", description="Municipio donde se ubica la finca"),
     *             @OA\Property(property="direccion", type="string", example="Vereda El Retiro, Km 8", description="Dirección específica de la finca"),
     *             @OA\Property(property="telefono", type="string", example="3009876543", description="Teléfono de contacto"),
     *             @OA\Property(property="email", type="string", example="nuevo@elparaiso.com", description="Email de contacto"),
     *             @OA\Property(property="area_total", type="number", format="decimal", example=300.00, description="Área total en hectáreas"),
     *             @OA\Property(property="area_cultivada", type="number", format="decimal", example=220.75, description="Área cultivada en hectáreas"),
     *             @OA\Property(property="tipo_cultivo", type="string", example="Café Orgánico", description="Tipo principal de cultivo"),
     *             @OA\Property(property="estado", type="boolean", example=true, description="Estado de la finca (activa/inactiva)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Finca actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Finca actualizada correctamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/Finca")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Finca no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $finca = Finca::find($id);

        if (!$finca) {
            return response()->json([
                'success' => false,
                'message' => 'Finca no encontrada'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'nombre' => 'sometimes|required|string|max:100|unique:fincas,nombre,' . $id,
                'estado' => 'sometimes|boolean'
            ]);

            $finca->update($validated);

            return response()->json([
                'success' => true,
                'data' => $finca->fresh(),
                'message' => 'Finca actualizada exitosamente'
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
     *     path="/api/fincas/{id}",
     *     summary="Eliminar una finca",
     *     description="Elimina una finca existente. Nota: Solo se puede eliminar si no tiene empleados asociados.",
     *     tags={"Fincas"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la finca a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Finca eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Finca eliminada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Finca no encontrada"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflicto - No se puede eliminar porque tiene empleados asociados",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No se puede eliminar la finca porque tiene empleados asociados")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $finca = Finca::find($id);

        if (!$finca) {
            return response()->json([
                'success' => false,
                'message' => 'Finca no encontrada'
            ], 404);
        }

        if ($finca->empleados()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la finca porque tiene empleados asociados'
            ], 409);
        }

        $finca->delete();

        return response()->json([
            'success' => true,
            'message' => 'Finca eliminada exitosamente'
        ]);
    }
}
