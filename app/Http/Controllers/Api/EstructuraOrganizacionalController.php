<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EstructuraOrganizacional;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Estructura Organizacional",
 *     description="API para gestión de estructura organizacional"
 * )
 */
class EstructuraOrganizacionalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/estructuras-organizacionales",
     *     summary="Listar todas las estructuras organizacionales",
     *     description="Obtiene una lista paginada de todas las estructuras organizacionales con opciones de filtrado",
     *     tags={"Estructuras Organizacionales"},
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
     *         name="departamento_id",
     *         in="query",
     *         description="Filtrar por ID de departamento",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="estado",
     *         in="query",
     *         description="Filtrar por estado (activo/inactivo)",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="with_relations",
     *         in="query",
     *         description="Incluir relaciones (departamento, colores)",
     *         required=false,
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de estructuras organizacionales obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=4),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=48),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/EstructuraOrganizacional")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 15), 100);
        $query = EstructuraOrganizacional::query();

        // Incluir relaciones si se solicita
        if ($request->boolean('with_relations')) {
            $query->conRelaciones();
        }

        // Filtrar por estado si se proporciona
        if ($request->has('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }

        // Filtrar por departamento si se proporciona
        if ($request->has('departamento_id')) {
            $query->where('departamento_id', $request->get('departamento_id'));
        }

        $estructuras = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $estructuras
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/estructuras-organizacionales",
     *     summary="Crear una nueva estructura organizacional",
     *     description="Crea una nueva estructura organizacional en el sistema",
     *     tags={"Estructuras Organizacionales"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cargo", "departamento_id"},
     *             @OA\Property(property="cargo", type="string", maxLength=150, example="Gerente de Recursos Humanos", description="Cargo o nombre de la estructura organizacional"),
     *             @OA\Property(property="departamento_id", type="integer", example=1, description="ID del departamento al que pertenece la estructura"),
     *             @OA\Property(property="estado", type="boolean", example=true, description="Estado de la estructura (activa/inactiva). Default: true"),
     *             @OA\Property(
     *                 property="colores",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 description="Array de IDs de colores a asociar (opcional)",
     *                 example={1, 2, 3}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Estructura organizacional creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Estructura organizacional creada exitosamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/EstructuraOrganizacional")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="cargo", type="array", @OA\Items(type="string", example="El campo cargo es obligatorio.")),
     *                 @OA\Property(property="departamento_id", type="array", @OA\Items(type="string", example="El campo departamento_id es obligatorio."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cargo' => 'required|string|max:150',
                'departamento_id' => 'required|integer|exists:departamentos,id',
                'estado' => 'boolean',
                'colores' => 'sometimes|array',
                'colores.*' => 'integer|exists:color,id'
            ]);

            $validated['estado'] = $validated['estado'] ?? true;

            // Crear la estructura organizacional
            $estructura = EstructuraOrganizacional::create([
                'cargo' => $validated['cargo'],
                'departamento_id' => $validated['departamento_id'],
                'estado' => $validated['estado']
            ]);

            // Asociar colores si se proporcionaron
            if (isset($validated['colores'])) {
                $estructura->colores()->sync($validated['colores']);
            }

            // Cargar relaciones para la respuesta
            $estructura->load(['departamento', 'colores']);

            return response()->json([
                'success' => true,
                'data' => $estructura,
                'message' => 'Estructura organizacional creada exitosamente'
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
     *     path="/api/estructura-organizacional/{id}",
     *     summary="Obtener una estructura organizacional específica",
     *     description="Retorna la información de una estructura organizacional por su ID",
     *     tags={"Estructura Organizacional"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la estructura organizacional",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="with_relations",
     *         in="query",
     *         description="Incluir relaciones (departamento, colores)",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estructura organizacional encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/EstructuraOrganizacional")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estructura organizacional no encontrada"
     *     )
     * )
     */
    /**
     * @OA\Get(
     *     path="/api/estructuras-organizacionales/{id}",
     *     summary="Obtener una estructura organizacional específica",
     *     description="Retorna la información de una estructura organizacional por su ID",
     *     tags={"Estructuras Organizacionales"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la estructura organizacional",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="with_relations",
     *         in="query",
     *         description="Incluir relaciones (departamento, padre, hijos, colores)",
     *         required=false,
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estructura organizacional encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/EstructuraOrganizacional")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estructura organizacional no encontrada"
     *     )
     * )
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $query = EstructuraOrganizacional::query();

        // Incluir relaciones si se solicita
        if ($request->boolean('with_relations')) {
            $query->conRelaciones();
        }

        $estructura = $query->find($id);

        if (!$estructura) {
            return response()->json([
                'success' => false,
                'message' => 'Estructura organizacional no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $estructura
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/estructura-organizacional/{id}",
     *     summary="Actualizar una estructura organizacional",
     *     description="Actualiza la información de una estructura organizacional existente",
     *     tags={"Estructura Organizacional"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la estructura organizacional",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="cargo", type="string", maxLength=150, example="Coordinador de Recursos Humanos"),
     *             @OA\Property(property="departamento_id", type="integer", example=2),
     *             @OA\Property(property="estado", type="boolean", example=true),
     *             @OA\Property(
     *                 property="colores",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 description="Array de IDs de colores a asociar",
     *                 example={2, 3, 4}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estructura organizacional actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estructura organizacional no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    /**
     * @OA\Put(
     *     path="/api/estructuras-organizacionales/{id}",
     *     summary="Actualizar una estructura organizacional",
     *     description="Actualiza una estructura organizacional existente",
     *     tags={"Estructuras Organizacionales"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la estructura organizacional a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "nivel"},
     *             @OA\Property(property="nombre", type="string", example="Gerencia de Operaciones Avanzadas", description="Nombre de la estructura organizacional"),
     *             @OA\Property(property="descripcion", type="string", example="Estructura encargada de la gestión operativa avanzada", description="Descripción detallada de la estructura"),
     *             @OA\Property(property="nivel", type="integer", example=2, description="Nivel jerárquico (1 = más alto)"),
     *             @OA\Property(property="padre_id", type="integer", example=1, description="ID de la estructura padre (opcional)"),
     *             @OA\Property(property="departamento_id", type="integer", example=2, description="ID del departamento asociado (opcional)"),
     *             @OA\Property(property="responsable", type="string", example="María González", description="Nombre del responsable"),
     *             @OA\Property(property="email", type="string", example="operaciones.avanzadas@empresa.com", description="Email de contacto"),
     *             @OA\Property(property="telefono", type="string", example="3109876543", description="Teléfono de contacto"),
     *             @OA\Property(property="presupuesto", type="number", format="decimal", example=75000.00, description="Presupuesto asignado"),
     *             @OA\Property(property="estado", type="boolean", example=true, description="Estado de la estructura (activa/inactiva)"),
     *             @OA\Property(
     *                 property="colores",
     *                 type="array",
     *                 @OA\Items(type="integer", example=2),
     *                 description="Array de IDs de colores asociados"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estructura organizacional actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Estructura organizacional actualizada correctamente"),
     *             @OA\Property(property="data", ref="#/components/schemas/EstructuraOrganizacional")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estructura organizacional no encontrada"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $estructura = EstructuraOrganizacional::find($id);

        if (!$estructura) {
            return response()->json([
                'success' => false,
                'message' => 'Estructura organizacional no encontrada'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'cargo' => 'sometimes|required|string|max:150',
                'departamento_id' => 'sometimes|required|integer|exists:departamentos,id',
                'estado' => 'sometimes|boolean',
                'colores' => 'sometimes|array',
                'colores.*' => 'integer|exists:color,id'
            ]);

            // Actualizar campos básicos
            $estructura->update(collect($validated)->except('colores')->toArray());

            // Actualizar colores si se proporcionaron
            if (isset($validated['colores'])) {
                $estructura->colores()->sync($validated['colores']);
            }

            // Cargar relaciones para la respuesta
            $estructura->load(['departamento', 'colores']);

            return response()->json([
                'success' => true,
                'data' => $estructura,
                'message' => 'Estructura organizacional actualizada exitosamente'
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
     *     path="/api/estructura-organizacional/{id}",
     *     summary="Eliminar una estructura organizacional",
     *     description="Elimina una estructura organizacional del sistema",
     *     tags={"Estructura Organizacional"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la estructura organizacional",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estructura organizacional eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estructura organizacional no encontrada"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="No se puede eliminar la estructura porque está en uso"
     *     )
     * )
     */
    /**
     * @OA\Delete(
     *     path="/api/estructuras-organizacionales/{id}",
     *     summary="Eliminar una estructura organizacional",
     *     description="Elimina una estructura organizacional existente. Nota: Solo se puede eliminar si no tiene empleados o estructuras hijas asociadas.",
     *     tags={"Estructuras Organizacionales"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la estructura organizacional a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estructura organizacional eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Estructura organizacional eliminada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estructura organizacional no encontrada"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflicto - No se puede eliminar porque tiene dependencias asociadas",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No se puede eliminar la estructura porque tiene empleados o estructuras hijas asociadas")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $estructura = EstructuraOrganizacional::find($id);

        if (!$estructura) {
            return response()->json([
                'success' => false,
                'message' => 'Estructura organizacional no encontrada'
            ], 404);
        }

        // Verificar si la estructura está en uso por empleados
        if ($estructura->empleados()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la estructura organizacional porque está en uso por empleados'
            ], 409);
        }

        // Desasociar colores antes de eliminar
        $estructura->colores()->detach();
        
        $estructura->delete();

        return response()->json([
            'success' => true,
            'message' => 'Estructura organizacional eliminada exitosamente'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/estructuras-organizacionales/{id}/colores",
     *     summary="Asociar colores a una estructura organizacional",
     *     description="Asocia uno o más colores a una estructura organizacional específica",
     *     tags={"Estructuras Organizacionales"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la estructura organizacional",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"colores"},
     *             @OA\Property(
     *                 property="colores",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 description="Array de IDs de colores a asociar",
     *                 example={1, 2, 3}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Colores asociados exitosamente"
     *     )
     * )
     */
    public function asociarColores(Request $request, string $id): JsonResponse
    {
        $estructura = EstructuraOrganizacional::find($id);

        if (!$estructura) {
            return response()->json([
                'success' => false,
                'message' => 'Estructura organizacional no encontrada'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'colores' => 'required|array',
                'colores.*' => 'integer|exists:color,id'
            ]);

            $estructura->colores()->sync($validated['colores']);
            $estructura->load('colores');

            return response()->json([
                'success' => true,
                'data' => $estructura,
                'message' => 'Colores asociados exitosamente'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }
}
